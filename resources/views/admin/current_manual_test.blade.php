@extends('layouts.admin')

@section('content')
    <div class="page-header">
        <h1 class="display-4 text-center">Current Test</h1>
    </div>

    {{-- <section id="current-tests"> --}}
    <div class="container">
        <div class="row">
            {{-- group card --}}
            <div class="col">
                <h2 class="section-title">Current Test Group Standing</h2>
                <div class="card test-card mb-5" id="test-{{ $test->id }}-1">
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
                                    <span class="badge badge-success badge-pill">{{ $team->pivot->points }}</span>
                                </li>
                                <hr class="my-1">
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            {{-- end group card --}}

            <style>
                .category-card label {
                    white-space: nowrap;
                    /* overflow: hidden; */
                    /* text-overflow: ellipsis; */
                    font-size: xx-small;
                    padding: 0%;
                    max-width: 100%;
                    /* Allow variable width up to 100% */
                }

                .category-card {
                    border: none;
                    /* Remove card border */
                }

                .category-card .card-body {
                    padding: 0;
                    /* Remove padding from card body */
                }
            </style>

            <div class="col">
                <h2 class="section-title">Current Test Group Questions</h2>
                <div class="card-body">
                    <form action="{{ route('manual-tests.setQuestion') }}" method="post">
                        @csrf
                        <div class="row">
                            @if ($categories->isNotEmpty())
                                @foreach ($categories as $category)
                                    <div class="col-md-4">
                                        <div class="card mb-3 category-card">
                                            <div class="card-body text-center p-0">
                                                <input type="hidden" name="test_id" value="{{ $test->id }}">
                                                <input type="radio" name="category_id" value="{{ $category['id'] }}"
                                                    id="category_{{ $category['id'] }}"
                                                    class="category-radio visually-hidden">
                                                <label for="category_{{ $category['id'] }}"
                                                    class="btn btn-outline-primary btn-block text-sm">{{ $category['name'] }}</label>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                                <div class=" col-md-4">
                                    <div class="form-group">
                                        <button class="btn btn-primary btn-block text-md" type="submit">Set</button>
                                    </div>
                                </div>
                            @else
                                <div class="col">
                                    <p>No more questions</p>
                                </div>
                            @endif
                        </div>
                    </form>
                </div>
                <form action="{{ route('manual-test.endTest', ['test' => $test->id]) }}" method="get">
                    <div class="form-group">
                        <button class="btn btn-danger btn-block text-md" type="submit">End Test</button>
                    </div>
                </form>
            </div>


        </div>
    </div>
    <div class="row">
        @include('admin.manual_test_teams_view')
    </div>
    </div>
    {{-- </section> --}}
    {{-- current teams view --}}

@endsection

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
