<?php

namespace App\Http\Controllers;

use App\Http\Resources\CommonColletction;
use App\Http\Resources\ExcelJobCollection;
use App\Http\Resources\VolumeJobCollection;
use App\Jobs\ReadExcel;
use App\Model\ReadExcelJob;
use App\Model\SaleVolumeJob;
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

        $jobs = ReadExcelJob::paginate($pageSize);

        return new ExcelJobCollection($jobs);
    }

    public function showCalculateJob(Request $request)
    {
        $pageSize = $request->get('page_size') ?: $this->pageSize;

        $jobs = SaleVolumeJob::paginate($pageSize);

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
}
