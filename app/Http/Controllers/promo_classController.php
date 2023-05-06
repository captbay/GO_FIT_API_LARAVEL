<?php

namespace App\Http\Controllers;

use App\Models\promo_class;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class promo_classController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $promo_class = promo_class::first()->get();

        return response()->json([
            'success' => true,
            'message' => 'List Data promo_class',
            'data'    => $promo_class
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
            // 'id_class_detail' => 'required',
            'jumlah_sesi' => 'required|integer',
            'bonus_sesi' => 'required|integer',
            'durasi_aktif' => 'required|date',
        ]);

        //response error validation
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $promo_class = promo_class::create([
            // 'id_class_detail' => $request->id_class_detail,
            'jumlah_sesi' => $request->jumlah_sesi,
            'bonus_sesi' => $request->bonus_sesi,
            'durasi_aktif' => $request->durasi_aktif,
        ]);

        if ($promo_class) {

            return response()->json([
                'success' => true,
                'message' => 'promo_class Created',
                'data'    => $promo_class
            ], 201);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'promo_class Failed to Save',
                'data'    => $promo_class
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
        //find promo_class by ID
        $promo_class = promo_class::find($id);

        //make response JSON
        return response()->json([
            'success' => true,
            'message' => 'Detail Data promo_class',
            'data'    => $promo_class
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
        $promo_class = promo_class::find($id);
        if (!$promo_class) {
            //data promo_class not found
            return response()->json([
                'success' => false,
                'message' => 'promo_class Not Found',
            ], 404);
        }
        //validate form
        $validator = Validator::make($request->all(), [
            // 'id_class_detail' => 'required',
            'jumlah_sesi' => 'required|integer',
            'bonus_sesi' => 'required|integer',
            'durasi_aktif' => 'required|date',
        ]);

        //response error validation
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //update promo_class with new image
        $promo_class->update([
            // 'id_class_detail' => $request->id_class_detail,
            'jumlah_sesi' => $request->jumlah_sesi,
            'bonus_sesi' => $request->bonus_sesi,
            'durasi_aktif' => $request->durasi_aktif,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'promo_class Updated',
            'data'    => $promo_class
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
        $promo_class = promo_class::find($id);

        if ($promo_class) {
            //delete promo_class
            $promo_class->delete();

            return response()->json([
                'success' => true,
                'message' => 'promo_class Deleted',
            ], 200);
        }


        //data promo_class not found
        return response()->json([
            'success' => false,
            'message' => 'promo_class Not Found',
        ], 404);
    }
}