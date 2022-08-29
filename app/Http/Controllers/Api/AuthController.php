<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use \Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;
class AuthController extends Controller
{


    public function __construct() {
        $this->middleware('auth:api', ['except' => ['login']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::get();
        return response()->json([
           'success'=>true,
           'message'=>'Display a listing of the resource',
           'data'=>$users,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'name'=>'required',
            'email'=>'required',
            'password'=>'required|min:6',
        ]);
        if($validator->fails()){
            return response()->json([
                'success'=>false,
                'errors' => $validator->errors()
            ],401);
        }else{
            try {
                $users = new User();
                $users->name=$request->name;
                $users->email=$request->email;
                $users->password=Hash::make($request->password);
                $users->save();
                return response()->json([
                    'success'=>true,
                    'data'=>$users,
                    'message' =>'User Create Success'
                ],200);
            } catch (\Throwable $th) {
                return response()->json([
                    'success'=>false,
                    'message' => $th->getMessage()
                ],400);
            }
        }


    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $user = User::findOrFail($id);
            return response()->json([
                'success'=>true,
                'message'=>'Display the specified resource',
                'data'=>$user
             ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success'=>false,
                'message' => $th->getMessage()
            ],400);
        }
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(),[
            'name'=>'required',
            'email'=>'required',
            'password'=>'required|min:6',
        ]);
        if($validator->fails()){
            return response()->json([
                'success'=>false,
                'errors' => $validator->errors()
            ],401);
        }else{
            try {
                $users = User::FindOrFail($id);
                $users->name=$request->name;
                $users->email=$request->email;
                $users->password=Hash::make($request->password);
                $users->save();
                return response()->json([
                    'success'=>true,
                    'data'=>$users,
                    'message' =>'User data update success'
                ],200);
            } catch (\Throwable $th) {
                return response()->json([
                    'success'=>false,
                    'message' => $th->getMessage()
                ],400);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        try {
            $user = User::findOrFail($id)->delete();
            return response()->json([
                'success'=>true,
                'message'=>'Remove the specified resource from storage',
             ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success'=>false,
                'message' => $th->getMessage()
            ],400);
        }

    }


    public function login(Request $request){

        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $credentials = $request->only('email', 'password');

        if ($token = auth()->attempt($credentials)) {
            return $this->respondWithToken($token);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }


    public function logout()
    {
        auth()->logout();
        return response()->json(['message' => 'Successfully logged out']);
    }


    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $this->guard()->factory()->getTTL() * 60
        ]);
    }

      /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\Guard
     */
    public function guard()
    {
        return Auth::guard();
    }
}
