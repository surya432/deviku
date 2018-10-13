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
                                    <h4 class="modal-title" id="modelTitleId">Role Detail</h4>
                                </div>
                                <div class="modal-body">
                                    <div class="container-fluid" id="form-editor">
                                        {{ csrf_field() }}
                                        <input type="text" name="id" id="id" hidden>
                                        <div class="form-group">
                                            <label for="name">Name</label>
                                            <input type="text" class="form-control" name="name" id="name" required >
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
                    <table class="table table-hover table-condensed" style="width:100%" id="table-users">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Name</th>
                                <th>Created At</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
@section('scripts')
<script type="text/javascript">
    $(document).ready(function(){
        $( "#table-users" ).on( "click", "#btnShow" , function() {
            $("#formUser")[0].reset()
            $("input[name=id]").val($(this).attr('data-id')) ;
            $("input[name=name]").val($(this).attr('data-name'));
            $('#modelId').modal('show');
        });
        $( "#table-users" ).on( "click", "#btnAdd" , function() {
            $("#formUser")[0].reset()
        });
        $( "#table-users" ).on( "click", "#btnDelete" , function() {
            var fn = $(this).attr('data-name');
            if (confirm('Are you sure you want to delete '+ fn +'?')) {
                $.ajax({
                    url: "{{ route('users.RolesDelete') }}",
                    type: "delete",
                    data: {
                        id: $(this).attr('data-id')
                    },
                    success: function(data){
                        $(".alert-success").text("Delete User "+$(this).attr('data-fn')+" success")
                        $(".alert-success").show()
                        $('#modelId').modal('hide');
                        $("#table-users").DataTable().ajax.reload(null, false);

                    },
                    error: function(data){
                        $(".alert-danger").text("Delete User "+$(this).attr('data-fn')+" failed")
                        $(".alert-danger").show()
                    }
                });
            }
        });
        $("#formUser").on("submit",function(){
            event.preventDefault()
            $.ajax({
                type:"post",
                url: "{{ route('users.RolesPost') }}",
                data: $( this ).serializeArray(),
                success: function(data){
                    $(".alert-success").text(data.success)
                    $(".alert-success").show()
                    $('#modelId').modal('hide');
                    $("#table-users").DataTable().ajax.reload(null, false);

                },
                error: function(data){
                    $(".alert-danger").text(data.responseJSON.alert)
                    $(".alert-danger").show()
                    $("#table-users").DataTable().ajax.reload(null, false);
                    $('#modelId').modal('hide');

                }
            });
        });
    })
    $("#table-users").ready(function(){
        oTable = $("#table-users").DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": "{{ route('users.roleData') }}",
            "columns": [
                {data: 'id', name: 'id'},
                {data: 'name', name: 'name'},
                {data: 'created_at', name: 'create_at'},
                {data: 'action', name: 'action', orderable: false, searchable: false}
            ],
            dom: 'l<"toolbar">frtip',
            initComplete: function(){
                $("div.toolbar").html('<button type="button" onclick="btnAdd()" class="btn btn-primary btn-xs" data-toggle="modal" id="btnAdd" data-target="#modelId"> <i class="glyphicon glyphicon-plus"></i> Users</button>');           
            }
        });
    });
</script>
    <script>function btnAdd() { $("#formUser")[0].reset()};</script>

@endsection