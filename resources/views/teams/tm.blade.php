////////////
<script>

        function checkTimes() {
            var myRequest = new XMLHttpRequest();
            myRequest.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    var responseData = JSON.parse(this.responseText);

                    var startTime = new Date(responseData.data.question_start_at).getTime();

                    //question time remainding
                    var questionEndTime = startTime + ({{ $test->question_time }} * 1000);
                    var questionRemainingTime = questionEndTime - new Date().getTime();
                    questionRemainingTime = Math.ceil(questionRemainingTime / 1000);
                    questionSecondsRemaining = Math.max(questionRemainingTime, 0)

                    
                    //answer time remainding
                    var answerEndTime = startTime + ({{ $test->answer }} * 1000);
                    var answerRemainingTime = answerEndTime - new Date().getTime();
                    answerRemainingTime = Math.ceil(answerRemainingTime / 1000);
                    answerSecondsRemaining = Math.max(answerRemainingTime, 0)

                    setInterval(function() {
                        if (questionSecondsRemaining >= 0) {
                            getQuestion();
                        }
                        elseif(answerSecondsRemaining >= 0) {
                            correctAnswer(questionId);
                        } else {
                            document.querySelector('.test-questions').style.display = 'none';
                            document.querySelector('.answer-info').style.display = 'none';
                            checkTimes();
                        }
                    }, 1000);
                }
            };

            myRequest.open("GET", "{{ route('manual-test.question', ['test' => $test->id]) }}");
            myRequest.send();

        }

</script>
        /////////////
