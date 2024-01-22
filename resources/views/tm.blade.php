<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your View</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body class="bg-light">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="page-header">
                <h1 class="display-4 text-center">Current Test:</h1>
            </div>
            <div class="container">
                <a href="">Exit Main Screen</a>
                <div class="row">
                    <div class="col">
                        <h2 class="section-title text-center">Current Test Group Standing</h2>
                        <div class="card test-card mb-5" id="test--1">
                            <div class="card-header">
                                <h3 class="test-title"></h3>
                            </div>
                            <div class="card-body">
                                <p class="test-start" id="start-time-">Starts:
                                </p>

                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <h2 class="section-title text-center">Audience Questions</h2>
                        <div class="row justify-content-center">
                            <div class="col-md-12">
                                <div id="audiences-question-container" class="bg-light p-4 rounded shadow mb-4">
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

                                <div id="audiences-answer-container" class="bg-light p-4 rounded shadow mb-4">
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

    </div>
        <!-- Bootstrap JS and jQuery -->
        <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
        <!-- Your custom script -->
    
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
                    url: '/manual-tests/6/get-audience-questions',
                    type: 'GET',
                    success: function(data) {
                        console.log(data);
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




</body>

</html>
