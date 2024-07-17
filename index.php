<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Server Side DataTable CRUD operation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdn.datatables.net/v/bs5/jq-3.7.0/dt-2.0.8/r-3.0.2/datatables.min.css" rel="stylesheet">

</head>

<body>
    <h1 class="text-center mt-3">DataTable CRUD</h1>
    <div class="container-fluid">
        <div class="row">
            <div class="container">
                <div class="row">
                    <div class="col-md-2"></div>
                    <div class="col-md-8">
                        <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addUserModal">
                            Add User
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="container">
                <div class="row">
                    <div class="col-md-2"></div>
                    <div class="col-md-8">
                        <table id="dataTable" class="table">
                            <thead>
                                <th>No.</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Mobile</th>
                                <th>city</th>
                                <th>Options</th>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-2"></div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/v/bs5/jq-3.7.0/dt-2.0.8/r-3.0.2/datatables.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#dataTable').dataTable({
                'serverSide': true,
                'processing': true,
                'paging': true,
                'order': true,
                'ajax': {
                    'url': 'fetch_data.php',
                    'type': 'POST'
                },
                'fnCreateRow': function(nRow, aData, iDataIndex) {
                    $(nRow).attr('id', aData[0]);
                },
                'columnDefs': [{
                    'targets': [0, 5],
                    'orderable': false,
                }]
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            $(document).on('submit', '#saveUserForm', function(event) { // Ensure the correct ID of the form
                event.preventDefault();
                const username = $('#inputUsername').val();
                const email = $('#inputEmail').val();
                const mobile = $('#inputMobile').val();
                const city = $('#inputCity').val();

                if (username != '' && email != '' && mobile != '' && city != '') {
                    $.ajax({
                        url: 'add_user.php',
                        data: {
                            username: username,
                            email: email,
                            mobile: mobile,
                            city: city
                        },
                        type: 'POST',
                        dataType: 'json', // Ensure that the response is parsed as JSON
                        success: function(data) {
                            if (data.status == 'success') {
                                $('#dataTable').DataTable().ajax.reload(); // Reload the DataTable
                                alert('User added successfully');

                                // clear Form
                                $('#inputUsername').val('');
                                $('#inputEmail').val('');
                                $('#inputMobile').val('');
                                $('#inputCity').val('');

                                //  Close Form
                                $('#addUserModal').modal('hide');
                            } else {
                                alert('Failed to add user');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error(xhr.responseText); // Log any error response from the server
                        }
                    });
                } else {
                    alert('Please fill all the fields');
                }
            });
        });


        $(document).on('click', '.editBtn', function(event) {
            const id = $(this).data('id');
            const trid = $(this).closest('tr').attr('id');

            $.ajax({
                url: "get_single_user.php",
                type: "POST",
                data: {
                    id: id
                },
                success: function(data) {
                    const json = JSON.parse(data);
                    $('#id').val(json.id);
                    $('#trid').val(trid);
                    $('#_inputUsername').val(json.username);
                    $('#_inputEmail').val(json.email);
                    $('#_inputMobile').val(json.mobile);
                    $('#_inputCity').val(json.city);
                    $('#editUserModal').modal('show');
                }
            });
        });

        $(document).on('submit', '#updateUserForm', function(event) {
            event.preventDefault();
            const id = $('#id').val();
            const trid = $('#trid').val();
            const username = $('#_inputUsername').val();
            const email = $('#_inputEmail').val();
            const mobile = $('#_inputMobile').val();
            const city = $('#_inputCity').val();

            $.ajax({
                url: "update_user.php",
                data: {
                    id: id,
                    username: username,
                    email: email,
                    mobile: mobile,
                    city: city
                },
                type: 'POST',
                success: function(data) {
                    const json = JSON.parse(data);
                    const status = json.status;

                    if (status === 'success') {
                        const table = $('#dataTable').DataTable();
                        const button = '<a href="javascript:void(0);" class="btn btn-sm btn-info editBtn" data-id="' + id + '">Edit</a> <a href="javascript:void(0);" class="btn btn-sm btn-danger deleteBtn" data-id="' + id + '">Delete</a>';

                        const row = table.row('tr' + trid);
                        row.data([id, username, email, mobile, city, button]).draw();

                        alert('User updated successfully.');
                        $('#editUserModal').modal('hide');
                    } else {
                        alert('Failed to update user');
                    }
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                }
            });
        });

        $(document).on('click', '.btnDelete', function(event) {
            const id = $(this).data('id')
            if (confirm('Are you sure want to delete this user ?')) {
                $.ajax({
                    url: "delete_user.php",
                    data: {
                        id: id,
                    },
                    type: 'POST',
                    success: function(data) {
                        const json = JSON.parse(data);

                        const status = json.status;
                        if (status == 'success') {
                            const table = $('#dataTable').DataTable();
                            table.row($('#' + id).closest('tr')).remove().draw();
                            alert('User deleted successfully.');
                        } else {
                            alert('Failed to delete user')
                        }
                    }
                })
            }
        });
    </script>


    <!-- Add user Modal  -->
    <!-- Modal -->
    <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Add User</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="saveUserForm" action="javascript:void(0);" method="post">
                    <div class="modal-body">
                        <div class="mb-3 row">
                            <label for="username" class="col-sm-2 col-form-label">Username</label>
                            <div class="col-sm-10">
                                <input type="text" name="username" class="form-control" id="inputUsername">
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <label for="email" class="col-sm-2 col-form-label">Email</label>
                            <div class="col-sm-10">
                                <input type="text" name="email" class="form-control" id="inputEmail">
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <label for="mobile" class="col-sm-2 col-form-label">Mobile</label>
                            <div class="col-sm-10">
                                <input type="text" name="mobile" class="form-control" id="inputMobile">
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <label for="city" class="col-sm-2 col-form-label">City</label>
                            <div class="col-sm-10">
                                <input type="text" name="city" class="form-control" id="inputCity">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Add user Modal  -->

    <!-- Edit user Modal  -->
    <!-- Modal -->
    <div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Update User</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="updateUserForm" action="javascript:void(0);" method="post">
                    <input type="hidden" id="id" name="id" value="">
                    <input type="hidden" id="trid" name="trid" value="">
                    <div class="modal-body">
                        <div class="mb-3 row">
                            <label for="username" class="col-sm-2 col-form-label">Username</label>
                            <div class="col-sm-10">
                                <input type="text" name="_username" class="form-control" id="_inputUsername">
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <label for="email" class="col-sm-2 col-form-label">Email</label>
                            <div class="col-sm-10">
                                <input type="text" name="_email" class="form-control" id="_inputEmail">
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <label for="mobile" class="col-sm-2 col-form-label">Mobile</label>
                            <div class="col-sm-10">
                                <input type="text" name="_mobile" class="form-control" id="_inputMobile">
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <label for="city" class="col-sm-2 col-form-label">City</label>
                            <div class="col-sm-10">
                                <input type="text" name="_city" class="form-control" id="_inputCity">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Edit user Modal  -->
</body>

</html>