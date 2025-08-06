<?php

namespace App\Http\Controllers;

use App\Models\Ruang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RuangController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = DB::table(Ruang::getTable())->select(Ruang::getColumns())->whereNull('deleted_at')->orderByRaw(Ruang::getOrder())->get();
        $response = [
            'from' => "RuangController@index",
            'status' => "success",
            'code' => 200,
            'desc' => [],
            'message' => "",
            'data' => $data
        ];
        return response()->json($this->jsendJson($response),$this->jsendCode($response));
    }

}
