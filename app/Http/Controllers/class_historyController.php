<?php

namespace App\Http\Controllers;

use App\Models\class_detail;
use App\Models\class_history;
use App\Models\instruktur;
use App\Models\instruktur_presensi;
use App\Models\member;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class class_historyController extends Controller
{
    /**
     * Display a listing of the resource where status is hadir or 1.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $class_history = class_history::with(['class_booking.member', 'class_booking.class_running.jadwal_umum.class_detail', 'class_booking.class_running.instruktur'])->get();

        return response()->json([
            'success' => true,
            'message' => 'List Data class_history',
            'data'    => $class_history
        ], 200);
    }

    // /**
    //  * Store a newly created resource in storage.
    //  *
    //  * @param  \Illuminate\Http\Request  $request
    //  * @return \Illuminate\Http\Response
    //  */
    // public function store(Request $request)
    // {
    //     //Validasi Formulir
    //     $validator = Validator::make($request->all(), [
    //         'status' => 'required',
    //         'id_class_booking' => 'required',
    //         'date_time' => 'required',
    //         'sisa_deposit' => 'required',
    //     ]);

    //     //response error validation
    //     if ($validator->fails()) {
    //         return response()->json($validator->errors(), 422);
    //     }

    //     $class_history = class_history::create([
    //         'no_class_history' => $request->no_class_history,
    //         'id_class_booking' => $request->id_class_booking,
    //         'date_time' => $request->date_time,
    //         'sisa_deposit' => $request->sisa_deposit,
    //     ]);

    //     if ($class_history) {

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'class_history Created',
    //             'data'    => $class_history
    //         ], 201);
    //     } else {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'class_history Failed to Save',
    //             'data'    => $class_history
    //         ], 409);
    //     }
    // }

    // /**
    //  * Display the specified resource.
    //  *
    //  * @param  int  $id
    //  * @return \Illuminate\Http\Response
    //  */
    // public function show($id)
    // {
    //     //find class_history by ID
    //     $class_history = class_history::find($id);

    //     //make response JSON
    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Detail Data class_history',
    //         'data'    => $class_history
    //     ], 200);
    // }

    // /**
    //  * Update the specified resource in storage.
    //  *
    //  * @param  \Illuminate\Http\Request  $request
    //  * @param  int  $id
    //  * @return \Illuminate\Http\Response
    //  */
    // public function updateStatus(Request $request, $id)
    // {
    //     $class_history = class_history::with(['class_booking.member', 'class_booking.class_running.jadwal_umum.class_detail', 'class_booking.class_running.instruktur'])->find($id);
    //     if (!$class_history) {
    //         //data class_history not found
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'class_history Not Found',
    //         ], 404);
    //     }
    //     //validate form
    //     $validator = Validator::make($request->all(), [
    //         'status' => 'required|boolean',
    //     ]);

    //     //response error validation
    //     if ($validator->fails()) {
    //         return response()->json($validator->errors(), 422);
    //     }

    //     $instruktur_presensi = instruktur_presensi::where('id_class_running', $class_history->class_booking->class_running->id)->where('id_instruktur', $class_history->class_booking->class_running->instruktur->id)->with(['instruktur', 'class_running'])->first();

    //     if ($instruktur_presensi->status_class == 0) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Instruktur belum presensi',
    //         ], 422);
    //     }

    //     $date_time = Carbon::now();

    //     $member = Member::find($class_history->class_booking->member->id);
    //     $jumlahUangSekarang = $member->jumlah_deposit_reguler;
    //     $uangBerkurang = $class_history->class_booking->class_running->jadwal_umum->class_detail->harga;
    //     $jumlahUangSekarangBanget = $jumlahUangSekarang - $uangBerkurang;

    //     $member->update([
    //         'jumlah_deposit_reguler' => $jumlahUangSekarangBanget,
    //     ]);


    //     $class_history->update([
    //         'status' => $request->status,
    //         'date_time' => $date_time,
    //         'sisa_deposit' => $jumlahUangSekarangBanget
    //     ]);


    //     return response()->json([
    //         'success' => true,
    //         'message' => 'class_history Updated and money decreased',
    //         'data'    => $class_history
    //     ], 200);
    // }

    // /**
    //  * Remove the specified resource from storage.
    //  *
    //  * @param  int  $id
    //  * @return \Illuminate\Http\Response
    //  */
    // public function destroy($id)
    // {
    //     $class_history = class_history::with(['class_booking.member', 'class_booking.class_running.jadwal_umum.class_detail', 'class_booking.class_running.instruktur'])->find($id);

    //     if ($class_history) {
    //         //delete class_history
    //         $class_history->delete();

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'class_history Deleted',
    //         ], 200);
    //     }


    //     //data class_history not found
    //     return response()->json([
    //         'success' => false,
    //         'message' => 'class_history Not Found',
    //     ], 404);
    // }

    /**
     * Display a listing of the resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function generate_class_historyCard($id)
    {
        $class_history = class_history::with(['class_booking.member', 'class_booking.class_running.jadwal_umum.class_detail', 'class_booking.class_running.instruktur'])->find($id);
        if (!$class_history) {
            //data class_history not found
            return response()->json([
                'success' => false,
                'message' => 'class_history Not Found',
            ], 404);
        }

        //no member , nama member, sisa deposit uang
        $member = member::find($class_history->class_booking->member->id);
        //nama kelas, harga kelas
        $class_detail = class_detail::find($class_history->class_booking->class_running->jadwal_umum->class_detail->id);
        //nama instruktur
        $instruktur = instruktur::find($class_history->class_booking->class_running->instruktur->id);
        $data = [
            'class_history' => $class_history,
            'member' => $member,
            'class_detail' => $class_detail,
            'instruktur' => $instruktur,
        ];

        $pdf = Pdf::loadview('class_historyCard', $data);

        return $pdf->output();
    }
}
