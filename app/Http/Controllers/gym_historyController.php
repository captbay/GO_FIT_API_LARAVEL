<?php

namespace App\Http\Controllers;

use App\Models\gym_history;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class gym_historyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $gym_history = gym_history::with(['gym_booking'])->get();

        return response()->json([
            'success' => true,
            'message' => 'List Data gym_history',
            'data'    => $gym_history
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
            'no_gym_history' => 'required',
            'id_gym_booking' => 'required',
            'date_time' => 'required',
        ]);

        //response error validation
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $gym_history = gym_history::create([
            'no_gym_history' => $request->no_gym_history,
            'id_gym_booking' => $request->id_gym_booking,
            'date_time' => $request->date_time,
        ]);

        if ($gym_history) {

            return response()->json([
                'success' => true,
                'message' => 'gym_history Created',
                'data'    => $gym_history
            ], 201);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'gym_history Failed to Save',
                'data'    => $gym_history
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
        //find gym_history by ID
        $gym_history = gym_history::find($id);

        //make response JSON
        return response()->json([
            'success' => true,
            'message' => 'Detail Data gym_history',
            'data'    => $gym_history
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
        $gym_history = gym_history::find($id);
        if (!$gym_history) {
            //data gym_history not found
            return response()->json([
                'success' => false,
                'message' => 'gym_history Not Found',
            ], 404);
        }
        //validate form
        $validator = Validator::make($request->all(), [
            'no_gym_history' => 'required',
            'id_gym_booking' => 'required',
            'date_time' => 'required',
        ]);

        //response error validation
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //update gym_history with new image
        $gym_history->update([
            'no_gym_history' => $request->no_gym_history,
            'id_gym_booking' => $request->id_gym_booking,
            'date_time' => $request->date_time,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'gym_history Updated',
            'data'    => $gym_history
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
        $gym_history = gym_history::find($id);

        if ($gym_history) {
            //delete gym_history
            $gym_history->delete();

            return response()->json([
                'success' => true,
                'message' => 'gym_history Deleted',
            ], 200);
        }


        //data gym_history not found
        return response()->json([
            'success' => false,
            'message' => 'gym_history Not Found',
        ], 404);
    }
}