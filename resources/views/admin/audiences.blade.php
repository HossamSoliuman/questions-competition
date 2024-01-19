@extends('layouts.admin')

@section('content')
    <div class="container mt-5">
        <div class="row justify-content-center mt-5">
            <div class="col-md-11">
                <h1>Audiences</h1>
                @foreach ($tests as $test)
                    <h3> Test Name: {{ $test->name }}</h3>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Number</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Points</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($test->audiences as $audience)
                                <tr data-audience-id="{{ $audience->id }}">
                                    <td class="audience-number">{{ $audience->number }}</td>
                                    <td class="audience-name">{{ $audience->name }}</td>
                                    <td class="audience-email">{{ $audience->email }}</td>
                                    <td class="audience-phone">{{ $audience->phone }}</td>
                                    <td class="audience-points">{{ $audience->points }}</td>
                                    <td class="d-flex">
                                        <button type="button" class="btn btn-warning btn-edit" data-toggle="modal"
                                            data-target="#editModal">
                                            Edit
                                        </button>
                                        <form action="{{ route('audiences.destroy', ['audience' => $audience->id]) }}"
                                            method="post">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class=" ml-3 btn btn-danger">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endforeach

                {{ $tests->links() }} <!-- Pagination Links -->

            </div>
        </div>
    </div>

    <!-- Edit Audience Modal -->
    <div class="modal fade" id="editModal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog"
        aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Audience</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="editForm" method="post">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <div class="form-group">
                                <label for="number">Number:</label>
                                <input type="text" class="form-control" id="number" name="number">
                            </div>
                            <label for="name">Name:</label>
                            <input type="text" class="form-control" id="name" name="name">
                        </div>
                        <div class="form-group">
                            <label for="email">Email:</label>
                            <input type="email" class="form-control" id="email" name="email">
                        </div>
                        <div class="form-group">
                            <label for="phone">Phone:</label>
                            <input type="text" class="form-control" id="phone" name="phone">
                        </div>

                        <div class="form-group">
                            <label for="points">Points:</label>
                            <input type="text" class="form-control" id="points" name="points">
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

    <!-- Add this script at the end of your HTML file or in a separate JS file -->
    <script>
        $(document).ready(function() {
            // Attach click event to each "Edit" button
            $('.btn-edit').on('click', function() {
                // Get audience details and populate the edit modal
                var audienceId = $(this).closest('tr').data('audience-id');
                var audienceName = $(this).closest('tr').find('.audience-name').text();
                var audienceEmail = $(this).closest('tr').find('.audience-email').text();
                var audiencePhone = $(this).closest('tr').find('.audience-phone').text();
                var audienceNumber = $(this).closest('tr').find('.audience-number').text();
                var audiencePoints = $(this).closest('tr').find('.audience-points').text();

                // Set the input fields in the edit modal
                $('#editModal input[name="name"]').val(audienceName);
                $('#editModal input[name="email"]').val(audienceEmail);
                $('#editModal input[name="phone"]').val(audiencePhone);
                $('#editModal input[name="number"]').val(audienceNumber);
                $('#editModal input[name="points"]').val(audiencePoints);

                // Set the form action with the dynamic audience ID
                $('#editForm').attr('action', '/audiences/' + audienceId);

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
