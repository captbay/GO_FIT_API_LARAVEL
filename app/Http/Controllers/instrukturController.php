<?php

namespace App\Http\Controllers;

use App\Models\instruktur;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class instrukturController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $instruktur = instruktur::first()->get();

        return response()->json([
            'success' => true,
            'message' => 'List Data instruktur',
            'data'    => $instruktur
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
            'name' => 'required|unique:instruktur',
            'address' => 'required',
            'number_phone' => 'required',
            'born_date' => 'required',
            'gender' => 'required',
        ]);

        //response error validation
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        //membuatIddengan format(Xy) X= huruf dan Y = angka
        if (DB::table('instruktur')->count() == 0) {
            $id_terakhir = 0;
        } else {
            $id_terakhir = instruktur::latest('id')->first()->id;
        }
        $count = $id_terakhir + 1;
        $id_generate = sprintf("%02d", $count);

        //membuat password dengan format dmy
        $datePass = Carbon::parse($request->born_date)->format('dmY');
        $password = bcrypt($datePass);

        //no instruktur
        $no_instruktur = 'I' . $id_generate;

        $user = User::create([
            'username' => $no_instruktur,
            'password' => $password,
            'role' => 'instruktur'
        ]);

        $instruktur = $user->instruktur()->create([
            'no_instruktur' => $no_instruktur,
            'name' => $request->name,
            'address' => $request->address,
            'number_phone' => $request->number_phone,
            'born_date' => $request->born_date,
            'gender' => $request->gender,
            'total_late' => 0,
        ]);

        if ($instruktur) {

            return response()->json([
                'success' => true,
                'message' => 'instruktur Created',
                'data diri instruktur'    => $instruktur,
                'data user instruktur'    => $user
            ], 201);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'instruktur Failed to Save',
                'data diri instruktur'    => $instruktur,
                'data user instruktur'    => $user
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
        //find instruktur by ID
        $instruktur = instruktur::find($id);

        //make response JSON
        return response()->json([
            'success' => true,
            'message' => 'Detail Data instruktur',
            'data'    => $instruktur
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
        $instruktur = instruktur::find($id);
        if (!$instruktur) {
            //data instruktur not found
            return response()->json([
                'success' => false,
                'message' => 'instruktur Not Found',
            ], 404);
        }
        //validate form
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'address' => 'required',
            'number_phone' => 'required',
            // 'born_date' => 'required',
            // 'gender' => 'required',
        ]);

        //response error validation
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        //update instruktur with new image
        $instruktur->update([
            'name' => $request->name,
            'address' => $request->address,
            'number_phone' => $request->number_phone,
            // 'born_date' => $request->born_date,
            // 'gender' => $request->gender,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'instruktur Updated',
            'data'    => $instruktur
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
        $instruktur = instruktur::find($id);
        $user = User::find($instruktur->id_users);


        if ($instruktur) {
            //delete instruktur
            $instruktur->delete();
            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'instruktur Deleted',
            ], 200);
        }


        //data instruktur not found
        return response()->json([
            'success' => false,
            'message' => 'instruktur Not Found',
        ], 404);
    }
}
