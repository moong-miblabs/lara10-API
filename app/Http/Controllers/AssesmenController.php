<?php

namespace App\Http\Controllers;

use App\Models\Assesmen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

use Ramsey\Uuid\Uuid;
use Illuminate\Support\Carbon;

class AssesmenController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $db = DB::table(Assesmen::getTable())->select(Assesmen::getColumns())->whereNull('deleted_at')->orderByRaw(Assesmen::getOrder());
        if($request->input('pasien_id')) {
            $db->where('as.pasien_id',$request->input('pasien_id'));
        }
        $data = $db->get();
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

        $pasien_id = $request->input('pasien_id');
        $is_first = DB::select("SELECT first_assesmen FROM pasien pp WHERE id=:id",['id'=>$pasien_id]);
        $is_first = (array) $is_first[0];
        if($is_first['first_assesmen']) {
            $data = Assesmen::create($input);
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
