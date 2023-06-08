<?php

namespace App\Http\Controllers;

use App\Models\aktivasi_history;
use App\Models\member;
use App\Models\member_activity;
use App\Models\pegawai;
use App\Models\report_income;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;


class aktivasi_historyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $aktivasi_history = aktivasi_history::with(['member', 'pegawai'])->get();

        return response()->json([
            'success' => true,
            'message' => 'List Data aktivasi_history',
            'data'    => $aktivasi_history
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
            'id_member' => 'required',
            'id_pegawai' => 'required',
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
        //kalau member belum aktivasi maka buat baru

        //membuatIddengan format(Xy) X= huruf dan Y = angka
        $count = DB::table('aktivasi_history')->count() + 1;
        $id_generate = sprintf("%03d", $count);

        //membuat angka dengan format y
        $digitYear = Carbon::parse(now())->format('y');

        //membuat angka dengan format m
        $digitMonth = Carbon::parse(now())->format('m');

        //no aktivasi_history
        $no_aktivasi_history = $digitYear . '.' . $digitMonth . '.' . $id_generate;

        //set price selalu 3 jt
        $price = 3000000;

        // get date time now
        $date_time = Carbon::now();

        //check if already aktivasi and update 
        $countYear = aktivasi_history::where('id_member', $request->id_member)->count();
        if ($countYear <= 0) {
            // set expired date 1 year after date time now
            $expired_date = Carbon::parse($date_time)->addYear();
        } else {
            $expired_date = Carbon::parse($date_time)->addYears($countYear + 1);
        }

        $aktivasi_history = aktivasi_history::create([
            'no_aktivasi_history' => $no_aktivasi_history,
            'id_member' => $request->id_member,
            'id_pegawai' => $request->id_pegawai,
            'date_time' => $date_time,
            'price' => $price,
            'expired_date' => $expired_date,
        ]);

        if ($aktivasi_history) {

            $member = member::find($request->id_member);
            $member->update([
                'expired_date_membership' => $expired_date,
                'status_membership' => 1,
            ]);

            member_activity::create([
                'id_member' => $aktivasi_history->id_member,
                'date_time' => $aktivasi_history->date_time,
                'name_activity' => 'Aktivasi Membership',
                'no_activity' => $aktivasi_history->no_aktivasi_history,
                'price_activity' => 'Rp.' . $aktivasi_history->price,
            ]);

            //pembuatan report income
            $tahun = Carbon::parse($aktivasi_history->date_time)->format('Y');
            $bulan = Carbon::parse($aktivasi_history->date_time)->format('F');
            //cari dulu apakah report income ada ga di tahun dan bulan itu
            $report_income = report_income::where('tahun', $tahun)->where('bulan', $bulan)->first();
            if ($report_income) {
                $report_income->update([
                    'aktivasi' => $report_income->aktivasi + $aktivasi_history->price,
                    // 'deposit' => $aktivasi_history->no_aktivasi_history,
                    'total' => $report_income->total + $aktivasi_history->price,
                ]);
            } else {
                report_income::create([
                    'tahun' => $tahun,
                    'bulan' => $bulan,
                    'aktivasi' => $aktivasi_history->price,
                    // 'deposit' => $aktivasi_history->no_aktivasi_history,
                    'total' => $aktivasi_history->price,
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'aktivasi_history ditambah',
                'data'    => $aktivasi_history,
                'data member'    => $member,
            ], 201);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'aktivasi_history gagal dibuat',
                'data'    => $aktivasi_history
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
        //find aktivasi_history by ID
        $aktivasi_history = aktivasi_history::find($id);

        //make response JSON
        return response()->json([
            'success' => true,
            'message' => 'Detail Data aktivasi_history',
            'data'    => $aktivasi_history
        ], 200);
    }

    //////////////sebuah history / recipt ga bisa di update untuk sementara
    // /**
    //  * Update the specified resource in storage.
    //  *
    //  * @param  \Illuminate\Http\Request  $request
    //  * @param  int  $id
    //  * @return \Illuminate\Http\Response
    //  */
    // public function update(Request $request, $id)
    // {
    //     $aktivasi_history = aktivasi_history::find($id);
    //     if (!$aktivasi_history) {
    //         //data aktivasi_history not found
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'aktivasi_history Not Found',
    //         ], 404);
    //     }
    //     //validate form
    //     $validator = Validator::make($request->all(), [
    //         'id_member' => 'required',
    //         'id_pegawai' => 'required',
    //         'date_time' => 'required',
    //         'expired_date' => 'required'
    //     ]);

    //     //response error validation
    //     if ($validator->fails()) {
    //         return response()->json($validator->errors(), 422);
    //     }

    //     //update aktivasi_history with new image
    //     $aktivasi_history->update([
    //         'id_member' => $request->id_member,
    //         'id_pegawai' => $request->id_pegawai,
    //         'date_time' => $request->date_time,
    //         'expired_date' => $request->date_time,
    //     ]);

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'aktivasi_history Updated',
    //         'data'    => $aktivasi_history
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
        $aktivasi_history = aktivasi_history::find($id);

        if ($aktivasi_history) {
            //delete aktivasi_history
            $aktivasi_history->delete();

            return response()->json([
                'success' => true,
                'message' => 'aktivasi_history Deleted',
            ], 200);
        }


        //data aktivasi_history not found
        return response()->json([
            'success' => false,
            'message' => 'aktivasi_history Not Found',
        ], 404);
    }

    /**
     * Display a listing of the resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function generate_aktivasi_historyCard($id)
    {
        $aktivasi_history = aktivasi_history::find($id);
        if (!$aktivasi_history) {
            //data aktivasi_history not found
            return response()->json([
                'success' => false,
                'message' => 'aktivasi_history Not Found',
            ], 404);
        }

        $member = member::find($aktivasi_history->id_member);
        $pegawai = pegawai::find($aktivasi_history->id_pegawai);
        $dateTime = Carbon::now()->format('Y-m-d H:i:s');

        $data = [
            'aktivasi_history' => $aktivasi_history,
            'member' => $member,
            'pegawai' => $pegawai,
            'dateTime' => $dateTime,
        ];

        $pdf = Pdf::loadview('aktivasi_historyCard', $data);

        return $pdf->output();
    }
}
