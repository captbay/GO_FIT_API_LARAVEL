<?php

namespace App\Http\Controllers;

use App\Models\deposit_package;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class deposit_packageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $deposit_package = deposit_package::with(['class_detail', 'member'])->get();

        return response()->json([
            'success' => true,
            'message' => 'List Data deposit_package',
            'data'    => $deposit_package
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
            'id_class_detail' => 'required',
            'id_member' => 'required',
            'package_amount' => 'required|integer',
            'expired_date' => 'required|date',
        ]);

        //response error validation
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $deposit_package = deposit_package::create([
            'id_class_detail' => $request->id_class_detail,
            'id_member' => $request->id_member,
            'package_amount' => $request->package_amount,
            'expired_date' => $request->expired_date,
        ]);

        if ($deposit_package) {

            return response()->json([
                'success' => true,
                'message' => 'deposit_package Created',
                'data'    => $deposit_package
            ], 201);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'deposit_package Failed to Save',
                'data'    => $deposit_package
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
        //find deposit_package by ID
        $deposit_package = deposit_package::find($id);

        //make response JSON
        return response()->json([
            'success' => true,
            'message' => 'Detail Data deposit_package',
            'data'    => $deposit_package
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
        $deposit_package = deposit_package::find($id);
        if (!$deposit_package) {
            //data deposit_package not found
            return response()->json([
                'success' => false,
                'message' => 'deposit_package Not Found',
            ], 404);
        }
        //validate form
        $validator = Validator::make($request->all(), [
            'id_class_detail' => 'required',
            'id_member' => 'required',
            'package_amount' => 'required|integer',
            'expired_date' => 'required|date',
        ]);

        //response error validation
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //update deposit_package with new image
        $deposit_package->update([
            'id_class_detail' => $request->id_class_detail,
            'id_member' => $request->id_member,
            'package_amount' => $request->package_amount,
            'expired_date' => $request->expired_date,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'deposit_package Updated',
            'data'    => $deposit_package
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
        $deposit_package = deposit_package::find($id);

        if ($deposit_package) {
            //delete deposit_package
            $deposit_package->delete();

            return response()->json([
                'success' => true,
                'message' => 'deposit_package Deleted',
            ], 200);
        }


        //data deposit_package not found
        return response()->json([
            'success' => false,
            'message' => 'deposit_package Not Found',
        ], 404);
    }
}