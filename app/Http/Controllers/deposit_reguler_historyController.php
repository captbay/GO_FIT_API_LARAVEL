<?php

namespace App\Http\Controllers;

use App\Models\deposit_reguler_history;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class deposit_reguler_historyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $deposit_reguler_history = deposit_reguler_history::latest()->get();

        return response()->json([
            'success' => true,
            'message' => 'List Data deposit_reguler_history',
            'data'    => $deposit_reguler_history
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
            'no_deposit_reguler_history' => 'required',
            'id_promo_cash' => 'required',
            'id_member' => 'required',
            'id_pegawai' => 'required',
            'date_time' => 'required',
            'topup_amount' => 'required',
            'bonus' => 'required',
            'sisa' => 'required',
            'total' => 'required'
        ]);

        //response error validation
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $aktivasi_hitory = deposit_reguler_history::create([
            'no_deposit_reguler_history' => $request->no_deposit_reguler_history,
            'id_promo_cash' => $request->id_promo_cash,
            'id_member' => $request->id_member,
            'id_pegawai' => $request->id_pegawai,
            'date_time' => $request->date_time,
            'topup_amount' => $request->topup_amount,
            'bonus' => $request->bonus,
            'sisa' => $request->sisa,
            'total' => $request->total
        ]);

        if ($aktivasi_hitory) {

            return response()->json([
                'success' => true,
                'message' => 'deposit_reguler_history Created',
                'data'    => $aktivasi_hitory
            ], 201);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'deposit_reguler_history Failed to Save',
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
        //find deposit_reguler_history by ID
        $deposit_reguler_history = deposit_reguler_history::find($id);

        //make response JSON
        return response()->json([
            'success' => true,
            'message' => 'Detail Data deposit_reguler_history',
            'data'    => $deposit_reguler_history
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
        $deposit_reguler_history = deposit_reguler_history::find($id);
        if (!$deposit_reguler_history) {
            //data deposit_reguler_history not found
            return response()->json([
                'success' => false,
                'message' => 'deposit_reguler_history Not Found',
            ], 404);
        }
        //validate form
        $validator = Validator::make($request->all(), [
            'no_deposit_reguler_history' => 'required',
            'id_promo_cash' => 'required',
            'id_member' => 'required',
            'id_pegawai' => 'required',
            'date_time' => 'required',
            'topup_amount' => 'required',
            'bonus' => 'required',
            'sisa' => 'required',
            'total' => 'required'
        ]);

        //response error validation
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        //update deposit_reguler_history with new image
        $deposit_reguler_history->update([
            'no_deposit_reguler_history' => $request->no_deposit_reguler_history,
            'id_promo_cash' => $request->id_promo_cash,
            'id_member' => $request->id_member,
            'id_pegawai' => $request->id_pegawai,
            'date_time' => $request->date_time,
            'topup_amount' => $request->topup_amount,
            'bonus' => $request->bonus,
            'sisa' => $request->sisa,
            'total' => $request->total
        ]);

        return response()->json([
            'success' => true,
            'message' => 'deposit_reguler_history Updated',
            'data'    => $deposit_reguler_history
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
        $deposit_reguler_history = deposit_reguler_history::find($id);

        if ($deposit_reguler_history) {
            //delete deposit_reguler_history
            $deposit_reguler_history->delete();

            return response()->json([
                'success' => true,
                'message' => 'deposit_reguler_history Deleted',
            ], 200);
        }


        //data deposit_reguler_history not found
        return response()->json([
            'success' => false,
            'message' => 'deposit_reguler_history Not Found',
        ], 404);
    }
}
