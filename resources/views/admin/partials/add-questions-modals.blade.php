<!-- Add Question Options Modal -->
<div class="modal fade" id="addQuestionOptionsModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
    role="dialog" aria-labelledby="addQuestionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addQuestionModalLabel">Add Question Options</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <button type="button" class=" mb-3 btn btn-primary" data-toggle="modal"
                    data-target="#addQuestionsManuallyModal">
                    Select the Questions Manually
                </button>
                <button type="button" class=" mb-3 btn btn-primary" data-toggle="modal"
                    data-target="#addQuestionsAutoModal">
                    Select the Questions Automaticlly by the system
                </button>
                <button type="button" class=" mb-3 btn btn-primary" data-toggle="modal"
                    data-target="#addQuestionsCategoriesModal">
                    Select the Questions Manually by the Categories
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Add Questions Manually Modal -->
<div class="modal fade" id="addQuestionsManuallyModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
    role="dialog" aria-labelledby="addQuestionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addQuestionModalLabel">Add Question</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('tests.questions.add') }}" method="post">
                    @csrf
                    <input type="hidden" name="test_id" value="{{ $test->id }}">

                    @foreach ($allQuestions as $question)
                        <div class="form-check">
                            <input name="questions_id[]" class="form-check-input" type="checkbox"
                                value="{{ $question->id }}" id="question_{{ $question->id }}">
                            <label class="form-check-label" for="question_{{ $question->id }}">
                                <strong>{{ $question->category->name }}:</strong> {{ $question->name }}
                            </label>
                        </div>
                    @endforeach
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Submit</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Add Questions by Categories Modal -->
<div class="modal fade" id="addQuestionsCategoriesModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
    role="dialog" aria-labelledby="addQuestionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addQuestionModalLabel">Add Questions By Category</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('tests-questions.category') }}" method="post">
                    @csrf
                    <input type="hidden" name="test_id" value="{{ $test->id }}">
                    <div id="fields-container">
                        <div class="field-row">
                            <div class="form-row mb-1">
                                <div class="col-5">
                                    <input type="text" name="number_of_questions[]" class="form-control"
                                        placeholder="Number of questions" required>
                                </div>
                                <div class="col">
                                    <select name="categories[]" class="form-control" required>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col">
                                    <button type="button" class="btn btn-danger remove-field">Remove</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-success" id="add-field">Add Field</button>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Submit</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Add Questions Auto Modal -->
<div class="modal fade" id="addQuestionsAutoModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
    role="dialog" aria-labelledby="addQuestionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addQuestionModalLabel">Add Question</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('tests-questions.auto') }}" method="post">
                    @csrf
                    <input type="hidden" name="test_id" value="{{ $test->id }}">
                    <div class="form-check">
                        <label for="">Number of questions</label>
                        <input name="number_of_questions" class="form-input" type="text">
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
