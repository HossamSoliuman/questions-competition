@extends('layouts.admin')
@section('content')
    <div class="container mt-5">
        <div class="row justify-content-center mt-5">
            <div class="col-md-11">
                <h1>Tests</h1>
                <!-- Button trigger modal -->
                <button type="button" class=" mb-3 btn btn-primary" data-toggle="modal" data-target="#staticBackdrop">
                    Create a new Test
                </button>

                <!--Create Modal -->
                <div class="modal fade" id="staticBackdrop" data-backdrop="static" data-keyboard="false" tabindex="-1"
                    role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="staticBackdropLabel">New Test</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form action="{{ route('tests.store') }}" method="post">
                                    @csrf
                                    <div class="form-group">
                                        <input type="text" name="name" class="form-control"
                                            placeholder="Test Name" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="start_time">Start Time</label>
                                        <input type="datetime-local" name="start_time" class="form-control" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="question_time">Question Time</label>
                                        <input type="number" name="question_time" class="form-control" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="answer_time">Answer Time</label>
                                        <input type="number" name="answer_time" class="form-control" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="group">Group</label>
                                        <select name="group_id" class="form-control">
                                            @foreach ($groups as $group)
                                                <option value="{{ $group->id }}">{{ $group->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary">Submit</button>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            </div>
                            </form>

                        </div>
                    </div>
                </div>
                <!-- Edit Test Modal -->
                <div class="modal fade" id="editModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
                    role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editModalLabel">Edit Test</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form id="editForm" method="post">
                                    @csrf
                                    @method('PUT')
                                    <div class="form-group">
                                        <input type="text" name="name" class="form-control"
                                            placeholder="Test Name" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="start_time">Start Time</label>
                                        <input type="datetime-local" name="start_time" class="form-control" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="question_time">Question Time</label>
                                        <input type="number" name="question_time" class="form-control" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="answer_time">Answer Time</label>
                                        <input type="number" name="answer_time" class="form-control" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="group">Group</label>
                                        <select name="group_id" class="form-control">
                                            @foreach ($groups as $group)
                                                <option value="{{ $group->id }}">{{ $group->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-primary" id="saveChangesBtn">Save Changes</button>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            </div>
                            </form>

                        </div>
                    </div>
                </div>


                <table class="table">
                    <thead>
                        <tr>
                            <th>Test Name</th>
                            <th style="width: 15%; font-size:small">Start Time</th>
                            <th>Status</th>
                            <th>Question Time</th>
                            <th>Answer Time</th>
                            <th style="width: 15%; font-size:small">Group</th>
                            <th style="width: 15%; font-size:small">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($tests as $test)
                            <tr data-test-id="{{ $test->id }}"
                                data-test-start_time="{{ $test->start_time }}"
                                data-test-group_id="{{ $test->group->id }}">
                                <td class="test-name">{{ $test->name }}</td>
                                <td style="width: 15%; font-size:small" class="start-time">
                                    {{ Carbon\Carbon::parse($test->start_time)->format('Y-m-d h:i a') }}</td>
                                <td>{{$status[$test->status] }}
                                </td>
                                <td class="question-time">{{ $test->question_time }}</td>
                                <td class="answer-time">{{ $test->answer_time }}</td>
                                <td style="width: 15%; font-size:small" class="group-name">{{ $test->group->name }}</td>
                                <td style="width: 20%; font-size:small">

                                    <div class="d-flex">
                                        <button style="font-size:small" type="button" class="btn btn-warning btn-edit mr-2" data-toggle="modal"
                                            data-target="#editModal">
                                            Edit
                                        </button>
                                        <form
                                            action="{{ route('tests.destroy', ['test' => $test->id]) }}"
                                            method="post">
                                            @csrf
                                            @method('DELETE')
                                            <button style="font-size:small" type="submit" class="btn btn-danger">Delete</button>
                                        </form>

                                    </div>
                                    <div class="btn-group mr-2 mt-2" role="group" aria-label="Actions">
                                        <a style="font-size:small" class="btn btn-primary btn-view"
                                            href="{{ route('tests.questions', ['test' => $test->id]) }}">
                                            View Questions
                                        </a>
                                    </div>
                                </td>

                            </tr>
                        @endforeach

                    </tbody>
                </table>
                {{ $tests->links() }}
            </div>
        </div>
    </div>
    <!-- Add this script at the end of your HTML file or in a separate JS file -->
    <script>
        $(document).ready(function() {
            // Attach click event to each "Edit" button
            $('.btn-edit').on('click', function() {
                // Get the test name from the table row
                var testName = $(this).closest('tr').find('.test-name').text();
                var groupName = $(this).closest('tr').find('.group-name').text();
                var startTime = $(this).closest('tr').data('test-start_time');

                var questionTime = $(this).closest('tr').find('.question-time').text();
                var answerTime = $(this).closest('tr').find('.answer-time').text();

                // Set the test name in the edit modal input field
                $('#editModal input[name="name"]').val(testName);
                $('#editModal input[name="start_time"]').val(startTime);
                $('#editModal input[name="question_time"]').val(questionTime);
                $('#editModal input[name="answer_time"]').val(answerTime);
                $('#editModal input[name="group_name"]').val(groupName);


                // Get the test ID from the current table row
                var testId = $(this).closest('tr').data('test-id');

                // Set the form action with the dynamic test ID
                $('#editForm').attr('action', '/tests/' + testId);

                // Show the edit modal
                $('#editModal').modal('show');
            });

            // Attach click event to the "Save Changes" button
            $('#saveChangesBtn').on('click', function() {
                // Submit the form when the button is clicked
                $('#editForm').submit();
            });
        });
    </script>
@endsection
