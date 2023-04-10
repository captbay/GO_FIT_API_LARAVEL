<?php

namespace App\Http\Controllers;

use App\Models\member;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class memberController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $member = member::latest()->get();

        return response()->json([
            'success' => true,
            'message' => 'List Data member',
            'data'    => $member
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //Validasi Formulir
        $validator = Validator::make($request->all(), [
            // 'id_users' => 'required',
            // 'no_member' => 'required',
            'name' => 'required',
            'address' => 'required',
            'number_phone' => 'required',
            'born_date' => 'required',
            'gender' => 'required',
            // 'jumlah_deposit_reguler' => 'required',
            // 'expired_date_membership' => 'required',
            // 'status_membership' => 'required',
        ]);

        //response error validation
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $member = member::create([
            // 'id_users' => $request->id_users,
            // 'no_member' => $request->no_member,
            'name' => $request->name,
            'address' => $request->address,
            'number_phone' => $request->number_phone,
            'born_date' => $request->born_date,
            'gender' => $request->gender,
            // 'jumlah_deposit_reguler' => $request->jumlah_deposit_reguler,
            // 'expired_date_membership' => $request->expired_date_membership,
            // 'status_membership' => $request->status_membership,
        ]);

        if ($member) {

            return response()->json([
                'success' => true,
                'message' => 'member Created',
                'data'    => $member
            ], 201);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'member Failed to Save',
                'data'    => $member
            ], 409);
        }
    }

    public function aktivasi($id)
    {

        //find member by ID
        $member = member::find($id);

        $user = User::create([
            'username' => '1234',
            'password' => '123',
            'role' => 'member'
        ]);

        $member->users()->associate($user);
        $member->save();

        //make response JSON
        return response()->json([
            'success' => true,
            'message' => 'Detail Data member',
            'data'    => $member
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
        //find member by ID
        $member = member::find($id);

        //make response JSON
        return response()->json([
            'success' => true,
            'message' => 'Detail Data member',
            'data'    => $member
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
        $member = member::find($id);
        if (!$member) {
            //data member not found
            return response()->json([
                'success' => false,
                'message' => 'member Not Found',
            ], 404);
        }
        //validate form
        $validator = Validator::make($request->all(), [
            'id_users' => 'required',
            'no_member' => 'required',
            'name' => 'required',
            'address' => 'required',
            'number_phone' => 'required',
            'born_date' => 'required',
            'gender' => 'required',
            'jumlah_deposit_reguler' => 'required',
            'expired_date_membership' => 'required',
            'status_membership' => 'required',
        ]);

        //response error validation
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        //update member with new image
        $member->update([
            'id_users' => $request->id_users,
            'no_member' => $request->no_member,
            'name' => $request->name,
            'address' => $request->address,
            'number_phone' => $request->number_phone,
            'born_date' => $request->born_date,
            'gender' => $request->gender,
            'jumlah_deposit_reguler' => $request->jumlah_deposit_reguler,
            'expired_date_membership' => $request->expired_date_membership,
            'status_membership' => $request->status_membership,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'member Updated',
            'data'    => $member
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $member = member::find($id);

        if ($member) {
            //delete member
            $member->delete();

            return response()->json([
                'success' => true,
                'message' => 'member Deleted',
            ], 200);
        }


        //data member not found
        return response()->json([
            'success' => false,
            'message' => 'member Not Found',
        ], 404);
    }
}
