@extends('layouts.app')
@section('content')
    <style>
        body {
            margin: 0;
            padding: 0;
        }

        .background-image-container {
            background-image: url('{{ asset('logo2.png') }}');
            background-size: contain;
            background-position: center;
            background-repeat: no-repeat;
            opacity: 0.5;
            height: 100vh;
            position: fixed;
            width: 100%;
            z-index: 4;
        }

        .a {
            z-index: 9999;
        }
    </style>
    <a class="text-center d-block" href="{{ route('admin.index') }}">الخروج من الشاشة الرئيسية</a>

    <div class="background-image-container"></div>

    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="page-header">
                <h1 class="display-4 text-center">{{ $test->group->competition->name }}</h1>
                <p class="text-center">وقت السؤال {{ $test->question_time }} ثانية</p>
                <p class="text-center">وقت الإجابة {{ $test->answer_time }} ثانية</p>
            </div>
            <div class="container">
                <div class="row">
                    <div class="col">
                        <div class="row justify-content-center">
                            <div id="randomNumberBox"
                                class="col-md-6 bg-info text-white p-3 rounded shadow mb-4 text-right">
                                <p class="mb-0" style="font-size: 2em;">رقم عشوائي: <span class="font-weight-bold"
                                        id="randomNumber"></span></p>
                            </div>
                            <div class="col-md-12 text-right">
                                <div id="audiences-question-container" class="bg-light p-4 rounded shadow mb-4 ">
                                    <h2 class="section-title text-center" style="font-size: 1.5em;">أسئلة الجمهور</h2>
                                    <h3 id="audienceQuestion" class="text-primary mb-4"></h3>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <div class="bg-white p-3 rounded shadow">
                                                <span id="b" style="font-size: 1.2em;"></span> :B
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="bg-white p-3 rounded shadow">
                                                <span id="a" style="font-size: 1.2em;"></span> :A
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <div class="bg-white p-3 rounded shadow">
                                                <span id="d" style="font-size: 1.2em;"></span> :D
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="bg-white p-3 rounded shadow">
                                                <span id="c" style="font-size: 1.2em;"></span> :C
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div id="audiences-answer-container" class="bg-light p-4 rounded shadow mb-4 ">
                                    <p style="font-size: 2.2em;" class="mb-0"><span id="audience-correct-answer"
                                            class="font-weight-bold text-success"></span> : الإجابة الصحيحة هي</p>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="row justify-content-center" >
                    <div class="col-md-6">
                        <div class="card test-card mb-5" id="test-{{ $test->id }}-1">
                            <div class="card-header">
                                <h3 class="test-title text-center">{{ $test->name }}</h3>
                            </div>
                            <div class="card-body">
                                <p class="test-start" id="start-time-{{ $test->id }}">Starts: {{ $test->start_time }}
                                </p>
                                <ul class="list-group team-list">
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span class="badge badge-primary badge-pill">الترتيب</span>
                                        <span class="team-name" style="font-size: larger; font-weight: bold;">الفريق</span>
                                        <span class="badge badge-success badge-pill">النقاط</span>
                                    </li>
                                    <hr class="my-1">
                                    @foreach ($test->group->teams->sortByDesc('pivot.points') as $team)
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span class="badge badge-primary badge-pill">{{ $loop->iteration }}</span>
                                            <span class="team-name"
                                                style="font-size: larger; font-weight: bold;">{{ $team->name }}</span>
                                            <span class="badge badge-success badge-pill">{{ $team->pivot->points }}</span>
                                        </li>
                                        <hr class="my-1">
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        @include('admin.main_screen_teams_view')
                    </div>
                </div>
            </div>
        </div>
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
                        var teamListHtml =
                            '<li class="list-group-item d-flex justify-content-between align-items-center">' +
                            '<span class="badge badge-primary badge-pill">الترتيب</span>' +
                            '<span style="font-size: larger; font-weight: bold;" class="team-name">الفريق</span>' +
                            '<span class="badge badge-success badge-pill">النقاط</span>' +
                            '</li>' +
                            '<hr class="my-1">';

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
                                    '<span style="font-size: larger; font-weight: bold;" class="team-name">' +
                                    team.name + '</span>' +
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
                        // document.getElementById('countdown').innerHTML = 'Test has started!';
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
                    var show_answers_delay = responseData.data.show_answers_delay;
                    console.log(responseData.data.category);
                    document.getElementById('category').innerHTML = responseData.data.category.name;
                    document.getElementById('question').innerHTML = responseData.data.name;
                    document.getElementById('question_id').value = responseData.data.question_id;
                    document.querySelector('.answers-container').style.display = 'none';

                    document.getElementById('label-a').innerHTML = responseData.data.a;
                    document.getElementById('label-b').innerHTML = responseData.data.b;
                    document.getElementById('label-c').innerHTML = responseData.data.c;
                    document.getElementById('label-d').innerHTML = responseData.data.d;

                    document.querySelector('.test-questions').style.display = 'block';
                    document.querySelector('.answer-info').style.display = 'none';

                    updateQuestionTimerDisplay(questionSecondsRemaining);
                    clearInterval(questionTimer); // Clear previous timer if exists

                    questionTimer = setInterval(function() {
                        $.ajax({
                            url: '/get-server-time',
                            method: 'GET',
                            success: function(response) {
                                var serverTime = new Date(response.server_time).getTime();

                                var startTime = new Date(responseData.data.question_start_at)
                                    .getTime();
                                var endTime = startTime + ({{ $test->question_time }} * 1000);
                                var remainingTime = endTime -
                                    serverTime;
                                remainingTime = Math.ceil(remainingTime / 1000);
                                questionSecondsRemaining = Math.max(remainingTime, 0);
                                document.getElementById('question-timer').innerHTML =
                                    questionSecondsRemaining;
                                if (({{ $test->question_time }} - show_answers_delay) >
                                    questionSecondsRemaining) {
                                    document.querySelector('.answers-container').style.display =
                                        'block';
                                }
                                if (questionSecondsRemaining <= 0) {
                                    clearInterval(questionTimer);
                                    correctAnswer(questionId);
                                }
                            }
                        });
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
