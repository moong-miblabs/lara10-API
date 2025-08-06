<?php

namespace App\Http\Controllers;

use App\Models\Assesmen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class AssesmenController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = DB::table(Assesmen::getTable())->select(Assesmen::getColumns())->whereNull('deleted_at')->orderByRaw(Assesmen::getOrder())->get();
        $response = [
            'from' => "AssesmenController@index",
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
            'pasien_id' => "required",
        ];

        $validator = Validator::make($input,$rules);

        if ($validator->fails()) {
            $response = [
                'from' => "AssesmenController@create",
                'status' => "fail",
                'code' => 400,
                'desc' => $validator->errors()->toArray(),
                'message' => $this->validationMessage($validator),
                'data' => NULL
            ];
            return response()->json($this->jsendJson($response),$this->jsendCode($response));
        }

        $data = Assesmen::create($input);
        $response = [
            'from'      => "AssesmenController@create",
            'status'    => "success",
            'code'      => 200,
            'desc'      => [],
            'message'   => "",
            'data'      => $data
        ];
        return response()->json($this->jsendJson($response),$this->jsendCode($response));
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $data = DB::table(Assesmen::getTable())->where('id',$id)->whereNull('deleted_at')->get();
        $response = [
            'from' => "AssesmenController@show",
            'status' => "success",
            'code' => 200,
            'desc' => [],
            'message' => "",
            'data' => $data
        ];
        return response()->json($this->jsendJson($response),$this->jsendCode($response));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, $id)
    {
        $input = $request->all();
        $filtered = array_filter($input, fn($value) => $value !== NULL && $value !== "");
        $data = Assesmen::update($id,$filtered);

        $response = [
            'from' => "AssesmenController@edit",
            'status' => "success",
            'code' => 200,
            'desc' => [],
            'message' => "",
            'data' => $data
        ];
        return response()->json($this->jsendJson($response),$this->jsendCode($response));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $data = Assesmen::destroy($id);
        $response = [
            'from' => "AssesmenController@destroy",
            'status' => "success",
            'code' => 200,
            'desc' => [],
            'message' => "",
            'data' => $data
        ];
        return response()->json($this->jsendJson($response),$this->jsendCode($response));
    }
}
