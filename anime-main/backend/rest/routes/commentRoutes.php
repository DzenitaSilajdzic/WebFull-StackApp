<?php

require_once __DIR__ . '/../services/commentService.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

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
 * summary="Delete a comment (Admin only)",
 * security={{"ApiKeyAuth": {}}},
 * @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 * @OA\Response(response="200", description="Comment deleted successfully"),
 * @OA\Response(response="500", description="Internal Server Error")
 * )
 */
Flight::route("DELETE /comments/@id", function ($id) {
    
    Flight::authMiddleware()->authorize(Roles::ADMIN);

    try {
        Flight::commentService()->delete($id);
        Flight::json(['message' => "Comment deleted successfully"]);
    } catch (Exception $e) {
        Flight::json(['error' => $e->getMessage()], 500);
    }
});