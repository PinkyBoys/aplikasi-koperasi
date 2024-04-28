<?php

namespace App\Http\Controllers;

use App\Models\PrimarySaving;
use Illuminate\Http\Request;

class PrimarySavingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $savings = PrimarySaving::getPrimarySavings();
        // dd($savings);
        return view('pages.pengurus.primary_savings.index', compact('savings'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(PrimarySaving $primarySaving)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PrimarySaving $primarySaving)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PrimarySaving $primarySaving)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PrimarySaving $primarySaving)
    {
        //
    }
}
