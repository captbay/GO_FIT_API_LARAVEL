<?php

namespace App\Http\Controllers;

use App\Models\class_detail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class class_detailController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $class_detail = class_detail::first()->get();

        return response()->json([
            'success' => true,
            'message' => 'List Data class_detail',
            'data'    => $class_detail
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
            'name' => 'required',
            'price' => 'required',
        ]);

        //response error validation
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $class_detail = class_detail::create([
            'name' => $request->name,
            'price' => $request->price,
        ]);

        if ($class_detail) {

            return response()->json([
                'success' => true,
                'message' => 'class_detail Created',
                'data'    => $class_detail
            ], 201);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'class_detail Failed to Save',
                'data'    => $class_detail
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
        //find class_detail by ID
        $class_detail = class_detail::find($id);

        //make response JSON
        return response()->json([
            'success' => true,
            'message' => 'Detail Data class_detail',
            'data'    => $class_detail
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
        $class_detail = class_detail::find($id);
        if (!$class_detail) {
            //data class_detail not found
            return response()->json([
                'success' => false,
                'message' => 'class_detail Not Found',
            ], 404);
        }
        //validate form
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'price' => 'required',
        ]);

        //response error validation
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        //update class_detail with new image
        $class_detail->update([
            'name' => $request->name,
            'price' => $request->price,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'class_detail Updated',
            'data'    => $class_detail
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
        $class_detail = class_detail::find($id);

        if ($class_detail) {
            //delete class_detail
            $class_detail->delete();

            return response()->json([
                'success' => true,
                'message' => 'class_detail Deleted',
            ], 200);
        }


        //data class_detail not found
        return response()->json([
            'success' => false,
            'message' => 'class_detail Not Found',
        ], 404);
    }
}