<?php

require_once 'vendor/autoload.php';



require_once '/rest/services/UserService.php';
require_once '/rest/services/AnimeService.php';
require_once '/rest/services/EpisodeService.php';
require_once '/rest/services/CommentService.php';
require_once '/rest/services/CategoryService.php';
require_once '/rest/services/StudioService.php';

Flight::register('userService', 'UserService');
Flight::register('animeService', 'AnimeService');
Flight::register('episodeService', 'EpisodeService');
Flight::register('commentService', 'CommentService');
Flight::register('categoryService', 'CategoryService');
Flight::register('studioService', 'StudioService');



require_once '/rest/routes/UserRoutes.php';
require_once '/rest/routes/AnimeRoutes.php';
require_once '/rest/routes/CommentRoutes.php';
require_once '/rest/routes/EpisodeRoutes.php';
require_once '/rest/routes/UtilityRoutes.php';


Flight::start();