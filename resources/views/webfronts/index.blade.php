@extends('parts.default')
@section('title-page')
WebFronts
@endsection
@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="page-header">
            WebFronts
        </div>
        <div class="panel panel-primary">
            <div class="panel-heading">WebFronts Detail</div>
            <div class="panel-body">
                <div class="alert alert-danger" id="alert-danger" style="display:none"></div>
                <div class="alert alert-success" id="alert-succes" style="display:none"></div>
                <form action="{{ route("webfrontSingkron") }}" method="post" id="formGmail" role="form">
                    {{ csrf_field() }}
                    <input type="text" name="id" id="id" hidden>
                    <div class="form-group">
                        <label class="control-label" for="site">Site URL</label>
                        <input type="text" class="form-control" name="site" id="site" required></div>
                    <div class="form-group">
                        <label class="control-label" for="token">Username</label>
                        <input type="text" class="form-control" name="username" id="username" required>
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="folderid">Password</label>
                        <input type="text" class="form-control" name="password" id="password" required>
                    </div>
                    <button type="reset" class="btn btn-danger">Reset</button>
                    <button class="btn btn-primary text-center" type="submit">Save</button>
                </form>
            </div>
        </div>
        <table class="table table-striped table-bordered table-hover dataTable no-footer dtr-inline" style="width:100%"
            id="table-users">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Site URL</th>
                    <th>Username</th>
                    <th>Password</th>
                    <th>Action</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
@endsection
@section('scripts')

<script type="text/javascript">
$(document).ready(function() {
    $("#table-users").on("click", "#btnShow", function() {
        $("#formGmail")[0].reset()
        $("input[name=id]").val($(this).attr('data-id'));
        $("input[name=site]").val($(this).attr('data-site'));
        $("input[name=username]").val($(this).attr('data-username'));
        $("input[name=password]").val($(this).attr('data-password'));
    });
    $("#formGmail").on("submit", function() {
        event.preventDefault()
        $.ajax({
            type: "post",
            url: "{{ route('webfrontGet') }}",
            data: $(this).serializeArray(),
            success: function(data) {
                $(".alert-success").text("Update Success")
                $(".alert-success").show()
                $("#table-users").DataTable().ajax.reload(null, false);
                $("#formGmail")[0].reset()

            },
            error: function(data) {
                $(".alert-danger").text("Failed Update")
                $(".alert-danger").show()
                $("#table-users").DataTable().ajax.reload(null, false);
                $("#formGmail")[0].reset()
            }
        });
    });
    $("#table-users").on("click", "#btnDelete", function() {
        var fn = $(this).attr('data-site');
        if (confirm('Are you sure you want to delete ' + fn + '?')) {
            $.ajax({
                url: "{{ route('webfrontDelete') }}",
                type: "delete",
                data: {
                    id: $(this).attr('data-id')
                },
                success: function(data) {
                    $(".alert-success").text("Delete Site " + fn + " success")
                    $(".alert-success").show();
                    $("#table-users").DataTable().ajax.reload(null, false);

                },
                error: function(data) {
                    $(".alert-danger").text("Delete User " + fn + " failed")
                    $(".alert-danger").show()
                    $("#table-users").DataTable().ajax.reload(null, false);
                }
            });
        }
    });
    $("#table-users").ready(function() {
        oTable = $("#table-users").DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": "{{ route('webfrontGet') }}",
            "columns": [{
                    data: 'id',
                    name: 'id'
                },
                {
                    data: 'site',
                    name: 'site'
                },
                {
                    data: 'username',
                    name: 'username'
                },
                {
                    data: 'password',
                    name: 'password'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                }
            ],

        });
    });
});
</script>
@endsection