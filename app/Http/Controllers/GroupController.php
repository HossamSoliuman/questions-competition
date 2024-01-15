<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Http\Requests\StoreGroupRequest;
use App\Http\Requests\UpdateGroupRequest;
use App\Http\Resources\GroupResource;
use App\Models\Competition;
use App\Models\GroupTeam;
use App\Models\Team;
use GuzzleHttp\Psr7\Request;
use Hossam\Licht\Controllers\LichtBaseController;
use Illuminate\Http\Request as HttpRequest;

class GroupController extends LichtBaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $groups = Group::with('teams')->orderBy('id', 'desc')->paginate(2);
        $allTeams = Team::all();
        $allCompetitions = Competition::orderBy('id', 'desc')->get();
        return view('admin.groups', compact('groups', 'allTeams','allCompetitions'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreGroupRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreGroupRequest $request)
    {
        $group = Group::create($request->validated());
        return redirect()->route('groups.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Group  $group
     * @return \Illuminate\Http\Response
     */
    public function show(Group $group)
    {
        return $this->successResponse(GroupResource::make($group));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateGroupRequest  $request
     * @param  \App\Models\Group  $group
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateGroupRequest $request, Group $group)
    {
        $group->update($request->validated());
        return redirect()->route('groups.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Group  $group
     * @return \Illuminate\Http\Response
     */
    public function destroy(Group $group)
    {
        $group->delete();
        return redirect()->route('groups.index');
    }
    public function addTeam(HttpRequest $request)
    {
        GroupTeam::create([
            'group_id' => $request->group_id,
            'team_id' => $request->team_id,
        ]);
        return redirect()->route('groups.index');
    }
    public function removeTeam(HttpRequest $request)
    {
        GroupTeam::where('group_id', $request->group_id)->where('team_id', $request->team_id)->delete();
        return redirect()->route('groups.index');
    }
    public function standing($group)
    {
        return $group;
        return view('teams.standing');
    }
}
