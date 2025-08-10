<?php

namespace App\Http\Controllers;

use App\Models\Device;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DeviceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = DB::table(Device::getTable())->select(Device::getColumns())->whereNull('deleted_at')->orderByRaw(Device::getOrder())->get();
        $response = [
            'from' => "DeviceController@index",
            'status' => "success",
            'code' => 200,
            'desc' => [],
            'message' => "",
            'data' => $data
        ];
        return response()->json($this->jsendJson($response),$this->jsendCode($response));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $input = $request->all();
        $rules = [
            'nama_device'   => "required"
        ];

        $validator = Validator::make($input,$rules);

        if ($validator->fails()) {
            $response = [
                'from'      => "DeviceController@create",
                'status'    => "fail",
                'code'      => 400,
                'desc'      => $validator->errors()->toArray(),
                'message'   => $this->validationMessage($validator),
                'data'      => NULL
            ];
            return response()->json($this->jsendJson($response),$this->jsendCode($response));
        }

        $data = Device::create($input);
        $response = [
            'from'      => "DeviceController@create",
            'status'    => "success",
            'code'      => 200,
            'desc'      => [],
            'message'   => "",
            'data'      => $data
        ];
        return response()->json($this->jsendJson($response),$this->jsendCode($response));
    }
}
