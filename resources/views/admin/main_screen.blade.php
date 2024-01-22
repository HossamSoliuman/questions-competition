@extends('layouts.app')
@section('content')
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="page-header">
                <h1 class="display-4 text-center">Current Test: {{ $test->name }}</h1>
            </div>
            <div class="container">
                <a href="{{ route('admin.index') }}">Exit Main Screen</a>
                <div class="row">
                    <div class="col">
                        <h2 class="section-title text-center">Current Test Group Standing</h2>
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
                    <div class="col">
                        <h2 class="section-title text-center">Audience Questions</h2>
                        <div class="row justify-content-center">
                            <div id="randomNumberBox" class="col-md-6 bg-info text-white p-3 rounded shadow mb-4">
                                <p class="mb-0">Random Number: <span style="font-size: 2em;" class="font-weight-bold" id="randomNumber"></span></p>
                            </div>

                            <div class="col-md-12">
                                <div id="audiences-question-container" class="bg-light p-4 rounded shadow mb-4 ">
                                    <h3 id="question" class="text-primary mb-4"></h3>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <div class="bg-white p-3 rounded shadow">
                                                <label for="a" class="option-label font-weight-bold">A:</label>
                                                <span id="a" class="form-control-plaintext option-text"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="bg-white p-3 rounded shadow">
                                                <label for="b" class="option-label font-weight-bold">B:</label>
                                                <span id="b" class="form-control-plaintext option-text"></span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <div class="bg-white p-3 rounded shadow">
                                                <label for="c" class="option-label font-weight-bold">C:</label>
                                                <span id="c" class="form-control-plaintext option-text"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="bg-white p-3 rounded shadow">
                                                <label for="d" class="option-label font-weight-bold">D:</label>
                                                <span id="d" class="form-control-plaintext option-text"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div id="audiences-answer-container" class="bg-light p-4 rounded shadow mb-4 ">
                                    <p class="mb-0">Correct Answer is: <span id="correct-answer"
                                            class="font-weight-bold text-success"></span></p>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- current teams view --}}
    <div class="row">
        @include('admin.manual_test_teams_view')
    </div>
@endsection
{{-- @section('scripts')

    <script>
        function updateTestData(testId, loopIndex) {
            $.ajax({
                url: '/admin/' + testId + '/update-tests-data',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.data && response.data.currentTests && Array.isArray(response.data.currentTests)) {
                        var testElement = $('#test-' + testId + '-' + loopIndex);
                        var teamListHtml = '';

                        $.each(response.data.currentTests, function(_, test) {
                            testElement.find('#start-time-' + testId).text('Starts: ' + test.start_time);

                            // Sort teams based on points (descending order)
                            test.group.teams.sort(function(a, b) {
                                return b.pivot.points - a.pivot.points;
                            });

                            $.each(test.group.teams, function(index, team) {
                                teamListHtml +=
                                    '<li class="list-group-item d-flex justify-content-between align-items-center">' +
                                    '<span class="badge badge-primary badge-pill">' + (index + 1) + '</span>' +
                                    '<span class="team-name">' + team.name + '</span>' +
                                    '<span class="badge badge-success badge-pill">' + team.pivot.points + '</span>' +
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

        $(document).ready(function() {
            function fetchData() {
                $.ajax({
                    url: '/manual-tests/6/get-audience-questions',
                    type: 'GET',
                    success: function(data) {
                        if (data.data.show_question) {
                            $('#audiences-question-container').show();
                        } else {
                            $('#audiences-question-container').hide();
                        }
                        if (data.data.show_answer) {
                            $('#audiences-answer-container').show();
                        } else {
                            $('#audiences-answer-container').hide();
                        }
                        $('#question').text(data.data.question.name);
                        $('#a').text(data.data.question.a);
                        $('#b').text(data.data.question.b);
                        $('#c').text(data.data.question.c);
                        $('#d').text(data.data.question.d);
                        $('#correct-answer').text(data.data.question.correct_answer);
                    },
                    error: function(xhr, status, error) {
                        console.error('Error fetching data:', error);
                    }
                });
            }
            fetchData();
            setInterval(fetchData, 5000);
        });

    </script>
@endsection --}}
