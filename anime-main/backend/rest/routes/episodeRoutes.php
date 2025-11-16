<?php

/**
* @OA\Get(
* path="/episodes/anime/{anime_id}/default",
* tags={"episodes"},
* summary="Get the first episode of an anime (default for Watch page)",
* @OA\Parameter(
* name="anime_id",
* in="path",
* required=true,
* description="ID of the anime",
* @OA\Schema(type="integer", example=1)
* ),
* @OA\Response(
* response=200,
* description="Returns the first episode object"
* ),
* @OA\Response(response=404, description="No episodes found")
* )
*/
Flight::route('GET /episodes/anime/@anime_id/default', function($anime_id){
    $episode = Flight::episodeService()->get_default_episode($anime_id);
    if ($episode) {
        Flight::json($episode);
    } else {
        Flight::halt(404, json_encode(['error' => 'No episodes found for this anime.']));
    }
});

/**
* @OA\Get(
* path="/episodes/anime/{anime_id}/list",
* tags={"episodes"},
* summary="Get a list of all episodes for an anime (for episode navigation buttons)",
* @OA\Parameter(
* name="anime_id",
* in="path",
* required=true,
* description="ID of the anime",
* @OA\Schema(type="integer", example=1)
* ),
* @OA\Response(
* response=200,
* description="Returns an array of episode objects (id, title, number)"
* )
* )
*/
Flight::route('GET /episodes/anime/@anime_id/list', function($anime_id){
    Flight::json(Flight::episodeService()->get_episodes_by_anime($anime_id));
});


/**
* @OA\Post(
* path="/episodes/add",
* tags={"episodes"},
* summary="Add a new episode to an existing anime (Admin Only)",
* @OA\RequestBody(
* required=true,
* @OA\JsonContent(
* required={"anime_id", "season", "episode_number", "title", "video_url"},
* @OA\Property(property="anime_id", type="integer", example=10),
* @OA\Property(property="season", type="integer", example=1),
* @OA\Property(property="episode_number", type="integer", example=15),
* @OA\Property(property="title", type="string", example="The Grand Finale"),
* @OA\Property(property="video", type="string", example="episode15.mp4"),
* @OA\Property(property="duration", type="integer", example=24, description="Duration in minutes (optional)")
* )
* ),
* @OA\Response(
* response=200,
* description="Episode successfully added"
* ),
* @OA\Response(response=400, description="Validation error"),
* @OA\Response(response=403, description="Forbidden (Requires Admin role)")
* )
*/
Flight::route('POST /episodes/add', function(){
    try {
        $data = Flight::request()->data->getData();
        Flight::json(Flight::episodeService()->add_new_episode($data));
    } catch (Exception $e) {
        Flight::halt(400, json_encode(['error' => $e->getMessage()]));
    }
});