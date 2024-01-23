<div class="container-fluid">
    <br>
    <hr class="bold">
    <h2 class="text-center">Current Teams view</h2>
    <div class="row">
        <div class="col">
            <div class="test-info text-center">
                <h2>{{ $test->name }}</h2>
                <p id="countdown"></p>
                <p> Question time {{ $test->question_time }} seconds</p>
                <p> Answer time {{ $test->answer_time }} seconds</p>
                <p style="display: none"> group id {{ $test->group_id }} </p>
            </div>
        </div>
    </div>
    <div class=" mb-5">
        <div class="testEnded alert alert-danger text-center">
            <p class="mb-0">Test has ended</p>
            <p>Moved to groups standings view</p>
        </div>
        {{-- question form start --}}
        <div class="test-questions">
            <div class="col-md-6 offset-md-3">
                <div class="question-container">
                    <div class="timer-container">
                        <div class="timer" id="question-timer">25</div>
                    </div>
                    <form id="testForm" action="{{ route('manual-test.sendAnswer', ['test' => $test->id]) }}"
                        method="post">
                        @csrf
                        <h3 class="question" id="question">Question</h3>
                        <div class="answers-container">
                            <input type="hidden" name="question_id" id="question_id" value="">
                            <input type="hidden" name="team_id" value="{{ $team_id }}">

                            <div class="answer-row">
                                <label for="answer-a" class="btn btn-primary btn-answer mr-3">
                                    <input type="radio" name="answer" id="answer-a" value="a"
                                        {{ $answerSubmitted ? 'disabled' : '' }}>
                                    <label for="answer-a" id="label-a">Answer 1</label>
                                </label>
                                <label for="answer-b" class="btn btn-primary btn-answer mr-3">
                                    <input type="radio" name="answer" id="answer-b" value="b"
                                        {{ $answerSubmitted ? 'disabled' : '' }}>
                                    <label for="answer-b" id="label-b">Answer 2</label>
                                </label>
                            </div>

                            <div class="answer-row">
                                <label for="answer-c" class="btn btn-primary btn-answer mr-3">
                                    <input type="radio" name="answer" id="answer-c" value="c"
                                        {{ $answerSubmitted ? 'disabled' : '' }}>
                                    <label for="answer-c" id="label-c">Answer 3</label>
                                </label>
                                <label for="answer-d" class="btn btn-primary btn-answer">
                                    <input type="radio" name="answer" id="answer-d" value="d"
                                        {{ $answerSubmitted ? 'disabled' : '' }}>
                                    <label for="answer-d" id="label-d">Answer 4</label>
                                </label>
                            </div>

                            <input hidden class="btn btn-danger btn-answer" type="submit" value="Answer"
                                {{ $answerSubmitted ? 'disabled' : '' }}>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        {{-- question form end --}}

        <div class="answer-info">
            <div class="col-md-6 offset-md-3">
                <div class="question-container">
                    <div class="timer-container">
                        <div class="timer" id="answer-timer">25</div>
                    </div>

                    <h3 class="question" id="corrcorrect-team-answer">
                        </h2>
                        <h3 class="question" id="answer-question"></h3>
                        <p class="btn btn-primary btn-answer mr-3" id="correct-answer"></p>
                </div>
            </div>
        </div>
    </div>
</div>
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
                            testElement.find('#start-time-' + testId).text('Starts: ' + test
                                .start_time);

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
                        if (response.data.random_number !=0 ) {
                            $('#randomNumberBox').show();
                            $('#randomNumber').text(response.data.random_number);
                        }else{
                            $('#randomNumberBox').hide();
                        }
                        if (response.data.question == null) {
                            $('#audiences-answer-container').hide();
                            $('#audiences-question-container').hide();
                            return;
                        }
                        $('#question').text(response.data.question.name);
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
@section('style')
    <style>
        .hidden-radio {
            position: absolute;
            opacity: 0;
            width: 0;
            height: 0;
        }

        .btn-answer {
            cursor: pointer;
            position: relative;
        }

        .btn-answer label {
            cursor: pointer;
        }

        body {
            background-color: #f8f9fa;
        }

        .question-container {
            border: 1px solid #dee2e6;
            border-radius: 10px;
            padding: 30px;
            /* Increase padding for the question container */
            margin-top: 50px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .question {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .timer-container {
            border-radius: 50%;
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: #28a745;
            box-shadow: 0 0 8px rgba(0, 0, 0, 0.2);
        }

        .timer {
            font-size: 16px;
            font-weight: bold;
            color: #ffffff;
        }

        .answers-container {
            display: flex;
            flex-direction: column;
            margin-top: 20px;
        }

        .answer-row {
            display: flex;
            margin-bottom: 15px;
            /* Increase margin between answer rows */
        }

        .btn-answer {
            flex: 1;
            box-shadow: 0 0 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            padding: 15px;
            /* Increase padding for the answer buttons */
            font-size: 18px;
            /* Increase font size for the answer buttons */
        }

        .answer-info {
            display: none;
        }
    </style>
@endsection
