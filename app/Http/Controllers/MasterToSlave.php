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
		$task = $request->input('task','play');
		$client = new Client();
		$id = $client->formattedId('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', 3);
		DB::insert("INSERT INTO master_to_slave (id,pasien_id,audio_id,`on`,`light`,`task`) VALUES (:id,:pasien_id,:audio_id,NOW(),'1',:task)",['id'=>$id,'pasien_id'=>$pasien_id,'audio_id'=>$audio_id,'task'=>$task]);

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
			$task = DB::select("SELECT id, audio_id, task FROM master_to_slave WHERE pasien_id = :pasien_id AND `light` = '1' ORDER BY `on` ASC LIMIT 1",['pasien_id'=>$pasien_id]);
			if(count($task)) {
				if($task[0]->task == "play") {
					$data = DB::select("SELECT * FROM ms_audio WHERE id=:id LIMIT 1",['id'=>$task[0]->audio_id]);
					if(count($data)) {
						DB::update("UPDATE master_to_slave SET `off` = NOW(), `light` = '0' WHERE id=:id",['id'=>$task[0]->id]);
						return ['task'=>"play",'data'=>$data[0]];
					} else {
						return [];
					}
				} else {
					DB::update("UPDATE master_to_slave SET `off` = NOW(), `light` = '0' WHERE id=:id",['id'=>$task[0]->id]);
					return ['task'=>$task[0]->task,'data'=>[]];
				}
			} else {
				return [];
			}
		});

		if($data) {
			if($data['task'] == 'play') {
				$data = $data['data'];
				$new_data = [];
				$new_data['src'] 		= env('APP_URL')."/audio/".$data->src;
				$new_data['title'] 		= $data->title;
				$new_data['artist'] 	= $data->artist;
				$new_data['album'] 		= $data->kategori;
				
				$new_data['artwork'] 	= [];
				$new_data['artwork'][0] = [];		
				$new_data['artwork'][0]['src']	= env('APP_URL')."/artwork/".$data->artwork_src;
				$new_data['artwork'][0]['sizes']= $data->artwork_sizes;
				$new_data['artwork'][0]['type'] = $data->artwork_type;
				$response = [
		            'from' => "MasterToSlave@turn_off",
		            'status' => "success",
		            'code' => 200,
		            'desc' => [],
		            'message' => "",
		            'data' => $new_data,
		            'task' => "play"
		        ];
		        return response()->json($this->jsendJson($response),$this->jsendCode($response));
			} else {
				$response = [
		            'from' => "MasterToSlave@turn_off",
		            'status' => "success",
		            'code' => 200,
		            'desc' => [],
		            'message' => "",
		            'data' => $data['data'],
		            'task' => $data['task']
		        ];
		        return response()->json($this->jsendJson($response),$this->jsendCode($response));
			}
		} else {
			$response = [
	            'from' => "MasterToSlave@turn_off",
	            'status' => "error",
	            'code' => 200,
	            'desc' => [],
	            'message' => "Task Not Found",
	            'data' => NULL
	        ];
	        return response()->json($this->jsendJson($response),$this->jsendCode($response));
		}
	}
}