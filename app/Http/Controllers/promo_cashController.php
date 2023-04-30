<?php

namespace App\Http\Controllers;

use App\Models\promo_cash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class promo_cashController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $promo_cash = promo_cash::first()->get();

        return response()->json([
            'success' => true,
            'message' => 'List Data promo_cash',
            'data'    => $promo_cash
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
            'min_deposit_cash' => 'required',
            'min_topup_cash' => 'required',
            'bonus_cash' => 'required',
        ]);

        //response error validation
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $promo_cash = promo_cash::create([
            'min_deposit_cash' => $request->min_deposit_cash,
            'min_topup_cash' => $request->min_topup_cash,
            'bonus_cash' => $request->bonus_cash,
        ]);

        if ($promo_cash) {

            return response()->json([
                'success' => true,
                'message' => 'promo_cash Created',
                'data'    => $promo_cash
            ], 201);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'promo_cash Failed to Save',
                'data'    => $promo_cash
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
        //find promo_cash by ID
        $promo_cash = promo_cash::find($id);

        //make response JSON
        return response()->json([
            'success' => true,
            'message' => 'Detail Data promo_cash',
            'data'    => $promo_cash
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
        $promo_cash = promo_cash::find($id);
        if (!$promo_cash) {
            //data promo_cash not found
            return response()->json([
                'success' => false,
                'message' => 'promo_cash Not Found',
            ], 404);
        }
        //validate form
        $validator = Validator::make($request->all(), [
            'min_deposit_cash' => 'required',
            'min_topup_cash' => 'required',
            'bonus_cash' => 'required',
        ]);

        //response error validation
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        //update promo_cash with new image
        $promo_cash->update([
            'min_deposit_cash' => $request->min_deposit_cash,
            'min_topup_cash' => $request->min_topup_cash,
            'bonus_cash' => $request->bonus_cash,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'promo_cash Updated',
            'data'    => $promo_cash
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
        $promo_cash = promo_cash::find($id);

        if ($promo_cash) {
            //delete promo_cash
            $promo_cash->delete();

            return response()->json([
                'success' => true,
                'message' => 'promo_cash Deleted',
            ], 200);
        }


        //data promo_cash not found
        return response()->json([
            'success' => false,
            'message' => 'promo_cash Not Found',
        ], 404);
    }
}
