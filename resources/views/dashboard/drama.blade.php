@extends('parts.default')
@section('title-page')
Drama
@endsection
@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="page-header">
            List Of Drama
        </div>
        <div class="panel panel-primary">
            <div class="panel-heading">Drama</div>
            <div class="panel-body">
                <div class="alert alert-danger alert-dismissible" id="alert-danger" style="display:none"></div>
                <div class="alert alert-success alert-dismissible" id="alert-succes" style="display:none"></div>
                <!-- Button trigger modal -->
                <button type="button" id="btnModal" onclick="btnAdd()" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modelId">
                    <i class="fa fa-plus fa-fw"></i> Drama Command Action
                </button>
                <button type="button" id="btnSingkron" onclick="btnSingkron()" class="btn btn-primary btn-sm">
                    <i class="fa fa-plus fa-fw"></i> Singkron Folder
                </button>
                <!-- Modal -->
                <div class="modal fade" id="modelId" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <form action="{{ route("dramaPost") }}" method="post" id="formDrama" role="form">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                    <h4 class="modal-title" id="modelTitleId">Drama</h4>
                                </div>
                                <div class="modal-body">
                                    <div class="container-fluid">
                                        <input type="hidden" name="id" id="id" hidden>
                                        {{ csrf_field() }}
                                        <div class="form-group">
                                            <label class="control-label" for="email">Name</label>
                                            <input type="text" class="form-control" name="title" id="title" required>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label" for="email">Folder ID</label>
                                            <input type="text" class="form-control" name="folderid" id="folderid">
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label" for="email">torrentlink</label>
                                            <input type="text" class="form-control" name="torrentlink" id="torrentlink">
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label" for="email">Link Subscanes</label>
                                            <input type="text" class="form-control" name="subsceneslink" id="subsceneslink">
                                        </div>
                                        <div class="form-group">
                                            <label for="type" class="form-label">Status</label>
                                            <select class="custom-select form-control" name="status" id="status" required>
                                                <option selected>Select one</option>
                                                @foreach($status as $status){
                                                <option value="{{$status->status}}">{{$status->status}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="type" class="form-label">Country</label>
                                            <select class="custom-select form-control" name="country_id" id="country_id" required>
                                                <option selected>Select one</option>
                                                @foreach($country as $country){
                                                <option value="{{$country->id}}">{{$country->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="type" class="form-label">Type</label>
                                            <select class="custom-select form-control" name="type_id" id="type_id" required>
                                                <option selected>Select one</option>
                                                @foreach($Type as $Type){
                                                <option value="{{$Type->id}}">{{$Type->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary">Save</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <table class="table table-striped table-bordered table-hover dataTable no-footer dtr-inline" style="width:100%" id="table-users">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Title</th>
                    <th>Status</th>
                    <th>Country</th>
                    <th>Type</th>
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
            event.preventDefault()
            $('#modelId').modal('show');
            $("#formDrama")[0].reset()
            $("input[name=id]").val($(this).attr('data-id'));
            $("input[name=title]").val($(this).attr('data-title'));
            $("input[name=folderid]").val($(this).attr('data-folderid'));
            $("select[name=status]").val($(this).attr('data-status'));
            $("select[name=type_id]").val($(this).attr('data-type_id'));
            $("select[name=country_id]").val($(this).attr('data-country_id'));
            $("input[name=subsceneslink]").val($(this).attr('data-subsceneslink'));
            $("input[name=torrentlink]").val($(this).attr('data-torrentlink'));
        });
        $("#formDrama").on("submit", function() {
            event.preventDefault()
            $.ajax({
                type: "post",
                url: "{{ route('dramaPost') }}",
                data: $(this).serializeArray(),
                success: function(data) {
                    $(".alert-success").fadeIn().html(data).wait(2000).fadeOut('slow');
                    $("#table-users").DataTable().ajax.reload(null, false);
                    $("#formDrama")[0].reset()
                    $('#modelId').modal('hide');


                },
                error: function(data) {
                    $(".alert-success").fadeIn().html(data).wait(2000).fadeOut('slow');
                    $("#table-users").DataTable().ajax.reload(null, false);
                    $("#formDrama")[0].reset()
                    $('#modelId').modal('hide');

                }
            });
        });
        $("#table-users").on("click", "#btnDelete", function() {
            var fn = $(this).attr('data-title');
            if (confirm('Are you sure you want to delete ' + fn + '?')) {
                $.ajax({
                    url: "{{ route('dramaDelete') }}",
                    type: "get",
                    data: {
                        _method: 'delete',
                        id: $(this).attr('data-id')
                    },
                    success: function(data) {
                        $(".alert-success").fadeIn().html('Delete Success').wait(2000).fadeOut(
                            'slow');
                        $("#table-users").DataTable().ajax.reload(null, false);

                    },
                    error: function(data) {
                        $(".alert-success").fadeIn().html('Delete Error').wait(2000).fadeOut(
                            'slow');
                        $("#table-users").DataTable().ajax.reload(null, false);
                    }
                });
            }
        });
        $("#table-users").ready(function() {
            oTable = $("#table-users").DataTable({
                "processing": true,
                "serverSide": true,
                "pageLength": 25,
                "ajax": "{{ route('dramaData') }}",
                "columns": [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'title',
                        name: 'title'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'country',
                        name: 'country',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'type',
                        name: 'type',
                        orderable: false
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
        jQuery.fn.wait = function(MiliSeconds) {
            $(this).animate({
                opacity: '+=0'
            }, MiliSeconds);
            return this;
        }
    });

    function btnSingkron() {
        $.ajax({
            url: "{{ route('singkronFolder') }}",
            type: "get",

            success: function(data) {
                $(".alert-success").fadeIn().html('Singkron Success').wait(2000).fadeOut('slow');
                $("#table-users").DataTable().ajax.reload(null, false);

            },
            error: function(data) {
                $(".alert-success").fadeIn().html('Singkron Error').wait(2000).fadeOut('slow');
                $("#table-users").DataTable().ajax.reload(null, false);
            }
        });
    }

    function btnAdd() {
        $("#formDrama")[0].reset()
    };
</script>
@endsection