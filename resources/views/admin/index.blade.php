@extends('layouts.admin')

@section('content')
    <div class="page-header">
        <h1 class="display-4 text-center">Explore Tests</h1>
    </div>

    <section id="current-tests">
        <div class="container">
            <h2 class="section-title">Current Tests</h2>
            @foreach ($currentTests as $test)
                <div class="card test-card mb-5" id="test-{{ $test->id }}-{{ $loop->index }}">
                    <div class="card-header">
                        <h3 class="test-title">{{ $test->name }}</h3>
                        <a href="{{ route('manual-tests.index', ['test' => $test->id]) }}">Enter Test</a>
                    </div>
                    <div class="card-body">
                        <p class="test-start" id="start-time-{{ $test->id }}">Starts:
                            {{ $test->start_time }}</p>
                        <ul class="list-group team-list">
                            @foreach ($test->group->teams->sortByDesc('pivot.points') as $team)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span class="badge badge-primary badge-pill">{{ $loop->iteration }}</span>
                                    <span class="team-name">{{ $team->name }}</span>
                                    <span class="badge badge-success badge-pill">{{ $team->pivot->points }}</span>
                                </li>
                                <hr class="my-1">
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <section id="upcoming-tests">
        <div class="container">
            <h2 class="section-title">Upcoming Tests</h2>
            @foreach ($upcomingTests->chunk(2) as $upcomingChunk)
                <div class="row">
                    @foreach ($upcomingChunk as $test)
                        <div class="col-md-6 mb-3">
                            <div class="card test-card" id="test-{{ $test->id }}">
                                <div class="card-header">
                                    <h3 class="test-title">{{ $test->name }}</h3>
                                    <a href="{{ route('manual-tests.index', ['test' => $test->id]) }}">Enter Test</a>

                                </div>
                                <div class="card-body">
                                    <p class="test-start" id="start-time-{{ $test->id }}">Starts:
                                        {{ $test->start_time }}</p>
                                    <ul class="list-group team-list">
                                        @foreach ($test->group->teams->sortByDesc('pivot.points') as $team)
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <span class="badge badge-primary badge-pill">{{ $loop->iteration }}</span>
                                                <span class="team-name">{{ $team->name }}</span>
                                                <span
                                                    class="badge badge-success badge-pill">{{ $team->pivot->points }}</span>
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
    </section>


@endsection
