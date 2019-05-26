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
                        <div class="form-group">
                            <label class="control-label" for="folder360p">Folder Upload</label>
                            <input type="text" class="form-control" name="folderUpload" id="folderUpload" required>
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="folder360p">Folder Upload</label>
                            <input type="text" class="form-control" name="sizeCount" id="sizeCount" required>
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="folder360p">Token Drive Admin</label>
                            <input type="text" class="form-control" name="tokenDriveAdmin" id="tokenDriveAdmin"
                                required>
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="folder360p">Google API</label>
                            <input type="text" class="form-control" name="apiUrl" id="apiUrl" required>
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="folder360p">Token Viu</label>
                            <input type="text" class="form-control" name="tokenViu" id="tokenViu" required>
                        </div>
                        <div class="form-group col-md-12">
                            <div class="col-md-3">
                                <label class="control-label" for="folder360p">ViuSenin</label>
                                <input type="text" class="form-control" name="viuSenin" id="viuSenin" required>
                            </div>
                            <div class="form-group col-md-3">
                                <label class="control-label" for="folder360p">ViuSelasa</label>
                                <input type="text" class="form-control" name="viuSelasa" id="viuSelasa" required>
                            </div>
                            <div class="form-group col-md-3">
                                <label class="control-label" for="folder360p">ViuRabu</label>
                                <input type="text" class="form-control" name="viuRabu" id="viuRabu" required>
                            </div>
                            <div class="form-group col-md-3">
                                <label class="control-label" for="folder360p">ViuKamis</label>
                                <input type="text" class="form-control" name="viuKamis" id="viuKamis" required>
                            </div>
                            <div class="form-group col-md-3">
                                <label class="control-label" for="folder360p">ViuJumat</label>
                                <input type="text" class="form-control" name="viuJumat" id="viuJumat" required>
                            </div>
                            <div class="form-group col-md-3">
                                <label class="control-label" for="folder360p">ViuSabtu</label>
                                <input type="text" class="form-control" name="viuSabtu" id="viuSabtu" required>
                            </div>
                            <div class="col-md-3">
                                <label class="control-label" for="folder360p">ViuMinggu</label>
                                <input type="text" class="form-control" name="viuMinggu" id="viuMinggu" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <button class="btn btn-primary text-center" type="submit">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')

<script type="text/javascript">
$(document).ready(function() {
    function getData() {
        $.ajax({
            type: "GET",
            url: "{{ route('setting.getData')}}",
            success: function(data) {
                $("input[name=id]").val(data[0].id);
                $("input[name=site_name]").val(data[0].site_name);
                $("input[name=folder720p]").val(data[0].folder720p);
                $("input[name=folder360p]").val(data[0].folder360p);
                $("input[name=folderUpload]").val(data[0].folderUpload);
                $("input[name=tokenDriveAdmin]").val(data[0].tokenDriveAdmin);
                $("input[name=apiUrl]").val(data[0].apiUrl);
                $("input[name=tokenViu]").val(data[0].tokenViu);
                $("input[name=viuSenin]").val(data[0].viuSenin);
                $("input[name=viuSelasa]").val(data[0].viuSelasa);
                $("input[name=viuRabu]").val(data[0].viuRabu);
                $("input[name=viuKamis]").val(data[0].viuKamis);
                $("input[name=viuJumat]").val(data[0].viuJumat);
                $("input[name=viuSabtu]").val(data[0].viuSabtu);
                $("input[name=viuMinggu]").val(data[0].viuMinggu);
                $("input[name=sizeCount]").val(data[0].sizeCount);
            },
            error: function(data) {
                $(".alert-danger").text("Error")
                $(".alert-danger").show()

            }
        });
    };
    getData();
    $("#formSetting").on("submit", function() {
        event.preventDefault()
        $.ajax({
            type: "post",
            url: "{{ route('setting.postData') }}",
            data: $(this).serializeArray(),
            success: function(data) {
                $("input[name=id]").val(data.id);
                $("input[name=site_name]").val(data.site_name);
                $("input[name=folder720p]").val(data.folder720p);
                $("input[name=folder360p]").val(data.folder360p);
                $("input[name=folderUpload]").val(data.folderUpload);
                $("input[name=tokenDriveAdmin]").val(data.tokenDriveAdmin);
                $("input[name=apiUrl]").val(data.apiUrl);
                $("input[name=tokenViu]").val(data.tokenViu);
                $("input[name=viuSenin]").val(data.viuSenin);
                $("input[name=viuSelasa]").val(data.viuSelasa);
                $("input[name=viuRabu]").val(data.viuRabu);
                $("input[name=viuKamis]").val(data.viuKamis);
                $("input[name=viuJumat]").val(data.viuJumat);
                $("input[name=viuSabtu]").val(data.viuSabtu);
                $("input[name=viuMinggu]").val(data.viuMinggu);
                $(".alert-success").text("Update Success")
                $(".alert-success").show()
            },
            error: function(data) {
                $("input[name=id]").val(data.id);
                $("input[name=site_name]").val(data.site_name);
                $("input[name=folder720p]").val(data.folder720p);
                $("input[name=folder360p]").val(data.folder360p);
                $("input[name=folderUpload]").val(data.folderUpload);
                $("input[name=tokenDriveAdmin]").val(data.tokenDriveAdmin);
                $("input[name=apiUrl]").val(data.apiUrl);
                $("input[name=tokenViu]").val(data.tokenViu);
                $("input[name=viuSenin]").val(data.viuSenin);
                $("input[name=viuSelasa]").val(data.viuSelasa);
                $("input[name=viuRabu]").val(data.viuRabu);
                $("input[name=viuKamis]").val(data.viuKamis);
                $("input[name=viuJumat]").val(data.viuJumat);
                $("input[name=viuSabtu]").val(data.viuSabtu);
                $("input[name=viuMinggu]").val(data.viuMinggu);
                $(".alert-danger").text("Failed Update")
                $(".alert-danger").show()
            }
        });
    });
});
</script>
@endsection