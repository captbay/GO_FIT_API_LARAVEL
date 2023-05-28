<?php

namespace App\Http\Controllers;

use App\Models\member_activity;
use Illuminate\Http\Request;

class member_activityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $member_activity = member_activity::orderBy('created_at', 'desc')->with(['member'])->get();

        return response()->json([
            'success' => true,
            'message' => 'List Data member_activity',
            'data'    => $member_activity
        ], 200);
    }

    public function indexByIdMember($id_member)
    {
        $member_activity = member_activity::where('id_member', $id_member)->orderBy('created_at', 'desc')->with(['member'])->get();

        return response()->json([
            'success' => true,
            'message' => 'List Data member_activity',
            'data'    => $member_activity
        ], 200);
    }
}
