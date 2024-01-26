@extends('layouts.admin')
@section('content')
    <div class="container mt-5">
        <div class="row justify-content-center mt-5">
            <div class="col-md-11">
                <h1>Groups</h1>
                <!-- Button trigger modal -->
                <button type="button" class=" mb-3 btn btn-primary" data-toggle="modal" data-target="#staticBackdrop">
                    Create a new Group
                </button>

                <!-- Modal -->
                <div class="modal fade" id="staticBackdrop" data-backdrop="static" data-keyboard="false" tabindex="-1"
                    role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="staticBackdropLabel">New Group</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form action="{{ route('groups.store') }}" method="post">
                                    @csrf
                                    <div class="form-group">
                                        <input type="text" name="name" class="form-control" placeholder="Group Name"
                                            required>
                                    </div>
                                    <div class="form-group">
                                        <select name="round" class="form-control" placeholder="Round" required>
                                            <option value="Groups Round"> Groups Round </option>
                                            <option value="Final Round"> Final Round </option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <select name="competition_id" class="form-control" placeholder="Competition"
                                            required>
                                            @foreach ($allCompetitions as $competition)
                                                <option value="{{ $competition->id }}"> {{ $competition->name }} </option>
                                            @endforeach
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
                <!-- Edit Group Modal -->
                <div class="modal fade" id="editModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
                    role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editModalLabel">Edit Group</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form id="editForm" method="post">
                                    @csrf
                                    @method('PUT')
                                    <div class="form-group">
                                        <input type="text" name="name" class="form-control" required>
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
                            <th>Group Name</th>
                            <th>Group Teams</th>
                            <th>Add Team</th>
                            <th>Remove Team</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($groups as $group)
                            <tr data-group-id="{{ $group->id }}">
                                <td class="group-name">{{ $group->name }}</td>

                                <td>
                                    <ul class="list-group">
                                        @foreach ($group->teams->sortByDesc('pivot.points') as $team)
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <span class="badge badge-info badge-pill">{{ $loop->iteration }}</span>
                                                <span class="team-name">{{ $team->name }}</span>
                                                <span
                                                    class="badge badge-success badge-pill">{{ $team->pivot->points }}</span>
                                            </li>
                                            <!-- Separator line -->
                                            <hr class="my-1">
                                        @endforeach
                                    </ul>
                                </td>


                                <td>
                                    <form action="{{ route('groups.add-team') }}" method="post">
                                        @csrf
                                        <input type="hidden" name="group_id" value="{{ $group->id }}">
                                        <div class="input-group">
                                            <select name="team_id"
                                                class="form-control custom-select"><!-- Added class 'custom-select' -->
                                               @foreach ($group->allowedAddedTeams as $team)
                                                    <option value="{{ $team->id }}">{{ $team->name }}</option>
                                                @endforeach
                                            </select>
                                            <div class="input-group-append">
                                                <button type="submit" class="btn btn-success">Add</button>
                                            </div>
                                        </div>
                                    </form>

                                </td>
                                <td>

                                    <!-- Remove Team from Group Form -->
                                    <form action="{{ route('groups.remove-team') }}" method="post">
                                        @csrf
                                        <input type="hidden" name="group_id" value="{{ $group->id }}">
                                        <div class="input-group">
                                            <select name="team_id"
                                                class="form-control custom-select"><!-- Added class 'custom-select' -->
                                                @foreach ($group->teams as $team)
                                                    <option value="{{ $team->id }}">{{ $team->name }}</option>
                                                @endforeach
                                            </select>
                                            <div class="input-group-append">
                                                <button type="submit" class="btn btn-danger">Remove</button>
                                            </div>
                                        </div>
                                    </form>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-warning btn-edit" data-toggle="modal"
                                            data-target="#editModal">
                                            Edit
                                        </button>
                                        <form action="{{ route('groups.destroy', ['group' => $group->id]) }}"
                                            method="post">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ $groups->links() }}
            </div>
        </div>
    </div>
    <!-- Add this script at the end of your HTML file or in a separate JS file -->
    <script>
        $(document).ready(function() {
            // Attach click event to each "Edit" button
            $('.btn-edit').on('click', function() {
                // Get the group name from the table row
                var groupName = $(this).closest('tr').find('.group-name').text();

                // Set the group name in the edit modal input field
                $('#editModal input[name="name"]').val(groupName);

                // Get the group ID from the current table row
                var groupId = $(this).closest('tr').data('group-id');

                // Set the form action with the dynamic group ID
                $('#editForm').attr('action', '/groups/' + groupId);

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
