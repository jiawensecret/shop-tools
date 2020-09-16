<?php

namespace App\Http\Controllers;

use App\Http\Resources\SaleVolumeCollection;
use App\Http\Resources\SaleVolumeResource;

use App\Model\SaleVolumeOrderLog;
use Illuminate\Http\Request;

class SaleVolumeOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id,Request $request)
    {
        $pageSize = $request->get('page_size') ?: $this->pageSize;

        $query = SaleVolumeOrderLog::orderBy('id','desc');

        $query->where('sales_volume_id',$id);

        if ($shop_id = $request->get('shop_id','')) {
            $query->where('shop_id',$shop_id);
        }

        if ($order_no = $request->get('order_no','')) {
            $query->whereHas('order',function ($query) use ($order_no){
                $query->where('order_no','like',$order_no.'%');
            });
        }

        $saleVolumes = $query->paginate($pageSize);

        return new SaleVolumeCollection($saleVolumes);
    }


}
