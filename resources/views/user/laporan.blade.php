@extends('parts.default')
@section('title-page')
All Users
@endsection
@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="page-header">
            All Laporan
        </div>
        <div class="col-lg-12">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    List All Laporan <button type="button" onclick="btnAdd()" class="btn btn-success btn-xs"
                        data-toggle="modal" id="btnAdd" data-target="#modelId"> <i class="glyphicon glyphicon-plus"></i>
                        Tambah Laporan</button>
                </div>
                <div class="panel-body">
                    <div id="alert-danger" class="alert alert-danger" style="display:none"></div>
                    <div id="alert-success" class="alert alert-success" style="display:none"></div>
                    <div class="modal fade" id="modelId" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
                        aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <form action="/admin/register" method="post" id="formUser" role="form">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                        <h4 class="modal-title" id="modelTitleId">Laporan Detail</h4>
                                    </div>
                                    <div class="modal-body">
                                        <div class="container-fluid" id="form-editor">
                                            {{ csrf_field() }}
                                            <input type="text" name="id" id="id" hidden>
                                            <div class="form-group">
                                                <label for="date">Tanggal </label>
                                                <input type="text" class="form-control" name="date" id="date" readonly
                                                    value='{{ now() }}'>
                                            </div>
                                            <div class="form-group">
                                                <label for="User Name">User Name</label>
                                                <input type="text" name="user_id" id="user_id"
                                                    value='{{ Sentinel::getUser()->id }}' hidden>
                                                <input type="text" class="form-control" name="username" id="username"
                                                    required readonly value='{{Sentinel::getUser()->first_name}}'>
                                            </div>


                                            <div class="form-group">
                                                <label for="inputLaporan" class="control-label">Laporan:</label>
                                                <textarea class="form-control" rows="5" id="comment"
                                                    name="comment"></textarea>
                                            </div>

                                        </div>

                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-dismiss="modal">Close</button>
                                        <button type="submit" id="btnSimpan" class="btn btn-primary">Save</button>
                                    </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="dataTables_wrapper form-inline dt-bootstrap no-footer">
                    <table class="table table-striped table-bordered table-hover dataTable no-footer dtr-inline"
                        aria-describedby="dataTables-example_info" style="width: 100%;" role="grid" style="width:100%"
                        id="table-users">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Members</th>
                                <th>Created at</th>
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
        $("input[name=user_id]").val($(this).attr('data-userid'));
        $("input[name=date]").val($(this).attr('data-date'));
        $("#comment").val($(this).attr('data-comment'));
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
                    $(".alert-success").text("Delete User " + fn + " success")
                    $(".alert-success").show()
                    $('#modelId').modal('hide');
                    $("#table-users").DataTable().ajax.reload(null, false);

                },
                error: function(data) {
                    $(".alert-danger").text("Delete User " + fn + " failed")
                    $(".alert-danger").show()
                }
            });
        }
    });
    $("#formUser").on("submit", function() {
        event.preventDefault()
        $.ajax({
            type: "post",
            url: "{{ route('users.addlaporan') }}",
            data: $(this).serializeArray(),
            success: function(data) {
                $(".alert-success").text(data)
                $(".alert-success").show()
                $('#modelId').modal('hide');
                $("#table-users").DataTable().ajax.reload(null, false);

            },
            error: function(data) {
                $(".alert-danger").text(data)
                $(".alert-danger").show()
                $("#table-users").ajax.reload();
                $('#modelId').modal('hide');

            }
        });
    });
})
$("#table-users").ready(function() {
    oTable = $("#table-users").DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": "{{ route('users.getlaporan') }}",
        "columns": [{
                data: 'id',
                name: 'id'
            },
            {
                data: 'first_name',
                name: 'first_name'
            },
            {
                data: 'created_at',
                name: 'created_at'
            },
            {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false
            }
        ],
        dom: 'l<"toolbar">frtip'
    });
});
</script>
<script>
function btnAdd() {
    $("#formUser")[0].reset()
};
</script>

@endsection