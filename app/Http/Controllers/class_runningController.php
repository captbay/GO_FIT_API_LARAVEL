<?php

namespace App\Http\Controllers;

use App\Models\class_running;
use App\Models\instruktur_presensi;
use App\Models\jadwal_umum;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Carbon\CarbonPeriod;
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
        // Get the start and end dates for the week now
        $WeekStart = Carbon::now()->copy()->startOfWeek(Carbon::MONDAY);
        $WeekEnd = Carbon::now()->copy()->endOfWeek(Carbon::SUNDAY);

        $class_running = class_running::where('date', '>=', $WeekStart)->where('date', '<=', $WeekEnd)->orderBy('date', 'ASC')->with(['jadwal_umum.instruktur', 'jadwal_umum.class_detail', 'instruktur'])->get();

        return response()->json([
            'success' => true,
            'message' => 'List Data class_running',
            'data'    => $class_running
        ], 200);
    }


    public function indexClassRunningByIdInstruktur($id_instruktur)
    {
        // Get the start and end dates for the week now
        $WeekStart = Carbon::now()->copy()->startOfWeek(Carbon::MONDAY);
        $WeekEnd = Carbon::now()->copy()->endOfWeek(Carbon::SUNDAY);

        $class_running = class_running::where('date', '>=', $WeekStart)->where('date', '<=', $WeekEnd)->where('id_instruktur', $id_instruktur)->orderBy('date', 'ASC')->with(['jadwal_umum.instruktur', 'jadwal_umum.class_detail', 'instruktur'])->get();

        // Get the IDs of class_running
        $classRunningIds = $class_running->pluck('id');

        //show instruktur_presensi base on id_instruktur and id_class_running
        $instruktur_presensi = instruktur_presensi::whereIn('id_class_running', $classRunningIds)
            ->where('id_instruktur', $id_instruktur)
            ->get();

        // Merge the data of $instruktur_presensi into $class_running
        $class_running = $class_running->map(function ($item) use ($instruktur_presensi) {
            $presensi = $instruktur_presensi->where('id_class_running', $item->id)->first();
            $item->presensi = $presensi;
            return $item;
        });

        return response()->json([
            'success' => true,
            'message' => 'List Data class_running',
            'data'    => $class_running
        ], 200);
    }

    // function untuk mengecek apakah class_running date sudah ada di tanggal minggu ini
    private function checkDateThisWeek()
    {
        $class_running_list = class_running::all();

        $nextWeekStart = Carbon::now()->copy()->startOfWeek(Carbon::MONDAY);
        $nextWeekEnd = Carbon::now()->copy()->endOfWeek(Carbon::SUNDAY);

        $nextWeek = CarbonPeriod::create($nextWeekStart, $nextWeekEnd);

        foreach ($class_running_list as $class_running) {
            foreach ($nextWeek as $nextWeekTemp) {
                if (Carbon::parse($nextWeekTemp)->isSameDay(Carbon::parse($class_running->date))) {
                    return true;
                }
            }
        }
        return false;
    }

    //function untuk mengecek apakah class_running sudah ada di tanggal minggu depan
    private function checkDateNextWeek()
    {
        $class_running_list = class_running::all();

        $nextWeekStart = Carbon::now()->copy()->startOfWeek(Carbon::MONDAY)->addWeek();
        $nextWeekEnd = Carbon::now()->copy()->endOfWeek(Carbon::SUNDAY)->addWeek();

        $nextWeek = CarbonPeriod::create($nextWeekStart, $nextWeekEnd);

        foreach ($class_running_list as $class_running) {
            foreach ($nextWeek as $nextWeekTemp) {
                if (Carbon::parse($nextWeekTemp)->isSameDay(Carbon::parse($class_running->date))) {
                    return true;
                }
            }
        }
        return false;
    }

    //function untuk mengecek apakah ada data jadwal_umum yang belum ada di class_running
    private function checkIsNotSameData()
    {
        $class_running_list = class_running::all();
        $jadwal_umum_list = jadwal_umum::all();

        // Mengecek apakah id jadwal_umum ada yang belum ada di class_running
        foreach ($jadwal_umum_list as $jadwal_umum_item) {
            $found = false;
            foreach ($class_running_list as $class_running) {
                if ($jadwal_umum_item->id == $class_running->id_jadwal_umum) {
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                return true;
            }
        }
        return false;
    }

    /**
     * generate a week schedule of class_running store data.
     *
     */
    public function generateDateAWeek()
    {
        $class_running_list = class_running::all();
        $jadwal_umum = jadwal_umum::all();

        if ($this->checkIsNotSameData() == true) {
            foreach ($jadwal_umum as $jadwal_umum_item) {
                $found = false;
                foreach ($class_running_list as $class_running) {
                    if ($jadwal_umum_item->id == $class_running->id_jadwal_umum) {
                        $found = true;
                        break;
                    }
                }
                if (!$found) {
                    //get tanggal sekarang dan cari tanggal di minggu ini
                    $now = Carbon::now();
                    $weekStartDate = $now->copy()->startOfWeek(Carbon::MONDAY);

                    if ($jadwal_umum_item['day_name'] == 'Monday') {
                        $date = $weekStartDate;
                    } else if ($jadwal_umum_item['day_name'] == 'Tuesday') {
                        $date = $weekStartDate->addDays(1);
                    } else if ($jadwal_umum_item['day_name'] == 'Wednesday') {
                        $date = $weekStartDate->addDays(2);
                    } else if ($jadwal_umum_item['day_name'] == 'Thursday') {
                        $date = $weekStartDate->addDays(3);
                    } else if ($jadwal_umum_item['day_name'] == 'Friday') {
                        $date = $weekStartDate->addDays(4);
                    } else if ($jadwal_umum_item['day_name'] == 'Saturday') {
                        $date = $weekStartDate->addDays(5);
                    } else if ($jadwal_umum_item['day_name'] == 'Sunday') {
                        $date = $weekStartDate->addDays(6);
                    }

                    $date_fix = Carbon::parse($date)->format('Y-m-d');
                    $status = '';
                    $day_name = Carbon::parse($date_fix)->format('l');
                    // dicek lagi pake date biasa apa date fix
                    $class_running = class_running::create([
                        'id_jadwal_umum' => $jadwal_umum_item['id'],
                        'id_instruktur' => $jadwal_umum_item['id_instruktur'],
                        'capacity' => $jadwal_umum_item['capacity'],
                        'start_class' => $jadwal_umum_item['start_class'],
                        'end_class' => $jadwal_umum_item['end_class'],
                        'date' => $date_fix,
                        'day_name' => $day_name,
                        'status' => $status,
                    ]);

                    $instruktur_presensi = $class_running->instruktur_presensi()->create([
                        'id_instruktur' => $class_running['id_instruktur'],
                    ]);
                }
            }

            if ($class_running && $instruktur_presensi) {

                return response()->json([
                    'success' => true,
                    'message' => 'class_running scheduled add new generated successfully',
                    'data'    => $class_running,
                ], 201);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'class_running new Failed to generate scheduled',
                    'data'    => $class_running,
                ], 409);
            }
        } else if ($this->checkDateThisWeek() == false) {
            foreach ($jadwal_umum as $jadwal_umum) {
                //get tanggal sekarang dan cari tanggal di minggu ini
                $now = Carbon::now();
                $weekStartDate = $now->copy()->startOfWeek(Carbon::MONDAY);

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
                $class_running = class_running::create([
                    'id_jadwal_umum' => $jadwal_umum['id'],
                    'id_instruktur' => $jadwal_umum['id_instruktur'],
                    'capacity' => $jadwal_umum['capacity'],
                    'start_class' => $jadwal_umum['start_class'],
                    'end_class' => $jadwal_umum['end_class'],
                    'date' => $date_fix,
                    'day_name' => $day_name,
                    'status' => $status,
                ]);

                $instruktur_presensi = $class_running->instruktur_presensi()->create([
                    'id_instruktur' => $class_running['id_instruktur'],
                ]);
            }

            $class_running_list = class_running::all();
            if ($class_running && $instruktur_presensi) {

                return response()->json([
                    'success' => true,
                    'message' => 'class_running scheduled from 0 generated successfully',
                    'data'    => $class_running_list,
                ], 201);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'class_running Failed to generate scheduled',
                    'data'    => $class_running_list,
                ], 409);
            }
        } else if ($this->checkDateNextWeek() == false) {
            $class_running_list = class_running::all();
            $jadwal_umum = jadwal_umum::all();

            $now = Carbon::now();

            if ($now->isSunday() && $now->hour >= 10) {

                foreach ($jadwal_umum as $jadwal_umum) {
                    //get tanggal sekarang dan cari tanggal di minggu ini
                    $now = Carbon::now();
                    $weekStartDate = $now->copy()->startOfWeek(Carbon::MONDAY)->addWeek();

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
                    $class_running = class_running::create([
                        'id_jadwal_umum' => $jadwal_umum['id'],
                        'id_instruktur' => $jadwal_umum['id_instruktur'],
                        'capacity' => $jadwal_umum['capacity'],
                        'start_class' => $jadwal_umum['start_class'],
                        'end_class' => $jadwal_umum['end_class'],
                        'date' => $date_fix,
                        'day_name' => $day_name,
                        'status' => $status,
                    ]);

                    $instruktur_presensi = $class_running->instruktur_presensi()->create([
                        'id_instruktur' => $class_running['id_instruktur'],
                    ]);
                }

                $class_running_list = class_running::all();
                if ($class_running && $instruktur_presensi) {

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
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'The schedule for the this week has already been generated. You Can Regenerate it at sunday after 10 AM :).',
                    'data'    => $class_running_list,
                ], 409);
            }
        } else if ($this->checkDateThisWeek() == true) {
            return response()->json([
                'success' => false,
                'message' => 'Already generated for this week.',
            ], 409);
        } else if ($this->checkDateNextWeek() == true) {
            return response()->json([
                'success' => false,
                'message' => 'Already generated for next week.',
            ], 409);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'class_running Failed to generate scheduled',
                'data'    => $class_running_list,
            ], 409);
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
