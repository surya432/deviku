@extends('parts.default')
@section('title-page')
All Users
@endsection
@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="page-header">
            All User
        </div>
        <div class="col-lg-12">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    List All Users
                </div>
                <div class="panel-body">
                    <div id="alert-danger" class="alert alert-danger" style="display:none"></div>
                    <div id="alert-success" class="alert alert-success" style="display:none"></div>
                    <div class="modal fade" id="modelId" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <form action="/admin/register" method="post" id="formUser" role="form">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                        <h4 class="modal-title" id="modelTitleId">User Detail</h4>
                                    </div>
                                    <div class="modal-body">
                                        <div class="container-fluid" id="form-editor">
                                            {{ csrf_field() }}
                                            <input type="text" name="id" id="id" hidden>
                                            <input type="text" name="roles" id="roles" hidden>
                                            <div class="form-group">
                                                <label for="Email">Email</label>
                                                <input type="text" class="form-control" name="email" id="email" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="First Name">First Name</label>
                                                <input type="text" class="form-control" name="first_name" id="first_name" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="Last Name">Last Name</label>
                                                <input type="text" class="form-control" name="last_name" id="last_name" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="Password">Password</label>
                                                <input type="password" class="form-control" name="password" id="password">
                                            </div>
                                            <div class="form-group">
                                                <label for="Access">Access</label>
                                                <select class="form-control" name="access" id="access" required>
                                                    <option>Select one</option>
                                                    @foreach ($roles as $role)
                                                    <option value="{{$role->slug}}">{{$role->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                        </div>

                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                        <button type="submit" id="btnSimpan" class="btn btn-primary">Save</button>
                                    </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="dataTables_wrapper form-inline dt-bootstrap no-footer">
                    <table class="table table-striped table-bordered table-hover dataTable no-footer dtr-inline" aria-describedby="dataTables-example_info" style="width: 100%;" role="grid" style="width:100%" id="table-users">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>First Name</th>
                                <th>Last Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Update at</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

@endsection
@section('scripts')
<script type="text/javascript">
    $(document).ready(function() {
        $("#table-users").on("click", "#btnShow", function() {
            $("#formUser")[0].reset()
            $("input[name=id]").val($(this).attr('data-id'));
            $("input[name=email]").val($(this).attr('data-email'));
            $("input[name=first_name]").val($(this).attr('data-fn'));
            $("input[name=last_name]").val($(this).attr('data-ln'));
            $("select[name=access]").val($(this).attr('data-access'));
            $("input[name=roles]").val($(this).attr('data-access'));
            $('#modelId').modal('show');
        });
        $("#table-users").on("click", "#btnAdd", function() {
            $("#formUser")[0].reset()
        });
        $("#table-users").on("click", "#btnDelete", function() {
            var fn = $(this).attr('data-fn');
            if (confirm('Are you sure you want to delete ' + fn + '?')) {
                $.ajax({
                    url: "{{ route('users.add') }}",
                    type: "delete",
                    data: {
                        id: $(this).attr('data-id')
                    },
                    success: function(data) {
                        /* $(".alert-success").text("Delete User " + fn + " success")
                        $(".alert-success").show() */
                        swal("SUCCESS", "Success Save", "success")

                        $('#modelId').modal('hide');
                        $("#table-users").DataTable().ajax.reload(null, false);

                    },
                    error: function(data) {
                        swal("Error!!", "Something Error", "error")
                    }
                });
            }
        });
        $("#formUser").on("submit", function() {
            event.preventDefault()
            $.ajax({
                type: "post",
                url: "{{ route('users.add') }}",
                data: $(this).serializeArray(),
                success: function(data) {
                    swal("SUCCESS", "Success Save", "success")

                    $('#modelId').modal('hide');
                    $("#table-users").DataTable().ajax.reload(null, false);

                },
                error: function(data) {
                    
                    swal("Error!!", data.responseJSON.alert, "error")

                    $("#table-users").ajax.reload();
                    $('#modelId').modal('hide');

                }
            });
        });
    })
    $("#table-users").ready(function() {
        oTable = $("#table-users").DataTable({
            "processing": true,
            "pageLength": 25,
            "serverSide": true,
            "ajax": "{{ route('users.getData') }}",
            "columns": [{
                data: 'id',
                        name: 'id',
                        orderable: false,
                        searchable: false
                },
                {
                    data: 'first_name',
                    name: 'first_name'
                },
                {
                    data: 'last_name',
                    name: 'last_name'
                },
                {
                    data: 'email',
                    name: 'email'
                },
                {
                    data: 'roles[0].name',
                    name: 'roles[0].name',
                    orderable: false
                },
                {
                    data: 'updated_at',
                    name: 'updated_at'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                }
            ],
            dom: 'l<"toolbar">frtip',
            initComplete: function() {
                $("div.toolbar").html(
                    '<button type="button" onclick="btnAdd()" class="btn btn-primary btn-xs" data-toggle="modal" id="btnAdd" data-target="#modelId"> <i class="glyphicon glyphicon-plus"></i> Users</button>'
                );
            }
        });
    });
</script>
<script>
    function btnAdd() {
        $("#formUser")[0].reset()
    };
</script>

@endsection