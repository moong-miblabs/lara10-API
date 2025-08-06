<?php

namespace App\Http\Controllers;

use App\Models\Users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

use App\Helper\BcryptHelper;
use App\Helper\JsonwebtokenHelper;
use Ramsey\Uuid\Uuid;
use Carbon\Carbon;

class UsersController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function login(Request $request)
    {
        $input = $request->all();
        $rules = [
            'username' => "required",
            'password' => "required"
        ];

        $validator = Validator::make($input,$rules);

        if ($validator->fails()) {
            $response = [
                'from' => "UsersController@login",
                'status' => "fail",
                'code' => 400,
                'desc' => $validator->errors()->toArray(),
                'message' => $this->validationMessage($validator),
                'data' => NULL
            ];
            return response()->json($this->jsendJson($response),$this->jsendCode($response));
        }

        $username = $request->input('username');
        $password = $request->input('password');
        $role_id  = $request->input('role_id');

        $user = DB::select("SELECT id, password, nama FROM users uu WHERE uu.deleted_at IS NULL AND uu.username = :username LIMIT 1",['username'=>$username]);
        $arr_user = json_decode(json_encode($user),true);

        if($arr_user) {
            $the_user = $arr_user[0];
            if(BcryptHelper::compare($password, $the_user['password'])) {
                if($role_id) {
                    $user_role = DB::select("SELECT id, user_id, role_id FROM user_role ur WHERE ur.deleted_at IS NULL AND ur.user_id = :user_id AND ur.role_id = :role_id LIMIT 1",['user_id'=>$the_user['id'],'role_id'=>$role_id]);

                    $arr_user_role = json_decode(json_encode($user_role),true);
                    $the_user_role = $arr_user_role[0];
                    $response = [
                        'from' => "UsersController@login",
                        'status' => "success",
                        'code' => 200,
                        'desc' => [],
                        'message' => "",
                        'data' => [
                            'nama' => $the_user['nama'],
                            'id' => $the_user_role['id'],
                            'user_id' => $the_user_role['user_id'],
                            'role_id' => $the_user_role['role_id']
                        ],
                        'token' => JsonwebtokenHelper::sign(['id'=>$the_user_role['id'],'user_id'=>$the_user_role['user_id'],'role_id'=>$the_user_role['role_id'],'password'=>$the_user['password']])
                    ];
                    return response()->json($this->jsendJson($response),$this->jsendCode($response));
                } else {
                    $user_role = DB::select("SELECT id, user_id, role_id FROM user_role ur WHERE ur.user_id = :user_id",['user_id'=>$the_user['id']]);
                    if(count($user_role) == 1) {
                        $arr_user_role = json_decode(json_encode($user_role),true);
                        $the_user_role = $arr_user_role[0];
                        $response = [
                            'from' => "UsersController@login",
                            'status' => "success",
                            'code' => 200,
                            'desc' => [],
                            'message' => "",
                            'data' => [
                                'nama' => $the_user['nama'],
                                'id' => $the_user_role['id'],
                                'user_id' => $the_user_role['user_id'],
                                'role_id' => $the_user_role['role_id']
                            ],
                            'token' => JsonwebtokenHelper::sign(['id'=>$the_user_role['id'],'user_id'=>$the_user_role['user_id'],'role_id'=>$the_user_role['role_id'],'password'=>$the_user['password']])
                        ];
                        return response()->json($this->jsendJson($response),$this->jsendCode($response));
                    } else {
                        $response = [
                            'from' => "UsersController@login",
                            'status' => "fail",
                            'code' => 204,
                            'desc' => [],
                            'message' => "require role_id",
                            'data' => NULL
                        ];
                        return response()->json($this->jsendJson($response),$this->jsendCode($response));
                    }
                }
            } else {
                $response = [
                    'from' => "UsersController@login",
                    'status' => "error",
                    'code' => 400,
                    'desc' => ['password' => ["not match"]],
                    'message' => "password not match",
                    'data' => NULL
                ];
                return response()->json($this->jsendJson($response),$this->jsendCode($response));
            }
        } else {
            $response = [
                'from' => "UsersController@login",
                'status' => "error",
                'code' => 400,
                'desc' => ['username' => ["not found"]],
                'message' => "username not found",
                'data' => NULL
            ];
            return response()->json($this->jsendJson($response),$this->jsendCode($response));
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $data = DB::table(Users::getTable())->select(Users::getColumns())->whereNull('deleted_at')->orderByRaw(Users::getOrder())->get();
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
            'nama' => "required",
            'username' => "required",
            'password' => "required",
            'role_id' => "required"
        ];

        $validator = Validator::make($input,$rules);

        if ($validator->fails()) {
            $response = [
                'from' => "UsersController@create",
                'status' => "fail",
                'code' => 400,
                'desc' => $validator->errors()->toArray(),
                'message' => $this->validationMessage($validator),
                'data' => NULL
            ];
            return response()->json($this->jsendJson($response),$this->jsendCode($response));
        }

        $nama       = $request->input('nama');
        $username   = $request->input('username');
        $password   = BcryptHelper::hash($request->input('password'));
        $role_id    = $request->input('role_id');

        $data = DB::transaction(function () use ($nama,$username,$password,$role_id) {
            $id = (string) Uuid::uuid4();
            $now = Carbon::now(new \DateTimeZone(env('APP_TIMEZONE','Asia/Jakarta')));

            // harusnya ada check username terlebih dahulu

            DB::insert("INSERT INTO users (id,nama,username,password,created_at,updated_at) VALUES (:id,:nama,:username,:password,:created_at,:updated_at)",['id'=>$id,'nama'=>$nama,'username'=>$username,'password'=>$password,'created_at'=>$now,'updated_at'=>$now]);
            DB::insert("INSERT INTO user_role (id,user_id,role_id,created_at,updated_at) VALUES (:id,:user_id,:role_id,:created_at,:updated_at)",['id'=>$id,'user_id'=>$id,'role_id'=>$role_id,'created_at'=>$now,'updated_at'=>$now]);

            return [
                'nama' => $nama,
                'id' => $id,
                'user_id' => $id,
                'role_id' => $role_id,
                'password' => $password
            ];
        });

        $response = [
            'from' => "UsersController@create",
            'status' => "success",
            'code' => 200,
            'desc' => [],
            'message' => "",
            'data' => [
                'nama' => $data['nama'],
                'id' => $data['id'],
                'user_id' => $data['user_id'],
                'role_id' => $data['role_id']
            ],
            'token' => JsonwebtokenHelper::sign(['id'=>$data['id'],'user_id'=>$data['user_id'],'role_id'=>$data['role_id'],'password'=>$data['password']])
        ];
        return response()->json($this->jsendJson($response),$this->jsendCode($response));
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $data = DB::table(Users::getTable())->where('id',$id)->whereNull('deleted_at')->get();
        $the_data = (array) $data[0];

        $user_roles = DB::select("SELECT * FROM user_role ur WHERE ur.deleted_at IS NULL AND ur.user_id = :user_id",['user_id'=>$the_data['id']]);
        $mapped = array_map(function ($row) {
            $the_row = (array) $row;
            $the_row['role'] = DB::select("SELECT * FROM ms_role mr WHERE mr.deleted_at IS NULL AND mr.id = :mr_id",['mr_id'=>$the_row['role_id']]);
            return $the_row;
        }, $user_roles);
        $the_data['user_role'] = $mapped;

        $response = [
            'from' => "PasienController@show",
            'status' => "success",
            'code' => 200,
            'desc' => [],
            'message' => "",
            'data' => $the_data
        ];
        return response()->json($this->jsendJson($response),$this->jsendCode($response));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, $id)
    {
        $input = $request->all();
        if($request->input('password')) {
            $input['password'] = BcryptHelper::hash($request->input('password'));
        }
        $filtered = array_filter($input, fn($value) => $value !== NULL && $value !== "");
        $data = Users::update($id,$filtered);

        $response = [
            'from' => "UsersController@edit",
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
        $data = Users::destroy($id);
        $response = [
            'from' => "UsersController@destroy",
            'status' => "success",
            'code' => 200,
            'desc' => [],
            'message' => "",
            'data' => $data
        ];
        return response()->json($this->jsendJson($response),$this->jsendCode($response));
    }
}
