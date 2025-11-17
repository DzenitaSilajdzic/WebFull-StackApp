<?php

/**
* @OA\Get(
* path="/utilities/studios",
* tags={"utilities"},
* summary="Get a list of all working studios",
* @OA\Response(
* response=200,
* description="Array of studios (ID and Name)"
* )
* )
*/
Flight::route('GET /utilities/studios', function(){
    Flight::json(Flight::studioService()->get_active_studios());
});