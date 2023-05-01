<?php

namespace App\Http\Controllers;

use App\Models\class_history;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class class_historyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $class_history = class_history::with(['class_booking'])->get();

        return response()->json([
            'success' => true,
            'message' => 'List Data class_history',
            'data'    => $class_history
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
            'no_class_history' => 'required',
            'id_class_booking' => 'required',
            'date_time' => 'required',
            'sisa_deposit' => 'required',
        ]);

        //response error validation
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $class_history = class_history::create([
            'no_class_history' => $request->no_class_history,
            'id_class_booking' => $request->id_class_booking,
            'date_time' => $request->date_time,
            'sisa_deposit' => $request->sisa_deposit,
        ]);

        if ($class_history) {

            return response()->json([
                'success' => true,
                'message' => 'class_history Created',
                'data'    => $class_history
            ], 201);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'class_history Failed to Save',
                'data'    => $class_history
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
        //find class_history by ID
        $class_history = class_history::find($id);

        //make response JSON
        return response()->json([
            'success' => true,
            'message' => 'Detail Data class_history',
            'data'    => $class_history
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
        $class_history = class_history::find($id);
        if (!$class_history) {
            //data class_history not found
            return response()->json([
                'success' => false,
                'message' => 'class_history Not Found',
            ], 404);
        }
        //validate form
        $validator = Validator::make($request->all(), [
            'no_class_history' => 'required',
            'id_class_booking' => 'required',
            'date_time' => 'required',
            'sisa_deposit' => 'required',
        ]);

        //response error validation
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        //update class_history with new image
        $class_history->update([
            'no_class_history' => $request->no_class_history,
            'id_class_booking' => $request->id_class_booking,
            'date_time' => $request->date_time,
            'sisa_deposit' => $request->sisa_deposit,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'class_history Updated',
            'data'    => $class_history
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
        $class_history = class_history::find($id);

        if ($class_history) {
            //delete class_history
            $class_history->delete();

            return response()->json([
                'success' => true,
                'message' => 'class_history Deleted',
            ], 200);
        }


        //data class_history not found
        return response()->json([
            'success' => false,
            'message' => 'class_history Not Found',
        ], 404);
    }
}
