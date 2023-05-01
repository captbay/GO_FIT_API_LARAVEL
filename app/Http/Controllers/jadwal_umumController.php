<?php

namespace App\Http\Controllers;

use App\Models\jadwal_umum;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class jadwal_umumController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $jadwal_umum = jadwal_umum::with(['instruktur', 'class_detail'])->get();

        return response()->json([
            'success' => true,
            'message' => 'List Data jadwal_umum',
            'data'    => $jadwal_umum
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
            'id_class_detail' => 'required',
            'start_class' => 'required',
            'day_name' => 'required',
        ]);

        //response error validation
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        //jam harus dalam kontek H:i:s dibuatin string dulu
        $start_class = Carbon::parse($request->start_class)->format('H:i:s');

        //menambahkan 1 jam setelah start class karena emang sejam setelah start class pasti selesai
        $end_class = Carbon::parse($start_class)->addHour();

        //mengeset kapasitas karena max emang 10 saja (nanti kalo ada ikut berarti --)
        $capacity = 10;

        //cek apakah jadwal dan instuktur tersebut sudah ada atau belum
        $jadwal_umum_temp = jadwal_umum::all();
        foreach ($jadwal_umum_temp as $jadwal_umum_temp) {
            //intruktur = class = date = start_class 
            if ($jadwal_umum_temp['id_instruktur'] == $request->id_instruktur && $jadwal_umum_temp['id_class_detail'] == $request->id_class_detail  && $jadwal_umum_temp['start_class'] == $request->start_class && $jadwal_umum_temp['day_name'] == $request->day_name) {
                return response()->json([
                    'success' => false,
                    'message' => 'jadwal yang anda input sudah ada',
                ], 409);
            }
            // instuktur = date = start class
            else if ($jadwal_umum_temp['id_instruktur'] == $request->id_instruktur  && $jadwal_umum_temp['start_class'] == $start_class && $jadwal_umum_temp['day_name'] == $request->day_name) {
                return response()->json([
                    'success' => false,
                    'message' => 'instruktur tersebut sudah ada di jadwal yang anda input',
                ], 409);
            }
        }

        $jadwal_umum = jadwal_umum::firstOrCreate([
            'id_instruktur' => $request->id_instruktur,
            'id_class_detail' => $request->id_class_detail,
            'start_class' => $start_class,
            'end_class' => $end_class,
            'capacity' => $capacity,
            'day_name' => $request->day_name,
        ]);

        if ($jadwal_umum) {

            return response()->json([
                'success' => true,
                'message' => 'jadwal_umum Created',
                'data'    => $jadwal_umum,
            ], 201);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'jadwal_umum Failed to Save',
                'data'    => $jadwal_umum
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
        //find jadwal_umum by ID
        $jadwal_umum = jadwal_umum::find($id);

        //make response JSON
        return response()->json([
            'success' => true,
            'message' => 'Detail Data jadwal_umum',
            'data'    => $jadwal_umum
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
        $jadwal_umum = jadwal_umum::find($id);
        if (!$jadwal_umum) {
            //data jadwal_umum not found
            return response()->json([
                'success' => false,
                'message' => 'jadwal_umum Not Found',
            ], 404);
        }
        //validate form
        $validator = Validator::make($request->all(), [
            'id_instruktur' => 'required',
            'id_class_detail' => 'required',
            'start_class' => 'required',
            'capacity' => 'required',
            'day_name' => 'required',
        ]);

        //response error validation
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        //jam harus dalam kontek H:i:s dibuatin string dulu
        $start_class = Carbon::parse($request->start_class)->format('H:i:s');

        //menambahkan 1 jam setelah start class karena emang sejam setelah start class pasti selesai
        $end_class = Carbon::parse($start_class)->addHour();

        //cek apakah jadwal dan instuktur tersebut sudah ada atau belum
        $jadwal_umum_temp = jadwal_umum::all();
        foreach ($jadwal_umum_temp as $jadwal_umum_temp) {
            //intruktur = class = date = start_class 
            if ($jadwal_umum_temp['id_instruktur'] == $request->id_instruktur && $jadwal_umum_temp['id_class_detail'] == $request->id_class_detail  && $jadwal_umum_temp['start_class'] == $request->start_class && $jadwal_umum_temp['day_name'] == $request->day_name) {
                return response()->json([
                    'success' => false,
                    'message' => 'jadwal yang anda input sudah ada',
                ], 409);
            }
            // instuktur = date = start class
            else if ($jadwal_umum_temp['id_instruktur'] == $request->id_instruktur  && $jadwal_umum_temp['start_class'] == $start_class && $jadwal_umum_temp['day_name'] == $request->day_name) {
                return response()->json([
                    'success' => false,
                    'message' => 'instruktur tersebut sudah ada di jadwal yang anda input',
                ], 409);
            }
        }

        //update jadwal_umum with new image
        $jadwal_umum->update([
            'id_instruktur' => $request->id_instruktur,
            'id_class_detail' => $request->id_class_detail,
            'start_class' => $start_class,
            'end_class' => $end_class,
            'capacity' => $request->capacity,
            'day_name' => $request->day_name,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'jadwal_umum Updated',
            'data'    => $jadwal_umum
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
        $jadwal_umum = jadwal_umum::find($id);

        if ($jadwal_umum) {
            //delete jadwal_umum
            $jadwal_umum->delete();

            return response()->json([
                'success' => true,
                'message' => 'jadwal_umum Deleted',
            ], 200);
        }


        //data jadwal_umum not found
        return response()->json([
            'success' => false,
            'message' => 'jadwal_umum Not Found',
        ], 404);
    }
}
