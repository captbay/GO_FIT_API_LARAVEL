<?php

namespace App\Http\Controllers;

use App\Models\aktivasi_history;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class aktivasi_historyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $aktivasi_history = aktivasi_history::latest()->get();

        return response()->json([
            'success' => true,
            'message' => 'List Data aktivasi_history',
            'data'    => $aktivasi_history
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
            'no_aktivasi_history' => 'required',
            'id_member' => 'required',
            'id_pegawai' => 'required',
            'date_time' => 'required',
            'expired_date' => 'required'
        ]);

        //response error validation
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $aktivasi_history = aktivasi_history::create([
            'no_aktivasi_history' => $request->no_aktivasi_history,
            'id_member' => $request->id_member,
            'id_pegawai' => $request->id_pegawai,
            'date_time' => $request->date_time,
            'expired_date' => $request->date_time,
        ]);

        if ($aktivasi_history) {

            return response()->json([
                'success' => true,
                'message' => 'aktivasi_history Created',
                'data'    => $aktivasi_history
            ], 201);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'aktivasi_history Failed to Save',
                'data'    => $aktivasi_history
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
        //find aktivasi_history by ID
        $aktivasi_history = aktivasi_history::find($id);

        //make response JSON
        return response()->json([
            'success' => true,
            'message' => 'Detail Data aktivasi_history',
            'data'    => $aktivasi_history
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
        $aktivasi_history = aktivasi_history::find($id);
        if (!$aktivasi_history) {
            //data aktivasi_history not found
            return response()->json([
                'success' => false,
                'message' => 'aktivasi_history Not Found',
            ], 404);
        }
        //validate form
        $validator = Validator::make($request->all(), [
            'no_aktivasi_history' => 'required',
            'id_member' => 'required',
            'id_pegawai' => 'required',
            'date_time' => 'required',
            'expired_date' => 'required'
        ]);

        //response error validation
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        //update aktivasi_history with new image
        $aktivasi_history->update([
            'no_aktivasi_history' => $request->no_aktivasi_history,
            'id_member' => $request->id_member,
            'id_pegawai' => $request->id_pegawai,
            'date_time' => $request->date_time,
            'expired_date' => $request->date_time,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'aktivasi_history Updated',
            'data'    => $aktivasi_history
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
        $aktivasi_history = aktivasi_history::find($id);

        if ($aktivasi_history) {
            //delete aktivasi_history
            $aktivasi_history->delete();

            return response()->json([
                'success' => true,
                'message' => 'aktivasi_history Deleted',
            ], 200);
        }


        //data aktivasi_history not found
        return response()->json([
            'success' => false,
            'message' => 'aktivasi_history Not Found',
        ], 404);
    }
}