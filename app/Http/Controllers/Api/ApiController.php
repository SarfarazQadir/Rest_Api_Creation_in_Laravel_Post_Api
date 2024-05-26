<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\MOdels\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class ApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
   
    public function index($flag)
    {
        $query = User::select('email','name');
        if($flag == 1){
            // show only active users
            $query->where('status',1);
        }else{
            return response->json([
                'message' => 'Invalid Paramete, It should be 1 or 0',
                'status' => 0
            ],400);
        }

        $users = $query->get();
        if(count($users) > 0){
            $response = [
                'message' => count($users)  . 'Users Found',
                'status' => 'success',
                'data' => $users,
            ];
        }else{
            $response = [
                'message' => count($users) . 'Users Not Found',
                'status' => 0,
            ];
        }
        return response()->json($response, 200);
        // return response()->json($users);
        // p($users);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'name' => ['required'],
            'email' => ['required','email','unique:users,email'],
            'password' => ['min:8','confirmed'],
            'confirm_password' => ['required']  
        ]);
        if($validator->fails()){
            return response()->json($validator->messages(), 400);
        }else{
            $data = [
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password)
            ];
            
            DB::beginTransaction();
            try{
               $user = User::create($data);
                DB::commit();
            }
            catch(\Exception $e){
                DB::rollBack();
                p($e->getMessage());
                $user = null;
            }
            if($user != null){
                return response()->json(['message' => 'User created successfully'], 200);
            }else{
                return response()->json(['message' => 'intenal server error'], 500);
            }
        }
      // p($request->all());
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, $id)
    {
        $user = User::find($id);
        if(is_null($user)){
            return response()->json(['message' => 'User not found', 'status' => 0], 404);
        }else{
            if($user->password == $request['old_password']){
                if($request['new_password'] == $request['confirm_password']){
                    DB::beginTransaction();
                    try{
                        $user->password = Hash::make($request['new_password']);
                        $user->save();
                        DB::commit();
                        }
                        catch(\Exception $err){
                            $user = null;
                            DB::rollBack();
                        } 
                        if(is_null($user)){
                            return response()->json(['message' => 'Internal server error', 'status' => 0, 'error_message' => $err->getmessage()], 500);
                        }else{
                            return response()->json(['message' => 'Password updated successfully', 'status' => 1], 200);
                        }
                }else{
                    return response()->json(['message' => 'New password and Confirm Password Does not match', 'status' => 0], 400);   
                }
            }else{
                return response()->json(['message' => 'Old password is not correct', 'status' => 0], 400);
            }
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $user = User::find($id);
        if(is_null($user)){
            return response()->json(['message' => 'User not found', 'status' => 0], 404);
        }
        else{
            DB::beginTransaction();
            try{
                $user->name = $request['name'];
                $user->email = $request['email'];
                $user->conatct = $request['conatct'];
                $user->pincode = $request['pincode'];
                $user->address = $request['address'];
                $user->save();
                DB::commit();
            }
            catch(\Exception $err){
                DB::rollBack();
                $user = null;
            }
            if(is_null($user)){
                return response()->json(['message' => 'Internal server error', 'status' => 0, 'error_message' => $err->getmessage()], 500);
            }else{
                return response()->json(['message' => 'User updated successfully', 'status' => 1], 200);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::find($id);
        if(is_null($user)){
            $response = [
                'message' => 'User isnot  exist ',
                'status' => 0
            ]; 
            $respocode = 404;
            }else{
               DB::beginTransaction();
               try{
                    $user->delete();
                    DB::commit();
                    $response = [
                    'message' => 'User Delete Successful!',
                    'status' => 1
                    ];
                    $respocode = 200;
                }
                catch(\Exception $e){
                    DB::rollBack();
                    $response = [
                        'message' => 'Internal Server Error',
                        'status' => 0
                        ];
                        $respocode = 500;
                }
            
            }
            return response()->json($response, $respocode);
    }
}