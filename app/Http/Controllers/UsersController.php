<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UsersController extends Controller
{
    public function signup(Request $request)
    {
        $validator = Validator::make($request->all() , [
			'name' => 'required|min:3', 
			'mobile' => 'required|size:13|unique:users', 
			'type' => 'in:buyer,seller'
		], [
			'name.required' => 'Enter your full name', 
			'name.min' => 'Name too short', 
			'mobile.required' => 'Enter mobile number', 
			'mobile.size' => 'Invalid mobile number', 
			'mobile.unique' => 'Mobile number already exists'
		]);

        if ($validator->fails())
        {

            return response()
                ->json(array(
                'status' => 0,
                'message' => 'Something went wrong!',
                'errors' => $validator->errors()
            ) , 200);

        }

        $validated = $validator->valid();

        $validated['otp'] = '112233';
        $validated['password'] = Hash::make(time());

        $user = User::create($validated);

        return response()->json(['status' => 1, 'message' => 'user created successfully!', 'data' => ['user_id' => $user->id]]);
    }

    public function loginRequest(Request $request)
    {
		$validator = Validator::make($request->all() , 
		[
			'mobile' => 'required', 
		], 
		[
			'mobile.required' => 'Enter mobile number',
		]);

        if ($validator->fails())
        {

            return response()
                ->json(array(
                'status' => 0,
                'message' => 'Something went wrong!',
                'errors' => $validator->errors()
            ) , 200);

        }

        $validated = $validator->valid();

		$user = User::where('mobile', '=', $validated['mobile'])->first();

		if(empty($user)){
			return response()
                ->json(array(
                'status' => 0,
                'message' => 'Something went wrong!',
                'errors' => 'User not found!'
            ) , 200);
		}

        $user->update([
			'otp' => rand(1000, 9900)
		]);

		return response()->json(['status' => 1, 'message' => 'OTP sent successfully!', 'data' => ['user_id' => $user->id]]);
    }

    public function signin(Request $request)
    {
        $validator = Validator::make($request->all() , [
			'mobile' => 'required', 
			'otp' => 'required'
		], [ 
			'mobile.required' => 'Enter mobile number', 
			'otp.required' => 'Enter OTP', 
		]);

        if ($validator->fails())
        {

            return response()
                ->json(array(
                'status' => 0,
                'message' => 'Something went wrong!',
                'errors' => $validator->errors()
            ) , 200);

        }

        $validated = $validator->valid();

        $user = User::where('mobile', '=', $validated['mobile'])
        ->where('otp', '=', $validated['otp'])
        ->first();

        if(empty($user)){
			return response()
                ->json(array(
                'status' => 0,
                'message' => 'Something went wrong!',
                'errors' => 'User not found!'
            ) , 200);
		}

        $user->update([
			'mobile_verified_at' => date('Y-m-d H:i:s')
		]);

        return response()->json(['status' => 1, 'message' => 'user logged in', 'data' => ['user_id' => $user->id]]);
    }
}

