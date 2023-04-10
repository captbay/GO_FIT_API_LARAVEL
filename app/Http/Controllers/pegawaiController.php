<?php

namespace App\Http\Controllers;

use App\Models\pegawai;
use Illuminate\Http\Request;
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
        $pegawai = pegawai::latest()->get();

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
            'id_users' => 'required',
            'no_pegawai' => 'required',
            'name' => 'required',
            'address' => 'required',
            'number_phone' => 'required',
            'born_date' => 'required',
            'gender' => 'required',
            'role' => 'required',
        ]);

        //response error validation
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $aktivasi_hitory = pegawai::create([
            'id_users' => $request->id_users,
            'no_pegawai' => $request->no_pegawai,
            'name' => $request->name,
            'address' => $request->address,
            'number_phone' => $request->number_phone,
            'born_date' => $request->born_date,
            'gender' => $request->gender,
            'role' => $request->role,
        ]);

        if ($aktivasi_hitory) {

            return response()->json([
                'success' => true,
                'message' => 'pegawai Created',
                'data'    => $aktivasi_hitory
            ], 201);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'pegawai Failed to Save',
                'data'    => $aktivasi_hitory
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
            'id_users' => 'required',
            'no_pegawai' => 'required',
            'name' => 'required',
            'address' => 'required',
            'number_phone' => 'required',
            'born_date' => 'required',
            'gender' => 'required',
            'role' => 'required',
        ]);

        //response error validation
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        //update pegawai with new image
        $pegawai->update([
            'id_users' => $request->id_users,
            'no_pegawai' => $request->no_pegawai,
            'name' => $request->name,
            'address' => $request->address,
            'number_phone' => $request->number_phone,
            'born_date' => $request->born_date,
            'gender' => $request->gender,
            'role' => $request->role,
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

        if ($pegawai) {
            //delete pegawai
            $pegawai->delete();

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
}
