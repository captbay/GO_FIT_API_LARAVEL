<?php

namespace App\Http\Controllers;

use App\Models\instruktur_presensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class instruktur_presensi_presensiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $instruktur_presensi = instruktur_presensi::with(['instruktur'])->get();

        return response()->json([
            'success' => true,
            'message' => 'List Data instruktur_presensi',
            'data'    => $instruktur_presensi
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
            'id_instruktur' => 'required',
            'status_class' => 'required|boolean',
            'start_class' => 'required|date_format:H:i',
            'end_class' => 'required|date_format:H:i|after:start_class',
            'date' => 'required|date',
        ]);

        //response error validation
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $instruktur_presensi = instruktur_presensi::create([
            'id_instruktur' => $request->id_instruktur,
            'status_class' => $request->status_class,
            'start_class' => $request->start_class,
            'end_class' => $request->end_class,
            'date' => $request->date,
        ]);

        if ($instruktur_presensi) {

            return response()->json([
                'success' => true,
                'message' => 'instruktur_presensi Created',
                'data'    => $instruktur_presensi
            ], 201);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'instruktur_presensi Failed to Save',
                'data'    => $instruktur_presensi
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
        //find instruktur_presensi by ID
        $instruktur_presensi = instruktur_presensi::find($id);

        //make response JSON
        return response()->json([
            'success' => true,
            'message' => 'Detail Data instruktur_presensi',
            'data'    => $instruktur_presensi
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
        $instruktur_presensi = instruktur_presensi::find($id);
        if (!$instruktur_presensi) {
            //data instruktur_presensi not found
            return response()->json([
                'success' => false,
                'message' => 'instruktur_presensi Not Found',
            ], 404);
        }
        //validate form
        $validator = Validator::make($request->all(), [
            'id_instruktur' => 'required',
            'status_class' => 'required|boolean',
            'start_class' => 'required|date_format:H:i',
            'end_class' => 'required|date_format:H:i|after:start_class',
            'date' => 'required|date',
        ]);

        //response error validation
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //update instruktur_presensi with new image
        $instruktur_presensi->update([
            'id_instruktur' => $request->id_instruktur,
            'status_class' => $request->status_class,
            'start_class' => $request->start_class,
            'end_class' => $request->end_class,
            'date' => $request->date,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'instruktur_presensi Updated',
            'data'    => $instruktur_presensi
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
        $instruktur_presensi = instruktur_presensi::find($id);

        if ($instruktur_presensi) {
            //delete instruktur_presensi
            $instruktur_presensi->delete();

            return response()->json([
                'success' => true,
                'message' => 'instruktur_presensi Deleted',
            ], 200);
        }


        //data instruktur_presensi not found
        return response()->json([
            'success' => false,
            'message' => 'instruktur_presensi Not Found',
        ], 404);
    }
}