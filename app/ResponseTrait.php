<?php

namespace App;

trait ResponseTrait
{
    public function Success($data, $message, $code = 200)
    {
        return response()->json([
            'data' => $data,
            'message' => $message,
            'code' => $code
        ]);
    }
//    public function Error($data, $message, $code = 401)
//    {
//        return response()->json([
//            'data' => $data,
//            'message' => $message,
//            'code' => $code
//        ]);
//    }
}
