<?php

namespace App\Http\Controllers;

use App\Models\instruktur_activity;
use Illuminate\Http\Request;

class instruktur_activityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $instruktur_activity = instruktur_activity::orderBy('created_at', 'desc')->with(['instruktur'])->get();

        return response()->json([
            'success' => true,
            'message' => 'List Data instruktur_activity',
            'data'    => $instruktur_activity
        ], 200);
    }

    public function indexByIdInstruktur($id_instruktur)
    {
        $instruktur_activity = instruktur_activity::where('id_instruktur', $id_instruktur)->orderBy('created_at', 'desc')->with(['instruktur'])->get();

        return response()->json([
            'success' => true,
            'message' => 'List Data instruktur_activity',
            'data'    => $instruktur_activity
        ], 200);
    }
}