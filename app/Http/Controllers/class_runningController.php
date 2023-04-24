<?php

namespace App\Http\Controllers;

use App\Models\class_running;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class class_runningController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $class_running = class_running::latest()->get();

        return response()->json([
            'success' => true,
            'message' => 'List Data class_running',
            'data'    => $class_running
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
            'date' => 'required',
        ]);

        //response error validation
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }



        //menambahkan 1 jam setelah start class karena emang sejam setelah start class pasti selesai
        $end_class = Carbon::parse($request->start_class)->addHour();

        //mengeset kapasitas karena max emang 10 saja (nanti kalo ada ikut berarti --)
        $capacity = 10;

        //status awal pasti ada (1)
        $status = 1;

        //cek apakah jadwal dan instuktur tersebut sudah ada atau belum
        //jam harus dalam kontek H:i:s dibuatin string dulu
        $start_class = Carbon::parse($request->start_class)->format('H:i:s');
        $class_running_temp = class_running::all();
        foreach ($class_running_temp as $class_running_temp) {
            //intruktur = class = date = start_class 
            if ($class_running_temp['id_instruktur'] == $request->id_instruktur && $class_running_temp['id_class_detail'] == $request->id_class_detail && $class_running_temp['date'] == $request->date  && $class_running_temp['start_class'] == $start_class) {
                return response()->json([
                    'success' => false,
                    'message' => 'jadwal yang anda input sudah ada',
                ], 409);
            }
            // instuktur = date = start class
            else if ($class_running_temp['id_instruktur'] == $request->id_instruktur  && $class_running_temp['date'] == $request->date  && $class_running_temp['start_class'] == $start_class) {
                return response()->json([
                    'success' => false,
                    'message' => 'instruktur tersebut sudah ada di jadwal yang anda input',
                ], 409);
            }
        }

        $class_running = class_running::firstOrCreate([
            'id_instruktur' => $request->id_instruktur,
            'id_class_detail' => $request->id_class_detail,
            'start_class' => $start_class,
            'end_class' => $end_class,
            'capacity' => $capacity,
            'date' => $request->date,
            'status' => $status,
        ]);

        if ($class_running) {

            return response()->json([
                'success' => true,
                'message' => 'class_running Created',
                'data'    => $class_running,
            ], 201);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'class_running Failed to Save',
                'data'    => $class_running
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
        //find class_running by ID
        $class_running = class_running::find($id);

        //make response JSON
        return response()->json([
            'success' => true,
            'message' => 'Detail Data class_running',
            'data'    => $class_running
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
        $class_running = class_running::find($id);
        if (!$class_running) {
            //data class_running not found
            return response()->json([
                'success' => false,
                'message' => 'class_running Not Found',
            ], 404);
        }
        //validate form
        $validator = Validator::make($request->all(), [
            'id_instruktur' => 'required',
            'id_class_detail' => 'required',
            'start_class' => 'required',
            'capacity' => 'required',
            'date' => 'required',
            'status' => 'required',
        ]);

        //response error validation
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        //menambahkan 1 jam setelah start class karena emang sejam setelah start class pasti selesai
        $end_class = Carbon::parse($request->start_class)->addHour();

        //cek apakah jadwal dan instuktur tersebut sudah ada atau belum
        //jam harus dalam kontek H:i:s dibuatin string dulu
        $start_class = Carbon::parse($request->start_class)->format('H:i:s');
        $class_running_temp = class_running::all();
        foreach ($class_running_temp as $class_running_temp) {
            //intruktur = class = date = start_class 
            if ($class_running_temp['id_instruktur'] == $request->id_instruktur && $class_running_temp['id_class_detail'] == $request->id_class_detail && $class_running_temp['date'] == $request->date  && $class_running_temp['start_class'] == $start_class) {
                return response()->json([
                    'success' => false,
                    'message' => 'jadwal yang anda input sudah ada',
                ], 409);
            }
            // instuktur = date = start class
            else if ($class_running_temp['id_instruktur'] == $request->id_instruktur  && $class_running_temp['date'] == $request->date  && $class_running_temp['start_class'] == $start_class) {
                return response()->json([
                    'success' => false,
                    'message' => 'instruktur tersebut sudah ada di jadwal yang anda input',
                ], 409);
            }
        }

        //update class_running with new image
        $class_running->update([
            'id_instruktur' => $request->id_instruktur,
            'id_class_detail' => $request->id_class_detail,
            'start_class' => $start_class,
            'end_class' => $end_class,
            'capacity' => $request->capacity,
            'date' => $request->date,
            'status' => $request->status,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'class_running Updated',
            'data'    => $class_running
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
        $class_running = class_running::find($id);

        if ($class_running) {
            //delete class_running
            $class_running->delete();

            return response()->json([
                'success' => true,
                'message' => 'class_running Deleted',
            ], 200);
        }


        //data class_running not found
        return response()->json([
            'success' => false,
            'message' => 'class_running Not Found',
        ], 404);
    }
}
