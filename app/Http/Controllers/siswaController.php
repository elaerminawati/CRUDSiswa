<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\Html\Builder;
use Illuminate\Support\Facades\Validator;
use DataTables;
use App\siswa;

class siswaController extends Controller
{
    public function index(Builder $builder){
        if (request()->ajax()) {
            $data = siswa::all();
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function($data){
                        return "<button class='btn btn-warning' id='edit' data-id='".$data->id."'>Edit</button> <button class='btn btn-danger' id='delete' data-id='".$data->id."'>Delete</button>";
                    })
                    ->rawColumns(['action'])
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
        $validator = Validator::make($request->all(), [
            'nis' => 'required|numeric|min:4',
            'nama' => 'required',
        ]);
 
        if($validator->fails()){
            return response()->json(['message' => $validator->errors()], 422);
        }else{
            try{
                    $save = siswa::create([
                        'nis' => $request->input('nis'),
                        'nama' => $request->input('nama')
                    ]);

                    return response()->json(['message' => 'success'], 200);
            }catch(\Exception $e){
                return response()->json(['message' => $e->getMessage()], 500);
            }
        
        }
    }

    public function edit(Request $request){
        $validator = Validator::make($request->all(),[
                        'nis' => 'required|numeric|min:4',
                        'nama' => 'required', 
                    ]);
        if($validator->fails()) {
            return response()->json(['message' => $validator->errors()], 422);
        }else{
            $siswa = siswa::findOrFail($request->input('id'));
            $siswa->nis = $request->input('nis');
            $siswa->nama = $request->input('nama');

            try{
                $siswa = siswa::findOrFail($request->input('id'));
                $siswa->nis = $request->input('nis');
                $siswa->nama = $request->input('nama');
                $siswa->save();

                return response()->json(['message' => 'success'], 200);
            }catch(\Exception $e){
                return response()->json(['message' => $e->getMessage()], 500);
            }
        }  
    }

    public function delete(Request $request){
        $siswa = siswa::findOrFail($request->id);
        $siswa->delete();  
        return response()->json(['message' => 'success'], 200);
    }
}
