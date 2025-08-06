<?php

namespace App\Helper;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;
use Carbon\Carbon;

class JsonwebtokenHelper {

    public static function sign($arr_assoc){
        $key = env('APP_KEY');
        $now = Carbon::now(new \DateTimeZone(env('APP_TIMEZONE','Asia/Jakarta')));
        $exp = Carbon::create($now->year,$now->month,$now->day,23,59,59,env('APP_TIMEZONE','Asia/Jakarta'));
        $options = [
            // 'iat' => $now->timestamp,
            // 'exp' => $exp->timestamp
        ];
        $arr_assoc = array_merge($arr_assoc,$options);
        return JWT::encode($arr_assoc, $key, 'HS256');
    }

    public static function verify($token){
        $key = env('APP_KEY');
        try {
            $decode = JWT::decode($token, new Key($key, 'HS256'));
            return ['error'=>false, 'message'=> '', 'data'=>$decode];
        } catch(ExpiredException $e) {
            return ['error'=>true, 'message'=> "jwt expired", 'data'=>$e->getPayload()];
        } catch(\Exception $e) {
            return ['error'=>true, 'message'=> $e->getMessage(), 'data'=>NULL];
        }
    }
}