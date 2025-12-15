<?php
require_once 'vendor/autoload.php';
require_once __DIR__ . '/rest/config.php';


require_once 'rest/services/AuthService.php';
require_once 'middleware/AuthMiddleware.php';
require_once 'rest/services/UserService.php';
require_once 'rest/services/AnimeService.php';
require_once 'rest/services/EpisodeService.php';
require_once 'rest/services/CommentService.php';
require_once 'rest/services/CategoryService.php';
require_once 'rest/services/StudioService.php';


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


Flight::register('db', 'PDO', array('mysql:host='.Config::DB_HOST().';dbname='.Config::DB_NAME().';port='.Config::DB_PORT(), Config::DB_USER(), Config::DB_PASSWORD(), array(
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
)));


Flight::register('authService', 'AuthService');
Flight::register('authMiddleware', 'AuthMiddleware');
Flight::register('userService', 'UserService');
Flight::register('animeService', 'AnimeService');


$path = str_replace($_SERVER['DOCUMENT_ROOT'], '', str_replace('\\', '/', __DIR__));
Flight::set('flight.base_url', $path);


Flight::route('/*', function() {
    $url = Flight::request()->url;
    
    if ($url === '/' ||
        strpos($url, '/auth/login') !== FALSE ||
        strpos($url, '/auth/register') !== FALSE ||
        strpos($url, '/docs') !== FALSE) {
        return TRUE;
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


require_once 'rest/routes/AuthRoutes.php';
require_once 'rest/routes/userRoutes.php';
require_once 'rest/routes/animeRoutes.php';

Flight::start();
?>