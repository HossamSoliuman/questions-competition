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
