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
    public function index()
    {
        $groups = Group::with('teams')->orderBy('id', 'desc')->paginate(2);
        $allCompetitions = Competition::orderBy('id', 'desc')->get();
        $allTeams = Team::all();
        foreach ($groups as $group) {
            $competitionId = $group->competition_id;
            $round = $group->round;
            $competitionGroups = Competition::with(['groups' => function ($query) use ($round) {
                $query->where('round', $round);
            }, 'groups.teams'])->find($competitionId);
            $alreadyAddedTeams = $competitionGroups->groups->flatMap(function ($group) {
                return $group->teams;
            });
            $teamsNotAdded = $allTeams->diff($alreadyAddedTeams);
            $group->allowedAddedTeams = $teamsNotAdded;
        }
        return view('admin.groups', compact('groups', 'allCompetitions'));
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
    public function createGroupWithTeams(HttpRequest $request)
    {
        $number_of_qualifiers = $request->max_teams;

        $competition = Competition::with(['groups' => function ($query) {
            $query->where('round', Competition::GROUPS);
        }, 'groups.teams'])->find($request->competition_id);

        $newGroup = Group::create([
            'name' => 'winners from groups round competition ' . $competition->name,
            'round' => Competition::FINAL,
            'competition_id' => $competition->id,
        ]);

        foreach ($competition->groups as $existingGroup) {
            $sortedTeams = $existingGroup->teams->sortBy('name')->sortByDesc('pivot.points')->take($number_of_qualifiers);

            foreach ($sortedTeams as $team) {
                GroupTeam::create([
                    'group_id' => $newGroup->id,
                    'team_id' => $team->id,
                ]);
            }
        }
        return redirect()->route('competitions.show', ['competition' => $competition]);
    }
}
