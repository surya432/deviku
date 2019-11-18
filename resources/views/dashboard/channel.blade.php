@extends('parts.default')
@section('title-page')
Channels
@endsection
@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="page-header">
            List Of Channel
        </div>
        <div class="panel panel-primary">
            <div class="panel-heading">Channel</div>
            <div class="panel-body">
                <div class="alert alert-danger" id="alert-danger" style="display:none"></div>
                <div class="alert alert-success" id="alert-succes" style="display:none"></div>
                <form action="{{ route("countryPost") }}" method="post" id="formtype" role="form">
                    <input type="hidden" name="id" id="id" hidden>
                    {{ csrf_field() }}

                    <div class="form-group">
                        <label class="control-label" for="email">Name</label>
                        <input type="text" class="form-control" name="name" id="name" required>
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
                    <th>Country</th>
                    <th>Created At</th>
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
        $("#formtype")[0].reset()
        $("input[name=id]").val($(this).attr('data-id'));
        $("input[name=name]").val($(this).attr('data-name'));
    });
    $("#formtype").on("submit", function() {
        event.preventDefault()
        $.ajax({
            type: "post",
            url: "{{ route('countryPost') }}",
            data: $(this).serializeArray(),
            success: function(data) {
                $("input[name=id]").val(data.id);
                $("input[name=name]").val(data.name);
                $(".alert-success").text("Update Success")
                $(".alert-success").show()
                $("#table-users").DataTable().ajax.reload(null, false);
                $("#formtype")[0].reset()

            },
            error: function(data) {
                $("input[name=id]").val(data.id);
                $("input[name=site_name]").val(data.site_name);
                $("input[name=folder720p]").val(data.folder720p);
                $("input[name=folder360p]").val(data.folder360p);
                $(".alert-danger").text("Failed Update")
                $(".alert-danger").show()
                $("#table-users").DataTable().ajax.reload(null, false);
                $("#formtype")[0].reset()
            }
        });
    });
    $("#table-users").on("click", "#btnDelete", function() {
        var fn = $(this).attr('data-email');
        if (confirm('Are you sure you want to delete ' + fn + '?')) {
            $.ajax({
                url: "{{ route('gmailDelete') }}",
                type: "delete",
                data: {
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
            "ajax": "{{ route('countryData') }}",
            "columns": [{
                    data: 'DT_Row_Index',
                    name: 'DT_Row_Index'
                },
                {
                    data: 'name',
                    name: 'name'
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
            ]

        });
    });
});
</script>
@endsection