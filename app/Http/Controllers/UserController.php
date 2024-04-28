<?php

namespace App\Http\Controllers;

use App\Models\PrimarySaving;
use App\Models\Profile;
use App\Models\Role;
use App\Models\Saving;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::getUsers();
        // dd($users);
        // $roles = Role::getActiveRole();
        return view('pages.admin.users.index', compact('users'));
    }

    public function addUser()
    {
        $roles = Role::getActiveRole();
        $genders = ['l' => 'Laki-Laki', 'p' => 'Perempuan'];
        return view('pages.admin.users.add', compact('roles', 'genders'));
    }

    public function adminAddUser(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|min:3',
            'address' => 'required',
            'member_id' => 'numeric|min_digits:7|unique:profiles,member_id',
            'amount' => 'numeric',
            'username' => 'required|unique:users,username',
            'password' => 'required|min:6|confirmed'
        ]);

        DB::beginTransaction();

        try {
            $data = $request->all();
            $data['created_by'] = User::getUserLogin(Auth::id())->profile->name;

            // dd($data);
            $user = User::create([
                'username' => $data['username'],
                'password' => Hash::make($data['password']),
                'created_by' => $data['created_by'],
                'role_id' => $data['role_id'],
                'joinOn' => $data['joinOn']
            ]);

            if ($user) {
                Profile::create([
                    'user_id' => $user->id,
                    'member_id' =>$data['member_id'],
                    'name' => $data['name'],
                    'address' => $data['address'],
                    'gender' => $data['gender'],
                    'job' => $data['job'],
                    'created_by' => $data['created_by'],
                ]);

                PrimarySaving::create([
                    'user_id' => $user->id,
                    'amount' => $data['amount'],
                    'date' => $data['joinOn'],
                    'type' => 'd',
                    'created_by' => $data['created_by'],
                ]);

            }

            DB::commit();

            Session::flash('success', 'Berhasil menambahkan user');
            return back();
        } catch (\Exception $e) {

            DB::rollback();

            Session::flash('error', $e->getMessage());
            // Session::flash('error', 'Error saat melakukan input data');
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
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $user = User::getSingleUser($id);
        $roles = ['guru' => 'Guru', 'wali_murid' => 'Wali Murid', 'admin' => 'Admin', 'kepala_sekolah' => 'Kepala Sekolah'];
        return view('pages.admin.users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $this->validate($request, [
            'username' => 'required|min:3',
            'role' => 'required',
            'status' => 'required'
        ]);

        $user = User::getSingleUser($id);

        try {

            $data = $request->all();

            $user->update([
                'username' => $data['username'],
                'role' => $data['role'],
                'status' => $data['status'],
            ]);

            Session::flash('success', 'Berhasil edit user');
            return redirect()->route('users.index');
        } catch (\Exception $e) {
            // Session::flash('error',$e);
            Session::flash('error', 'Kesalahan ketika mengirim data');
            return back();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::getSingleUser($id);
        $status = $user->delete();

        if ($status) {
            Session::flash('success', 'User berhasil dihapus');
        } else {
            Session::flash('error', 'Terjadi error ketika melakukan delete');
        }

        return back();
    }
}
