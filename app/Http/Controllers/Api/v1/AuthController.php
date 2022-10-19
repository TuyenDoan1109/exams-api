<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use App\Models\User;
use App\Models\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Auth;
use Mail;
use Illuminate\Support\Str;
use App\Http\Resources\User as UserResource;

class AuthController extends Controller
{
    //Register
    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|confirmed|min:8|max:255'
            // 'password_confirm' => 'required|same:password'
        ],
        [
            'name.required'=>'Tên người không được bỏ trống',
            'name.string'=>'Tên người dùng là một chuỗi ký tự',
            'name.max'=>'Tên không lớn hơn 255 ký tự',
            'email.required'=>'Email không được bỏ trống',
            'email.email'=>'Sai định dạng email, vd: example@gmail.com',
            'email.unique'=>'Email đã tồn tại, vui lòng cung cấp email khác',
            'email.string'=>'Email là một chuỗi ký tự',
            'email.max'=>'Email không lớn hơn 255 ký tự',
            'password.required'=>'Mật khẩu không được bỏ trống',
            'password.confirmed'=>'Mật khẩu confirm không chính xác',
            'password.min'=>'Mật khẩu tối thiểu 8 ký tự',
            'password.max' => 'Mật khẩu không lớn hơn 255 ký tự'
        ]
        );

        if($validator->fails()) {
            return response()->json($validator->errors());
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);
        $token = $user->createToken('access_token')->accessToken;
        return response()->json([
            'user' => new UserResource($user),
            'access_token' => $token, 
            'token_type' => 'Bearer'
        ], 201);
    }

    //Login
    public function login(Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
            'password' => 'required|min:8|max:255'
        ],
        [
            'email.required'=>'Email không được bỏ trống',
            'email.email'=>'Sai định dạng email, vd: example@gmail.com',
            'email.string'=>'Email là một chuỗi ký tự',
            'email.max'=>'Email không lớn hơn 255 ký tự',
            'password.required'=>'Mật khẩu không được bỏ trống',
            'password.min'=>'Mật khẩu tối thiểu 8 ký tự',
            'password.max' => 'Mật khẩu không lớn hơn 255 ký tự'
        ]
        );

        if($validator->fails()) {
            return response()->json($validator->errors());
        }
        if(!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Mật khẩu hoặc tài khoản không chính xác, vui lòng kiểm tra lại!'
            ], 401);
        }
        // if (!Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
        //     return response()->json([
        //         'message' => 'Mật khẩu hoặc tài khoản không chính xác, vui lòng kiểm tra lại!'
        //     ], 401);
        // }
        $user = User::where('email', $request->email)->firstOrFail();
        $token = $user->createToken('access_token')->accessToken;
        return response()->json([
            'message' => 'Chào ' . $user->name,
            'user' => new UserResource($user),
            'access_token' => $token, 
            'token_type' => 'Bearer'
        ]);
    }

    //Logout
    public function logout(Request $request) {
        $user_name = $request->user()->name;
        auth()->user()->tokens()->delete();
        // Auth::user()->tokens()->delete();
        return response()->json([
            'message' => 'Đăng xuất thành công. Bye ' . $user_name . '!'
        ], 200);
    }

    //Show Infor User
    public function getUser(Request $request)
    {
        $user = $request->user();
        return response()->json([
            'user' => new UserResource($user),
        ], 200);
    }

    //Change Password
    public function changePassword(Request $request) {
        $validator = Validator::make($request->all(),
            [
                'old_password'=>'required|min:8|max:255',
                'new_password'=>'required|min:8|confirmed|max:255'
            ],
            [
                'old_password.required'=>'Mật khẩu hiện tại không được để trống',
                'old_password.min'=>'Mật khẩu hiện tại không được nhỏ hơn 8 ký tự',
                'old_password.max'=>'Mật khẩu hiện tại không được lớn hơn 255 ký tự',
                'new_password.required'=>'Mật khẩu mới không được để trống',
                'new_password.min'=>'Mật khẩu mới không được nhỏ hơn 8 ký tự',
                'new_password.max'=>'Mật khẩu mới không được lớn hơn 255 ký tự',
                'new_password.confirmed'=>'Mật khẩu confirm không chính xác',
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()
            ], 422); 
        }

        if(Hash::check($request->old_password, $request->user()->password)) {
            // Old password correct
            if($request->old_password == $request->new_password) {
                return response()->json([
                    'message'=>'Mật khẩu mới giống với mật khẩu hiện tại, xin vui lòng nhập mật khẩu mới khác!'
                ], 422);
            } else {
                $request->user()->password = bcrypt($request->new_password);
                $request->user()->save();
                return response()->json([
                    'message' => 'Mật khẩu đã được thay đổi thành công'
                ], 200);
            }
        } else {
            // Old password wrong
            return response()->json([
                'message'=>'Mật khẩu hiện tại không đúng, vui lòng kiểm tra lại!'
            ], 404);
        }
    }

    public function forgotPassword(Request $request) {
        $validator = Validator::make($request->all(),
            [
                'email' => 'required|email'
            ],
            [
                'email.required' => 'Email không được để trống',
                'email.email' => 'Sai định dạng email, vd:example@gmail.com',
            ]
        );

        if($validator->fails()) {
            return response()->json([ 
                'messages'=>$validator->errors()
            ], 422);
        }
        $email = $request->input('email');
        $user = User::where('email', $email)->first();
        if($user) {
            // $name = 'Đoàn Khắc Tuyến';
            $token = Str::random(10);
            PasswordReset::create([
                'email' => $email,
                'token' => $token
            ]);
            Mail::send('emails.forgotPassword', compact('token'), function($email) use($user){
                $email->to($user->email);
                $email->subject('Reset your password exam-suntech.tech');
            });
            return response()->json([
                'message' => 'Check your email!'
            ], 200);
        } else {
            return response()->json([
                'message' => 'Địa chỉ mail không tồn tại!'
            ], 404);
        }
    }

    public function resetPassword(Request $request, $token) {
        $validator = Validator::make($request->all(), [
            'password' => 'required|min:8|max:255'
        ],
        [
            'password.required'=>'Mật khẩu không được bỏ trống',
            'password.min'=>'Mật khẩu tối thiểu 8 ký tự',
            'password.max' => 'Mật khẩu không lớn hơn 255 ký tự'
        ]
        );

        if($validator->fails()) {
            return response()->json([ 
                'messages'=>$validator->errors()
            ], 422);
        }

        if(!$passwordReset = PasswordReset::where('token', $token)->first()) {
            return response()->json([
                'message' => 'Invalid token!'
            ], 400);
        }

        $user = User::where('email', $passwordReset->email)->first();

        $user->password = Hash::make($request->password);
        if($user->save()) {
            PasswordReset::where('email', $user->email)->delete();
            return response()->json([
                'user' => new UserResource($user),
                'message' => 'Changed password successfully!'
            ], 200);
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::all();
        return response()->json([
            'users' => $users
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::find($id);
        return response()->json([
            'user' => $user
        ], 200);
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::find($id);
        $user->delete();
        return response()->json([
            'message' => 'Deleted user successfully'
        ], 200);
    }

}
