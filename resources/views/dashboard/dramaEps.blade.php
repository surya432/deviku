@extends('parts.default')
@section('title-page')
Drama {{$result->title}}
@endsection
@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="page-header">
                {{$result->title}} [{{$result->id}}]
            <button type="button" name="url_720p" id="url_720p" data-clipboard-text="{{$result->title}} [{{$result->id}}]" class="btn btn-xs btn-primary btncopy">Copy Folder</button>           
        </div>
        <div class="panel panel-primary">
            <div class="panel-heading">{{$result->title}} Command Action</div>
            <div class="panel-body">
               
                <!-- Button trigger modal -->
                <button type="button" id="btnModal" onclick="btnAdd()" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modelId">
                    <i class="fa fa-plus fa-fw"></i> Drama Eps.
                </button>
                <!-- Modal -->
                <div class="modal fade" id="modelId" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <form action="{{ route("epsPost", $result->id) }}" method="post" id="formDrama" role="form">
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
                                        <input type="hidden" name="drama_id" id="drama_id" hidden>
                                        {{ csrf_field() }}
                        
                                        <div class="form-group">
                                            <label class="control-label" for="email">Name</label>
                                            <input type="text" class="form-control" name="title" id="title" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="type" class="form-label">Status</label>
                                            <select class="custom-select form-control" name="status" id="status" required>
                                                <option value="Hardsub" selected>Hardsub</option>
                                                <option value="SUB">SUB</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label" for="email">Link 720p</label>
                                            <input type="text" class="form-control" name="f720p" id="f720p" >
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label" for="email">Link 360p</label>
                                            <input type="text" class="form-control" name="f360p" id="f360p" >
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
                <button type="button" id="btnReloadTable" onclick="btnReloadTable()" class="btn btn-primary btn-sm">
                    <i class="fa fa-refresh fa-fw"></i> Reload Table
                </button>
                <button type="button" id="btnSingkron" onclick="btnSingkron()" class="btn btn-primary btn-sm">
                    <i class="fa fa-refresh fa-fw"></i> Singkron Folder
                </button>
                <button type="button" id="btnSingkronWeb" data-title="{{$result->title}}" class="btn btn-primary btn-sm">
                        <i class="fa fa-refresh fa-fw"></i> Singkron Wordpress
                </button>
                <!-- Button trigger modal -->
                <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" onclick="btnDetail()" data-target="#modelId2">
                    <i class="fa fa-film fa-fw"></i> Detail Drama
                </button>
                
                <!-- Modal -->
                <div class="modal fade" id="modelId2" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                        </button>
                                <h4 class="modal-title" id="modelTitleId">Modal title</h4>
                            </div>
                            <div class="modal-body">
                                <div class="form-group">
                                    <label class="control-label" for="email">Name</label>
                                    <input type="text" class="form-control" name="titleDetail" id="titleDetail" required>
                                </div>
                                <div class="form-group">
                                    <label class="control-label" for="email">Tags</label>
                                    <input type="text" class="form-control" name="Tags" id="Tags" >
                                </div>
                                <div class="form-group">
                                    <label class="control-label" for="email">Iframe</label>
                                    <textarea class="form-control" id="iframe" style="margin: 0px 3px 3px 0px; width: 566px; height: 146px;" name="iframe"></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id='content' >
        </div>
        <div class="alert alert-danger alert-dismissible" id="alert-danger" style="display:none"></div>
        <div class="alert alert-success alert-dismissible" id="alert-succes" style="display:none"></div>
        <table class="table table-striped table-bordered table-hover dataTable no-footer dtr-inline" style="width:100%" id="table-users">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Title</th>
                    <th>720p</th>
                    <th>360p</th>
                    <th>Action</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
@endsection
@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/1.7.1/clipboard.min.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
        new Clipboard('.btncopy');

        $( "#table-users" ).on( "click", "#btnShow" , function() {
            event.preventDefault()
            $('#modelId').modal('show');
            $("#formDrama")[0].reset()
            $("input[name=id]").val($(this).attr('data-id')) ;
            $("input[name=drama_id]").val($(this).attr('data-drama_id')) ;
            $("input[name=title]").val($(this).attr('data-title'));
            $("select[name=status]").val($(this).attr('data-status'));
            $("input[name=f720p]").val($(this).attr('data-f720p'));
            $("input[name=f360p]").val($(this).attr('data-f360p'));
        });
        $("#btnSingkronWeb").on("click", function(){
            event.preventDefault()

            var seacrh = $(this).attr("data-title");
            $.ajax({
                type:"get",
                url: "{{ route('webfrontSingkron') }}",
                success: function(data){
                    $("#content").html(data);
                    $("input[name=searchKeyword]").val(seacrh) ;

                }
            });
        });
        
        $("#formDrama").on("submit",function(){
            event.preventDefault()
            $.ajax({
                type:"post",
                url: "{{ route('epsPost', $result->id) }}",
                data: $( this ).serializeArray(),
                success: function(data){
                    $(".alert-success").fadeIn().html(data).wait(20000).fadeOut('slow');
                    $("#table-users").DataTable().ajax.reload(null, false);
                    $("#formDrama")[0].reset()
                    $('#modelId').modal('hide');
                    $(".alert-success").fadeOut(20000);

                },
                error: function(data){
                    $(".alert-success").fadeIn().html(data).wait(20000).fadeOut('slow');
                    $("#table-users").DataTable().ajax.reload(null, false);
                    $("#formDrama")[0].reset()
                    $('#modelId').modal('hide');
                    $(".alert-danger").fadeOut(20000);

                }
            });
        
        });

        $("#content").on("click",'#btnSubmitSingkron',function(){
            event.preventDefault()
            var siteId = $('#siteid').val();
            var searchKeyword = $('#searchKeyword').val();
            var drama_id = '{{$result->id}}';
            $.ajax({
                type:"POST",
                url: "{{route('webfrontSingkronpost')}}",
                data: {"id":siteId,"seacrh":searchKeyword},
                success: function(data){
                    $('#contentSearch').html(data);
                }
            });      
  
        });
        $( "#table-users" ).on( "click", "#btnDelete" , function() {
            var fn = $(this).attr('data-title');
            var id = $(this).attr('data-id');
            if (confirm('Are you sure you want to delete '+ fn +'?')) {
                $.ajax({
                    url: "{{ route('epsDelete',$result->id) }}",
                    type: "get",
                    data: {
                        _method: 'delete',
                        id: id,
                    },
                    success: function(data){
                        $(".alert-success").fadeIn().html('Delete User '+fn+' success').wait(20000).fadeOut('slow');
                        $("#table-users").DataTable().ajax.reload(null, false);
                    },
                    error: function(data){
                        $(".alert-success").fadeIn().html('Delete User '+fn+' Failed').wait(20000).fadeOut('slow');
                        $("#table-users").DataTable().ajax.reload(null, false);

                    }
                });
            }
        });
        $("#table-users").ready(function(){
            var oTable = $("#table-users").DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": "{{ route('epsData',$result->id) }}",
                "columns": [
                    {data: 'id', name: 'id'},
                    {data: 'title', name: 'title'},
                    {data: 'f720ps', name: 'f720p', orderable: false, searchable: false},
                    {data: 'f360ps', name: 'f360p' , orderable: false, searchable: false},
                    {data: 'action', name: 'action', orderable: false, searchable: false}
                ],
               
            });
        });
    });
    function btnAdd() { 
        $("#formDrama")[0].reset()
        $("input[name=drama_id]").val('{{ $result->id}}') ;
        $("input[name=title]").val('{{ $result->title}} E') ;
    };
    function btnSingkronToweb(idPost,titlePost) { 
        event.preventDefault()
        var siteId = $('#siteid').val();
        var siteName = $('#siteid').find('option:selected').text();
        var searchKeyword = $('#searchKeyword').val();
        var drama_id = '{{$result->id}}';
        $.ajax({
            type:"POST",
            url: "{{route('webfrontSingkronpost')}}/"+siteId,
            data: {"drama_id":drama_id,"idPost":idPost},
            success: function(data){
                data = JSON.parse(data);
                if(data.id){
                    $(".alert-success").fadeIn().html("<a href='"+data.link+"' target='_blank'>"+data.title.rendered+" "+siteName+"</a>").wait(20000).fadeOut('slow');
                }else{
                    $(".alert-danger").fadeIn().html(data.massage).wait(20000).fadeOut('slow');
                }
            }
        });  
    };
    function btnReloadTable(){
        $(".alert-success").fadeIn().html('Reload Success').wait(20000).fadeOut('slow');
        $("#table-users").DataTable().ajax.reload(null, false);
    }
    jQuery.fn.wait = function (MiliSeconds) {
        $(this).animate({ opacity: '+=0' }, MiliSeconds);
        return this;
    }
    function btnSingkron(){
        $.ajax({
                    url: "{{ route('driveEps',$result->id) }}",
                    type: "get",
                    success: function(data){
                        $("#table-users").DataTable().ajax.reload(null, false);
                        $("#content").fadeOut(function(){
                            setTimeout(function(){
                                $("#content").fadeIn().html(data).wait(20000).fadeOut('slow');
                            },1000);
                        }) 
                    }
        });    
    }
    function btnDetail(){
        $.ajax({
            type:"Get",
            url: "{{route('epsDetail',$result->id)}}",
            success: function(data){
                $("input[name=titleDetail]").val(data.title) ;
                $("input[name=Tags]").val(data.tag) ;
                $("textarea[name=iframe]").val(atob(data.iframe)) ;
            }
        });  
    }
</script>
@endsection