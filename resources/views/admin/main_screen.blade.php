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
                        <div class="row justify-content-center">
                            <div id="randomNumberBox" class="col-md-6 bg-info text-white p-3 rounded shadow mb-4">
                                <p class="mb-0">Random Number: <span style="font-size: 2em;" class="font-weight-bold"
                                        id="randomNumber"></span></p>
                            </div>

                            <div class="col-md-12">
                                <div id="audiences-question-container" class="bg-light p-4 rounded shadow mb-4 ">
                                    <h2 class="section-title text-center">Audience Questions</h2>
                                    <h3 id="audienceQuestion" class="text-primary mb-4"></h3>

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
                                    <p class="mb-0">Correct Answer is: <span id="audience-correct-answer"
                                            class="font-weight-bold text-success"></span></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

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
                </div>

            </div>
        </div>
    </div>
    <div class="row">
        @include('admin.manual_test_teams_view')
    </div>
@endsection
@section('scripts')
    <script>
        //update stading
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
                            testElement.find('#start-time-' + testId).text('Starts: ' + test
                                .start_time);

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
        //end updating standing

        //get audience quesiton
        $(document).ready(function() {
            function fetchData() {
                $.ajax({
                    url: '/manual-tests/{{ $test->id }}/get-audience-questions',
                    type: 'GET',
                    success: function(response) {
                        if (response.data == null) {
                            $('#audiences-answer-container').hide();
                            $('#audiences-question-container').hide();
                            $('#randomNumberBox').hide();

                            return;
                        }
                        if (response.data.random_number != 0) {
                            $('#randomNumberBox').show();
                            $('#randomNumber').text(response.data.random_number);
                        } else {
                            $('#randomNumberBox').hide();
                        }
                        if (response.data.question == null) {
                            $('#audiences-answer-container').hide();
                            $('#audiences-question-container').hide();
                            return;
                        }
                        $('#audienceQuestion').text(response.data.question.name);
                        $('#a').text(response.data.question.a);
                        $('#b').text(response.data.question.b);
                        $('#c').text(response.data.question.c);
                        $('#d').text(response.data.question.d);
                        $('#audience-correct-answer').text(response.data.question.correct_answer);

                        if (response.data.show_question) {
                            $('#audiences-question-container').show();
                        } else {
                            $('#audiences-question-container').hide();
                        }
                        if (response.data.show_answer) {
                            $('#audiences-answer-container').show();
                        } else {
                            $('#audiences-answer-container').hide();
                        }

                    },
                    error: function(xhr, status, error) {
                        console.error('Error fetching data:', error);
                    }
                });
            }
            fetchData();
            setInterval(fetchData, 1000);
        });
        //get audience quesiton end


        //teams view start
        var questionTimer = 0;
        var answerTimer = 0;
        var questionSecondsRemaining = 0;
        var answerSecondsRemaining = 0;
        var answerSubmitted = false;

        document.querySelector('.test-questions').style.display = 'none';
        document.querySelector('.answer-info').style.display = 'none';
        document.querySelector('.testEnded').style.display = 'none';

        function updateCountdown() {
            $.ajax({
                url: '/get-server-time',
                method: 'GET',
                success: function(response) {
                    var serverTime = new Date(response.server_time).getTime(); // Parse server time
                    var testStartTime = new Date('{{ $test->start_time }}').getTime();
                    var timeRemaining = testStartTime - serverTime;

                    if (timeRemaining > 0) {
                        var days = Math.floor(timeRemaining / (1000 * 60 * 60 * 24));
                        var hours = Math.floor((timeRemaining % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                        var minutes = Math.floor((timeRemaining % (1000 * 60 * 60)) / (1000 * 60));
                        var seconds = Math.floor((timeRemaining % (1000 * 60)) / 1000);

                        document.getElementById('countdown').innerHTML =
                            'Time remaining: ' + days + 'd ' + hours + 'h ' + minutes + 'm ' + seconds + 's';
                        setTimeout(updateCountdown, 1000);
                    } else {
                        document.getElementById('countdown').innerHTML = 'Test has started!';
                        var questionStartTime = new Date('{{ $test->question_start_at }}').getTime();
                        var remainingTime = questionStartTime - serverTime;
                        var questionRemaining = Math.max(remainingTime, 0);

                        setTimeout(function() {
                            getQuestion();
                        }, questionRemaining);
                    }
                }
            });
        }



        function updateQuestionTimerDisplay(seconds) {
            document.getElementById('question-timer').innerHTML = seconds;
        }

        function updateAnswerTimerDisplay(seconds) {
            document.getElementById('answer-timer').innerHTML = seconds;
        }

        function correctAnswer(questionId) {
            var formData = new FormData();
            formData.append('question_id', questionId);
            formData.append('_token', '{{ csrf_token() }}');

            var myRequest = new XMLHttpRequest();
            myRequest.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    var responseData = JSON.parse(this.responseText);


                    document.getElementById('answer-question').innerHTML = responseData.data.name;
                    document.getElementById('correct-answer').innerHTML = 'Correct Answer: ' + responseData.data
                        .correct_answer;
                    document.getElementById('corrcorrect-team-answer').innerHTML = 'First Correct Team Answer: ' +
                        responseData.data.corrcorrectTeamAnswer;


                    document.querySelector('.test-questions').style.display = 'none';
                    document.querySelector('.answer-info').style.display = 'block';

                    answerSecondsRemaining = {{ $test->answer_time }};
                    updateAnswerTimerDisplay(answerSecondsRemaining);

                    answerTimer = setInterval(function() {
                        updateAnswerTimerDisplay(--answerSecondsRemaining);
                        if (answerSecondsRemaining <= 0) {
                            clearInterval(answerTimer);
                            getQuestion();
                        }
                    }, 1000);
                }
                answerSubmitted = true;
            };

            var route =
                "{{ route('manual-test.answer', ['test' => $test->id, 'question' => ':question_id']) }}";
            route = route.replace(':question_id', questionId);

            myRequest.open("GET", route);
            myRequest.send(formData);
        }

        // Define a function to call getQuestion
        function startQuestionInterval() {
            getQuestion(); // Call getQuestion immediately

            // Set up an interval to call getQuestion every second
            setInterval(function() {
                getQuestion();
            }, 1000);
        }

        // Call the startQuestionInterval function to initiate the process

        // Call the startQuestionInterval function to initiate the process
        function getQuestion() {
            var myRequest = new XMLHttpRequest();
            myRequest.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    var responseData = JSON.parse(this.responseText);

                    if (responseData.data == null) {
                        setTimeout(function() {
                            getQuestion();
                        }, 1000)
                        console.log('equal null');
                        return;
                    }

                    if (responseData.data.question_id === null) {
                        document.querySelector('.testEnded').style.display = 'block';
                        return;
                    }

                    var serverTime = new Date(responseData.data.server_time).getTime(); // Get server time
                    var startTime = new Date(responseData.data.question_start_at).getTime();
                    var endTime = startTime + ({{ $test->question_time }} * 1000);
                    var remainingTime = endTime - serverTime; // Calculate remaining time based on server time
                    remainingTime = Math.ceil(remainingTime / 1000);
                    questionSecondsRemaining = Math.max(remainingTime, 0);

                    if (questionSecondsRemaining == 0) {
                        document.querySelector('.test-questions').style.display = 'none';
                        document.querySelector('.answer-info').style.display = 'none';
                        setTimeout(function() {
                            getQuestion();
                        }, 1000)
                        return;
                    }

                    var questionId = responseData.data.id;
                    document.getElementById('question').innerHTML = responseData.data.name;
                    document.getElementById('question_id').value = responseData.data.question_id;
                    document.getElementById('label-a').innerHTML = 'a: ' + responseData.data.a;
                    document.getElementById('label-b').innerHTML = 'b: ' + responseData.data.b;
                    document.getElementById('label-c').innerHTML = 'c: ' + responseData.data.c;
                    document.getElementById('label-d').innerHTML = 'd: ' + responseData.data.d;

                    document.querySelector('.test-questions').style.display = 'block';
                    document.querySelector('.answer-info').style.display = 'none';

                    updateQuestionTimerDisplay(questionSecondsRemaining);
                    clearInterval(questionTimer); // Clear previous timer if exists

                    questionTimer = setInterval(function() {
                        updateQuestionTimerDisplay(--questionSecondsRemaining);
                        if (questionSecondsRemaining <= 0) {
                            clearInterval(questionTimer);
                            correctAnswer(questionId);
                        }
                    }, 1000);
                }
            };

            myRequest.open("GET", "{{ route('manual-test.question', ['test' => $test->id]) }}");
            myRequest.send();

            clearInterval(answerTimer); // Clear answer timer
            answerSubmitted = false;
        }
        updateCountdown();

        function selectAnswer(answerId) {
            document.getElementById(answerId).checked = true;
        }
    </script>
@endsection
