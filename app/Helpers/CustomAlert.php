<?php

namespace App\Helpers;

class CustomAlert {
    public static function alert($alertType, $message)
    {
        return [
            'alert-type' => $alertType,
            'message' => $message,
        ];
    }
}