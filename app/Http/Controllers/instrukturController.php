<?php

namespace App\Http\Controllers;

use App\Models\instruktur;
use Illuminate\Http\Request;
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
        $instruktur = instruktur::latest()->get();

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
            'id_users' => 'required',
            'no_instruktur' => 'required',
            'name' => 'required',
            'address' => 'required',
            'number_phone' => 'required',
            'born_date' => 'required',
            'gender' => 'required',
            'total_late' => 'required',
        ]);

        //response error validation
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $aktivasi_hitory = instruktur::create([
            'id_users' => $request->id_users,
            'no_instruktur' => $request->no_instruktur,
            'name' => $request->name,
            'address' => $request->address,
            'number_phone' => $request->number_phone,
            'born_date' => $request->born_date,
            'gender' => $request->gender,
            'total_late' => $request->total_late,
        ]);

        if ($aktivasi_hitory) {

            return response()->json([
                'success' => true,
                'message' => 'instruktur Created',
                'data'    => $aktivasi_hitory
            ], 201);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'instruktur Failed to Save',
                'data'    => $aktivasi_hitory
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
            'id_users' => 'required',
            'no_instruktur' => 'required',
            'name' => 'required',
            'address' => 'required',
            'number_phone' => 'required',
            'born_date' => 'required',
            'gender' => 'required',
            'total_late' => 'required',
        ]);

        //response error validation
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        //update instruktur with new image
        $instruktur->update([
            'id_users' => $request->id_users,
            'no_instruktur' => $request->no_instruktur,
            'name' => $request->name,
            'address' => $request->address,
            'number_phone' => $request->number_phone,
            'born_date' => $request->born_date,
            'gender' => $request->gender,
            'total_late' => $request->total_late,
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

        if ($instruktur) {
            //delete instruktur
            $instruktur->delete();

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
