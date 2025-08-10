<?php

namespace App\Http\Controllers;

use App\Models\Audio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AudioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $db = DB::table(Audio::getTable())->select(Audio::getColumns())->whereNull('deleted_at')->orderByRaw(Audio::getOrder());
        if($request->input('agama_id')) {
            $db->where('au.agama_id',$request->input('agama_id'));
        }
        $data = $db->get();
        $response = [
            'from' => "AudioController@index",
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
                'from' => "AudioController@create",
                'status' => "fail",
                'code' => 400,
                'desc' => $validator->errors()->toArray(),
                'message' => $this->validationMessage($validator),
                'data' => NULL
            ];
            return response()->json($this->jsendJson($response),$this->jsendCode($response));
        }

        $pasien_id = $request->input('pasien_id');
        $is_first = DB::select("SELECT first_assesmen FROM pasien pp WHERE id=:id",['id'=>$pasien_id]);
        $is_first = (array) $is_first[0];
        if($is_first['first_assesmen']) {
            $data = Audio::create($input);
        } else {
            $data = DB::transaction(function () use ($input) {
                $now = Carbon::now(new \DateTimeZone(env('TIMEZONE','Asia/Jakarta')));

                $columns = array_filter($input, fn($key) => in_array($key, ['pasien_id','keyakinan_c1','keyakinan_e1','praktik_c1','praktik_e1','pengalaman_c1','pengalaman_e1','skor','klasifikasi','keyakinan','praktik','pengalaman','perasaan','resume_terapis']), ARRAY_FILTER_USE_KEY);
                if(empty($columns)) throw new \Exception('Data does not match the column.');
                $columns['id'] = Uuid::uuid4()->toString();
                $columns['created_at'] = $columns['updated_at'] = $now;

                DB::table('pasien')->where('id',$input['pasien_id'])->whereNull('deleted_at')->update(['first_assesmen'=>$columns['id'], 'updated_at'=>$now]);
                DB::table('assesmen')->insert($columns);
                $data  = DB::table('assesmen')->find($columns['id']);
                return (array) $data;
            });
        }
        $response = [
            'from'      => "AudioController@create",
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
        $data = DB::table(Audio::getTable())->where('id',$id)->whereNull('deleted_at')->get();
        $response = [
            'from' => "AudioController@show",
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
        $data = Audio::update($id,$filtered);

        $response = [
            'from' => "AudioController@edit",
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
        $data = Audio::destroy($id);
        $response = [
            'from' => "AudioController@destroy",
            'status' => "success",
            'code' => 200,
            'desc' => [],
            'message' => "",
            'data' => $data
        ];
        return response()->json($this->jsendJson($response),$this->jsendCode($response));
    }
}
