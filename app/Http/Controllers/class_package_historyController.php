<?php

namespace App\Http\Controllers;

use App\Models\class_detail;
use App\Models\class_package_history;
use App\Models\deposit_package;
use App\Models\instruktur;
use App\Models\instruktur_presensi;
use App\Models\member;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class class_package_historyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $class_package_history = class_package_history::with(['class_booking.member', 'class_booking.class_running.jadwal_umum.class_detail', 'class_booking.class_running.instruktur'])->get();

        return response()->json([
            'success' => true,
            'message' => 'List Data class_package_history',
            'data'    => $class_package_history
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //find class_package_history by ID
        $class_package_history = class_package_history::find($id);

        //make response JSON
        return response()->json([
            'success' => true,
            'message' => 'Detail Data class_package_history',
            'data'    => $class_package_history
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
    //     $class_package_history = class_package_history::find($id);
    //     if (!$class_package_history) {
    //         //data class_package_history not found
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'class_package_history Not Found',
    //         ], 404);
    //     }
    //     //validate form
    //     $validator = Validator::make($request->all(), [
    //         'no_class_package_history' => 'required',
    //         'id_class_booking' => 'required',
    //         'date_time' => 'required',
    //         'sisa_deposit_kelas' => 'required',
    //         'expired_date' => 'required',
    //     ]);

    //     //response error validation
    //     if ($validator->fails()) {
    //         return response()->json($validator->errors(), 422);
    //     }

    //     //update class_package_history with new image
    //     $class_package_history->update([
    //         'no_class_package_history' => $request->no_class_package_history,
    //         'id_class_booking' => $request->id_class_booking,
    //         'date_time' => $request->date_time,
    //         'sisa_deposit_kelas' => $request->sisa_deposit_kelas,,
    //         'expired_date' => $request->expired_date,
    //     ]);

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'class_package_history Updated',
    //         'data'    => $class_package_history
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
        $class_package_history = class_package_history::find($id);

        if ($class_package_history) {
            //delete class_package_history
            $class_package_history->delete();

            return response()->json([
                'success' => true,
                'message' => 'class_package_history Deleted',
            ], 200);
        }


        //data class_package_history not found
        return response()->json([
            'success' => false,
            'message' => 'class_package_history Not Found',
        ], 404);
    }

    /**
     * Display a listing of the resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function generate_class_package_historyCard($id)
    {
        $class_package_history = class_package_history::with(['class_booking.member', 'class_booking.class_running.jadwal_umum.class_detail', 'class_booking.class_running.instruktur'])->find($id);
        if (!$class_package_history) {
            //data class_package_history not found
            return response()->json([
                'success' => false,
                'message' => 'class_package_history Not Found',
            ], 404);
        }

        //no member , nama member, sisa deposit uang
        $member = member::find($class_package_history->class_booking->member->id);
        //nama kelas
        $class_detail = class_detail::find($class_package_history->class_booking->class_running->jadwal_umum->class_detail->id);
        //nama instruktur
        $instruktur = instruktur::find($class_package_history->class_booking->class_running->instruktur->id);
        // sisa deposit, berlaku sampai
        $deposit_package = deposit_package::where('id_member', $member->id)->where('id_class_detail', $class_detail->id)->with(['class_detail', 'member'])->orderBy('created_at', 'desc')->first();
        $data = [
            'class_package_history' => $class_package_history,
            'member' => $member,
            'class_detail' => $class_detail,
            'instruktur' => $instruktur,
            'deposit_package' => $deposit_package,
        ];

        $pdf = Pdf::loadview('class_package_historyCard', $data);

        return $pdf->output();
    }
}
