@extends('layouts.app')

@section('content')
    <br />
    <form action="{{ route('logout') }}" method="post">
        @csrf
        <button type="submit" class="btn btn-danger btn-block mb-4">تسجيل خروج</button>
    </form>

    <div class="test-info text-center" dir='rtl'>
        <h1 class="text-center" style="color : #9F8C76">{{ $team_name }}</h1>
        <h2 Style="color:#C19A6B">{{ $test->name }}</h2>
        <p id="countdown"></p>
        {{-- <p> Question time {{ $test->question_time }} seconds</p>
        <p> Answer time {{ $test->answer_time }} seconds</p>
        <p class="text-center">وقت السؤال {{ $test->question_time }} ثانية</p>
        <p class="text-center">وقت الإجابة {{ $test->answer_time }} ثانية</p> --}}
        <p style="display: none"> group id {{ $test->group_id }} </p>
    </div>

    {{-- question form start --}}
    <div class="row justify-content-center" dir='rtl'>
        <div class="test-questions">
            <div class="col">
                <div class="question-container">
                    <div class="timer-container">
                        <div class="timer" id="question-timer">25</div>
                    </div>
                    <form id="testForm" action="{{ route('manual-test.sendAnswer', ['test' => $test->id]) }}"
                        method="post">
                        @csrf
                        {{-- <h3 class="question text-center" id="category">Category</h3>
                        <h3 class="question mt-5 text-right" id="question">Question</h3> --}}
                        <h2 class="text-center" style="font-size: 6mm; font-weight: bold; color:#9F8C76" id="category">
                            category</h2>
                        <h3 class="mt-5 question text-right" style="font-size: 5mm; font-weight: bold; color:#C19A6B"
                            id="question">Question</h3>
                        <div class="answers-container">
                            <input type="hidden" name="question_id" id="question_id" value="">
                            <input type="hidden" name="team_id" value="{{ $team_id }}">

                            <div class="answer-row">
                                <label for="answer-a" class="btn btn-light btn-answer mr-3"
                                    style="color:#786D5F; font-size:6mm; font-weight:bold">
                                    <input type="radio" name="answer" id="answer-a" value="a"
                                        {{ $answerSubmitted ? 'disabled' : '' }}>
                                    <label for="answer-a" id="label-a">Answer 1</label>
                                </label>
                                <label for="answer-b" class="btn btn-light btn-answer mr-3"
                                    style="color:#786D5F; font-size:6mm; font-weight:bold">
                                    <input type="radio" name="answer" id="answer-b" value="b"
                                        {{ $answerSubmitted ? 'disabled' : '' }}>
                                    <label for="answer-b" id="label-b">Answer 2</label>
                                </label>
                            </div>

                            <div class="answer-row">
                                <label for="answer-c" class="btn btn-light btn-answer mr-3"
                                    style="color:#786D5F; font-size:6mm; font-weight:bold">
                                    <input type="radio" name="answer" id="answer-c" value="c"
                                        {{ $answerSubmitted ? 'disabled' : '' }}>
                                    <label for="answer-c" id="label-c">Answer 3</label>
                                </label>
                                <label for="answer-d" class="btn btn-light btn-answer mr-3"
                                    style="color:#786D5F; font-size:6mm; font-weight:bold">
                                    <input type="radio" name="answer" id="answer-d" value="d"
                                        {{ $answerSubmitted ? 'disabled' : '' }}>
                                    <label for="answer-d" id="label-d">Answer 4</label>
                                </label>
                            </div>
                            <div class="text-center">
                                <input class="btn btn-danger btn-answer" type="submit" value="إعتمد الإجابة"
                                    {{ $answerSubmitted ? 'disabled' : '' }}>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    {{-- question form end --}}

    <div class="row justify-content-center" dir='rtl'>
        <div class="col text-center">
            <div class="answer-info">
                <div class="question-container">
                    <div class="timer-container">
                        <div class="timer" id="answer-timer">25</div>
                    </div>
                    <h3 class="question" style="font-size: 5mm; font-weight: bold; color:#9F8C76"
                        id="corrcorrect-team-answer"></h3>
                    {{-- <h3 class="question" style="font-size: 5mm; font-weight: bold; color:#C19A6B" id="answer-question"></h3> --}}
                    <p class="btn btn-light mr-3" style="color:#786D5F; font-size:6mm; font-weight:bold"
                        id="correct-answer"></p>
                </div>
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
            $.ajax({
                url: '/get-server-time',
                method: 'GET',
                success: function(response) {
                    var serverTime = new Date(response.server_time).getTime();
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
                        //document.getElementById('countdown').innerHTML = 'بدأ الإختبار';
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


                    {{-- document.getElementById('answer-question').innerHTML = responseData.data.name; --}}
                    document.getElementById('correct-answer').innerHTML = 'الإجابة الصحيحة هي: ' + responseData.data
                        .correct_answer;
                    document.getElementById('corrcorrect-team-answer').innerHTML = 'الإجابة الأسرع للفريق: ' +
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
                        window.location.href = "{{ route('handle-teams.index') }}";
                        return;
                    }

                    var serverTime = new Date(responseData.data.server_time).getTime();
                    var startTime = new Date(responseData.data.question_start_at).getTime();
                    var endTime = startTime + ({{ $test->question_time }} * 1000);
                    var remainingTime = endTime - serverTime;
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
                    document.getElementById('category').innerHTML = responseData.data.category.name;
                    document.getElementById('question').innerHTML = responseData.data.name;
                    document.getElementById('question_id').value = responseData.data.question_id;
                    document.querySelector('.answers-container').style.display = 'none';
                    document.getElementById('label-a').innerHTML = responseData.data.a;
                    document.getElementById('label-b').innerHTML = responseData.data.b;
                    document.getElementById('label-c').innerHTML = responseData.data.c;
                    document.getElementById('label-d').innerHTML = responseData.data.d;
                    enableFormInputs()

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
                                if (({{ $test->question_time }} - show_answers_delay) >
                                    questionSecondsRemaining) {
                                    document.querySelector('.answers-container').style.display =
                                        'block';
                                }
                                document.getElementById('question-timer').innerHTML =
                                    questionSecondsRemaining;
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

        /*.question {
                        font-size: 18px;
                        font-weight: bold;
                        margin-bottom: 20px;
                    }*/

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
        }

        /*.answer {
                        flex: 1;
                        box-shadow: 0 0 8px rgba(0, 0, 0, 0.1);
                    }*/

        .btn-answer {
            width: 100%;
            padding: 10px;
            font-size: 16px;
        }
    </style>
@endsection
