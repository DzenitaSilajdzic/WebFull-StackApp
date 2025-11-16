<?php

require 'flight/Flight.php';

require_once 'vendor/autoload.php';
require_once 'config.php'; 
require_once 'swagger.php'; 


Flight::set('flight.base_url', '/');


Flight::before('start', function(&$params, &$output){

    header("Access-Control-Allow-Origin: *");

    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, PATCH, OPTIONS");

    header("Access-Control-Allow-Headers: Content-Type, X-Requested-With");


    if (Flight::request()->method == 'OPTIONS') {
        exit(0);
    }


    Flight::response()->header('Content-Type', 'application/json');
});


Flight::map('json', function($data, $status = 200) {
    Flight::response()
        ->status($status)
        ->header('Content-Type', 'application/json')
        ->write(json_encode($data))
        ->send();
});



require_once 'services/UserService.php';
require_once 'services/AnimeService.php';
require_once 'services/EpisodeService.php';
require_once 'services/CommentService.php';
require_once 'services/CategoryService.php';
require_once 'services/StudioService.php';

Flight::register('userService', 'UserService');
Flight::register('animeService', 'AnimeService');
Flight::register('episodeService', 'EpisodeService');
Flight::register('commentService', 'CommentService');
Flight::register('categoryService', 'CategoryService');
Flight::register('studioService', 'StudioService');



require_once 'routes/UserRoutes.php';
require_once 'routes/AnimeRoutes.php';
require_once 'routes/CommentRoutes.php';
require_once 'routes/EpisodeRoutes.php';
require_once 'routes/UtilityRoutes.php';


Flight::route('GET /docs.json', function() {
    $openapi = \OpenApi\scan([
        __DIR__ . '/routes',
        __DIR__ . '/swagger.php'
    ]);
    Flight::json($openapi->toArray());
});

Flight::start();