<?php

namespace App\Http\Controllers;

use App\Models\class_running;
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
            'end_class' => 'required',
            'capacity' => 'required',
            'date' => 'required',
            'status' => 'required',
        ]);

        //response error validation
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $aktivasi_hitory = class_running::create([
            'id_instruktur' => $request->id_instruktur,
            'id_class_detail' => $request->id_class_detail,
            'start_class' => $request->start_class,
            'end_class' => $request->end_class,
            'capacity' => $request->capacity,
            'date' => $request->date,
            'status' => $request->status,
        ]);

        if ($aktivasi_hitory) {

            return response()->json([
                'success' => true,
                'message' => 'class_running Created',
                'data'    => $aktivasi_hitory
            ], 201);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'class_running Failed to Save',
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
            'end_class' => 'required',
            'capacity' => 'required',
            'date' => 'required',
            'status' => 'required',
        ]);

        //response error validation
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        //update class_running with new image
        $class_running->update([
            'id_instruktur' => $request->id_instruktur,
            'id_class_detail' => $request->id_class_detail,
            'start_class' => $request->start_class,
            'end_class' => $request->end_class,
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
