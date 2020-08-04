<?php

namespace App\Http\Controllers;

use App\Http\Requests\ShopCreateRequest;
use App\Http\Requests\ShopUpdateRequest;
use App\Http\Resources\ShopCollection;
use App\Http\Resources\ShopResource;
use App\Model\Shop;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $pageSize = $request->get('page_size') ?: $this->pageSize;

        $shops = Shop::orderBy('id','desc')->paginate($pageSize);

        return new ShopCollection($shops);
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
    public function store(ShopCreateRequest $request)
    {
        $data = $request->only(['person_id','account_id','name','code','uri','desc','charge_percent']);

        $shop = Shop::create($data);

        return new ShopResource($shop);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $shop = Shop::find($id);

        return new ShopResource($shop);
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
    public function update(ShopUpdateRequest $request, $id)
    {
        $data = $request->only(['person_id','account_id','name','code','uri','desc','charge_percent']);

        $shop = Shop::find($id);

        $shop->update(array_filter($data));

        return new ShopResource($shop->refresh());
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
}
