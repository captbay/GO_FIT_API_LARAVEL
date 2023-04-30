<?php

namespace App\Http\Controllers;

use App\Models\gym_booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class gym_bookingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $gym_booking = gym_booking::first()->get();

        return response()->json([
            'success' => true,
            'message' => 'List Data gym_booking',
            'data'    => $gym_booking
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
            'id_gym' => 'required',
            'id_member' => 'required',
            'date_time' => 'required'
        ]);

        //response error validation
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $gym_booking = gym_booking::create([
            'id_gym' => $request->id_gym,
            'id_member' => $request->id_member,
            'date_time' => $request->date_time
        ]);

        if ($gym_booking) {

            return response()->json([
                'success' => true,
                'message' => 'gym_booking Created',
                'data'    => $gym_booking
            ], 201);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'gym_booking Failed to Save',
                'data'    => $gym_booking
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
        //find gym_booking by ID
        $gym_booking = gym_booking::find($id);

        //make response JSON
        return response()->json([
            'success' => true,
            'message' => 'Detail Data gym_booking',
            'data'    => $gym_booking
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
        $gym_booking = gym_booking::find($id);
        if (!$gym_booking) {
            //data gym_booking not found
            return response()->json([
                'success' => false,
                'message' => 'gym_booking Not Found',
            ], 404);
        }
        //validate form
        $validator = Validator::make($request->all(), [
            'id_gym' => 'required',
            'id_member' => 'required',
            'date_time' => 'required'
        ]);

        //response error validation
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        //update gym_booking with new image
        $gym_booking->update([
            'id_gym' => $request->id_gym,
            'id_member' => $request->id_member,
            'date_time' => $request->date_time
        ]);

        return response()->json([
            'success' => true,
            'message' => 'gym_booking Updated',
            'data'    => $gym_booking
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
        $gym_booking = gym_booking::find($id);

        if ($gym_booking) {
            //delete gym_booking
            $gym_booking->delete();

            return response()->json([
                'success' => true,
                'message' => 'gym_booking Deleted',
            ], 200);
        }


        //data gym_booking not found
        return response()->json([
            'success' => false,
            'message' => 'gym_booking Not Found',
        ], 404);
    }
}