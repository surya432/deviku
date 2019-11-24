@extends('parts.default')
@section('title-page')
Add New Post Wordpress
@endsection
@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="page-header">
            Add New Post Wordpress
        </div>
        <div class="col-lg-12">
            <div class="panel panel-primary">
                <div class="panel-heading"> Add New Post Wordpress</div>
                <div class="panel-body">
                <form method="POST" action="{{route('preCreate')}}" id="formDrama2" enctype="multipart/form-data">
                        <div class="form-group">
                            <input type="text" class="form-control " name="urlLink" id="urlLink" required>
                            <button type="button" id="btnGet" name="btnGet" class="btn btn-md btn-primary "><i
                                    class="glyphicon glyphicon-search"></i></button>
                        </div>
                        <div class="form-group">
                            <label for="">Site</label>
                            <select name="siteid" id="siteid" class="form-control">
                                @foreach($site as $site)
                                <option value="{{$site->id}}">{{$site->site}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="email">Name</label>
                            <input type="text" class="form-control" value="{{$result['title']}}" name="titleDetail"
                                id="titleDetail" required>
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="email">Image</label>
                            <input type="file" class="form-control" name="imageupload" id="imageupload" required>
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="email">Tags</label>
                            <input type="text" class="form-control" name="post_tag" value="{{$result['tag']}}"
                                id="post_tag">
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="email">Categories</label>
                            <input type="text" class="form-control" name="categories" value="{{$result['category']}}"
                                id="categories">
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="email">Status</label>
                            <input type="text" class="form-control" value="{{$result['status']}}" name="status"
                                id="status">
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="email">Genre</label>
                            <input type="text" class="form-control" name="genre" id="genre">
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="email">Country</label>
                            <input type="text" class="form-control" name="country" value="{{$result['country']}}"
                                id="country">
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="email">Cast</label>
                            <input type="text" class="form-control" name="cast" id="cast">
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="email">Iframe</label>
                            <textarea class="form-control" id="iframe"
                                style="margin: 0px 3px 3px 0px; width: 100%; height: 146px;"
                                name="iframe"> {{ base64_decode($result['iframe']) }}</textarea>
                        </div>
                        <div class="form-group text-right">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save</button>
                    </form>
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
    $("#btnGet").on("click", function() {
        event.preventDefault()
        var urlSearch = $("#urlLink").val();
        if (urlSearch) {

            $.ajax({
                url: "/detail/drama?source=" + urlSearch,
                type: "get",
                success: function(data) {
                    JSON.stringify(data); //to string
                    // console.log(data.plot);
                    $('#cast').val(data.cast);
                    $('#genre').val(data.genre);
                },
                error: function(data) {
                    Swal.fire({
                        position: 'top-end',
                        icon: 'error',
                        title: 'error',
                        showConfirmButton: false,
                        timer: 1500
                    });
                }
            });
        }
    });
    $("#formDrama2").on("submit", function() {
        event.preventDefault()
        var formData = new FormData(this);

        $.ajax({
            type: "post",
            url: $(this).attr('action'),
            data:formData,
            cache:false,
            contentType: false,
            processData: false,
            success: function(data) {
                // $("#table-users").DataTable().ajax.reload(null, false);
                // $("#formDrama2")[0].reset()
                $('#modelId').modal('hide');
                Swal.fire({
                    position: 'top-end',
                    icon: 'success',
                    title: 'success',
                    showConfirmButton: false,
                    timer: 1500
                });

            },
            error: function(data) {

                // $("#table-users").DataTable().ajax.reload(null, false);
                // $("#formDrama2")[0].reset()
                $('#modelId').modal('hide');
                Swal.fire({
                    position: 'top-end',
                    icon: 'error',
                    title: 'error',
                    showConfirmButton: false,
                    timer: 1500
                });
            }
        });

    });
})
</script>
@endsection
