<?php

namespace App\Http\Controllers;

use App\Models\PrimarySaving;
use App\Models\PrimarySavingDetail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class PrimarySavingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view();
    }

    public function pIndexSaving()
    {
        $savings = PrimarySaving::getPrimarySavings();
        $profiles = User::getActiveUser();

        return view('pages.pengurus.primary_savings.index', compact('savings', 'profiles'));
    }

    public function saving(Request $request)
    {
        $this->validate($request,[
            'user_id' => 'required|exists:primary_savings,user_id',
            'amount' => 'required|min:0|numeric'
        ]);

        DB::beginTransaction();

        try {
            $data = $request->all();
            $created_by = User::getUserLogin(Auth::id())->profile->name;
            $saldo = PrimarySaving::where('user_id', $data['user_id'])->first();
            $date = Carbon::now()->toDateString();
            $saving_id = $saldo->id;

            if ($saldo) {
                $amount = $saldo->amount + $data['amount'];
            } else {
                $amount = $data['amount'];
            }

                $saldo->update([
                    'amount' => $amount,
                ]);

                PrimarySavingDetail::create([
                    'primary_id' => $saving_id,
                    'amount' => $data['amount'],
                    'date' => $date,
                    'type' => 'd',
                    'description' => $data['description'],
                    'created_by' => $created_by
                ]);


            DB::commit();

            Session::flash('success', 'Berhasil menambahkan tabungan anggota');
            return redirect()->route('primary.index');
        } catch(\Exception $e) {
            DB::rollback();

            Session::flash('error', $e->getMessage());
            // Session::flash('error', 'Error saat melakukan input data');
            return back();
        }
    }

    public function withdraw(Request $request)
    {
        $this->validate($request,[
            'user_id' => 'required|exists:primary_savings,user_id',
            'amount' => 'required|min:0|numeric'
        ]);

        DB::beginTransaction();

        try {
            $data = $request->all();
            $created_by = User::getUserLogin(Auth::id())->profile->name;
            $saldo = PrimarySaving::where('user_id', $data['user_id'])->first();
            $date = Carbon::now()->toDateString();
            $saving_id = $saldo->id;

            if ($saldo) {
                $amount = $saldo->amount - $data['amount'];
            } else {
                $amount = $data['amount'];
            }

                $saldo->update([
                    'amount' => $amount,
                ]);

                PrimarySavingDetail::create([
                    'primary_id' => $saving_id,
                    'amount' => $data['amount'],
                    'date' => $date,
                    'type' => 'c',
                    'description' => $data['description'],
                    'created_by' => $created_by
                ]);


            DB::commit();

            Session::flash('success', 'Berhasil melakukan penarikan tabungan anggota');
            return redirect()->route('primary.index');
        } catch(\Exception $e) {
            DB::rollback();

            // Session::flash('error', $e->getMessage());
            Session::flash('error', 'Error saat melakukan input data');
            return back();
        }
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
    public function show($id)
    {
        $savings = PrimarySavingDetail::getSinglePrimarySavingDetail($id);
        return view('pages.member.primary_savings.show', compact('savings'));
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
