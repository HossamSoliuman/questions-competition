@extends('layouts.admin')

@section('content')
    <div class="container mt-5">
        <div class="row justify-content-center mt-5">
            <div class="col-md-11">
                <h1>Test Questions</h1>

                <!-- Button trigger modal -->
                <button type="button" class="mb-3 btn btn-primary" data-toggle="modal" data-target="#addQuestionOptionsModal">
                    Add a Question Options
                </button>

                {{-- Add Questions Options Modals --}}
                @include('admin.partials.add-questions-modals')

                <!-- Questions Table -->
                <table class="table table-bordered table-hover">
                    <thead class="thead-dark">
                        <tr>
                            <th style="width: 15%; font-size: small">Question Category</th>
                            <th style="width: 15%;">Question</th>
                            <th colspan="4" style="width: 40%;">Answers A, B, C, D</th>
                            <th style="width: 10%; font-size: x-small">Correct Answer</th>
                            <th style="width: 20%;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($test->questions as $questionTest)
                            <tr style="font-size: x-small">
                                <td class="question-category">{{ $questionTest->category->name }}</td>
                                <td class="question-name">{{ $questionTest->name }}</td>
                                <td colspan="4" class="answer">{{ $questionTest->a }}, {{ $questionTest->b }}, {{ $questionTest->c }}, {{ $questionTest->d }}</td>
                                <td class="correct-answer">{{ $questionTest->correct_answer }}</td>
                                <td class="d-flex">
                                    <form action="{{ route('tests-questions.remove', ['questionTest' => $questionTest->pivot->id]) }}" method="post">
                                        @csrf
                                        <button type="submit" class="ml-2 btn btn-danger btn-sm">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('add-field').addEventListener('click', function() {
            const fieldsContainer = document.getElementById('fields-container');
            const fieldRow = document.querySelector('.field-row').cloneNode(true);
            fieldsContainer.appendChild(fieldRow);
        });

        document.addEventListener('click', function(event) {
            if (event.target && event.target.classList.contains('remove-field')) {
                event.target.closest('.field-row').remove();
            }
        });
    </script>
@endsection
