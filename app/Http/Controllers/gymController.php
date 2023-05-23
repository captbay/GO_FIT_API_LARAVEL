<?php

namespace App\Http\Controllers;

use App\Models\gym;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;

class gymController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $gym = gym::first()->get();

        return response()->json([
            'success' => true,
            'message' => 'List Data gym',
            'data'    => $gym
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
            'capacity' => 'required|integer',
            'start_gym' => 'required|date_format:H:i',
            'end_gym' => 'required|date_format:H:i|after:start_gym'
        ]);

        //response error validation
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $gym = gym::create([
            'capacity' => $request->capacity,
            'start_gym' => $request->start_gym,
            'end_gym' => $request->end_gym
        ]);

        if ($gym) {

            return response()->json([
                'success' => true,
                'message' => 'gym Created',
                'data'    => $gym
            ], 201);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'gym Failed to Save',
                'data'    => $gym
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
        //find gym by ID
        $gym = gym::find($id);

        //make response JSON
        return response()->json([
            'success' => true,
            'message' => 'Detail Data gym',
            'data'    => $gym
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
        $gym = gym::find($id);
        if (!$gym) {
            //data gym not found
            return response()->json([
                'success' => false,
                'message' => 'gym Not Found',
            ], 404);
        }
        //validate form
        $validator = Validator::make($request->all(), [
            'capacity' => 'required|integer',
            'start_gym' => 'required|date_format:H:i',
            'end_gym' => 'required|date_format:H:i|after:start_gym'
        ]);

        //response error validation
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //update gym with new image
        $gym->update([
            'capacity' => $request->capacity,
            'date' => $request->date,
            'start_gym' => $request->start_gym,
            'end_gym' => $request->end_gym
        ]);

        return response()->json([
            'success' => true,
            'message' => 'gym Updated',
            'data'    => $gym
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
        $gym = gym::find($id);

        if ($gym) {
            //delete gym
            $gym->delete();

            return response()->json([
                'success' => true,
                'message' => 'gym Deleted',
            ], 200);
        }


        //data gym not found
        return response()->json([
            'success' => false,
            'message' => 'gym Not Found',
        ], 404);
    }
}
