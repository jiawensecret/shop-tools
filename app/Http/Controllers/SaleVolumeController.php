<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfitReportRequest;
use App\Http\Resources\SaleVolumeCollection;
use App\Http\Resources\SaleVolumeResource;
use App\Jobs\SaleVolume;
use App\Model\SalesVolume;
use App\Model\SaleVolumeJob;
use Illuminate\Http\Request;

class SaleVolumeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $pageSize = $request->get('page_size') ?: $this->pageSize;

        $query = SalesVolume::orderBy('id','desc');

        if ($personName = $request->get('person_name','')) {
            $query->whereHas('person',function ($q) use ($personName){
                $q->where('name','like',$personName.'%');
            });
        }

        if ($month = $request->get('month','')) {
            $query->where('month',$month);
        }

        $saleVolumes = $query->paginate($pageSize);

        return new SaleVolumeCollection($saleVolumes);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $saleVolume = SalesVolume::find($id);

        return new SaleVolumeResource($saleVolume);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function profitReport(ProfitReportRequest $request)
    {
        $data = $request->only(['month','exchange']);

        $job = SaleVolumeJob::create($data);

        $this->dispatch(new SaleVolume($job));

        return response($this->responseData);
    }
}
