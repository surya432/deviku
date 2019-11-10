@extends('parts.default')
@section('title-page')
Viu Generator
@endsection
@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="page-header">
            Viu Generator
        </div>
        <div class="col-lg-12">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    Viu Generator
                </div>
                <div class="panel-body">
                    <div id="alert-danger" class="alert alert-danger" style="display:none"></div>
                    <div id="alert-success" class="alert alert-success" style="display:none"></div>

                    <div class="col-12">
                        <div class="form-group">
                            <button type="button" id="btnSingkron" onclick="getCode('senin')"
                                class="btn btn-primary btn-sm">
                                <i class="fa fa-plus fa-fw"></i> Senin
                            </button>
                            <button type="button" id="btnSingkron" onclick="getCode('selasa')"
                                class="btn btn-primary btn-sm">
                                <i class="fa fa-plus fa-fw"></i> Selasa
                            </button>
                            <button type="button" id="btnSingkron" onclick="getCode('rabu')"
                                class="btn btn-primary btn-sm">
                                <i class="fa fa-plus fa-fw"></i> Rabu
                            </button>
                            <button type="button" id="btnSingkron" onclick="getCode('kamis')"
                                class="btn btn-primary btn-sm">
                                <i class="fa fa-plus fa-fw"></i> Kamis
                            </button>
                            <button type="button" id="btnSingkron" onclick="getCode('jumat')"
                                class="btn btn-primary btn-sm">
                                <i class="fa fa-plus fa-fw"></i> Jumat
                            </button>
                            <button type="button" id="btnSingkron" onclick="getCode('sabtu')"
                                class="btn btn-primary btn-sm">
                                <i class="fa fa-plus fa-fw"></i> Sabtu
                            </button>
                            <button type="button" id="btnSingkron" onclick="getCode('minggu')"
                                class="btn btn-primary btn-sm">
                                <i class="fa fa-plus fa-fw"></i> Minggu
                            </button>
                        </div>
                    </div>
                    <div class="form-inline col-12">
                        <div class="form-group mx-sm-3 mb-2">
                            <label for="inputPassword2" class="sr-only">Password</label>
                            <input type="text" class="form-control" id="inputIdViu" placeholder="ID Drama Viu ">
                            <input type="text" class="form-control" id="inputStartEp" placeholder="Start Eps">
                            <input type="text" class="form-control" id="inputEndEp" placeholder="End Eps">
                            <input type="text" class="form-control" id="dramaId" placeholder="Drama id">
                        </div>
                        <button type="submit" onclick="getById()" class="btn btn-primary mb-2">Generate</button>
                    </div>


                </div>
            </div>
            <div class="form-group">
                <label for="exampleFormControlTextarea1">FFMPEG SCRIPT</label>
                <textarea class="form-control" id="exampleFormControlTextarea1" rows="13"></textarea>
            </div>
        </div>
    </div>
</div>

@endsection
@section('scripts')
<script type="text/javascript">
$(document).ready(function() {
    $("#table-users").on("click", "#btnSingkron", function() {
        alert("You CLicker");
    });

})

function getById() {
    var inputIdViu = $("#inputIdViu").val();
    var inputStartEp = $("#inputStartEp").val();
    var inputEndEp = $("#inputEndEp").val();
    var dramaId = $("#dramaId").val();
    $.ajax({
        type: "post",
        url: "{{ route('viugetdata') }}",
        data: {
            _method: 'post',
            id: inputIdViu,
            inputStartEp: inputStartEp,
            inputEndEp: inputEndEp,
            dramaId: dramaId,

        },
        success: function(data) {
            swal("success", "success", "success");
            $('#exampleFormControlTextarea1').html(data);
        },
        error: function(request, status, error) {
            swal("error", "error", "error");
            $('#exampleFormControlTextarea1').html("");
            alert(request.responseText);
        }
    });
}

function getCode(id) {

    $.ajax({
        type: "post",
        url: "{{ route('viugetdata') }}",
        data: {
            _method: 'post',
            id: id
        },
        success: function(data) {
            $(".alert-success").fadeIn().html("Berhasil Di Dapatkan").wait(3000).fadeOut('slow');
            $('#exampleFormControlTextarea1').html(data);
        },
        error: function(request, status, error) {
            $(".alert-danger").fadeIn().html("Gagal Di Dapatkan").wait(3000).fadeOut('slow');
            $('#exampleFormControlTextarea1').html("");
        }
    });
}
jQuery.fn.wait = function(MiliSeconds) {
    $(this).animate({
        opacity: '+=0'
    }, MiliSeconds);
    return this;
}
</script>

@endsection