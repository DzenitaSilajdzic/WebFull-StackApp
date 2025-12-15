<?php
require_once 'vendor/autoload.php';
require_once __DIR__ . '/rest/config.php';

// 1. Load Services & Middleware
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

// 2. Database Connection
Flight::register('db', 'PDO', array('mysql:host='.Config::DB_HOST().';dbname='.Config::DB_NAME().';port='.Config::DB_PORT(), Config::DB_USER(), Config::DB_PASSWORD(), array(
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
)));

// 3. Register Services (CamelCase for easy access)
Flight::register('authService', 'AuthService');
Flight::register('authMiddleware', 'AuthMiddleware');
Flight::register('userService', 'UserService');
Flight::register('animeService', 'AnimeService');

// 4. FIX: Dynamic Base URL Configuration
// This automatically detects /WebFull-StackApp/anime-main/backend so you don't have to guess
$path = str_replace($_SERVER['DOCUMENT_ROOT'], '', str_replace('\\', '/', __DIR__));
Flight::set('flight.base_url', $path);

// 5. Global Middleware (Security)
Flight::route('/*', function() {
    $url = Flight::request()->url;
    
    // Public routes (Login, Register, Docs)
    if ($url === '/' ||
        strpos($url, '/auth/login') !== FALSE ||
        strpos($url, '/auth/register') !== FALSE ||
        strpos($url, '/docs') !== FALSE) {
        return TRUE;
    } 
    
    // Protected routes
    try {
        $authHeader = Flight::request()->getHeader("Authorization");
        if(!$authHeader) $authHeader = Flight::request()->getHeader("Authentication");

        if(Flight::authMiddleware()->verifyToken($authHeader))
            return TRUE;
            
    } catch (\Exception $e) {
        Flight::halt(401, $e->getMessage());
    }
});

// 6. Load Routes
require_once 'rest/routes/AuthRoutes.php';
require_once 'rest/routes/userRoutes.php';
require_once 'rest/routes/animeRoutes.php';
// Add other route files here as needed...

Flight::start();
?>