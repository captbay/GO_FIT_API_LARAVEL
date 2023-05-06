<?php

namespace App\Http\Controllers;

use App\Models\class_detail;
use App\Models\deposit_package;
use App\Models\deposit_package_history;
use App\Models\member;
use App\Models\promo_class;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        $deposit_package_history = deposit_package_history::with(['class_detail', 'promo_class', 'member', 'pegawai'])->get();

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
        /// user pilih kelas dan dicek apakah kelas tersebut ada promo atau tidak
        //Validasi Formulir
        $validator = Validator::make($request->all(), [
            'id_class_detail' => 'required', //show class and price
            'id_member' => 'required', //put id memmber
            'id_pegawai' => 'required', // put id pegawai
            'package_amount' => 'required|integer', // put berapa class dibeli
        ]);

        //response error validation
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //class_detail find
        $class_detail = class_detail::find($request->id_class_detail);
        if (!$class_detail) {
            //data member not found
            return response()->json([
                'success' => false,
                'message' => 'class_detail Not Found',
            ], 404);
        }

        //class detail find
        $member = member::find($request->id_member);
        if (!$member) {
            //data member not found
            return response()->json([
                'success' => false,
                'message' => 'member Not Found',
            ], 404);
        }

        //membuatIddengan format(Xy) X= huruf dan Y = angka
        $count = DB::table('deposit_package_history')->count() + 1;
        $id_generate = sprintf("%03d", $count);
        //membuat angka dengan format y
        $digitYear = Carbon::parse(now())->format('y');
        //membuat angka dengan format m
        $digitMonth = Carbon::parse(now())->format('m');
        //no aktivasi_history
        $no_deposit_package_history = $digitYear . '.' . $digitMonth . '.' . $id_generate;

        // get date time now
        $date_time = Carbon::now();

        //setdefault when no promo
        $id_promo_class = null;
        $jumlah_sesi = $request->package_amount;
        $package_amount = $request->package_amount;
        $bonus_sesi = 0;
        $expired_date = null;
        //check what promo gofit have
        $promo_class = promo_class::all();
        foreach ($promo_class as $promo_class) {
            if ($jumlah_sesi == $promo_class['jumlah_sesi']) {
                $id_promo_class = $promo_class['id'];
                $bonus_sesi = $promo_class['bonus_sesi'];
                $expired_date_time = Carbon::now()->addMonth($promo_class['durasi_aktif']);
                $expired_date = $expired_date_time->toDateString();
                //jumlah package amount kalo ada dapet promo
                $package_amount = $jumlah_sesi + $bonus_sesi;
            }
        }


        //total price base on promo class
        $total_price = $jumlah_sesi * $class_detail->price;

        $deposit_package_history = deposit_package_history::create([
            'no_deposit_package_history' => $no_deposit_package_history,
            'id_promo_class' => $id_promo_class,
            'id_class_detail' => $class_detail->id,
            'id_member' => $request->id_member,
            'id_pegawai' => $request->id_pegawai,
            'date_time' => $date_time,
            'total_price' => $total_price,
            'package_amount' => $package_amount,
            'expired_date' => $expired_date
        ]);

        if ($deposit_package_history) {
            //memasukan deposit ke dalam deposit member
            $deposit_package = deposit_package::create([
                'id_class_detail' => $deposit_package_history->id_class_detail,
                'id_member' => $deposit_package_history->id_member,
                'package_amount' => $deposit_package_history->package_amount,
                'expired_date' => $deposit_package_history->expired_date,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'deposit_package_history Created and add to deposit member successfully',
                'data recipt'    => $deposit_package_history,
                'data deposit member'    => $deposit_package
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

    // /**
    //  * Update the specified resource in storage.
    //  *
    //  * @param  \Illuminate\Http\Request  $request
    //  * @param  int  $id
    //  * @return \Illuminate\Http\Response
    //  */
    // public function update(Request $request, $id)
    // {
    //     $deposit_package_history = deposit_package_history::find($id);
    //     if (!$deposit_package_history) {
    //         //data deposit_package_history not found
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'deposit_package_history Not Found',
    //         ], 404);
    //     }
    //     //validate form
    //     $validator = Validator::make($request->all(), [
    //         'no_deposit_package_history' => 'required',
    //         'id_promo_class' => 'required',
    //         'id_member' => 'required',
    //         'id_pegawai' => 'required',
    //         'date_time' => 'required',
    //         'total_price' => 'required',
    //         'package_amount' => 'required',
    //         'expired_date' => 'required'
    //     ]);

    //     //response error validation
    //     if ($validator->fails()) {
    //         return response()->json($validator->errors(), 422);
    //     }

    //     //update deposit_package_history with new image
    //     $deposit_package_history->update([
    //         'no_deposit_package_history' => $request->no_deposit_package_history,
    //         'id_promo_class' => $request->id_promo_class,
    //         'id_member' => $request->id_member,
    //         'id_pegawai' => $request->id_pegawai,
    //         'date_time' => $request->date_time,
    //         'total_price' => $request->total_price,
    //         'package_amount' => $request->package_amount,
    //         'expired_date' => $request->expired_date
    //     ]);

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'deposit_package_history Updated',
    //         'data'    => $deposit_package_history
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

    /**
     * Display a listing of the resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function generate_deposit_package_historyCard($id)
    {
        $deposit_package_history = deposit_package_history::find($id);
        if (!$deposit_package_history) {
            //data deposit_package_history not found
            return response()->json([
                'success' => false,
                'message' => 'deposit_package_history Not Found',
            ], 404);
        }

        $class_detail = class_detail::find($deposit_package_history->id_class_detail);
        $member = member::find($deposit_package_history->id_member);
        $pegawai = member::find($deposit_package_history->id_pegawai);
        $promo_class = promo_class::find($deposit_package_history->id_promo_class);

        $data = [
            'deposit_package_history' => $deposit_package_history,
            'member' => $member,
            'pegawai' => $pegawai,
            'class_detail' => $class_detail,
            'promo_class' => $promo_class
        ];

        $pdf = Pdf::loadview('deposit_package_historyCard', $data);

        return $pdf->download('deposit_package_history_Card_' . $member->name . '.pdf');
    }
}