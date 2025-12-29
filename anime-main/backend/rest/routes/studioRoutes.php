<?php

/**
 * @OA\Get(
 * path="/studios",
 * tags={"studios"},
 * summary="Get all production studios",
 * @OA\Response(
 * response=200,
 * description="List of studios"
 * )
 * )
 */
Flight::route('GET /studios', function(){
    Flight::json(Flight::studioService()->get_all_studios());
});
?>