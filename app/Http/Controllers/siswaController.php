<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\Html\Builder;
use DataTables;
use App\siswa;

class siswaController extends Controller
{
    public function index(Builder $builder){
        if (request()->ajax()) {
            $data = siswa::all();
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('nis', function($data){
                        return $data->nis;
                    })
                    ->addColumn('nama', function($data){
                        return $data->nama;
                    })
                    ->addColumn('action', function($data){
                        return "<button class='btn btn-warning' id='edit' data-id='".$data->id."'>Edit</button> <button class='btn btn-danger' id='delete' data-id='".$data->id."'>Delete</button>";
                           
                    })
                    ->rawColumns(['nis','nama','action'])
                    ->toJson();
        }

        $html = $builder->columns([
            [
                'data' => 'DT_RowIndex','title' => '#',
                'orderable' => false,'searchable' => false,
                'width' => '24px'
            ],
            ['data' => 'nis', 'name' => 'nis', 'title' => 'NIS'],
            ['data' => 'nama', 'name' => 'nama', 'title' => 'Nama'],
            [
                'data' => 'action', 'name' => 'action', 
                'title' => 'Action',
                'orderable' => false, 'searchable' => false,
            ],
        ]);
      
        return view('index', compact('html'));
    }

    public function save(Request $request){
        $this->validate($request,[
    		'nis' => 'required',
    		'nama' => 'required'
    	]);
 
        siswa::create([
    		'nis' => $request->nis,
    		'nama' => $request->nama
        ]);
        
        return response()->json(array('message'=> 1));
    }

    public function search(Request $request){
        $siswa = siswa::find($request->id);

        return response()->json(array('siswa'=> $siswa));
    }

    public function edit(Request $request){
        $this->validate($request,[
    		'nis' => 'required',
    		'nama' => 'required'
    	]);
 
        $siswa = siswa::find($request->id);
        $siswa->nis = $request->nis;
        $siswa->nama = $request->nama;
        $siswa->save();

        return response()->json(array('message'=> 1));
    }

    public function delete(Request $request){
        $siswa = siswa::find($request->id);

        if($siswa !== null){
          $siswa->delete();  
        }
        return response()->json(array('message'=> 1));
    }
}
