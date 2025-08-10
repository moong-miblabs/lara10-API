<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Hidehalo\Nanoid\Client;

class MasterToSlave extends Controller {
	function see(Request $request) {
		$pasien_id = $request->input('pasien_id');
		$check = DB::select("SELECT light FROM master_to_slave WHERE pasien_id = :pasien_id ORDER BY `on` DESC LIMIT 1",['pasien_id'=>$pasien_id]);
		if(count($check)) {
			$response = [
	            'from' => "MasterToSlave@see",
	            'status' => "success",
	            'code' => 200,
	            'desc' => [],
	            'message' => "",
	            'data' => (bool) $check[0]->light
	        ];
	        return response()->json($this->jsendJson($response),$this->jsendCode($response));
		} else {
			$response = [
                'from' => "MasterToSlave@see",
                'status' => "success",
                'code' => 200,
                'desc' => [],
                'message' => "",
                'data' => FALSE
            ];
            return response()->json($this->jsendJson($response),$this->jsendCode($response));
		}
	}

	function turn_on(Request $request){
		$pasien_id = $request->input('pasien_id');
		$audio_id = $request->input('audio_id');
		$client = new Client();
		$id = $client->formattedId('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', 3);
		DB::insert("INSERT INTO master_to_slave (id,pasien_id,audio_id,`on`,`light`) VALUES (:id,:pasien_id,:audio_id,NOW(),'1')",['id'=>$id,'pasien_id'=>$pasien_id,'audio_id'=>$audio_id]);

		$response = [
            'from' => "MasterToSlave@turn_on",
            'status' => "success",
            'code' => 200,
            'desc' => [],
            'message' => "",
            'data' => NULL
        ];
        return response()->json($this->jsendJson($response),$this->jsendCode($response));
	}

	function turn_off(Request $request){
		$pasien_id = $request->input('pasien_id');
		$data = DB::transaction(function () use ($pasien_id) {
			$task = DB::select("SELECT id, audio_id FROM master_to_slave WHERE pasien_id = :pasien_id AND `light` = '1' ORDER BY `on` DESC LIMIT 1",['pasien_id'=>$pasien_id]);
			if(count($task)) {
				DB::update("UPDATE master_to_slave SET `off` = NOW(), `light` = '0' WHERE id=:id",['id'=>$task[0]->id]);
				$data = DB::select("SELECT * FROM ms_audio WHERE id=:id LIMIT 1",['id'=>$task[0]->audio_id]);
				return $data[0];
			} else {
				return [];
			}
		});

		if($data) {
			$response = [
	            'from' => "MasterToSlave@turn_off",
	            'status' => "success",
	            'code' => 200,
	            'desc' => [],
	            'message' => "",
	            'data' => $data
	        ];
	        return response()->json($this->jsendJson($response),$this->jsendCode($response));
		} else {
			$new_data = [];
			$new_data['id'] = $data['id'];
			$response = [
	            'from' => "MasterToSlave@turn_off",
	            'status' => "success",
	            'code' => 200,
	            'desc' => [],
	            'message' => "",
	            'data' => $data
	        ];
	        return response()->json($this->jsendJson($response),$this->jsendCode($response));
		}
	}
}