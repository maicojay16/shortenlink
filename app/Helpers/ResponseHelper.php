<?php 

namespace App\Helpers;

class ResponseHelper
{
    public static function createJsonResponse($status, $message, $data)
    {
        return response()->json(
            [
                'status' => $status, 
                'message' => $message, 
                'data' => $data
            ]
        );
    }
}