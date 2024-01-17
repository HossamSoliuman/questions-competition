@extends('layouts.admin')
@section('content')
    <div class="container mt-5">
        <div class="row justify-content-center mt-5">
            <div class="col-md-11">
                <h1>Questions</h1>
                <!-- Button trigger modal -->
                <button type="button" class=" mb-3 btn btn-primary" data-toggle="modal" data-target="#staticBackdrop">
                    Create a new Question
                </button>

                <!-- Modal -->
                <div class="modal fade" id="staticBackdrop" data-backdrop="static" data-keyboard="false" tabindex="-1"
                    role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="staticBackdropLabel">New Question</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form action="{{ route('questions.store') }}" method="post">
                                    @csrf
                                    <div class="form-group">
                                        <select name="category_id" class="form-control" placeholder="Question Category">
                                            @foreach ($categories as $category)
                                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <input type="text" name="name" class="form-control" placeholder="Question"
                                            required>
                                    </div>
                                    <div class="form-group">
                                        <input type="text" name="a" class="form-control" placeholder="Answer A"
                                            required>
                                    </div>
                                    <div class="form-group">
                                        <input type="text" name="b" class="form-control" placeholder="Answer B"
                                            required>
                                    </div>
                                    <div class="form-group">
                                        <input type="text" name="c" class="form-control" placeholder="Answer C"
                                            required>
                                    </div>
                                    <div class="form-group">
                                        <input type="text" name="d" class="form-control" placeholder="Answer D"
                                            required>
                                    </div>
                                    <div class="form-group">
                                        <select name="correct_answer" class="form-control" placeholder="Correct Answer">
                                            <option value="a">A</option>
                                            <option value="b">B</option>
                                            <option value="c">C</option>
                                            <option value="d">D</option>
                                        </select>
                                    </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary">Submit</button>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Edit Category Modal -->
                <div class="modal fade" id="editModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
                    role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editModalLabel">Edit Question</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form id="editForm" method="post">
                                    @csrf
                                    @method('PUT')
                                    <div class="form-group">
                                        <select name="category_id" class="form-control" placeholder="Question Category">
                                            @foreach ($categories as $category)
                                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <input type="text" name="name" class="form-control" placeholder="Question"
                                            required>
                                    </div>
                                    <div class="form-group">
                                        <input type="text" name="a" class="form-control" placeholder="Answer A"
                                            required>
                                    </div>
                                    <div class="form-group">
                                        <input type="text" name="b" class="form-control" placeholder="Answer B"
                                            required>
                                    </div>
                                    <div class="form-group">
                                        <input type="text" name="c" class="form-control" placeholder="Answer C"
                                            required>
                                    </div>
                                    <div class="form-group">
                                        <input type="text" name="d" class="form-control" placeholder="Answer D"
                                            required>
                                    </div>
                                    <div class="form-group">
                                        <select name="correct_answer" class="form-control" placeholder="Correct Answer">
                                            <option value="a">A</option>
                                            <option value="b">B</option>
                                            <option value="c">C</option>
                                            <option value="d">D</option>
                                        </select>
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-primary" id="saveChangesBtn">Save Changes</button>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
                <table class="table table-bordered table-hover">
                    <thead class="thead-dark">
                        <tr>
                            <th>Repeats</th>
                            <th style="width: 15%; font-size:small">Question Category</th>
                            <th style="width: 15%;">Question</th>
                            <th style="width: 10%;">Answer A</th>
                            <th style="width: 10%;">Answer B</th>
                            <th style="width: 10%;">Answer C</th>
                            <th style="width: 10%;">Answer D</th>
                            <th style="width: 10%;font-size:x-small">Correct Answer</th>
                            <th style="width: 20%;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($questions as $question)
                            <tr style="font-size:x-small" data-question-id="{{ $question->id }}"
                                data-question-category-id="{{ $question->category->id }}">
                                <td>{{ $question->repeated }}</td>
                                <td class="question-category">{{ $question->category->name }}</td>
                                <td class="question-name">{{ $question->name }}</td>
                                <td class="answer-a">{{ $question->a }}</td>
                                <td class="answer-b">{{ $question->b }}</td>
                                <td class="answer-c">{{ $question->c }}</td>
                                <td class="answer-d">{{ $question->d }}</td>
                                <td class="correct-answer">{{ $question->correct_answer }}</td>
                                <td class="d-flex">
                                    <button type="button" class="btn btn-warning btn-sm btn-edit" data-toggle="modal"
                                        data-target="#editModal">
                                        Edit
                                    </button>
                                    <form action="{{ route('questions.destroy', ['question' => $question->id]) }}"
                                        method="post">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="ml-2 btn btn-danger btn-sm">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ $questions->links() }}

            </div>
        </div>
    </div>
    <!-- Add this script at the end of your HTML file or in a separate JS file -->
    <script>
        $(document).ready(function() {
            // Attach click event to each "Edit" button
            $('.btn-edit').on('click', function() {
                // Get the question details from the table row
                var questionName = $(this).closest('tr').find('.question-name').text();
                var answerA = $(this).closest('tr').find('.answer-a').text();
                var answerB = $(this).closest('tr').find('.answer-b').text();
                var answerC = $(this).closest('tr').find('.answer-c').text();
                var answerD = $(this).closest('tr').find('.answer-d').text();
                var correctAnswer = $(this).closest('tr').find('.correct-answer').text();
                var categoryId = $(this).closest('tr').data('question-category-id');

                // Set the question details in the edit modal form fields
                $('#editModal input[name="name"]').val(questionName);
                $('#editModal input[name="a"]').val(answerA);
                $('#editModal input[name="b"]').val(answerB);
                $('#editModal input[name="c"]').val(answerC);
                $('#editModal input[name="d"]').val(answerD);
                $('#editModal select[name="correct_answer"]').val(correctAnswer);
                $('#editModal select[name="category_id"]').val(categoryId);

                // Get the question ID from the current table row
                var questionId = $(this).closest('tr').data('question-id');

                // Set the form action with the dynamic question ID
                $('#editForm').attr('action', '/questions/' + questionId);

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
