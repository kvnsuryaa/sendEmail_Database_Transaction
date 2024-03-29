<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class UserController extends Controller
{
    
	public function login(Request $req)
	{
		$only = $req->only('email', 'password');
		try {
			if (!$token = JWTAuth::attempt($only)) {
				return response()->json(['error' => 'Invalid Login'], 400);
			}
		} catch (Exception $e) {
			return response()->json(['error' => "can't create a token"]);
		}

		return response()->json(compact('token'));
	}

	public function register(Request $req)
	{
		$validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }
        
		$new = User::create([
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'password' => Hash::make($request->get('password')),
        ]);

		$token = JWTAuth::fromUser($new);

		return response()->json(compact('user', 'token'), 201);


	}

	public function getAuthenticatedUser()
	{
		
		try {
			
		} catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

            return response()->json(['token_expired'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

            return response()->json(['token_invalid'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {

            return response()->json(['token_absent'], $e->getStatusCode());

        }

		return response()->json(compact('user'));

	}


}
