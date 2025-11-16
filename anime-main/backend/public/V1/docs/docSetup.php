<<?php
/**
* @OA\Info(
*     title="API",
*     description="Anime API",
*     version="1.0",
*     @OA\Contact(
*         email="dzenita.silahdzic@stu.ibu.edu.ba",
*         name="Anime"
*     )
* )
*/
/**
* @OA\Server(
*     url= "http://http://localhost/WebFull-StackApp/anime-main/backend",
*     description="API server"
* )
*/
/**
* @OA\SecurityScheme(
*     securityScheme="ApiKey",
*     type="apiKey",
*     in="header",
*     name="Authentication"
* )
*/

/**
 * @OA\Schema(
 * schema="Anime",
 * title="Anime",
 * description="Details of a single anime entry",
 * @OA\Property(
 * property="id",
 * type="integer",
 * format="int64",
 * description="Unique identifier for the anime"
 * ),
 * @OA\Property(
 * property="title",
 * type="string",
 * description="The official title of the anime"
 * ),
 * @OA\Property(
 * property="episodes",
 * type="integer",
 * description="The number of episodes"
 * ),
 * @OA\Property(
 * property="rating",
 * type="number",
 * format="float",
 * description="The average rating of the anime (e.g., 8.5)"
 * ),
 * @OA\Property(
 * property="synopsis",
 * type="string",
 * description="A brief summary of the anime plot"
 * ),
 * example={
 * "id": 101,
 * "title": "Samurai Champloo",
 * "episodes": 26,
 * "rating": 8.5,
 * "synopsis": "A tale of three disparate individuals on a journey to find a samurai who smells of sunflowers."
 * }
 * )
 */

/**
 * @OA\Schema(
 * schema="AnimeList",
 * title="Anime List",
 * description="A collection of anime entries",
 * type="array",
 * @OA\Items(
 * ref="#/components/schemas/Anime"
 * )
 * )
 */

/**
 * @OA\Schema(
 * schema="Error",
 * title="Error",
 * description="Standard error response object",
 * @OA\Property(
 * property="code",
 * type="integer",
 * format="int32",
 * description="Error code (e.g., 404, 500)"
 * ),
 * @OA\Property(
 * property="message",
 * type="string",
 * description="A descriptive error message"
 * ),
 * example={
 * "code": 404,
 * "message": "Anime not found with the given ID."
 * }
 * )
 */