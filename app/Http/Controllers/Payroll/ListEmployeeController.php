<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Models\memployee;

class ListEmployeeController extends Controller
{
    public function getIndex(Request $request)
    {
        if ($request->wantsJson()) {
            $data = memployee::all();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn_show =
                        '<a class="btn btn-primary" href="'.route('payroll.employee.index', $row->id).'" type="button"><i class="icon-eye"></i></a>';
                    $btn =
                        '<div class="btn-group">' .
                        $btn_show .
                        '</div>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        } else {
            return view('pages.payroll.list-employee');
        }
    }
}
