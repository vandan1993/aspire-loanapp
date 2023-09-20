<?php

namespace App\Traits;


trait HttpResponse {

    protected function success($data , $message = null , $code = null)
    {
      $code = $code ?? 200;
         return response()->json([
            'status' => "Success",
            'message' => $message ,
            'data' => $data,
          ] , $code);
    }

    protected function errorMsg($data , $message = null , $code=null)
    {
      $code = $code ?? 422;
         return response()->json([
            'status' => "Error",
            'message' => $message ,
            'data' => $data,
          ] , $code );
    }
}


?>