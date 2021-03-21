<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Shop;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

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

        $validated['otp'] = rand(1000, 9900);
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

    public function uploadProfilePicture(Request $request){
        $validator = Validator::make($request->all() , [
			'profile_picture' => 'bail|required|image',
            'user_id' => 'bail|required' 
		]);

        if ($validator->fails())
        {

            return array(
                'status' => 0,
                'message' => 'Something went wrong!',
                'errors' => $validator->errors()
            ); 

        }

        $user = User::find($request->user_id);

        if(empty($user)){
			return response()
                ->json(array(
                'status' => 0,
                'message' => 'Something went wrong!',
                'errors' => 'User not found!'
            ) , 200);
		}

        $user->update([
			'profile_picture' => $request->profile_picture->store('public')
		]);

        return response()->json(['status' => 1, 'message' => 'Profile picture updated', 'data' => ['user_id' => $user->id]]);

    }

    public function getProfilePicture(Request $request){
        $validator = Validator::make($request->all() , [
            'user_id' => 'required' 
		]);

        if ($validator->fails())
        {

            return array(
                'status' => 0,
                'message' => 'Something went wrong!',
                'errors' => $validator->errors()
            );

        }

        $user = User::find($request->user_id);

        if(empty($user)){
			return response()
                ->json(array(
                'status' => 0,
                'message' => 'Something went wrong!',
                'errors' => 'User not found!'
            ) , 200);
		}

        return response()->json([
            'status' => 1, 
            'message' => 'Profile picture', 
            'data' => [
                'user_id' => $user->id,
                'profile_picture' => Storage::url($user->profile_picture)
            ]
        ]);

    }

    public function addShop(Request $request){
        $validator = Validator::make($request->all() , [
            'user_id' => 'required',
            'name' => 'required',
            'number' => 'required',
            'block' => 'required',
            'mandi_name' => 'required',
            'state' => 'required',
            'pincode' => 'required',
            'is_active' => 'required' 
		], [], [
            'name' => 'Shop Name',
            'number' => 'Shop Number',
            'block' => 'Shop Block',
            'is_active' => 'Shop Active' 
        ]);

        if ($validator->fails())
        {

            return array(
                'status' => 0,
                'message' => 'Something went wrong!',
                'errors' => $validator->errors()
            );

        }

        $validated = $validator->valid();

        $shop = Shop::create($validated);

        return response()->json([
            'status' => 1, 
            'message' => 'Your Shop ' . $validated['name'] . ' Added!', 
            'data' => [
                'shop_id' => $shop->id
            ]
        ]);

    }

    public function addProduct(Request $request){
        $validator = Validator::make($request->all() , [
            'category' => 'required|in:fruit,vegitable',
            'user_id' => 'required',
            'name' => 'required|min:3',
            'price_per_kg' => 'required',
            'is_out_of_stock' => 'in:0,1',
            'is_dynamic_price_enabled' => 'in:0,1'
		], [], [
            'category' => 'Product category',
            'name' => 'Product Name',
        ]);

        if ($validator->fails())
        {

            return array(
                'status' => 0,
                'message' => 'Something went wrong!',
                'errors' => $validator->errors()
            );

        }

        $validated = $validator->valid();

        $product = Product::create($validated);

        

        foreach($request->images as $image){
            $img = $image->store('public');
            $productImage = new ProductImage();
            $productImage->product_id = $product->id;
            $productImage->image = $img;
            $productImage->save();
        }
        

        return response()->json([
            'status' => 1, 
            'message' => 'Your Product ' . $validated['name'] . ' Added!', 
            'data' => [
                'product_id' => $product->id
            ]
        ]);


    }
}
