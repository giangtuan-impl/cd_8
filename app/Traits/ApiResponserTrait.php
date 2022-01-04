<?php

namespace App\Traits;

trait ApiResponserTrait
{
    public function successResponse($data, $message = null, $code = 200)
    {
        return response()->json([
            'message' => $message,
            'data' => $data
        ], $code);
    }

    public function errorResponse($message = null, $code)
    {
        return response()->json([
            'message' => $message
        ], $code);
    }
}
