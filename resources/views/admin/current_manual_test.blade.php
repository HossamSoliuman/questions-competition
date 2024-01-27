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
                    font-size: xx-small;
                    padding: 0%;
                    max-width: 100%;
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
                                                <input required type="radio" name="category_id"
                                                    value="{{ $category['id'] }}" id="category_{{ $category['id'] }}"
                                                    class="category-radio visually-hidden">
                                                <label for="category_{{ $category['id'] }}"
                                                    class="btn btn-outline-primary btn-block text-sm">{{ $category['name'] }}</label>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="col">
                                    <p>No more questions</p>
                                </div>
                            @endif
                        </div>
                        <div class="row">
                            @if ($categories->isNotEmpty())
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <button class="btn btn-primary btn-block text-md" type="submit">Set</button>
                                    </div>
                                </div>
                            @endif

                    </form>
                    <form id="endTestForm" action="{{ route('manual-test.endTest', ['test' => $test->id]) }}"
                        method="get">
                        <div class="col">
                            <div class="form-group">
                                <button class="btn btn-danger btn-block text-md" type="submit">End Test</button>
                            </div>

                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
    {{-- audience section  --}}
    <hr>
    <div class="row">
        <div class="col">
            <h3 class="mb-3">Audience Section</h3>

            <!-- Random Audience Number Form -->
            <form action="{{ route('manual-test.setRandomAudienceNumber', ['test' => $test->id]) }}" method="post">
                @csrf
                <div class="row">
                    <div class="form-group mr-3">
                        <label for="maxAudiences">Max Audiences:</label>
                        <input type="number" class="form-control" id="maxAudiences" name="maxAudiences"
                            placeholder="Enter max audiences" required>
                    </div>
                    <div class="form-group">
                        <label for="randomAudience">Random Audience Number:</label>
                        <input type="text" name="number" class="form-control" id="randomAudience" readonly>
                    </div>
                    <div class="d-flex">
                        <div class="form-group mr-3">
                            <button type="button" class="btn btn-success btn-sm" id="generateRandomAudience">Generate Random
                                Audience
                                Number</button>
                        </div>
                        <div class="form-group">
                            <input class="btn-primary btn btn-sm" type="submit" value="Show in main screen">
            </form>
        </div>
        <div class="">
            <form action="{{ route('manual-test.setRandomAudienceNumber', ['test' => $test->id]) }}" method="post">
                @csrf
                <input type="hidden" name="number" value="0">
                <input class="btn-danger btn mb-3 btn-sm" type="submit" value="Hide From Main Screen">
            </form>
        </div>
    </div>
    </div>

    <hr>
    <!-- Set Question Form -->
    <div class="row">

        <form id="setQuestionForm" action="{{ route('audience-questions.set') }}" method="post">
            @csrf
            <input type="hidden" name="test_id" value="{{ $test->id }}">
            <button type="submit" class="btn btn-primary mb-1">Set Question</button>
        </form>
        <button class="btn btn-info" id="showAudienceQuestion" data-show="1">Show Question</button>
        <button class="btn btn-warning" id="hideAudienceQuestion" data-show="0">Hide Question</button>
    </div>

    </div>

    <div class="col">
        <h3>Audience Correct Answer</h3>
        <!-- Audience Answer Form -->
        <form id="setAnswerForm" action="{{ route('audiences.store') }}" method="post">
            @csrf
            <input type="hidden" name="test_id" value="{{ $test->id }}">
            <div class="row space-between">
                <div class="col">
                    <div class="form-group">
                        <label for="audienceName">Audience Number</label>
                        <input type="text" name="number" id="audienceName" class="form-control" required>
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label for="audienceName">Audience Name</label>
                        <input type="text" name="name" id="audienceName" class="form-control" required>
                    </div>
                </div>
            </div>
            <div class="row space-between">
                <div class="col">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" name="email" id="email" class="form-control" required>
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label for="phone">Phone</label>
                        <input type="tel" name="phone" id="phone" class="form-control" required>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-success">Submit</button>
        </form>
        <!-- Show/Hide Answer Buttons -->
        <button class="btn btn-info" id="showAudienceAnswer" data-show="1">Show Answer</button>
        <button class="btn btn-warning" id="hideAudienceAnswer" data-show="0">Hide Answer</button>
    </div>
    </div>


    <div class="row">
        @include('admin.manual_test_teams_view')
    </div>
    </div>

@endsection

@section('scripts')
    <script>
        document.getElementById('generateRandomAudience').addEventListener('click', function() {
            var maxAudiences = document.getElementById('maxAudiences').value;
            var randomAudience = Math.floor(Math.random() * maxAudiences) + 1;
            document.getElementById('randomAudience').value = randomAudience;
        });


        document.getElementById('endTestForm').addEventListener('submit', function(event) {
            // Display a confirmation dialog before submitting the form
            if (!confirm('Are you sure you want to end the test?')) {
                event.preventDefault(); // Prevent form submission if the user cancels
            }
        });


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

        //start show or hide audienc questions
        $(document).ready(function() {
            $('#showAudienceQuestion').on('click', function() {
                var show = $(this).data('show');
                var testId = "{{ $test->id }}";
                console.log(show);

                $.get("/current-audience-questions/" + testId + "/" + show + "/show-question", function(
                    data) {

                });

            });
            $('#hideAudienceQuestion').on('click', function() {
                var show = $(this).data('show');
                var testId = "{{ $test->id }}";
                console.log(show);

                $.get("/current-audience-questions/" + testId + "/" + show + "/show-question", function(
                    data) {

                });

            });
            $('#showAudienceAnswer').on('click', function() {
                var show = $(this).data('show');
                var testId = "{{ $test->id }}";
                console.log(show);

                $.get("/current-audience-answers/" + testId + "/" + show + "/show-answer", function(data) {

                });

            });
            $('#hideAudienceAnswer').on('click', function() {
                var show = $(this).data('show');
                var testId = "{{ $test->id }}";
                console.log(show);

                $.get("/current-audience-answers/" + testId + "/" + show + "/show-answer", function(data) {

                });

            });

        });
        //end show or hide questions

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
            var startTime = new Date('{{ $test->start_time }}').getTime();
            var currentTime = new Date().getTime();
            var timeRemaining = startTime - currentTime;

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

                var startTime = new Date('{{ $test->question_start_at }}').getTime();
                var remainingTime = startTime - new Date().getTime();
                remainingTime = Math.ceil(remainingTime / 1000);

                questionRemaining = Math.max(remainingTime, 0);

                setTimeout(function() {
                    getQuestion();
                }, questionRemaining * 1000);

            }
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

        function getQuestion() {
            var myRequest = new XMLHttpRequest();
            myRequest.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    var responseData = JSON.parse(this.responseText);

                    if (responseData.data == null) {
                        setTimeout(function() {
                            getQuestion();
                        }, 1000)
                        console.log('equel null');
                        return;
                    }

                    if (responseData.data.question_id === null) {
                        document.querySelector('.testEnded').style.display = 'block';
                        return;
                    }

                    var startTime = new Date(responseData.data.question_start_at).getTime();
                    var endTime = startTime + ({{ $test->question_time }} * 1000);
                    var remainingTime = endTime - new Date().getTime();
                    remainingTime = Math.ceil(remainingTime / 1000);
                    questionSecondsRemaining = Math.max(remainingTime, 0)

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

                    var startTime = new Date(responseData.data.question_start_at).getTime();
                    var endTime = startTime + ({{ $test->question_time }} * 1000);
                    var remainingTime = endTime - new Date().getTime();
                    remainingTime = Math.ceil(remainingTime / 1000);

                    questionSecondsRemaining = Math.max(remainingTime, 0)
                    updateQuestionTimerDisplay(questionSecondsRemaining);


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

            clearInterval(questionTimer);
            clearInterval(answerTimer);
            answerSubmitted = false;
        }

        updateCountdown();

        function selectAnswer(answerId) {
            document.getElementById(answerId).checked = true;
        }
    </script>
@endsection
