<?php

namespace App\Http\Controllers;

use App\Models\gym;
use App\Models\gym_booking;
use App\Models\member;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class gym_bookingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $gym_booking = gym_booking::with(['gym', 'member'])->get();

        return response()->json([
            'success' => true,
            'message' => 'List Data gym_booking',
            'data'    => $gym_booking
        ], 200);
    }

    public function indexByIdMember($id_member)
    {
        $gym_booking = gym_booking::where('id_member', $id_member)->with(['gym', 'member'])->get();

        return response()->json([
            'success' => true,
            'message' => 'List Data gym_booking',
            'data'    => $gym_booking
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
            'id_gym' => 'required',
            'id_member' => 'required',
            'date_booking' => 'required|after:yesterday',
        ]);

        //response error validation
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $date_booking = Carbon::parse($request->date_booking)->format('Y-m-d');

        //cek apakah dia sudah buat data yang sama
        $cekAlreadyExist = gym_booking::all();
        foreach ($cekAlreadyExist as $cekAlreadyExistData) {
            if ($cekAlreadyExistData['id_gym'] == $request->id_gym &&  $cekAlreadyExistData['id_member'] == $request->id_member && $cekAlreadyExistData['date_booking'] == $date_booking) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda sudah booking gym tersebut! Ditunggu di GYM ya :)',
                ], 409);
            } else if ($cekAlreadyExistData['id_member'] == $request->id_member && $cekAlreadyExistData['date_booking'] == $date_booking) {
                return response()->json([
                    'success' => false,
                    'message' => 'Booking GYM Hanya Bisa 1X Dalam Sehari! :)',
                ], 409);
            }
        }

        //ambil semua data
        $member = member::with(['users'])->find($request->id_member);
        $gym = gym::find($request->id_gym);

        //memeriksa status aktif untuk member
        if (!$member || $member->status_membership == 0) {
            return response()->json([
                'success' => false,
                'message' => 'Anda bukan member aktif',
                'data'    => $member
            ], 409);
        }
        //memeriksa kuota penuh atau tidak di gym_runnning
        if ($gym->capacity <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Gym ini sudah penuh',
            ], 409);
        }

        //membuatIddengan format(Xy) X= huruf dan Y = angka
        if (DB::table('gym_booking')->count() == 0) {
            $id_terakhir = 0;
        } else {
            $id_terakhir = gym_booking::latest('id')->first()->id;
        }
        $count = $id_terakhir + 1;
        $id_generate = sprintf("%03d", $count);

        //membuat angka dengan format y
        $digitYear = Carbon::parse(now())->format('y');

        //membuat angka dengan format m
        $digitMonth = Carbon::parse(now())->format('m');

        //no  
        $no_gym_booking = $digitYear . '.' . $digitMonth . '.' . $id_generate;

        $date_time = Carbon::now();
        $gym_booking = gym_booking::create([
            'no_gym_booking' => $no_gym_booking,
            'id_gym' => $request->id_gym,
            'id_member' => $request->id_member,
            'date_booking' => $date_booking,
            'date_time' => $date_time
        ]);

        $gym_history = $gym_booking->gym_history()->create([
            'no_gym_history' => $no_gym_booking,
        ]);

        if ($gym_booking && $gym_history) {
            $gym->update([
                'capacity' => $gym->capacity - 1
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Booking Created',
                'data'    => $gym_booking
            ], 201);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Booking Failed to Save',
                'data'    => $gym_booking
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
        //find gym_booking by ID
        $gym_booking = gym_booking::find($id);

        //make response JSON
        return response()->json([
            'success' => true,
            'message' => 'Detail Data gym_booking',
            'data'    => $gym_booking
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
        $gym_booking = gym_booking::find($id);
        if (!$gym_booking) {
            //data gym_booking not found
            return response()->json([
                'success' => false,
                'message' => 'gym_booking Not Found',
            ], 404);
        }
        //validate form
        $validator = Validator::make($request->all(), [
            'id_gym' => 'required',
            'id_member' => 'required',
            'date_time' => 'required'
        ]);

        //response error validation
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //update gym_booking with new image
        $gym_booking->update([
            'id_gym' => $request->id_gym,
            'id_member' => $request->id_member,
            'date_time' => $request->date_time
        ]);

        return response()->json([
            'success' => true,
            'message' => 'gym_booking Updated',
            'data'    => $gym_booking
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
        $gym_booking = gym_booking::find($id);

        if (!$gym_booking) {
            //data gym_booking not found
            return response()->json([
                'success' => false,
                'message' => 'gym_booking Not Found',
            ], 404);
        }

        $gym = gym::find($gym_booking->id_gym);

        //batal max h-1
        $dateNow = Carbon::now()->format('Y-m-d');
        if ($gym_booking->date_booking <= $dateNow) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak bisa membatalkan booking gym! (max h-1)',
            ], 409);
        } else {
            //set gym running capacity
            $gym->update([
                'capacity' => $gym->capacity + 1
            ]);
            //delete rimwayat presensi yang belum di absensi
            if ($gym_booking->gym_history()->exists()) {
                $gym_booking->gym_history()->delete();
            }

            //delete gym_booking
            $gym_booking->delete();

            return response()->json([
                'success' => true,
                'message' => 'gym booking and presensi Deleted',
            ], 200);
        }
    }
}
