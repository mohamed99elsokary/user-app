<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    public function dataResponse($data, $message = null, $code = null)
    {
        $success = [
            'code' => $code ? $code : 200,
            'message' => $message ? $message : 'success',
        ];

        $success = array_merge($success, $data);

        return response()->json($success, 200);
    }
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
