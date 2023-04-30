<?php

namespace App\Http\Controllers;

use App\Models\class_booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class class_bookingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $class_booking = class_booking::first()->get();

        return response()->json([
            'success' => true,
            'message' => 'List Data class_booking',
            'data'    => $class_booking
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
            'id_class_running' => 'required',
            'id_member' => 'required',
            'date_time' => 'required',
        ]);

        //response error validation
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $class_booking = class_booking::create([
            'id_class_running' => $request->id_class_running,
            'id_member' => $request->id_member,
            'date_time' => $request->date_time,
        ]);

        if ($class_booking) {

            return response()->json([
                'success' => true,
                'message' => 'class_booking Created',
                'data'    => $class_booking
            ], 201);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'class_booking Failed to Save',
                'data'    => $class_booking
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
        //find class_booking by ID
        $class_booking = class_booking::find($id);

        //make response JSON
        return response()->json([
            'success' => true,
            'message' => 'Detail Data class_booking',
            'data'    => $class_booking
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
        $class_booking = class_booking::find($id);
        if (!$class_booking) {
            //data class_booking not found
            return response()->json([
                'success' => false,
                'message' => 'class_booking Not Found',
            ], 404);
        }
        //validate form
        $validator = Validator::make($request->all(), [
            'id_class_running' => 'required',
            'id_member' => 'required',
            'date_time' => 'required',
        ]);

        //response error validation
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        //update class_booking with new image
        $class_booking->update([
            'id_class_running' => $request->id_class_running,
            'id_member' => $request->id_member,
            'date_time' => $request->date_time,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'class_booking Updated',
            'data'    => $class_booking
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
        $class_booking = class_booking::find($id);

        if ($class_booking) {
            //delete class_booking
            $class_booking->delete();

            return response()->json([
                'success' => true,
                'message' => 'class_booking Deleted',
            ], 200);
        }


        //data class_booking not found
        return response()->json([
            'success' => false,
            'message' => 'class_booking Not Found',
        ], 404);
    }
}
