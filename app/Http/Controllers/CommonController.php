<?php

namespace App\Http\Controllers;

use App\Jobs\ReadExcel;
use App\Model\ReadExcelJob;
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
}
