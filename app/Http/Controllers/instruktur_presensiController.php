<?php

namespace App\Http\Controllers;

use App\Models\instruktur;
use App\Models\instruktur_activity;
use App\Models\instruktur_presensi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class instruktur_presensiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $instruktur_presensi = instruktur_presensi::with(['instruktur', 'class_running.jadwal_umum.class_detail'])->get();

        // // Get the start and end dates for the current week
        // $WeekStart = Carbon::now()->copy()->startOfWeek(Carbon::SUNDAY);
        // $WeekEnd = Carbon::now()->copy()->endOfWeek(Carbon::SATURDAY);

        //date now
        $dateNow = Carbon::now()->format('Y-m-d');

        $temp = [];
        //cek kondisi dimana antara week sekarang aja
        foreach ($instruktur_presensi as $value) {
            $classDate = Carbon::parse($value->class_running->date)->format('Y-m-d');
            // if ($classDate->isBetween($WeekStart, $WeekEnd)) {
            //     array_push($temp, $value);
            // }
            if ($classDate == $dateNow) {
                array_push($temp, $value);
            }
        }

        // Sort $temp array by date in ascending order
        usort($temp, function ($a, $b) {
            return strtotime($a->class_running->date) - strtotime($b->class_running->date);
        });

        return response()->json([
            'success' => true,
            'message' => 'List Data instruktur_presensi for this week',
            'data'    => $temp,
        ], 200);
    }

    /**
     * update when class is not available.
     *
     */
    public function updateClassEndClass(Request $request, $id)
    {

        $instruktur_presensi = instruktur_presensi::with(['instruktur', 'class_running.jadwal_umum.class_detail'])->find($id);
        if (!$instruktur_presensi) {
            //data instruktur_presensi not found
            return response()->json([
                'success' => false,
                'message' => 'instruktur_presensi Not Found',
            ], 404);
        }

        //validate form
        $validator = Validator::make($request->all(), [
            'end_class' => 'required',
        ]);

        //response error validation
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if ($instruktur_presensi->end_class != null) {
            return response()->json([
                'success' => false,
                'message' => 'end class already updated',
            ], 409);
        }

        if ($instruktur_presensi->class_running->status == "libur") {
            return response()->json([
                'success' => false,
                'message' => 'class libur! cant update end class',
            ], 409);
        }

        if ($request->end_class < Carbon::parse($instruktur_presensi->class_running->start_class)->format('H:i')) {
            return response()->json([
                'success' => false,
                'message' => 'end class must be greater than start class',
            ], 409);
        }

        $date_time = Carbon::now();

        $instruktur_presensi->update([
            'end_class' => $request->end_class,
            'status_class' => 1,
            'date_time' => $date_time,
        ]);

        $instruktur_presensi->class_running->update([
            'end_class' => $request->end_class,
        ]);

        if ($instruktur_presensi) {

            $dateTimeNow = Carbon::now();

            instruktur_activity::create([
                'id_instruktur' => $instruktur_presensi->id_instruktur,
                'date_time' => $dateTimeNow,
                'name_activity' => 'Instruktur Presensi End Class',
                'description_activity' => 'Presensi End Class di kelas ' . $instruktur_presensi->class_running->jadwal_umum->class_detail->name . ' - ' . $instruktur_presensi->class_running->day_name . ' - ' . $instruktur_presensi->class_running->start_class . ', Tgl Class : ' . $instruktur_presensi->class_running->date,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'update end class successfully',
                'data'    => $instruktur_presensi,
            ], 201);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'instruktur_presensi Failed to update end_class scheduled',
                'data'    => $instruktur_presensi,
            ], 409);
        }
    }

    /**
     * update when class is not available.
     *
     */
    public function updateClassStartClass(Request $request, $id)
    {

        $instruktur_presensi = instruktur_presensi::with(['instruktur', 'class_running.jadwal_umum.class_detail'])->find($id);
        if (!$instruktur_presensi) {
            //data instruktur_presensi not found
            return response()->json([
                'success' => false,
                'message' => 'instruktur_presensi Not Found',
            ], 404);
        }

        //validate form
        $validator = Validator::make($request->all(), [
            'start_class' => 'required',
        ]);

        //response error validation
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if ($instruktur_presensi->start_class != null) {
            return response()->json([
                'success' => false,
                'message' => 'start class already updated',
            ], 409);
        }

        if ($instruktur_presensi->class_running->status == "libur") {
            return response()->json([
                'success' => false,
                'message' => 'class libur! cant update start class',
            ], 409);
        }

        if ($request->start_class < Carbon::parse($instruktur_presensi->class_running->start_class)->format('H:i')) {
            return response()->json([
                'success' => false,
                'message' => 'your input must be greater than start class already scheduled',
            ], 409);
        } else if ($request->start_class > Carbon::parse($instruktur_presensi->class_running->end_class)->format('H:i')) {
            return response()->json([
                'success' => false,
                'message' => 'start class must be less than end class || YOU SO LATE!!',
            ], 409);
        }

        $date_time = Carbon::now();

        $instruktur_presensi->update([
            'start_class' => $request->start_class,
            'status_class' => 1,
            'date_time' => $date_time,
        ]);

        $instruktur_presensi->class_running->update([
            'start_class' => $request->start_class,
        ]);

        $start_class_real = Carbon::parse($instruktur_presensi->class_running->jadwal_umum->start_class);
        $start_class = Carbon::parse($instruktur_presensi->start_class);
        $total_late = $start_class->diffInSeconds($start_class_real);

        $instruktur = instruktur::find($instruktur_presensi->id_instruktur);
        $total_late_old = $instruktur->total_late;

        if ($instruktur) {
            $instruktur->update([
                'total_late' => $total_late_old + $total_late,
            ]);
        }

        if ($instruktur_presensi) {

            $dateTimeNow = Carbon::now();

            instruktur_activity::create([
                'id_instruktur' => $instruktur_presensi->id_instruktur,
                'date_time' => $dateTimeNow,
                'name_activity' => 'Instruktur Presensi Start Class',
                'description_activity' => 'Presensi Start Class di kelas ' . $instruktur_presensi->class_running->jadwal_umum->class_detail->name . ' - ' . $instruktur_presensi->class_running->day_name . ' - ' . $instruktur_presensi->class_running->start_class . ', Tgl Class : ' . $instruktur_presensi->class_running->date,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'update start class successfully',
                'data'    => $instruktur_presensi,
            ], 201);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update start_class scheduled',
                'data'    => $instruktur_presensi,
            ], 409);
        }
    }

    /**
     * update presensi status
     *
     */
    public function updateClassStatus(Request $request, $id)
    {

        $instruktur_presensi = instruktur_presensi::with(['instruktur', 'class_running.jadwal_umum.class_detail'])->find($id);
        if (!$instruktur_presensi) {
            //data instruktur_presensi not found
            return response()->json([
                'success' => false,
                'message' => 'instruktur_presensi Not Found',
            ], 404);
        }

        //validate form
        $validator = Validator::make($request->all(), [
            'status_class' => 'required|boolean',
        ]);

        //response error validation
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $date_time = Carbon::now();
        $instruktur_presensi->update([
            'status_class' => $request->status_class,
            'date_time' => $date_time,
        ]);

        if ($instruktur_presensi) {

            return response()->json([
                'success' => true,
                'message' => 'instruktur_presensi scheduled update status_class successfully',
                'data'    => $instruktur_presensi,
            ], 201);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'instruktur_presensi Failed to update status_class scheduled',
                'data'    => $instruktur_presensi,
            ], 409);
        }
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
    //         'status_class' => 'required|boolean',
    //         'start_class' => 'required',
    //         'end_class' => 'required|after:start_class',
    //         'date_time' => 'required',
    //     ]);

    //     //response error validation
    //     if ($validator->fails()) {
    //         return response()->json($validator->errors(), 422);
    //     }

    //     $instruktur_presensi = instruktur_presensi::create([
    //         'id_instruktur' => $request->id_instruktur,
    //         'id_class_running' => $request->id_class_running,
    //         'status_class' => $request->status_class,
    //         'start_class' => $request->start_class,
    //         'end_class' => $request->end_class,
    //         'date_time' => $request->date,
    //     ]);

    //     if ($instruktur_presensi) {

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'instruktur_presensi Created',
    //             'data'    => $instruktur_presensi
    //         ], 201);
    //     } else {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'instruktur_presensi Failed to Save',
    //             'data'    => $instruktur_presensi
    //         ], 409);
    //     }
    // }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //find instruktur_presensi by ID
        $instruktur_presensi = instruktur_presensi::find($id);

        //make response JSON
        return response()->json([
            'success' => true,
            'message' => 'Detail Data instruktur_presensi',
            'data'    => $instruktur_presensi
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
        $instruktur_presensi = instruktur_presensi::find($id);
        if (!$instruktur_presensi) {
            //data instruktur_presensi not found
            return response()->json([
                'success' => false,
                'message' => 'instruktur_presensi Not Found',
            ], 404);
        }
        //validate form
        $validator = Validator::make($request->all(), [
            'id_instruktur' => 'required',
            'status_class' => 'required|boolean',
            'start_class' => 'required',
            'end_class' => 'required|after:start_class',
            'date_time' => 'required|date',
        ]);

        //response error validation
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //update instruktur_presensi with new image
        $instruktur_presensi->update([
            'id_instruktur' => $request->id_instruktur,
            'id_class_running' => $request->id_class_running,
            'status_class' => $request->status_class,
            'start_class' => $request->start_class,
            'end_class' => $request->end_class,
            'date_time' => $request->date,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'instruktur_presensi Updated',
            'data'    => $instruktur_presensi
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
        $instruktur_presensi = instruktur_presensi::find($id);

        if ($instruktur_presensi) {
            //delete instruktur_presensi
            $instruktur_presensi->delete();

            return response()->json([
                'success' => true,
                'message' => 'instruktur_presensi Deleted',
            ], 200);
        }


        //data instruktur_presensi not found
        return response()->json([
            'success' => false,
            'message' => 'instruktur_presensi Not Found',
        ], 404);
    }
}
