@extends('layouts.admin')
@section('content')
    <div class="container mt-5">
        <div class="row justify-content-center mt-5">
            <div class="col-md-11">
                <h1>Teams</h1>
                <!-- Button trigger modal -->
                <button type="button" class=" mb-3 btn btn-primary" data-toggle="modal" data-target="#staticBackdrop">
                    Create a new Team
                </button>

                <!--create Modal -->
                <div class="modal fade" id="staticBackdrop" data-backdrop="static" data-keyboard="false" tabindex="-1"
                    role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="staticBackdropLabel">New Team</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form action="{{ route('teams.store') }}" method="post">
                                    @csrf
                                    <div class="form-group">
                                        <input type="text" name="name" class="form-control"
                                            placeholder="Team Name" required>
                                    </div>
                                    <div class="form-group">
                                        <input type="text" name="email" class="form-control"
                                            placeholder="Team Email" required>
                                    </div>
                                    <div class="form-group">
                                        <input type="text" name="password" class="form-control"
                                            placeholder="Team Passwrd" required>
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
                                <h5 class="modal-title" id="editModalLabel">Edit Team</h5>
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
                                            placeholder="Team Name" required>
                                    </div>
                                    <div class="form-group">
                                        <input type="email" name="email" class="form-control"
                                            placeholder="Team Email" required>
                                    </div>
                                    <div class="form-group">
                                        <input type="text" name="password" class="form-control"
                                            placeholder="Team Passwrd" required>
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


                <table class="table">
                    <thead>
                        <tr>
                            <th>Team Name</th>
                            <th>Team Username</th>
                            <th>Team Password</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($teams as $team)
                            <tr data-team-id="{{ $team->id }}">
                                <td class="team-name">{{ $team->name }}</td>
                                <td class="team-email">{{ $team->email }}</td>
                                <td class="team-password">{{ $team->password }}</td>
                                <td class="d-flex">
                                    <button type="button" class="btn btn-warning btn-edit" data-toggle="modal"
                                        data-target="#editModal">
                                        Edit
                                    </button>
                                    <form action="{{ route('teams.destroy', ['team' => $team->id]) }}"
                                        method="post">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="ml-3 btn btn-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach

                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- Add this script at the end of your HTML file or in a separate JS file -->
    <script>
        $(document).ready(function() {
            // Attach click event to each "Edit" button
            $('.btn-edit').on('click', function() {
                // Get the category name from the table row
                var teamName = $(this).closest('tr').find('.team-name').text();
                var teamEmail = $(this).closest('tr').find('.team-email').text();
                var teamPassword = $(this).closest('tr').find('.team-password').text();

                // Set the category name in the edit modal input field
                $('#editModal input[name="name"]').val(teamName);
                $('#editModal input[name="email"]').val(teamEmail);
                $('#editModal input[name="password"]').val(teamPassword);

                // Get the category ID from the current table row
                var teamId = $(this).closest('tr').data('team-id');

                // Set the form action with the dynamic category ID
                $('#editForm').attr('action', '/teams/' + teamId);

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
