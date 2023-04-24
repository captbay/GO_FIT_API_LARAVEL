<?php

namespace App\Http\Controllers;

use App\Models\deposit_package_history;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class deposit_package_historyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $deposit_package_history = deposit_package_history::latest()->get();

        return response()->json([
            'success' => true,
            'message' => 'List Data deposit_package_history',
            'data'    => $deposit_package_history
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
            'no_deposit_package_history' => 'required',
            'id_promo_class' => 'required',
            'id_member' => 'required',
            'id_pegawai' => 'required',
            'date_time' => 'required',
            'total_price' => 'required',
            'package_amount' => 'required',
            'expired_date' => 'required'
        ]);

        //response error validation
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $deposit_package_history = deposit_package_history::create([
            'no_deposit_package_history' => $request->no_deposit_package_history,
            'id_promo_class' => $request->id_promo_class,
            'id_member' => $request->id_member,
            'id_pegawai' => $request->id_pegawai,
            'date_time' => $request->date_time,
            'total_price' => $request->total_price,
            'package_amount' => $request->package_amount,
            'expired_date' => $request->expired_date
        ]);

        if ($deposit_package_history) {

            return response()->json([
                'success' => true,
                'message' => 'deposit_package_history Created',
                'data'    => $deposit_package_history
            ], 201);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'deposit_package_history Failed to Save',
                'data'    => $deposit_package_history
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
        //find deposit_package_history by ID
        $deposit_package_history = deposit_package_history::find($id);

        //make response JSON
        return response()->json([
            'success' => true,
            'message' => 'Detail Data deposit_package_history',
            'data'    => $deposit_package_history
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
        $deposit_package_history = deposit_package_history::find($id);
        if (!$deposit_package_history) {
            //data deposit_package_history not found
            return response()->json([
                'success' => false,
                'message' => 'deposit_package_history Not Found',
            ], 404);
        }
        //validate form
        $validator = Validator::make($request->all(), [
            'no_deposit_package_history' => 'required',
            'id_promo_class' => 'required',
            'id_member' => 'required',
            'id_pegawai' => 'required',
            'date_time' => 'required',
            'total_price' => 'required',
            'package_amount' => 'required',
            'expired_date' => 'required'
        ]);

        //response error validation
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        //update deposit_package_history with new image
        $deposit_package_history->update([
            'no_deposit_package_history' => $request->no_deposit_package_history,
            'id_promo_class' => $request->id_promo_class,
            'id_member' => $request->id_member,
            'id_pegawai' => $request->id_pegawai,
            'date_time' => $request->date_time,
            'total_price' => $request->total_price,
            'package_amount' => $request->package_amount,
            'expired_date' => $request->expired_date
        ]);

        return response()->json([
            'success' => true,
            'message' => 'deposit_package_history Updated',
            'data'    => $deposit_package_history
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
        $deposit_package_history = deposit_package_history::find($id);

        if ($deposit_package_history) {
            //delete deposit_package_history
            $deposit_package_history->delete();

            return response()->json([
                'success' => true,
                'message' => 'deposit_package_history Deleted',
            ], 200);
        }


        //data deposit_package_history not found
        return response()->json([
            'success' => false,
            'message' => 'deposit_package_history Not Found',
        ], 404);
    }
}