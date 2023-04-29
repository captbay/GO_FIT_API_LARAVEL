<?php

namespace App\Http\Controllers;

use App\Models\member;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use FontLib\Table\Type\name;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
            'name' => 'required|unique:member',
            'address' => 'required',
            'number_phone' => 'required',
            'born_date' => 'required',
            'gender' => 'required',
        ]);

        //response error validation
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        //membuatIddengan format(Xy) X= huruf dan Y = angka
        $count = DB::table('member')->count() + 1;
        $id_generate = sprintf("%03d", $count);

        //membuat angka dengan format y
        $digitYear = Carbon::parse(now())->format('y');

        //membuat angka dengan format m
        $digitMonth = Carbon::parse(now())->format('m');


        //membuat password dengan format dmy
        $datePass = Carbon::parse($request->born_date)->format('dmY');
        $password = bcrypt($datePass);


        //no member
        $no_member = $digitYear . '.' . $digitMonth . '.' . $id_generate;


        $user = User::create([
            'username' => $no_member,
            'password' => $password,
            'role' => 'member'
        ]);

        $member = $user->member()->create([
            'no_member' => $no_member,
            'name' => $request->name,
            'address' => $request->address,
            'number_phone' => $request->number_phone,
            'born_date' => $request->born_date,
            'gender' => $request->gender,
            'jumlah_deposit_reguler' => 0,
            'status_membership' => 0,
        ]);

        if ($member) {

            return response()->json([
                'success' => true,
                'message' => 'member Created',
                'data diri member'    => $member,
                'data user member'    => $user
            ], 201);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'member Failed to Save',
                'data diri member'    => $member,
                'data user member'    => $user
            ], 409);
        }
    }

    // public function aktivasi($id)
    // {

    //     //find member by ID
    //     $member = member::find($id);

    //     $user = User::create([
    //         'username' => '1234',
    //         'password' => '123',
    //         'role' => 'member'
    //     ]);

    //     $member->users()->associate($user);
    //     $member->save();

    //     //make response JSON
    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Detail Data member',
    //         'data'    => $member
    //     ], 200);
    // }


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
            'name' => 'required',
            'address' => 'required',
            'number_phone' => 'required',
            // 'born_date' => 'required',
            // 'gender' => 'required',
        ]);

        //response error validation
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        //update member with new image
        $member->update([
            'name' => $request->name,
            'address' => $request->address,
            'number_phone' => $request->number_phone,
            // 'born_date' => $request->born_date,
            // 'gender' => $request->gender,
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

    /**
     * Display a listing of the resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function generateMemberCard($id)
    {
        $member = member::find($id);
        if (!$member) {
            //data member not found
            return response()->json([
                'success' => false,
                'message' => 'member Not Found',
            ], 404);
        }

        $data = [
            'title' => 'GoFit',
            'title2' => 'Member Card',
            'address' => 'Jl. Centralpark No. 10, Yogyakarta',
            'member' => $member
        ];

        $pdf = Pdf::loadview('memberCard', $data);

        return $pdf->download('Member_Card_' . $member->name . '.pdf');
    }
}
