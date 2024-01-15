@extends('layouts.app')
@section('content')
    <form action="{{ route('logout') }}" method="post">
        @csrf
        <button type="submit" class="btn btn-danger btn-block mb-4">Logout</button>
    </form>

    <div class="container">
        <h3>Upcomming Tests</h3>
        <div class="row">
            @foreach ($commingTests as $test)
                <div class="col-md-4 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">{{ $test->name }}</h5>
                            <a href="{{ route('tests.view', ['test' => $test->id]) }}">Enter
                                Test</a>
                            <p class="card-text">
                                <strong>Start Time:</strong>
                                {{ Carbon\Carbon::parse($test->start_time)->format('Y-m-d h:i a') }}<br>
                                <strong>Group:</strong> {{ $test->group->name }}
                            </p>
                            <ul class="list-group">
                                @foreach ($test->group->teams->sortByDesc('pivot.points') as $team)
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
        <h3>Past Tests</h3>
        <div class="row">
            @foreach ($pastTests as $test)
                <div class="col-md-4 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">{{ $test->name }}</h5>
                            <p class="card-text">
                                <strong>Start Time:</strong>
                                {{ Carbon\Carbon::parse($test->start_time)->format('Y-m-d h:i a') }}<br>
                                <strong>Group:</strong> {{ $test->group->name }}
                            </p>
                            <ul class="list-group">
                                @foreach ($test->group->teams->sortByDesc('pivot.points') as $team)
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
    </div>
@endsection
