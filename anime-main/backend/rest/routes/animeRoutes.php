<?php

/**
* @OA\Get(
* path="/anime",
* tags={"anime"},
* summary="Get a list of all anime with aggregated data",
* @OA\Parameter(
* name="offset",
* in="query",
* required=false,
* @OA\Schema(type="integer", default=0),
* description="Offset for pagination"
* ),
* @OA\Parameter(
* name="limit",
* in="query",
* required=false,
* @OA\Schema(type="integer", default=10),
* description="Limit for pagination"
* ),
* @OA\Parameter(
* name="category_id",
* in="query",
* required=false,
* @OA\Schema(type="integer"),
* description="Filter by category ID"
* ),
* @OA\Response(
* response=200,
* description="Array of anime list items"
* )
* )
*/
Flight::route('GET /anime', function(){
    $offset = Flight::request()->query['offset'] ?? 0;
    $limit = Flight::request()->query['limit'] ?? 10;
    $category_id = Flight::request()->query['category_id'] ?? null;
   
    Flight::json(Flight::animeService()->get_anime_listing($offset, $limit, $category_id));
});

/**
* @OA\Get(
* path="/anime/{id}",
* tags={"anime"},
* summary="Get detailed information for a single anime",
* @OA\Parameter(
* name="id",
* in="path",
* required=true,
* description="ID of the anime",
* @OA\Schema(type="integer", example=1)
* ),
* @OA\Response(
* response=200,
* description="Returns the anime details"
* ),
* @OA\Response(response=404, description="Anime not found")
* )
*/
Flight::route('GET /anime/@id', function($id){
    try {
        Flight::json(Flight::animeService()->get_anime_details($id));
    } catch (Exception $e) {
        Flight::halt(404, json_encode(['error' => $e->getMessage()]));
    }
});

/**
 * @OA\Post(
 *   path="/anime/add",
 *   tags={"anime"},
 *   summary="Add a new anime and its first episode (Admin Only)",
 *   @OA\RequestBody(
 *     required=true,
 *     @OA\JsonContent(
 *       required={"title", "type", "release_date", "details", "episode_video"},
 *       @OA\Property(
 *         property="title",
 *         type="string",
 *         example="New Anime Series"
 *       ),
 *       @OA\Property(
 *         property="type",
 *         type="string",
 *         example="TV"
 *       ),
 *       @OA\Property(
 *         property="release_date",
 *         type="string",
 *         format="date",
 *         example="2025-01-01"
 *       ),
 *       @OA\Property(
 *         property="details",
 *         type="string",
 *         example="A summary of the new show"
 *       ),
 *       @OA\Property(
 *         property="episode_video",
 *         type="string",
 *         example="episode1.mp4"
 *       ),
 *       @OA\Property(
 *         property="studio_id",
 *         type="integer",
 *         example=1,
 *         description="ID of the main studio"
 *       ),
 *       @OA\Property(
 *         property="category_ids",
 *         type="array",
 *         items=@OA\Items(type="integer"),
 *         example={1, 3},
 *         description="Array of category IDs"
 *       )
 *     )
 *   ),
 *   @OA\Response(
 *     response=200,
 *     description="Anime successfully added"
 *   ),
 *   @OA\Response(
 *     response=400,
 *     description="Validation error"
 *   ),
 *   @OA\Response(
 *     response=403,
 *     description="Forbidden (Requires Admin role)"
 *   )
 * )
 */

Flight::route('POST /admin/anime/add', function() {
    Flight::authMiddleware()->authorize(Roles::ADMIN);
    try {
        $data = Flight::request()->data->getData();
        Flight::json(Flight::animeService()->add_new_anime($data));
    } catch (Exception $e) {
        Flight::halt(400, json_encode(['error' => $e->getMessage()]));
    }
});

/**
* @OA\Delete(
* path="/anime/{id}",
* tags={"anime"},
* summary="Soft delete an anime (Admin Only)",
* @OA\Parameter(
* name="id",
* in="path",
* required=true,
* description="Anime ID to remove (status='deleted')",
* @OA\Schema(type="integer", example=1)
* ),
* @OA\Response(
* response=200,
* description="Anime status updated to deleted"
* ),
* @OA\Response(response=403, description="Forbidden (Requires Admin role)")
* )
*/
Flight::route('DELETE /anime/@id', function($id){
    Flight::authMiddleware()->authorize(Roles::ADMIN);
    try {
        Flight::json(Flight::animeService()->remove_anime($id), 200);
    } catch (Exception $e) {
        Flight::halt(400, json_encode(['error' => $e->getMessage()]));
    }
});