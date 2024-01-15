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
@section('scripts')
    <script>
        function updateTestData(testId, loopIndex) {
            $.ajax({
                url: '/admin/' + testId + '/update-tests-data',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.data && response.data.currentTests && Array.isArray(response.data
                            .currentTests)) {
                        var testElement = $('#test-' + testId + '-' + loopIndex);
                        var teamListHtml = '';

                        $.each(response.data.currentTests, function(_, test) {
                            testElement.find('#start-time-' + testId).text('Starts: ' +
                                test.start_time);

                            // Sort teams based on points (descending order)
                            test.group.teams.sort(function(a, b) {
                                return b.pivot.points - a.pivot.points;
                            });

                            $.each(test.group.teams, function(index, team) {
                                teamListHtml +=
                                    '<li class="list-group-item d-flex justify-content-between align-items-center">' +
                                    '<span class="badge badge-primary badge-pill">' + (index +
                                        1) + '</span>' +
                                    '<span class="team-name">' + team.name + '</span>' +
                                    '<span class="badge badge-success badge-pill">' + team.pivot
                                    .points + '</span>' +
                                    '</li>' +
                                    '<hr class="my-1">';
                            });
                        });

                        testElement.find('.team-list').html(teamListHtml);
                    } else {
                        console.error('Invalid response format:', response);
                    }
                },

                error: function(error) {
                    console.error('Error updating test data:', error);
                }
            });
        }

        $(document).ready(function() {
            $('.test-card').each(function() {
                var testId = $(this).attr('id').split('-')[1];
                var loopIndex = $(this).attr('id').split('-')[2];
                setInterval(function() {
                    updateTestData(testId, loopIndex);
                }, 5000);
            });
        });
    </script>
@endsection

@endsection
