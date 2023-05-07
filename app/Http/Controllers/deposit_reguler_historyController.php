<?php

namespace App\Http\Controllers;

use App\Models\deposit_reguler_history;
use App\Models\member;
use App\Models\pegawai;
use App\Models\promo_cash;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        $deposit_reguler_history = deposit_reguler_history::with(['promo_cash', 'member', 'pegawai'])->get();

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
            // 'no_deposit_reguler_history' => 'required',
            // 'id_promo_cash' => 'required',
            'id_member' => 'required',
            'id_pegawai' => 'required',
            // 'date_time' => 'required',
            'topup_amount' => 'required|integer',
            // 'bonus' => 'required',
            // 'sisa' => 'required',
            // 'total' => 'required'
        ]);

        //response error validation
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $member = member::find($request->id_member);
        if (!$member) {
            //data member not found
            return response()->json([
                'success' => false,
                'message' => 'member Not Found',
            ], 404);
        }

        //membuatIddengan format(Xy) X= huruf dan Y = angka
        $count = DB::table('deposit_reguler_history')->count() + 1;
        $id_generate = sprintf("%03d", $count);

        //membuat angka dengan format y
        $digitYear = Carbon::parse(now())->format('y');

        //membuat angka dengan format m
        $digitMonth = Carbon::parse(now())->format('m');

        //no aktivasi_history
        $no_deposit_reguler_history = $digitYear . '.' . $digitMonth . '.' . $id_generate;

        // get date time now
        $date_time = Carbon::now();

        //setdefault when no promo
        $id_promo_cash = null;
        $bonus = 0;
        $sisa = $member->jumlah_deposit_reguler;
        $total = $sisa + $request->topup_amount + $bonus;
        //check what promo gofit have
        $promo_cash = promo_cash::latest()->get();
        foreach ($promo_cash as $promo_cash) {
            if ($member->jumlah_deposit_reguler >= $promo_cash['min_deposit_cash']) {
                if ($request->topup_amount >= $promo_cash['min_topup_cash']) {
                    $id_promo_cash = $promo_cash['id'];
                    $bonus = $promo_cash['bonus_cash'];
                    $sisa = $member->jumlah_deposit_reguler;
                    $total = $sisa + $request->topup_amount + $bonus;
                }
            }
        }

        $deposit_reguler_history = deposit_reguler_history::create([
            'no_deposit_reguler_history' => $no_deposit_reguler_history,
            'id_promo_cash' => $id_promo_cash,
            'id_member' => $request->id_member,
            'id_pegawai' => $request->id_pegawai,
            'date_time' => $date_time,
            'topup_amount' => $request->topup_amount,
            'bonus' => $bonus,
            'sisa' => $sisa,
            'total' => $total
        ]);

        if ($deposit_reguler_history) {
            $member->update([
                'jumlah_deposit_reguler' => $total
            ]);
            return response()->json([
                'success' => true,
                'message' => 'deposit_reguler_history Created and member updated successfully',
                'data'    => $deposit_reguler_history,
                'member'  => $member
            ], 201);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'deposit_reguler_history Failed to Save',
                'data'    => $deposit_reguler_history
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

    // /**
    //  * Update the specified resource in storage.
    //  *
    //  * @param  \Illuminate\Http\Request  $request
    //  * @param  int  $id
    //  * @return \Illuminate\Http\Response
    //  */
    // public function update(Request $request, $id)
    // {
    //     $deposit_reguler_history = deposit_reguler_history::find($id);
    //     if (!$deposit_reguler_history) {
    //         //data deposit_reguler_history not found
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'deposit_reguler_history Not Found',
    //         ], 404);
    //     }
    //     //validate form
    //     $validator = Validator::make($request->all(), [
    //         'id_member' => 'required',
    //         'id_pegawai' => 'required',
    //         'date_time' => 'required',
    //         'topup_amount' => 'required',
    //         'bonus' => 'required',
    //         'sisa' => 'required',
    //         'total' => 'required'
    //     ]);

    //     //response error validation
    //     if ($validator->fails()) {
    //         return response()->json($validator->errors(), 422);
    //     }

    //     //update deposit_reguler_history with new image
    //     $deposit_reguler_history->update([
    //         'no_deposit_reguler_history' => $request->no_deposit_reguler_history,
    //         'id_promo_cash' => $request->id_promo_cash,
    //         'id_member' => $request->id_member,
    //         'id_pegawai' => $request->id_pegawai,
    //         'date_time' => $request->date_time,
    //         'topup_amount' => $request->topup_amount,
    //         'bonus' => $request->bonus,
    //         'sisa' => $request->sisa,
    //         'total' => $request->total
    //     ]);

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'deposit_reguler_history Updated',
    //         'data'    => $deposit_reguler_history
    //     ], 200);
    // }

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

    /**
     * Display a listing of the resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function generate_deposit_reguler_historyCard($id)
    {
        $deposit_reguler_history = deposit_reguler_history::find($id);
        if (!$deposit_reguler_history) {
            //data deposit_reguler_history not found
            return response()->json([
                'success' => false,
                'message' => 'deposit_reguler_history Not Found',
            ], 404);
        }

        $member = member::find($deposit_reguler_history->id_member);
        $pegawai = pegawai::find($deposit_reguler_history->id_pegawai);

        $data = [
            'deposit_reguler_history' => $deposit_reguler_history,
            'member' => $member,
            'pegawai' => $pegawai,
        ];

        $pdf = Pdf::loadview('deposit_reguler_historyCard', $data);

        return $pdf->output();
    }
}