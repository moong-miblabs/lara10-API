<?php

namespace App\Http\Controllers;

use App\Models\Pasien;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

use App\Models\Agama;
use App\Models\Ruang;
use App\Models\Assesmen;

class PasienController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = DB::select("
            SELECT
                pp.id, pp.nama_pasien, rg.nama_device, rg.gedung, rg.lantai, rg.kamar, ra.nama_agama
            FROM
                pasien pp
            LEFT JOIN 
                ms_ruang rg ON pp.ruang_id = rg.id
            LEFT JOIN
                ref_agama ra ON pp.agama_id = ra.id
            WHERE
                pp.deleted_at IS NULL
            ORDER BY
                nama_pasien ASC
            ");
        $response = [
            'from' => "PasienController@index",
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
            'nama_pasien'   => "required",
            'agama_id'      => "required",
            'ruang_id'      => "required",
            'tanggal_lahir' => "required",
            'jenis_kelamin' => "required"
        ];

        $validator = Validator::make($input,$rules);

        if ($validator->fails()) {
            $response = [
                'from' => "PasienController@create",
                'status' => "fail",
                'code' => 400,
                'desc' => $validator->errors()->toArray(),
                'message' => $this->validationMessage($validator),
                'data' => NULL
            ];
            return response()->json($this->jsendJson($response),$this->jsendCode($response));
        }

        $data = Pasien::create($input);
        $response = [
            'from'      => "PasienController@create",
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
        $columns = Pasien::getColumns();
        $column_agama = Agama::getColumnAllias();
        $column_ruang = Ruang::getColumnAllias();
        $column_assesmen = Assesmen::getColumnAllias();
        $data = DB::table(Pasien::getTable())
        ->leftJoin(Agama::getTable(),'pp.agama_id','=','ra.id')
        ->leftJoin(Ruang::getTable(),'pp.ruang_id','=','rg.id')
        ->leftJoin(Assesmen::getTable(),'pp.first_assesmen','=','as.id')
        ->select($columns)->addSelect($column_agama)->addSelect($column_ruang)->addSelect($column_assesmen)
        ->whereNull('pp.deleted_at')
        ->where('pp.id',$id)
        ->get();
        $response = [
            'from' => "PasienController@show",
            'status' => "success",
            'code' => 200,
            'desc' => [],
            'message' => "",
            'data' => $data[0]
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
        $data = Pasien::update($id,$filtered);

        $response = [
            'from' => "PasienController@edit",
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
        $data = Pasien::destroy($id);
        $response = [
            'from' => "PasienController@destroy",
            'status' => "success",
            'code' => 200,
            'desc' => [],
            'message' => "",
            'data' => $data
        ];
        return response()->json($this->jsendJson($response),$this->jsendCode($response));
    }
}
