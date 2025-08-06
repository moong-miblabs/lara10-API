<?php

namespace App\Http\Controllers;

use App\Models\Agama;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AgamaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = DB::table(Agama::getTable())->select(Agama::getColumns())->whereNull('deleted_at')->orderByRaw(Agama::getOrder())->get();
        $response = [
            'from' => "AgamaController@index",
            'status' => "success",
            'code' => 200,
            'desc' => [],
            'message' => "",
            'data' => $data
        ];
        return response()->json($this->jsendJson($response),$this->jsendCode($response));
    }

}
