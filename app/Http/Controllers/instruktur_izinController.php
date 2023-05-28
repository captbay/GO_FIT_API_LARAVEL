<?php

namespace App\Http\Controllers;

use App\Models\class_running;
use App\Models\instruktur;
use App\Models\instruktur_activity;
use App\Models\instruktur_izin;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class instruktur_izinController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $instruktur_izin = instruktur_izin::with(['instruktur', 'instruktur_pengganti', 'class_running.jadwal_umum.class_detail'])->get();

        return response()->json([
            'success' => true,
            'message' => 'List Data instruktur_izin',
            'data'    => $instruktur_izin
        ], 200);
    }

    public function indexNotConfirm()
    {
        $instruktur_izin = instruktur_izin::where('is_confirm', 0)->with(['instruktur', 'instruktur_pengganti', 'class_running.jadwal_umum.class_detail'])->get();

        return response()->json([
            'success' => true,
            'message' => 'List Data instruktur_izin',
            'data'    => $instruktur_izin
        ], 200);
    }

    public function indexAlredyConfirm()
    {
        $instruktur_izin = instruktur_izin::where('is_confirm', 1)->with(['instruktur', 'instruktur_pengganti', 'class_running.jadwal_umum.class_detail'])->get();

        return response()->json([
            'success' => true,
            'message' => 'List Data instruktur_izin',
            'data'    => $instruktur_izin
        ], 200);
    }

    public function indexByIdInstruktur($id)
    {
        $instruktur_izin = instruktur_izin::where('id_instruktur', $id)->with(['instruktur', 'instruktur_pengganti', 'class_running.jadwal_umum.class_detail'])->get();

        if ($instruktur_izin) {
            return response()->json([
                'success' => true,
                'message' => 'List Data instruktur_izin untuk seseorang instruktur',
                'data'    => $instruktur_izin
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Data izin tidak ditemukan',
                'data'    => ''
            ], 404);
        }
    }

    public function indexByUsernameInstruktur($username)
    {
        $instruktur = instruktur::where('no_instruktur', $username)->first();
        $instruktur_izin = instruktur_izin::where('id_instruktur', $instruktur->id)->with(['instruktur', 'instruktur_pengganti', 'class_running.jadwal_umum.class_detail'])->get();

        return response()->json([
            'success' => true,
            'message' => 'List Data instruktur_izin untuk seseorang instruktur',
            'data'    => $instruktur_izin
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
            'id_instruktur' => 'required', //siapa yang ijin
            'id_instruktur_pengganti' => 'required', // tampilin semua instruktur kecuali yang minta izin ini
            'id_class_running' => 'required', //kelas dimana dia ijin (buat nanti inputan kelas apa aja yang di ajarkan)
            'alasan' => 'required', // alasannya
        ]);

        //response error validation
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //cek apakah dia sudah buat ijin yang sama
        $cekAlreadyExist = instruktur_izin::all();
        foreach ($cekAlreadyExist as $cekAlreadyExist) {
            if ($cekAlreadyExist['id_instruktur'] == $request->id_instruktur &&  $cekAlreadyExist['id_class_running'] == $request->id_class_running) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda sudah membuat izin tersebut! Tunggu untuk di konfirmasi',
                ], 409);
            }
        }

        //cek apakah jadwal dan instuktur pengganti tersebut sudah ada atau belum
        $class_running_instruktur = class_running::where('id_instruktur', $request->id_instruktur)->with(['jadwal_umum.instruktur', 'jadwal_umum.class_detail', 'instruktur'])->get();
        $class_running_pengganti = class_running::where('id_instruktur', $request->id_instruktur_pengganti)->with(['jadwal_umum.instruktur', 'jadwal_umum.class_detail', 'instruktur'])->get();

        if ($class_running_instruktur->count() == 0) {
            return response()->json([
                'success' => false,
                'message' => 'Instruktur tidak memiliki jadwal mengajar!',
            ], 409);
        }

        //cek apakah jadwal dan instuktur pengganti tersebut sudah ada atau belum
        foreach ($class_running_instruktur as $running_instruktur) {
            foreach ($class_running_pengganti as $running_pengganti) {
                if (
                    isset($running_instruktur->jadwal_umum) &&
                    isset($running_pengganti->jadwal_umum) &&
                    $running_instruktur->jadwal_umum->start_class == $running_pengganti->jadwal_umum->start_class &&
                    $running_instruktur->jadwal_umum->day_name == $running_pengganti->jadwal_umum->day_name &&
                    $running_instruktur->date == $running_pengganti->date
                ) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Instruktur pengganti sudah memiliki jadwal yang bertabrakan!',
                    ], 409);
                }
            }
        }

        //buat ijin max h-1
        $class_runnning_date = class_running::find($request->id_class_running);
        // $cekDateHMin1 = Carbon::parse($class_runnning_date->date)->subDay()->format('Y-m-d');
        $dateNow = Carbon::now()->format('Y-m-d');
        if ($class_runnning_date->date <= $dateNow) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak bisa membuat izin pada hari H kelas dan H+ kelas!',
            ], 409);
        }

        $date = Carbon::now();
        $instruktur_izin = instruktur_izin::create([
            'id_instruktur' => $request->id_instruktur,
            'id_instruktur_pengganti' => $request->id_instruktur_pengganti,
            'id_class_running' => $request->id_class_running,
            'alasan' => $request->alasan,
            'is_confirm' => 0,
            'date' => $date,
        ]);

        if ($instruktur_izin) {

            return response()->json([
                'success' => true,
                'message' => 'Izin Created',
                'data'    => $instruktur_izin
            ], 201);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'instruktur_izin Failed to Save',
                'data'    => $instruktur_izin
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
        //find instruktur_izin by ID
        $instruktur_izin = instruktur_izin::find($id);

        //make response JSON
        return response()->json([
            'success' => true,
            'message' => 'Detail Data instruktur_izin',
            'data'    => $instruktur_izin
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
        $instruktur_izin = instruktur_izin::find($id);
        if (!$instruktur_izin) {
            //data instruktur_izin not found
            return response()->json([
                'success' => false,
                'message' => 'instruktur_izin Not Found',
            ], 404);
        }
        //validate form
        $validator = Validator::make($request->all(), [
            'id_instruktur' => 'required',
            'id_instruktur_pengganti' => 'required',
            'id_class_running' => 'required',
            'alasan' => 'required',
            'is_confirm' => 'required|boolean',
        ]);

        //response error validation
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $date = Carbon::now();

        //update instruktur_izin with new image
        $instruktur_izin->update([
            'id_instruktur' => $request->id_instruktur,
            'id_instruktur_pengganti' => $request->id_instruktur_pengganti,
            'id_class_running' => $request->id_class_running,
            'alasan' => $request->alasan,
            'is_confirm' => $request->is_confirm,
            'date' => $date,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'instruktur_izin Updated',
            'data'    => $instruktur_izin
        ], 200);
    }


    public function confirmIzin($id)
    {
        $instruktur_izin = instruktur_izin::find($id);
        if (!$instruktur_izin) {
            //data instruktur_izin not found
            return response()->json([
                'success' => false,
                'message' => 'instruktur_izin Not Found',
            ], 404);
        }

        //update instruktur_izin 
        $instruktur_izin->update([
            'is_confirm' => 1,
        ]);

        //update class_running status
        $instrukturPengganti = instruktur::find($instruktur_izin->id_instruktur_pengganti);
        $instrukturAsliName = $instruktur_izin->instruktur->name;
        $statusTemp = 'menggantikan ' . $instrukturAsliName;
        //update class_running status
        $class_running = class_running::with(['jadwal_umum.instruktur', 'jadwal_umum.class_detail', 'instruktur'])->find($instruktur_izin->id_class_running);
        $class_running->update([
            'id_instruktur' => $instrukturPengganti->id,
            'status' => $statusTemp,
        ]);

        $dateTimeNow = Carbon::now();

        instruktur_activity::create([
            'id_instruktur' => $instruktur_izin->id_instruktur,
            'date_time' => $dateTimeNow,
            'name_activity' => 'Instruktur izin',
            'description_activity' => 'Izin di kelas ' . $class_running->jadwal_umum->class_detail->name . ' Tgl Class ' . $class_running->date,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'instruktur_izin Updated status',
            'data'    => $instruktur_izin
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
        $instruktur_izin = instruktur_izin::find($id);

        if ($instruktur_izin) {
            //delete instruktur_izin
            $instruktur_izin->delete();

            return response()->json([
                'success' => true,
                'message' => 'instruktur_izin Deleted',
            ], 200);
        }


        //data instruktur_izin not found
        return response()->json([
            'success' => false,
            'message' => 'instruktur_izin Not Found',
        ], 404);
    }
}
