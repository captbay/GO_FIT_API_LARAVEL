<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;

class authController extends Controller
{

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'password' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = User::where('username', $request->username)->first();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'username user Not Found',
            ], 404);
        }

        $token = $user->createToken('Authentication Token')->accessToken;

        if (Hash::check($request->password, $user->password)) {
            if ($user->role == 'member') {
                $member = $user->member;
                return response()->json([
                    'message' => 'Authenticated as a member',
                    'user' => $user,
                    'member' => $member,
                    'token_type' => 'Bearer',
                    'access_token' => $token
                ], 200);
            } else if ($user->role == 'instruktur') {
                $instruktur = $user->instruktur;
                return response()->json([
                    'message' => 'Authenticated as a instruktur',
                    'user' => $user,
                    'instruktur' => $instruktur,
                    'token_type' => 'Bearer',
                    'access_token' => $token
                ], 200);
            } else if ($user->role == 'pegawai') {
                $pegawai = $user->pegawai;
                return response()->json([
                    'message' => 'Authenticated as a pegawai',
                    'user' => $user,
                    'pegawai' => $pegawai,
                    'token_type' => 'Bearer',
                    'access_token' => $token
                ], 200);
            }

            // return response()->json([
            //     'message' => 'Authenticated',
            //     'user' => $user,
            //     'token_type' => 'Bearer',
            //     'access_token' => $token
            // ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Login Failed',
            ], 409);
        }
    }

    public function updatePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'passwordOld' => 'required',
            'passwordNew' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = User::where('username', $request->username)->first();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User Not Found',
            ], 404);
        }

        if (Hash::check($request->passwordOld, $user->password)) {

            $passwordNew = bcrypt($request->passwordNew);
            $user->update([
                'password' => $passwordNew
            ]);


            return response()->json([
                'success' => true,
                'message' => 'Password changed successfully',
                'user' => $user,
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Password failed to change',
            ], 409);
        }
    }

    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'passwordNew' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = User::where('username', $request->username)->first();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User Not Found',
            ], 404);
        }

        $passwordNew = bcrypt($request->passwordNew);
        $user->update([
            'password' => $passwordNew
        ]);

        if ($user) {
            return response()->json([
                'success' => true,
                'message' => 'Password changed successfully',
                'user' => $user,
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Password failed to change',
            ], 409);
        }
    }

    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->json([
            'success' => true,
            'message' => 'Success Logout',
        ], 200);
    }



    // /**
    //  * store
    //  *
    //  * @param Request $request
    //  * @return void
    //  */
    // public function register(Request $request)
    // {
    //     //Validasi Formulir
    //     $validator = Validator::make($request->all(), [
    //         'username' => 'required|unique:users',
    //         'password' => 'optional',
    //         'role' => 'required',
    //         'name' => 'required',
    //         'address' => 'required',
    //         'number_phone' => 'required',
    //         'born_date' => 'required',
    //         'gender' => 'required',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json($validator->errors(), 422);
    //     }

    //     if ($request->role == 'member') {
    //         $user = User::create([
    //             'username' => $request->username,
    //             'password' => $request->password,
    //             'role' => $request->role,
    //         ]);

    //         $member = $user->member()->create([
    //             'name' => $request->name,
    //             'address' => $request->address,
    //             'number_phone' => $request->number_phone,
    //             'born_date' => $request->born_date,
    //             'gender' => $request->gender,
    //         ]);

    //         if ($member) {
    //             return response()->json([
    //                 'success' => true,
    //                 'message' => 'Registration successful',
    //                 'data'    => $user
    //             ], 201);
    //         }
    //     }

    //     // $password = bcrypt($request->password);

    //     // $user = User::create([
    //     //     'username' => $request->username,
    //     //     'password' => $password,
    //     //     'role' => $request->role,
    //     // ]);

    //     $user->sendApiEmailVerificationNotification();
    //     // event(new Registered($user));
    //     //Redirect jika berhasil mengirim email
    //     if ($user) {
    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Registration successful',
    //             'data'    => $user
    //         ], 201);
    //     } else {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'User Failed to Save',
    //             'data'    => $user
    //         ], 409);
    //     }
    // }
}
