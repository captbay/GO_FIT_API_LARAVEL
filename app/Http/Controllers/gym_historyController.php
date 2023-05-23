<?php

namespace App\Http\Controllers;

use App\Models\gym;
use App\Models\gym_booking;
use App\Models\gym_history;
use App\Models\member;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class gym_historyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $gym_history = gym_history::with(['gym_booking.member', 'gym_booking.gym'])->get();

        return response()->json([
            'success' => true,
            'message' => 'List Data gym_history',
            'data'    => $gym_history
        ], 200);
    }

    public function updateStatus(Request $request, $id)
    {
        $gym_history = gym_history::with(['gym_booking.member', 'gym_booking.gym'])->find($id);
        if (!$gym_history) {
            //data gym_history not found
            return response()->json([
                'success' => false,
                'message' => 'gym_history Not Found',
            ], 404);
        }
        //validate form
        $validator = Validator::make($request->all(), [
            'status' => 'required|boolean',
        ]);

        //response error validation
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $date_time = Carbon::now();
        //update gym_history with new image
        $gym_history->update([
            'status' => $request->status,
            'date_time' => $date_time,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'gym_history Updated',
            'data'    => $gym_history
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

        //cek apakah dia sudah buat data yang sama
        $cekAlreadyExist = gym_booking::all();
        foreach ($cekAlreadyExist as $cekAlreadyExist) {
            if ($cekAlreadyExist['id_gym'] == $request->id_gym &&  $cekAlreadyExist['id_member'] == $request->id_member && $cekAlreadyExist['date_booking'] == $request->date_booking) {
                return response()->json([
                    'success' => false,
                    'message' => 'Member sudah booking gym tersebut! Ditunggu di GYM ya :)',
                ], 409);
            } else if ($cekAlreadyExist['id_member'] == $request->id_member && $cekAlreadyExist['date_booking'] == $request->date_booking) {
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
                'message' => 'Member bukan member aktif',
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
            'date_booking' => $request->date_booking,
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
                'message' => 'Presensi and Booking Created',
                'data'    => $gym_booking
            ], 201);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Presensi and Booking Failed to Save',
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
        //find gym_history by ID
        $gym_history = gym_history::find($id);

        //make response JSON
        return response()->json([
            'success' => true,
            'message' => 'Detail Data gym_history',
            'data'    => $gym_history
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
        $gym_history = gym_history::find($id);
        if (!$gym_history) {
            //data gym_history not found
            return response()->json([
                'success' => false,
                'message' => 'gym_history Not Found',
            ], 404);
        }
        //validate form
        $validator = Validator::make($request->all(), [
            'no_gym_history' => 'required',
            'id_gym_booking' => 'required',
            'date_time' => 'required',
        ]);

        //response error validation
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //update gym_history with new image
        $gym_history->update([
            'no_gym_history' => $request->no_gym_history,
            'id_gym_booking' => $request->id_gym_booking,
            'date_time' => $request->date_time,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'gym_history Updated',
            'data'    => $gym_history
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
        $gym_history = gym_history::find($id);

        if ($gym_history) {
            //delete gym_history
            $gym_history->delete();

            return response()->json([
                'success' => true,
                'message' => 'gym_history Deleted',
            ], 200);
        }


        //data gym_history not found
        return response()->json([
            'success' => false,
            'message' => 'gym_history Not Found',
        ], 404);
    }

    /**
     * Display a listing of the resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function generate_gym_historyCard($id)
    {
        $gym_history = gym_history::with(['gym_booking.member', 'gym_booking.gym'])->find($id);
        if (!$gym_history) {
            //data gym_history not found
            return response()->json([
                'success' => false,
                'message' => 'gym_history Not Found',
            ], 404);
        }

        $member = member::find($gym_history->gym_booking->member->id);
        $gym = gym::find($gym_history->gym_booking->gym->id);

        $data = [
            'gym_history' => $gym_history,
            'member' => $member,
            'gym' => $gym,
        ];

        $pdf = Pdf::loadview('gym_historyCard', $data);

        return $pdf->output();
    }
}
