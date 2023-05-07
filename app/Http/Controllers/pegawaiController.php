<?php

namespace App\Http\Controllers;

use App\Models\pegawai;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class pegawaiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $pegawai = pegawai::with(['users'])->get();

        return response()->json([
            'success' => true,
            'message' => 'List Data pegawai',
            'data'    => $pegawai
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
            'name' => 'required|unique:pegawai',
            'address' => 'required',
            'number_phone' => 'required|regex:/^(0)8[1-9][0-9]{6,9}$/',
            'born_date' => 'required|date|before:today',
            'gender' => 'required',
        ], [
            'number_phone.regex' => 'The number phone format is invalid (please use NUMBER and start with 08)',
        ]);

        //response error validation
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //membuatIddengan format(Xy) X= huruf dan Y = angka
        // $count = DB::table('pegawai')->count() + 1;
        if (DB::table('pegawai')->count() == 0) {
            $id_terakhir = 0;
        } else {
            $id_terakhir = pegawai::latest('id')->first()->id;
        }
        $count = $id_terakhir + 1;
        $id_generate = sprintf("%02d", $count);

        //membuat password dengan format dmy
        $datePass = Carbon::parse($request->born_date)->format('dmY');
        $password = bcrypt($datePass);

        //no pegawai
        $no_pegawai = 'P' . $id_generate;

        $user = User::create([
            'username' => $no_pegawai,
            'password' => $password,
            'role' => 'pegawai'
        ]);

        $pegawai = $user->pegawai()->create([
            'no_pegawai' => $no_pegawai,
            'name' => $request->name,
            'address' => $request->address,
            'number_phone' => $request->number_phone,
            'born_date' => $request->born_date,
            'gender' => $request->gender,
            'role' => $request->role,
        ]);

        if ($pegawai) {

            return response()->json([
                'success' => true,
                'message' => 'pegawai Created',
                'data diri pegawai'    => $pegawai,
                'data user pegwai'    => $user
            ], 201);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'pegawai Failed to Save',
                'data diri pegawai'    => $pegawai,
                'data user pegwai'    => $user
            ], 409);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //find pegawai by ID
        $pegawai = pegawai::find($id);

        //make response JSON
        return response()->json([
            'success' => true,
            'message' => 'Detail Data pegawai',
            'data'    => $pegawai
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
        $pegawai = pegawai::find($id);
        if (!$pegawai) {
            //data pegawai not found
            return response()->json([
                'success' => false,
                'message' => 'pegawai Not Found',
            ], 404);
        }
        //validate form
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'address' => 'required',
            'number_phone' => 'required|regex:/^(0)8[1-9][0-9]{6,9}$/',
            // 'born_date' => 'required',
            // 'gender' => 'required',
        ], [
            'number_phone.regex' => 'The number phone format is invalid (please use NUMBER and start with 08)',
        ]);

        //response error validation
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $pegawai->update([
            'name' => $request->name,
            'address' => $request->address,
            'number_phone' => $request->number_phone,
            // 'born_date' => $request->born_date,
            // 'gender' => $request->gender,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'pegawai Updated',
            'data'    => $pegawai
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
        $pegawai = pegawai::find($id);
        $user = User::find($pegawai->id_users);

        if ($pegawai) {
            //delete pegawai
            $pegawai->delete();
            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'pegawai Deleted',
            ], 200);
        }


        //data pegawai not found
        return response()->json([
            'success' => false,
            'message' => 'pegawai Not Found',
        ], 404);
    }


    public function showOnlyKasir()
    {
        $pegawaiKasir = pegawai::where('role', 'kasir')->with(['users'])->get();

        if ($pegawaiKasir) {
            return response()->json([
                'success' => true,
                'message' => 'List Data pegawai Kasir',
                'data'    => $pegawaiKasir
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'kasir Not Found',
            ], 404);
        }
    }
}