<?php

namespace App\Http\Controllers;

use App\Models\class_booking;
use App\Models\class_history;
use App\Models\class_package_history;
use App\Models\class_running;
use App\Models\deposit_package;
use App\Models\instruktur_presensi;
use App\Models\member;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class class_bookingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $class_booking = class_booking::with(['class_running.jadwal_umum.class_detail', 'class_running.instruktur', 'member'])->get();

        return response()->json([
            'success' => true,
            'message' => 'List Data class_booking',
            'data'    => $class_booking
        ], 200);
    }

    public function indexByIdMember($id_member)
    {
        $class_booking = class_booking::where('id_member', $id_member)->with(['class_running.jadwal_umum.class_detail', 'class_running.instruktur', 'member'])->get();

        return response()->json([
            'success' => true,
            'message' => 'List Data class_booking',
            'data'    => $class_booking
        ], 200);
    }

    //ini pas klik ke detail class runnnig yang di ajarin
    // jadi perlu untuk show semua kelas yang di ajarin sama si instruktur yang login
    public function seachClassByIdInstrukturLogin($id_instruktur, $id_class_running)
    {
        $data = [];
        // ambil dulu semua sesuai id instruktur dan id class running sesuai parameter
        $class_booking = class_booking::with(['class_running.jadwal_umum.class_detail', 'class_running.instruktur', 'member'])->get();
        $class_running_instruktur = class_running::where('id_instruktur', $id_instruktur)->get();

        // Loop through all class_booking records
        foreach ($class_booking as $booking) {
            // Loop through class_running records taught by the instructor
            foreach ($class_running_instruktur as $running) {
                // Check if the class_running IDs match
                if ($booking->id_class_running == $running->id) {
                    // Check if the class_running ID matches the provided ID
                    if ($booking->id_class_running == $id_class_running) {
                        array_push($data, $booking);
                    }
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'List Data class_booking by instruktur',
            'data'    => $data
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
            'id_class_running' => 'required',
            'id_member' => 'required',
        ]);

        //response error validation
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //cek apakah dia sudah buat data yang sama
        $cekAlreadyExist = class_booking::all();
        foreach ($cekAlreadyExist as $cekAlreadyExist) {
            if ($cekAlreadyExist['id_class_running'] == $request->id_class_running &&  $cekAlreadyExist['id_member'] == $request->id_member) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda sudah booking kelas tersebut! Ditunggu dikelas ya :)',
                ], 409);
            }
        }

        //ambil semua element yang diperlukan
        $member = member::with(['users'])->find($request->id_member);
        $class_running = class_running::with(['jadwal_umum.instruktur', 'jadwal_umum.class_detail', 'instruktur'])->find($request->id_class_running);
        $deposit_package = deposit_package::where('id_member', $request->id_member)->where('id_class_detail', $class_running->jadwal_umum->class_detail->id)->with(['class_detail', 'member'])->orderBy('created_at', 'desc')->first();

        //kalo libur ga bisa book
        if ($class_running->status == "libur") {
            return response()->json([
                'success' => false,
                'message' => 'Kelas ini Sedang Libur, Tidak bisa Booking Untuk Kelas Ini!',
            ], 409);
        }
        //buat ijin max h-1
        // $cekDateHMin1 = Carbon::parse($class_runnning_date->date)->subDay()->format('Y-m-d');
        $dateNow = Carbon::now()->format('Y-m-d');


        //memeriksa status aktif untuk member
        if (!$member || $member->status_membership == 0) {
            return response()->json([
                'success' => false,
                'message' => 'Anda bukan member aktif',
                'data'    => $member
            ], 409);
        }
        //memeriksa kuota penuh atau tidak di class_runnning
        if ($class_running->capacity <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Kelas ini sudah penuh',
            ], 409);
        }

        //check booking apakah pas hari h atau tidak
        if ($class_running->date < $dateNow) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak bisa booking kelas pada hari H+ kelas!',
            ], 409);
        }

        //membuatIddengan format(Xy) X= huruf dan Y = angka
        if (DB::table('class_booking')->count() == 0) {
            $id_terakhir = 0;
        } else {
            $id_terakhir = class_booking::latest('id')->first()->id;
        }
        $count = $id_terakhir + 1;
        $id_generate = sprintf("%03d", $count);

        //membuat angka dengan format y
        $digitYear = Carbon::parse(now())->format('y');

        //membuat angka dengan format m
        $digitMonth = Carbon::parse(now())->format('m');

        //no  
        $no_class_booking = $digitYear . '.' . $digitMonth . '.' . $id_generate;

        //mengecek deposit paket untuk class detail yang sama dengan inputan ?
        if ($deposit_package != null && $deposit_package->package_amount > 0 && $deposit_package->expired_date > Carbon::now()->format('Y-m-d')) {
            //buat class_booking dan class_package_history

            $date_time = Carbon::now();
            $class_booking = class_booking::create([
                'no_class_booking' => $no_class_booking,
                'id_class_running' => $request->id_class_running,
                'id_member' => $request->id_member,
                'metode_pembayaran' => 'paket',
                'date_time' => $date_time,
            ]);

            if ($class_booking) {
                $class_running->update([
                    'capacity' => $class_running->capacity - 1
                ]);
                return response()->json([
                    'success' => true,
                    'message' => 'class_booking paket Created',
                    'data'    => $class_booking,
                ], 201);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'class_booking paket Failed to Save',
                    'data'    => $class_booking,
                ], 409);
            }
        } else if ($member->jumlah_deposit_reguler >= $class_running->jadwal_umum->class_detail->price) {
            //buat class_history
            //buat class_booking dan class_history

            $date_time = Carbon::now();
            $class_booking = class_booking::create([
                'no_class_booking' => $no_class_booking,
                'id_class_running' => $request->id_class_running,
                'id_member' => $request->id_member,
                'metode_pembayaran' => 'cash',
                'date_time' => $date_time,
            ]);

            if ($class_booking) {
                $class_running->update([
                    'capacity' => $class_running->capacity - 1
                ]);
                return response()->json([
                    'success' => true,
                    'message' => 'class_booking uang Created',
                    'data'    => $class_booking,
                ], 201);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'class_booking uang Failed to Save',
                    'data'    => $class_booking,
                ], 409);
            }
        } else {
            return response()->json([
                'success' => false,
                'message' => 'You dont have package class or your amount money is not enough',
            ], 409);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     * update status class_booking yang bisa cuma instruktur
     */
    public function presensiMember(Request $request, $id)
    {
        $class_booking = class_booking::with(['class_running.jadwal_umum.class_detail', 'class_running.instruktur', 'member'])->find($id);
        if (!$class_booking) {
            //data class_booking not found
            return response()->json([
                'success' => false,
                'message' => 'class_booking Not Found',
            ], 404);
        }
        //Validasi Formulir
        $validator = Validator::make($request->all(), [
            'status' => 'required',
        ]);

        //response error validation
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if ($class_booking->class_running->status == "libur") {
            return response()->json([
                'success' => false,
                'message' => 'class libur! cant presensi member',
            ], 409);
        }

        if ($class_booking->status != null) {
            return response()->json([
                'success' => false,
                'message' => 'Member already presensi',
            ], 409);
        }

        $instruktur_presensi = instruktur_presensi::where('id_class_running', $class_booking->class_running->id)->where('id_instruktur', $class_booking->class_running->instruktur->id)->with(['instruktur', 'class_running'])->first();

        if ($instruktur_presensi->status_class == 0 || $instruktur_presensi->status_class == null) {
            return response()->json([
                'success' => false,
                'message' => 'Instruktur belum presensi / tidak hadir',
            ], 409);
        }

        $date_time = Carbon::now();

        if ($class_booking->metode_pembayaran == 'cash') {
            //cari member
            $member = Member::find($class_booking->member->id);
            //perhitungan pengurangan saldo uang
            $jumlahUangSekarang = $member->jumlah_deposit_reguler;
            $uangBerkurang = $class_booking->class_running->jadwal_umum->class_detail->price;
            $jumlahUangSekarangBanget = $jumlahUangSekarang - $uangBerkurang;
            //update jumlah deposit member
            $member->update([
                'jumlah_deposit_reguler' => $jumlahUangSekarangBanget,
            ]);
            //update class boking hadir == 1
            $class_booking->update([
                'status' => $request->status,
                'date_time' => $date_time,
            ]);
            //buat data untuk class_history sejumlah transaksi
            $class_history = $class_booking->class_history()->create([
                'no_class_history' => $class_booking->no_class_booking,
                'date_time' => $class_booking->date_time,
                'sisa_deposit' => $jumlahUangSekarangBanget,
                'status' => $class_booking->status,
            ]);

            if ($class_booking && $class_history) {
                return response()->json([
                    'success' => true,
                    'message' => 'class_booking Updated and money decreased',
                    'data'    => $class_booking
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'class_booking Failed to Update',
                ], 409);
            }
        } else if ($class_booking->metode_pembayaran == 'paket') {
            //cari deposit paket yang dipake sesuai kelas yang di booking
            // update deposit_package
            $deposit_package = deposit_package::where('id_member', $class_booking->member->id)->where('id_class_detail', $class_booking->class_running->jadwal_umum->class_detail->id)->first();
            $depositTemp = $deposit_package->package_amount;
            $depositFinal = $depositTemp - 1;
            //update sejumlah pengurangan
            $deposit_package->update([
                'package_amount' => $depositFinal,
            ]);
            //update class boking hadir == 1
            $class_booking->update([
                'status' => $request->status,
                'date_time' => $date_time,
            ]);
            //buat data untuk class_package_history sejumlah transaksi
            $class_package_history = $class_booking->class_package_history()->create([
                'no_class_package_history' => $class_booking->no_class_booking,
                'date_time' => $class_booking->date_time,
                'sisa_deposit_kelas' => $deposit_package->package_amount,
                'expired_date' => $deposit_package->expired_date,
                'status' => $class_booking->status,
            ]);

            if ($class_booking && $class_package_history) {
                return response()->json([
                    'success' => true,
                    'message' => 'class_booking Updated and package decreased',
                    'data'    => $class_booking
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'class_booking Failed to Update',
                ], 409);
            }
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Metode pembayaran tidak diketahui',
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
        //find class_booking by ID
        $class_booking = class_booking::find($id);

        //make response JSON
        return response()->json([
            'success' => true,
            'message' => 'Detail Data class_booking',
            'data'    => $class_booking
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
    //     $class_booking = class_booking::find($id);
    //     if (!$class_booking) {
    //         //data class_booking not found
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'class_booking Not Found',
    //         ], 404);
    //     }
    //     //validate form
    //     $validator = Validator::make($request->all(), [
    //         'id_class_running' => 'required',
    //         'id_member' => 'required',
    //         'date_time' => 'required',
    //     ]);

    //     //response error validation
    //     if ($validator->fails()) {
    //         return response()->json($validator->errors(), 422);
    //     }

    //     //update class_booking with new image
    //     $class_booking->update([
    //         'id_class_running' => $request->id_class_running,
    //         'id_member' => $request->id_member,
    //         'date_time' => $request->date_time,
    //     ]);

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'class_booking Updated',
    //         'data'    => $class_booking
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
        $class_booking = class_booking::find($id);

        if (!$class_booking) {
            //data class_booking not found
            return response()->json([
                'success' => false,
                'message' => 'class_booking Not Found',
            ], 404);
        }

        //batal kelas h-1 max
        $class_running = class_running::with(['jadwal_umum.instruktur', 'jadwal_umum.class_detail', 'instruktur'])->find($class_booking->id_class_running);

        $dateNow = Carbon::now()->format('Y-m-d');
        if ($class_running->date <= $dateNow) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak bisa membatalkan booking kelas! (max h-1)',
            ], 409);
        } else {
            //set class running capacity
            $class_running->update([
                'capacity' => $class_running->capacity + 1
            ]);
            //delete rimwayat presensi yang belum di absensi
            if ($class_booking->class_history()->exists()) {
                $class_booking->class_history()->delete();
            } else if ($class_booking->class_package_history()->exists()) {
                $class_booking->class_package_history()->delete();
            }

            //delete class_booking
            $class_booking->delete();

            return response()->json([
                'success' => true,
                'message' => 'class_booking Deleted',
            ], 200);
        }
    }
}
