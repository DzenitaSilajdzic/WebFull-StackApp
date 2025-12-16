<?php

/**
* @OA\Get(
* path="/comments/anime/{anime_id}",
* tags={"comments"},
* summary="Get all active comments for a specific anime",
* @OA\Parameter(
* name="anime_id",
* in="path",
* required=true,
* description="ID of the anime",
* @OA\Schema(type="integer", example=1)
* ),
* @OA\Response(
* response=200,
* description="Returns a list of comments"
* )
* )
*/
Flight::route('GET /comments/anime/@anime_id', function($anime_id){
    Flight::json(Flight::commentService()->get_anime_comments($anime_id));
});

/**
* @OA\Post(
* path="/comments/add",
* tags={"comments"},
* summary="Post a new comment",
* @OA\RequestBody(
* required=true,
* @OA\JsonContent(
* required={"user_id", "anime_id", "text"},
* @OA\Property(property="user_id", type="integer", example=1),
* @OA\Property(property="anime_id", type="integer", example=10),
* @OA\Property(property="text", type="string", example="I really enjoyed this episode!"),
* @OA\Property(property="reply_id", type="integer", example=5, description="Optional ID of the comment being replied to")
* )
* ),
* @OA\Response(
* response=200,
* description="Comment successfully posted"
* ),
* @OA\Response(response=400, description="Validation error")
* )
*/
Flight::route('POST /comments/add', function(){
    try {
        $data = Flight::request()->data->getData();
        Flight::json(Flight::commentService()->add_comment($data));
    } catch (Exception $e) {
        Flight::halt(400, json_encode(['error' => $e->getMessage()]));
    }
});

/**
* @OA\Delete(
* path="/comments/{id}",
* tags={"comments"},
* summary="Soft delete a comment (set status to 'hidden')",
* @OA\Parameter(
* name="id",
* in="path",
* required=true,
* description="Comment ID to remove",
* @OA\Schema(type="integer", example=1)
* ),
* @OA\Response(
* response=200,
* description="Comment status updated to hidden"
* ),
* @OA\Response(response=403, description="Forbidden (Requires ownership or Admin role)")
* )
*/
Flight::route('DELETE /comments/@id', function($id){
    Flight::auth_middleware()->autorizeRole(Roles::ADMIN);
    try {
        Flight::json(Flight::commentService()->remove_comment($id), 200);
    } catch (Exception $e) {
        Flight::halt(400, json_encode(['error' => $e->getMessage()]));
    }
});
