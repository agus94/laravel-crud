<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SiswaController extends Controller
{

    public function index(Request $request)
    {
        if($request->has('cari')){
            $data_siswa = \App\Siswa::where('nama_depan', 
            'LIKE', '%'.$request->cari.'%')->get();
        } else {
        $data_siswa = \App\Siswa::all();
        }
        return view('siswa.index', ['data_siswa' => $data_siswa]);
    }

    public function create(Request $request) 
    {
        $this->validate($request, [
            'nama_depan' => 'required|min:5',
            'email' => 'required|email|unique:users',
            'jenis_kelamin' => 'required',
            'agama' => 'req uired',
            'avatar' => 'mimes:jpeg,bmp,png,jpg'
        ]);
        
        // Inser ke table Users
        $user = new \App\User;
        $user->role = 'siswa';
        $user->name = $request->nama_depan;
        $user->email = $request->email;
        $user->password = bcrypt('rahasia');
        $user->remember_token = Str::random(60);
        $user->save();
        
        // Insert ke table siswa
        $request->request->add(['user_id' => $user->id ]);
        $siswa = \App\Siswa::create($request->all());
        if($request->hasfile('avatar')){
            $request->file('avatar')->move('images/', $request->file('avatar')->getClientOriginalName());
            $siswa->avatar = $request->file('avatar')->getClientOriginalName();
            $siswa->save();
        }

        return redirect('/siswa')->with('sukses', 'Data Berhasil Diinput');
    }


    public function edit($id)
    {
        $siswa = \App\Siswa::find($id);
        
        return view('siswa/edit',['siswa' => $siswa]);
    }


    public function update(Request $request, $id)
    {
        // dd($request->all());

        $siswa = \App\Siswa::find($id);

        $siswa->update($request->all());
        if($request->hasfile('avatar')){
            $request->file('avatar')->move('images/', $request->file('avatar')->getClientOriginalName());
            $siswa->avatar = $request->file('avatar')->getClientOriginalName();
            $siswa->save();
        }

        return redirect('/siswa')->with('sukses', 'Data Berhasil DiUpdate');
    }

    public function delete($id)
    {
        $siswa = \App\Siswa::find($id);

        $siswa->delete();

        return redirect('/siswa')->with('sukses', 'Data Berhasil Dihapus');
    }

    public function profile($id)
    {
        $siswa = \App\Siswa::find($id);
        $matapelajaran = \App\Mapel::all();
        return view('siswa.profile', ['siswa' => $siswa, 'mapel' => $matapelajaran]);
    }

}
