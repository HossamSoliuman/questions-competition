@extends('layouts.admin')

@section('content')
    <div class="container mt-5">
        <div class="row justify-content-center mt-5">
            <div class="col-md-11">
                <h1>Competition {{ $competition->name }}</h1>

                <div class="groups-round">
                    <h3>Groups Round</h3>
                    @php
                        $groups = $competition->groups->where('round', 'Groups Round')->chunk(3);
                    @endphp

                    @foreach ($groups as $groupRow)
                        <div class="row mb-3">
                            @foreach ($groupRow as $group)
                                <div class="col-md-4">
                                    <div class="card">
                                        <div class="card-body">
                                            <h5 class="card-title">{{ $group->name }}</h5>
                                            <ul class="list-group">
                                                @foreach ($group->teams->sortByDesc('pivot.points') as $team)
                                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                                        <span class="badge badge-info badge-pill">{{ $loop->iteration }}</span>
                                                        <span class="team-name">{{ $team->name }}</span>
                                                        <span class="badge badge-success badge-pill">{{ $team->pivot->points }}</span>
                                                    </li>
                                                    <!-- Separator line -->
                                                    <hr class="my-1">
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>

                <div class="groups-round">
                    <h3>Final Round</h3>
                    @php
                        $finalGroups = $competition->groups->where('round', 'Final Round')->chunk(3);
                    @endphp

                    @foreach ($finalGroups as $finalGroupRow)
                        <div class="row mb-3">
                            @foreach ($finalGroupRow as $finalGroup)
                                <div class="col-md-4">
                                    <div class="card">
                                        <div class="card-body">
                                            <h5 class="card-title">{{ $finalGroup->name }}</h5>
                                            <ul class="list-group">
                                                @foreach ($finalGroup->teams->sortByDesc('pivot.points') as $team)
                                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                                        <span class="badge badge-info badge-pill">{{ $loop->iteration }}</span>
                                                        <span class="team-name">{{ $team->name }}</span>
                                                        <span class="badge badge-success badge-pill">{{ $team->pivot->points }}</span>
                                                    </li>
                                                    <!-- Separator line -->
                                                    <hr class="my-1">
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    <script></script>
@endsection
