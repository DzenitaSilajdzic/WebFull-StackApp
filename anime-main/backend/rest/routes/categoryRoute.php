<?php
/**
* @OA\Get(
* path="/utilities/categories",
* tags={"utilities"},
* summary="Get a list of all active categories",
* @OA\Response(
* response=200,
* description="Array of categories (ID and Name)"
* )
* )
*/
Flight::route('GET /utilities/categories', function(){
    Flight::json(Flight::categoryService()->get_active_categories());
});