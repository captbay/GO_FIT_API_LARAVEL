<?php

namespace App\Http\Controllers;

use App\Models\aktivasi_history;
use App\Models\member;
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
        $aktivasi_history = aktivasi_history::first()->get();

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
            return response()->json($validator->errors(), 400);
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

        // set expired date 1 year after date time now
        $expired_date = Carbon::parse($date_time)->addYear();

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

            return response()->json([
                'success' => true,
                'message' => 'aktivasi_history ditambah',
                'data'    => $aktivasi_history,
                'data member'    => $member
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
    //         return response()->json($validator->errors(), 400);
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
        $pegawai = member::find($aktivasi_history->id_pegawai);

        $data = [
            'aktivasi_history' => $aktivasi_history,
            'member' => $member,
            'pegawai' => $pegawai,
        ];

        $pdf = Pdf::loadview('aktivasi_historyCard', $data);

        return $pdf->download('aktivasi_history_Card_' . $member->name . '.pdf');
    }
}