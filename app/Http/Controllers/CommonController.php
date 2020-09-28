<?php

namespace App\Http\Controllers;

use App\Http\Resources\CommonColletction;
use App\Http\Resources\ExcelJobCollection;
use App\Http\Resources\VolumeJobCollection;
use App\Jobs\ReadExcel;
use App\Model\ReadExcelJob;
use App\Model\SaleVolumeJob;
use App\Model\Shop;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CommonController extends Controller
{
    public function uploadExcel(Request $request)
    {
        $file = $request->file('file');
        $type = $request->get('type');
        $fileName = $type . '-' . time() . '.' . $file->getClientOriginalExtension();
        $path = Storage::putFileAs('order', $file, $fileName);

        $readExcelJob = ReadExcelJob::create([
            'filename' => 'app/' . $path,
            'user_id' => Auth::id() ?? 0,
            'type' => $type,
        ]);

        $this->dispatch(new ReadExcel($readExcelJob));

        return response($this->responseData);
    }

    public function showExcelJob(Request $request)
    {
        $pageSize = $request->get('page_size') ?: $this->pageSize;

        $query = ReadExcelJob::orderBy('id','desc');
        if ($status = $request->get('status','')) {
            $query->where('status',$status);
        }
        $jobs = $query->paginate($pageSize);

        return new ExcelJobCollection($jobs);
    }

    public function showCalculateJob(Request $request)
    {
        $pageSize = $request->get('page_size') ?: $this->pageSize;

        $query = SaleVolumeJob::orderBy('id','desc');

        if ($month = $request->get('month','')) {
            $query->where('month',$month);
        }

        if ($status = $request->get('status','')) {
            $query->where('status',$status);
        }

        $jobs = $query->paginate($pageSize);

        return new VolumeJobCollection($jobs);
    }

    public function monthList()
    {
        $res = [];
        for ($i = 0; $i < 12; $i++) {
            $res[] = Carbon::now()->firstOfMonth()->subMonths($i)->format('Y-m');
        }

        return new CommonColletction($res);
    }

    public function shopList()
    {
        $shops = Shop::all()->toArray();

        $res = array_column($shops,'name','id');

        return new CommonColletction($res);
    }
}
