<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class UserController extends Controller
{
    
    public function login(Request $request)
    {
        try{
            $existingUser = User::where('email', $request->input('email'))->first();
    
            if (!$existingUser) {
                return response()->json(["error" => "User doesn't exists"], 404);
            }

            $passwordCorrect = Hash::check($request->password, $existingUser->password);
            if (!$passwordCorrect) {
                return response()->json(["error" => "passowrd incorrect"], 400);
            }

            $user = $existingUser;
            return response()->json(['user' => $user, 'message' => 'Logged in successfully'], 200);

        } catch (\Exception $e) {
            Log::error("Failed to login user: " . $e);
            return response()->json(['error' => $e], 401);
        }

    }

    public function register(Request $request)
    {
        try{
            $existingUser = User::where('email', $request->input('email'))->first();
    
            if ($existingUser) {
                return response()->json(["error" => "User with this email already exists"], 409); // 409 Conflict
            }

            $validator = Validator::make($request->all(), [
                'user_name' => 'required',
                'email' => 'required|email|unique:users',
                'password' => 'required|min:8',
            ]);
        
            if ($validator->fails()) {
                return response()->json(["error" => $validator->errors()], 400);
            }

            $user = new User;

            $user->user_name        = $request->input('user_name');
            $user->email            = $request->input('email');
            $user->password         = bcrypt($request->input('password'));
            $user->image_url        = $request->input('image_url', null);

            $user->save();
            
            return response()->json(["result" => "user registered Successfully"], 200);

        } catch (\Exception $e) {
            Log::error("Failed to login user: " . $e);
            return response()->json(["error" => "Failed to register user"], 403);
        }
    }
    
    public function update(Request $request, $userId)
    {
        try{
            $user = User::findOrFail($userId);

            $user->user_name        = $request->input('user_name');
            $user->email            = $request->input('email');
            $user->password         = bcrypt($request->input('password'));
            $user->image_url        = $request->input('image_url', null);

            $user->save();
            
            return response()->json(["result" => "user updated Successfully"], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json(["error" => "User not found"], 404);    
        } catch (\Exception $e) {
            return response()->json(["error" => "Failed to store user"], 403);
        }
    }

    public function destroy($userId)
    {
        try{
            $user = User::findOrFail($userId);
            $user->delete();
            
            return response()->json(["result" => "user was deleted successfully"], 200); 
            
        } catch (ModelNotFoundException $e) {
            return response()->json(["error" => "user not found"], 404);    
        } catch (\Exception $e) {
            return response()->json(["error" => "Failed to delete user"], 403);
        }      
    }
}
