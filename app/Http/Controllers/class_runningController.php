<?php

namespace App\Http\Controllers;

use App\Models\class_running;
use App\Models\jadwal_umum;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class class_runningController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $class_running = class_running::with(['jadwal_umum.instruktur', 'jadwal_umum.class_detail', 'instruktur'])->get();

        return response()->json([
            'success' => true,
            'message' => 'List Data class_running',
            'data'    => $class_running
        ], 200);
    }


    public function indexClassRunningByIdInstruktur($id_instruktur)
    {
        $class_running = class_running::where('id_instruktur', $id_instruktur)->with(['jadwal_umum.instruktur', 'jadwal_umum.class_detail', 'instruktur'])->get();

        return response()->json([
            'success' => true,
            'message' => 'List Data class_running',
            'data'    => $class_running
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
    //         'id_instruktur' => 'required',
    //         'id_class_detail' => 'required',
    //         'start_class' => 'required',
    //         'date' => 'required',
    //     ]);

    //     //response error validation
    //     if ($validator->fails()) {
    //         return response()->json($validator->errors(), 422);
    //     }
    //     //menambahkan 1 jam setelah start class karena emang sejam setelah start class pasti selesai
    //     $end_class = Carbon::parse($request->start_class)->addHour();

    //     //mengeset kapasitas karena max emang 10 saja (nanti kalo ada ikut berarti --)
    //     $capacity = 10;

    //     //status
    //     $status = '';

    //     //nama hari dari tanggal yang di pilih
    //     $day_name = Carbon::parse($request->date)->format('l');

    //     //cek apakah jadwal dan instuktur tersebut sudah ada atau belum
    //     //jam harus dalam kontek H:i:s dibuatin string dulu
    //     $start_class = Carbon::parse($request->start_class)->format('H:i:s');
    //     $class_running_temp = class_running::all();
    //     foreach ($class_running_temp as $class_running_temp) {
    //         //intruktur = class = date = start_class 
    //         if ($class_running_temp['id_instruktur'] == $request->id_instruktur && $class_running_temp['id_class_detail'] == $request->id_class_detail && $class_running_temp['date'] == $request->date  && $class_running_temp['start_class'] == $start_class) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'jadwal yang anda input sudah ada',
    //             ], 409);
    //         }
    //         // instuktur = date = start class
    //         else if ($class_running_temp['id_instruktur'] == $request->id_instruktur  && $class_running_temp['date'] == $request->date  && $class_running_temp['start_class'] == $start_class) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'instruktur tersebut sudah ada di jadwal yang anda input',
    //             ], 409);
    //         }
    //     }

    //     $class_running = class_running::firstOrCreate([
    //         'id_instruktur' => $request->id_instruktur,
    //         'id_class_detail' => $request->id_class_detail,
    //         'start_class' => $start_class,
    //         'end_class' => $end_class,
    //         'capacity' => $capacity,
    //         'date' => $request->date,
    //         'day_name' => $day_name,
    //         'status' => $status,
    //     ]);

    //     if ($class_running) {

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'class_running Created',
    //             'data'    => $class_running,
    //         ], 201);
    //     } else {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'class_running Failed to Save',
    //             'data'    => $class_running
    //         ], 409);
    //     }
    // }

    /**
     * generate a week schedule of class_running store data.
     *
     */
    public function generateDateAWeek()
    {
        $class_running_list = class_running::all();
        $jadwal_umum = jadwal_umum::all();

        //kalo belum ada sama sekali
        if ($class_running_list->isEmpty() || $class_running_list->count() != $jadwal_umum->count()) {
            //delete all dulu
            DB::table('class_running')->delete();

            foreach ($jadwal_umum as $jadwal_umum) {

                //get tanggal sekarang dan cari tanggal di minggu ini
                $now = Carbon::now();
                $weekStartDate = $now->copy()->startOfWeek();
                $weekEndDate = $now->copy()->endOfWeek();

                if ($jadwal_umum['day_name'] == 'Monday') {
                    $date = $weekStartDate;
                } else if ($jadwal_umum['day_name'] == 'Tuesday') {
                    $date = $weekStartDate->addDays(1);
                } else if ($jadwal_umum['day_name'] == 'Wednesday') {
                    $date = $weekStartDate->addDays(2);
                } else if ($jadwal_umum['day_name'] == 'Thursday') {
                    $date = $weekStartDate->addDays(3);
                } else if ($jadwal_umum['day_name'] == 'Friday') {
                    $date = $weekStartDate->addDays(4);
                } else if ($jadwal_umum['day_name'] == 'Saturday') {
                    $date = $weekStartDate->addDays(5);
                } else if ($jadwal_umum['day_name'] == 'Sunday') {
                    $date = $weekStartDate->addDays(6);
                }


                $date_fix = Carbon::parse($date)->format('Y-m-d');
                $status = '';
                $day_name = Carbon::parse($date_fix)->format('l');
                // dicek lagi pake date biasa apa date fix
                $class_running = class_running::firstOrCreate([
                    'id_jadwal_umum' => $jadwal_umum['id'],
                    'id_instruktur' => $jadwal_umum['id_instruktur'],
                    'capacity' => $jadwal_umum['capacity'],
                    'date' => $date_fix,
                    'day_name' => $day_name,
                    'status' => $status,
                ]);
            }

            $class_running = class_running::all();

            if ($class_running) {

                return response()->json([
                    'success' => true,
                    'message' => 'class_running scheduled generated successfully',
                    'data'    => $class_running,
                ], 201);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'class_running Failed to generate scheduled',
                    'data'    => $class_running,
                ], 409);
            }
        } else {
            foreach ($class_running_list as $class_running_list) {
                $date = $class_running_list->date;
                $day_name = Carbon::parse($date)->format('l');
                $class_running_list->update([
                    'date' => Carbon::parse($date)->addDays(7),
                    'day_name' => $day_name
                ]);
            }
            $class_running_list = class_running::all();
            if ($class_running_list) {

                return response()->json([
                    'success' => true,
                    'message' => 'class_running scheduled generated successfully',
                    'data'    => $class_running_list,
                ], 201);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'class_running Failed to generate scheduled',
                    'data'    => $class_running_list,
                ], 409);
            }
        }
    }

    /**
     * update when class is not available.
     *
     */
    public function updateClassNotAvailable(Request $request, $id)
    {

        $class_running = class_running::find($id);
        if (!$class_running) {
            //data class_running not found
            return response()->json([
                'success' => false,
                'message' => 'class_running Not Found',
            ], 404);
        }

        //validate form
        $validator = Validator::make($request->all(), [
            'status' => 'required',
        ]);

        //response error validation
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $class_running->update([
            'status' => $request->status,
        ]);

        if ($class_running) {

            return response()->json([
                'success' => true,
                'message' => 'class_running scheduled update status successfully',
                'data'    => $class_running,
            ], 201);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'class_running Failed to update status scheduled',
                'data'    => $class_running,
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
        //find class_running by ID
        $class_running = class_running::find($id);

        //make response JSON
        return response()->json([
            'success' => true,
            'message' => 'Detail Data class_running',
            'data'    => $class_running
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
    //     $class_running = class_running::find($id);
    //     if (!$class_running) {
    //         //data class_running not found
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'class_running Not Found',
    //         ], 404);
    //     }
    //     //validate form
    //     $validator = Validator::make($request->all(), [
    //         'id_instruktur' => 'required',
    //         'id_class_detail' => 'required',
    //         'start_class' => 'required',
    //         'capacity' => 'required',
    //         'date' => 'required',
    //         'status' => 'required',
    //     ]);

    //     //response error validation
    //     if ($validator->fails()) {
    //         return response()->json($validator->errors(), 422);
    //     }

    //     //menambahkan 1 jam setelah start class karena emang sejam setelah start class pasti selesai
    //     $end_class = Carbon::parse($request->start_class)->addHour();

    //     //nama hari dari tanggal yang dipilih
    //     $day_name = Carbon::parse($request->date)->format('l');

    //     //cek apakah jadwal dan instuktur tersebut sudah ada atau belum
    //     //jam harus dalam kontek H:i:s dibuatin string dulu
    //     $start_class = Carbon::parse($request->start_class)->format('H:i:s');
    //     $class_running_temp = class_running::all();
    //     foreach ($class_running_temp as $class_running_temp) {
    //         //intruktur = class = date = start_class 
    //         if ($class_running_temp['id_instruktur'] == $request->id_instruktur && $class_running_temp['id_class_detail'] == $request->id_class_detail && $class_running_temp['date'] == $request->date  && $class_running_temp['start_class'] == $start_class) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'jadwal yang anda input sudah ada',
    //             ], 409);
    //         }
    //         // instuktur = date = start class
    //         else if ($class_running_temp['id_instruktur'] == $request->id_instruktur  && $class_running_temp['date'] == $request->date  && $class_running_temp['start_class'] == $start_class) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'instruktur tersebut sudah ada di jadwal yang anda input',
    //             ], 409);
    //         }
    //     }

    //     //update class_running with new image
    //     $class_running->update([
    //         'id_instruktur' => $request->id_instruktur,
    //         'id_class_detail' => $request->id_class_detail,
    //         'start_class' => $start_class,
    //         'end_class' => $end_class,
    //         'capacity' => $request->capacity,
    //         'date' => $request->date,
    //         'day_name' => $day_name,
    //         'status' => $request->status,
    //     ]);

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'class_running Updated',
    //         'data'    => $class_running
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
        $class_running = class_running::find($id);

        if ($class_running) {
            //delete class_running
            $class_running->delete();

            return response()->json([
                'success' => true,
                'message' => 'class_running Deleted',
            ], 200);
        }


        //data class_running not found
        return response()->json([
            'success' => false,
            'message' => 'class_running Not Found',
        ], 404);
    }
}