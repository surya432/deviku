@extends('parts.default')
@section('title-page')
Accounts Gmail
@endsection
@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="page-header">
            Gmail Accounts
        </div>
        <div class="panel panel-primary">
            <div class="panel-heading">Accounts Gmail Detail</div>
            <div class="panel-body">
                <div class="alert alert-danger" id="alert-danger" style="display:none"></div>
                <div class="alert alert-success" id="alert-succes" style="display:none"></div>
                <form action="{{ route("gmailPost") }}" method="post" id="formGmail" role="form">
                    {{ csrf_field() }}
                    <input type="text" name="id" id="id" hidden>
                    <div class="form-group">
                        <label class="control-label" for="email">Email</label>
                        <input type="text" class="form-control" name="email" id="email" required></div>
                    <div class="form-group">
                        <label class="control-label" for="token">Token</label>
                        <input type="text" class="form-control" name="token" id="token" required>
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="folderid">Folder Id</label>
                        <input type="text" class="form-control" name="folderid" id="folderid" required>
                    </div>
                    <button type="reset" class="btn btn-danger">Reset</button>
                    <button class="btn btn-primary text-center" type="submit">Save</button>
                </form>
            </div>
        </div>
        <table class="table table-striped table-bordered table-responsive table-hover dataTable no-footer dtr-inline"
            style="width:100%" id="table-users">
            <thead class="thead-dark">
                <tr>
                    <th>No</th>
                    <th>Email</th>
                    <th>Total Files</th>
                    <th>Update At</th>
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
        $("input[name=email]").val($(this).attr('data-email'));
        $("input[name=token]").val($(this).attr('data-token'));
        $("input[name=folderid]").val($(this).attr('data-folderid'));
    });
    $("#formGmail").on("submit", function() {
        event.preventDefault()
        $.ajax({
            type: "post",
            url: "{{ route('gmailPost') }}",
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
        var fn = $(this).attr('data-email');
        if (confirm('Are you sure you want to delete ' + fn + '?')) {
            $.ajax({
                url: "{{ route('gmailDelete') }}",
                type: "get",
                data: {
                    _method: 'delete',
                    id: $(this).attr('data-id')
                },
                success: function(data) {
                    $(".alert-success").text("Delete User " + fn + " success")
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
            "pageLength": 10,
            "ajax": "{{ route('gmailData') }}",
            "order": [[ 3, "asc" ]],
            columnDefs: [
                { type: 'date-euro', targets: 3 }
            ],
            "columns": [{
                    data: 'id',
                    name: 'id'
                },
                {
                    data: 'email',
                    name: 'email'
                },
                {
                    data: 'totalfiles',
                    name: 'totalfiles'
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

        });
    });
});
</script>
@endsection