@extends('parts.default')
@section('title-page')
 Setting
@endsection
@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="page-header">
            Setting
        </div>
        <div class="col-lg-12">
            <div class="panel panel-primary">
                <div class="panel-heading">Setting</div>
                <div class="panel-body">
                    <div class="alert alert-danger" id="alert-danger" style="display:none"></div>
                    <div class="alert alert-success" id="alert-succes" style="display:none"></div>
                <form action="{{ route("setting.postData") }}" method="post" id="formSetting" role="form">
                        <input type="hidden" name="id" id="id" hidden>
                        {{ csrf_field() }}

                        <div class="form-group">
                            <label class="control-label" for="site_name">Site Name</label>
                            <input type="text" class="form-control" name="site_name" id="site_name" required></div>
                        <div class="form-group">
                            <label class="control-label" for="folder 720p">Folder 720p</label>
                            <input type="text" class="form-control" name="folder720p" id="folder720p" required>
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="folder360p">Folder 360p</label>
                            <input type="text" class="form-control" name="folder360p" id="folder360p" required>
                        </div>
                        <button class="btn btn-primary text-center"type="submit">Save</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')

<script type="text/javascript">
    $(document).ready(function(){
        function getData(){
            $.ajax({
                type: "GET",
                url:"{{ route('setting.getData')}}",
                success: function(data){
                    $("input[name=id]").val(data[0].id) ;
                    $("input[name=site_name]").val(data[0].site_name) ;
                    $("input[name=folder720p]").val(data[0].folder720p) ;
                    $("input[name=folder360p]").val(data[0].folder360p) ;
                },
                error: function(data){
                    $(".alert-danger").text("Error")
                    $(".alert-danger").show()

                }
            });
        };
        getData();
        $("#formSetting").on("submit",function(){
            event.preventDefault()
            $.ajax({
                type:"post",
                url: "{{ route('setting.postData') }}",
                data: $( this ).serializeArray(),
                success: function(data){
                    $("input[name=id]").val(data.id) ;
                    $("input[name=site_name]").val(data.site_name) ;
                    $("input[name=folder720p]").val(data.folder720p) ;
                    $("input[name=folder360p]").val(data.folder360p) ;
                    $(".alert-success").text("Update Success")
                    $(".alert-success").show()
                },
                error: function(data){
                    $("input[name=id]").val(data.id) ;
                    $("input[name=site_name]").val(data.site_name);
                    $("input[name=folder720p]").val(data.folder720p);
                    $("input[name=folder360p]").val(data.folder360p);
                    $(".alert-danger").text("Failed Update")
                    $(".alert-danger").show()
                }
            });
        });
    });
</script>
@endsection