@extends('layouts.app')

@section('content')
    <br/>
    <form action="{{ route('logout') }}" method="post">
        @csrf
        <button type="submit" class="btn btn-danger btn-block mb-4">Logout</button>
    </form>

    <div class="test-info text-center">
        <h2>{{ $test->name }}</h2>
        <p id="countdown"></p>
        {{--<p> Question time {{ $test->question_time }} seconds</p>
        <p> Answer time {{ $test->answer_time }} seconds</p>--}}
        <p style="display: none"> group id {{ $test->group_id }} </p>
    </div>

    {{-- question form start --}}
    <div class="test-questions">
        <div class="col-md-6 offset-md-3">
            <div class="question-container">
                <div class="timer-container">
                    <div class="timer" id="question-timer">25</div>
                </div>
                <form id="testForm"
                    action="{{ route('current-test.sendAnswer', ['test' => $test->id]) }}"
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

                        <input class="btn btn-danger btn-answer" type="submit" align="center" value="Answer"
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
@endsection
@section('scripts')
    <script>
        var questionTimer = 0;
        var answerTimer = 0;
        var questionSecondsRemaining = 0;
        var answerSecondsRemaining = 0;
        var answerSubmitted = false;

        document.querySelector('.test-questions').style.display = 'none';
        document.querySelector('.answer-info').style.display = 'none';

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

                // Set a timeout to call getQuestion after remainingTime has passed
                setTimeout(function() {
                    getQuestion();
                }, remainingTime * 1000); // Convert seconds to milliseconds

            }
        }

        function updateQuestionTimerDisplay(seconds) {
            document.getElementById('question-timer').innerHTML = seconds;
        }

        function updateAnswerTimerDisplay(seconds) {
            document.getElementById('answer-timer').innerHTML = seconds;
        }

        function correctAnswer(questionId) {
            // Create a new FormData instance
            var formData = new FormData();

            // Append the necessary data to formData
            formData.append('question_id', questionId);
            formData.append('_token', '{{ csrf_token() }}'); // Add CSRF token to the form data

            var myRequest = new XMLHttpRequest();
            myRequest.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    var responseData = JSON.parse(this.responseText);
                    document.getElementById('answer-question').innerHTML = responseData.data.name;
                    document.getElementById('correct-answer').innerHTML = 'Correct Answer: ' + responseData.data
                        .correct_answer;
                    document.getElementById('corrcorrect-team-answer').innerHTML = 'First Correct Team Answer: ' +
                        responseData.data.corrcorrectTeamAnswer;

                    // Hide question and show answer
                    document.querySelector('.test-questions').style.display = 'none';
                    document.querySelector('.answer-info').style.display = 'block';

                    // Set timer to load the next question after answer_time
                    answerSecondsRemaining = {{ $test->answer_time }};
                    updateAnswerTimerDisplay(answerSecondsRemaining);
                    answerTimer = setInterval(function() {
                        updateAnswerTimerDisplay(--answerSecondsRemaining);
                        if (answerSecondsRemaining <= 0) {
                            clearInterval(answerTimer);
                            getQuestion(); // Move to the next question
                        }
                    }, 1000);
                }
                answerSubmitted = true;
            };

            // Append the questionId and testId to the route
            var route =
                "{{ route('current-test.answer', ['test' => $test->id, 'question' => ':question_id']) }}";
            route = route.replace(':question_id', questionId);

            myRequest.open("GET", route);
            myRequest.send(formData);
        }


        function getQuestion() {
            var myRequest = new XMLHttpRequest();
            myRequest.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    var responseData = JSON.parse(this.responseText);
                    if (responseData.data.question_id === null) {
                        // Redirect user to the standing route
                        // window.location.href = "{{ route('handle-teams.index', ['group' => $test->group_id]) }}";
                        window.location.href = "{{ route('handle-teams.index') }}";
                        return; // Exit the function to prevent further execution
                    }
                    var questionId = responseData.data.id; // Extract the questionId

                    console.log(responseData.data.question_id);

                    document.getElementById('question').innerHTML = responseData.data.name;
                    document.getElementById('question_id').value = responseData.data.question_id;
                    document.getElementById('label-a').innerHTML = 'a: ' + responseData.data.a;
                    document.getElementById('label-b').innerHTML = 'b: ' + responseData.data.b;
                    document.getElementById('label-c').innerHTML = 'c: ' + responseData.data.c;
                    document.getElementById('label-d').innerHTML = 'd: ' + responseData.data.d;

                    // Show question and hide answer
                    document.querySelector('.test-questions').style.display = 'block';
                    document.querySelector('.answer-info').style.display = 'none';

                    var startTime = new Date(responseData.data.question_start_at).getTime();
                    var endTime = startTime + ({{ $test->question_time }} * 1000);
                    var remainingTime = endTime - new Date().getTime(); // Use client's timestamp
                    remainingTime = Math.ceil(remainingTime / 1000);

                    questionSecondsRemaining = Math.max(remainingTime, 0); // Ensure non-negative value
                    updateQuestionTimerDisplay(questionSecondsRemaining);
                    // Pass the questionId when calling correctAnswer
                    questionTimer = setInterval(function() {
                        updateQuestionTimerDisplay(--questionSecondsRemaining);
                        if (questionSecondsRemaining <= 0) {
                            clearInterval(questionTimer);
                            correctAnswer(questionId);
                        }
                    }, 1000);

                }
            };

            myRequest.open("GET", "{{ route('current-test.question', ['test' => $test->id]) }}");
            myRequest.send();

            // Clear the previous timers
            clearInterval(questionTimer);
            clearInterval(answerTimer);
            answerSubmitted = false;
            enableFormInputs();
        }

        updateCountdown();

        function selectAnswer(answerId) {
            document.getElementById(answerId).checked = true;
        }

        // submiting form start
        document.getElementById('testForm').addEventListener('submit', function(event) {
            event.preventDefault(); // Prevent default form submission

            // Get form data
            var formData = new FormData(this);

            // Make AJAX request
            var xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function() {
                if (this.readyState == 4) {
                    if (this.status == 200) {
                        // Handle successful response
                        console.log('Form submitted successfully');
                    } else {
                        // Handle error response
                        console.error('Form submission failed');
                    }
                }
            };
            xhr.open('POST', this.action);
            xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}'); // Add CSRF token header
            xhr.send(formData);

            // Disable form inputs
            disableFormInputs();
        });

        function disableFormInputs() {
            // Disable all input elements in the form
            var formInputs = document.getElementById('testForm').querySelectorAll('input');
            formInputs.forEach(function(input) {
                input.disabled = true;
            });
        }

        function enableFormInputs() {
            // Enable all input elements in the form
            var formInputs = document.getElementById('testForm').querySelectorAll('input');
            formInputs.forEach(function(input) {
                input.disabled = false;
            });
        }
        // submitting form end
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
            padding: 20px;
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
            margin-bottom: 10px;
        }

        .answer {
            flex: 1;
            box-shadow: 0 0 8px rgba(0, 0, 0, 0.1);
        }

        .btn-answer {
            width: 100%;
            padding: 10px;
            font-size: 16px;
        }
    </style>
@endsection
