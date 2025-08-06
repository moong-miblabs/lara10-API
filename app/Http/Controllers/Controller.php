<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function jsendJson(array $assoc) : array {
        unset($assoc['from']);
        return $assoc;
    }

    public function jsendCode(array $assoc) : int {
        return 200;
    }

    public function validationMessage($validator) : string {
        $firstMessage = $validator->errors()->first(); // Ambil satu pesan pertama
        $total = count($validator->errors()->all()) - 1;

        // Buat message seperti default Laravel
        $message = $total > 0 ? "$firstMessage (and $total more error" . ($total > 1 ? "s" : "") . ")" : $firstMessage;
        return $message;
    }
}
