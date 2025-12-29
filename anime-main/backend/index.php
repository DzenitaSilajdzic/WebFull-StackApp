<?php
require_once 'vendor/autoload.php';
require_once __DIR__ . '/rest/config.php';


require_once 'rest/services/AuthService.php';
require_once 'middleware/AuthMiddleware.php';
require_once 'rest/services/userService.php';
require_once 'rest/services/animeService.php';
require_once 'rest/services/episodeService.php';
require_once 'rest/services/commentService.php';
require_once 'rest/services/categoryService.php';
require_once 'rest/services/studioService.php';


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


Flight::register('db', 'PDO', array('mysql:host='.Config::DB_HOST().';dbname='.Config::DB_NAME().';port='.Config::DB_PORT(), Config::DB_USER(), Config::DB_PASSWORD(), array(
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
)));


Flight::register('authService', 'AuthService');
Flight::register('authMiddleware', 'AuthMiddleware');
Flight::register('userService', 'userService');
Flight::register('animeService', 'animeService');
Flight::register('categoryService', 'categoryService'); 
Flight::register('episodeService', 'episodeService');
Flight::register('commentService', 'commentService');
Flight::register('studioService', 'studioService');


$path = str_replace($_SERVER['DOCUMENT_ROOT'], '', str_replace('\\', '/', __DIR__));
Flight::set('flight.base_url', $path);


Flight::route('/*', function() {
    $url = Flight::request()->url;
    $method = Flight::request()->method;

    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization');
    header("Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS");

    if ($method === 'OPTIONS') {
        Flight::halt(200);
    }

   if ($method === 'GET' && (
        strpos($url, '/anime') !== FALSE || 
        strpos($url, '/utilities/categories') !== FALSE ||
        strpos($url, '/episodes') !== FALSE || 
        strpos($url, '/comments') !== FALSE || 
        strpos($url, '/studios') !== FALSE
    )) {
        if (strpos($url, '/admin/') === FALSE) {
            return TRUE;
        }
    }

    try {
        $authHeader = Flight::request()->getHeader("Authorization");
        if(!$authHeader) $authHeader = Flight::request()->getHeader("Authentication");

        if(Flight::authMiddleware()->verifyToken($authHeader))
            return TRUE;
            
    } catch (\Exception $e) {
        Flight::halt(401, $e->getMessage());
    }
});



require_once 'rest/routes/animeRoutes.php';
require_once 'rest/routes/AuthRoutes.php';
require_once 'rest/routes/categoryRoutes.php';
require_once 'rest/routes/commentRoutes.php';
require_once 'rest/routes/episodeRoutes.php';
require_once 'rest/routes/studioRoutes.php';
require_once 'rest/routes/userRoutes.php';

Flight::start();
?>