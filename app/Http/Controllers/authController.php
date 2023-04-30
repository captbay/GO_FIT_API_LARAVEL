<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon;
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
            return response()->json([$validator->errors(), 'message' => 'Login Failed',], 422);
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
                $expiredDate = Carbon::parse($member->expired_date_membership)->format('Y-m-d');
                $today = Carbon::now()->format('Y-m-d');

                $compareDateExprd = $expiredDate < $today;

                if ($compareDateExprd || $member->expired_date_membership == NULL) {
                    $member->update([
                        'status_membership' => 0,
                    ]);

                    return response()->json([
                        'success' => false,
                        'message' => 'Login Failed Because Membership is expired or not active, Please Contact Cashier',
                    ], 409);
                } else {
                    return response()->json([
                        'message' => 'Authenticated as a member active',
                        'user' => $user,
                        'member' => $member,
                        'role' => 'member',
                        'token_type' => 'Bearer',
                        'access_token' => $token
                    ], 200);
                }
            } else if ($user->role == 'instruktur') {
                $instruktur = $user->instruktur;
                return response()->json([
                    'message' => 'Authenticated as a instruktur',
                    'user' => $user,
                    'role' => 'instruktur',
                    'instruktur' => $instruktur,
                    'token_type' => 'Bearer',
                    'access_token' => $token
                ], 200);
            } else if ($user->role == 'pegawai') {
                $pegawai = $user->pegawai;
                if ($pegawai->role == 'kasir') {
                    return response()->json([
                        'message' => 'Authenticated as a kasir',
                        'user' => $user,
                        'role' => 'kasir',
                        'pegawai' => $pegawai,
                        'token_type' => 'Bearer',
                        'access_token' => $token
                    ], 200);
                } else if ($pegawai->role == 'mo') {
                    return response()->json([
                        'message' => 'Authenticated as a mo',
                        'user' => $user,
                        'role' => 'mo',
                        'pegawai' => $pegawai,
                        'token_type' => 'Bearer',
                        'access_token' => $token
                    ], 200);
                } else if ($pegawai->role == 'admin') {
                    return response()->json([
                        'message' => 'Authenticated as a admin',
                        'user' => $user,
                        'role' => 'admin',
                        'pegawai' => $pegawai,
                        'token_type' => 'Bearer',
                        'access_token' => $token
                    ], 200);
                }
            } else {
                return response()->json([
                    'message' => 'Authenticated as a super admin',
                    'user' => $user,
                    'role' => 'admin',
                    'token_type' => 'Bearer',
                    'access_token' => $token
                ], 200);
            }
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

    public function show(Request $request, $id)
    {
        $uid = $request->user()->id;
        $user = User::find($id);
        if ($user != null && $uid == $id) {
            if ($user->role == 'member') {
                //make response JSON
                return response()->json([
                    'success' => true,
                    'message' => 'Detail Data member',
                    'dataUser'    => $user,
                    'dataDiri'    => $user->member
                ], 200);
            } else if ($user->role == 'instruktur') {
                //make response JSON
                return response()->json([
                    'success' => true,
                    'message' => 'Detail Data instruktur',
                    'dataUser'    => $user,
                    'dataDiri'    => $user->instruktur
                ], 200);
            } else if ($user->role == 'pegawai') {
                //make response JSON
                return response()->json([
                    'success' => true,
                    'message' => 'Detail Data pegawai',
                    'dataUser'    => $user,
                    'dataDiri'    => $user->pegawai
                ], 200);
            } else {
                //make response JSON
                return response()->json([
                    'success' => true,
                    'message' => 'Detail Data Super Admin',
                    'superrole'    => 'superadmin',
                    'dataDiri'    => $user,
                ], 200);
            }
        } else {
            return response()->json([
                'success' => false,
                'message' => 'member Not Found',
            ], 404);
        }
    }


    public function getCurrentLoggedInUser(Request $request)
    {
        return $this->show($request, $request->user()->id);
    }

    /**
     * store
     *
     * @param Request $request
     * @return void
     */
    public function registerAdmin(Request $request)
    {
        //Validasi Formulir
        $validator = Validator::make($request->all(), [
            'username' => 'required|unique:users',
            'password' => 'required',
            'role' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $password = bcrypt($request->password);

        $user = User::create([
            'username' => $request->username,
            'password' => $password,
            'role' => $request->role,
        ]);
        // event(new Registered($user));
        //Redirect jika berhasil mengirim email
        if ($user) {
            return response()->json([
                'success' => true,
                'message' => 'Registration admin successful',
                'data'    => $user
            ], 201);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'admin Failed to Save',
                'data'    => $user
            ], 409);
        }
    }
}