<?php

namespace App\Http\Controllers;

use App\Models\Audience;
use App\Http\Requests\StoreAudienceRequest;
use App\Http\Requests\UpdateAudienceRequest;
use App\Http\Resources\AudienceResource;
use App\Models\Test;
use Hossam\Licht\Controllers\LichtBaseController;

class AudienceController extends LichtBaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tests = Test::with('audiences')->orderBy('start_time')->paginate(1);
        return view('admin.audiences', compact('tests'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreAudienceRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreAudienceRequest $request)
    {
        $audience = Audience::create($request->validated());
        return redirect()->route('manual-tests.index', ['test' => $request->validated('test_id')]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Audience  $audience
     * @return \Illuminate\Http\Response
     */
    public function show(Audience $audience)
    {
        return $this->successResponse(AudienceResource::make($audience));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateAudienceRequest  $request
     * @param  \App\Models\Audience  $audience
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateAudienceRequest $request, Audience $audience)
    {
        $audience->update($request->validated());
        return redirect()->route('audiences.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Audience  $audience
     * @return \Illuminate\Http\Response
     */
    public function destroy(Audience $audience)
    {
        $audience->delete();
        return redirect()->route('audiences.index');
    }
}
