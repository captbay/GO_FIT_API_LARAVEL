<?php

namespace App\Http\Controllers;

use App\Models\class_running;
use App\Models\instruktur;
use App\Models\instruktur_izin;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class instruktur_izinController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $instruktur_izin = instruktur_izin::with(['instruktur', 'instruktur_pengganti', 'class_running.jadwal_umum.class_detail'])->get();

        return response()->json([
            'success' => true,
            'message' => 'List Data instruktur_izin',
            'data'    => $instruktur_izin
        ], 200);
    }

    public function indexNotConfirm()
    {
        $instruktur_izin = instruktur_izin::where('is_confirm', 0)->with(['instruktur', 'instruktur_pengganti', 'class_running.jadwal_umum.class_detail'])->get();

        return response()->json([
            'success' => true,
            'message' => 'List Data instruktur_izin',
            'data'    => $instruktur_izin
        ], 200);
    }

    public function indexAlredyConfirm()
    {
        $instruktur_izin = instruktur_izin::where('is_confirm', 1)->with(['instruktur', 'instruktur_pengganti', 'class_running.jadwal_umum.class_detail'])->get();

        return response()->json([
            'success' => true,
            'message' => 'List Data instruktur_izin',
            'data'    => $instruktur_izin
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
            'id_instruktur_pengganti' => 'required',
            'id_class_running' => 'required',
            'alasan' => 'required',
            'is_confirm' => 'required|boolean',
        ]);

        //response error validation
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $date = Carbon::now();
        $instruktur_izin = instruktur_izin::create([
            'id_instruktur' => $request->id_instruktur,
            'id_instruktur_pengganti' => $request->id_instruktur_pengganti,
            'id_class_running' => $request->id_class_running,
            'alasan' => $request->alasan,
            'is_confirm' => $request->is_confirm,
            'date' => $date,
        ]);

        if ($instruktur_izin) {

            return response()->json([
                'success' => true,
                'message' => 'instruktur_izin Created',
                'data'    => $instruktur_izin
            ], 201);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'instruktur_izin Failed to Save',
                'data'    => $instruktur_izin
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
        //find instruktur_izin by ID
        $instruktur_izin = instruktur_izin::find($id);

        //make response JSON
        return response()->json([
            'success' => true,
            'message' => 'Detail Data instruktur_izin',
            'data'    => $instruktur_izin
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
        $instruktur_izin = instruktur_izin::find($id);
        if (!$instruktur_izin) {
            //data instruktur_izin not found
            return response()->json([
                'success' => false,
                'message' => 'instruktur_izin Not Found',
            ], 404);
        }
        //validate form
        $validator = Validator::make($request->all(), [
            'id_instruktur' => 'required',
            'id_instruktur_pengganti' => 'required',
            'id_class_running' => 'required',
            'alasan' => 'required',
            'is_confirm' => 'required|boolean',
        ]);

        //response error validation
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $date = Carbon::now();

        //update instruktur_izin with new image
        $instruktur_izin->update([
            'id_instruktur' => $request->id_instruktur,
            'id_instruktur_pengganti' => $request->id_instruktur_pengganti,
            'id_class_running' => $request->id_class_running,
            'alasan' => $request->alasan,
            'is_confirm' => $request->is_confirm,
            'date' => $date,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'instruktur_izin Updated',
            'data'    => $instruktur_izin
        ], 200);
    }


    public function confirmIzin($id)
    {
        $instruktur_izin = instruktur_izin::find($id);
        if (!$instruktur_izin) {
            //data instruktur_izin not found
            return response()->json([
                'success' => false,
                'message' => 'instruktur_izin Not Found',
            ], 404);
        }

        //update instruktur_izin 
        $instruktur_izin->update([
            'is_confirm' => 1,
        ]);

        //update class_running status
        $instrukturPengganti = instruktur::find($instruktur_izin->id_instruktur_pengganti);
        $instrukturAsliName = $instruktur_izin->instruktur->name;
        $statusTemp = 'menggantikan ' . $instrukturAsliName;
        //update class_running status
        $class_running = class_running::find($instruktur_izin->id_class_running);
        $class_running->update([
            'id_instruktur' => $instrukturPengganti->id,
            'status' => $statusTemp,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'instruktur_izin Updated status',
            'data'    => $instruktur_izin
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
        $instruktur_izin = instruktur_izin::find($id);

        if ($instruktur_izin) {
            //delete instruktur_izin
            $instruktur_izin->delete();

            return response()->json([
                'success' => true,
                'message' => 'instruktur_izin Deleted',
            ], 200);
        }


        //data instruktur_izin not found
        return response()->json([
            'success' => false,
            'message' => 'instruktur_izin Not Found',
        ], 404);
    }
}