@extends('parts.default')
@section('title-page')
Drama {{$result->title}}
@endsection
@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="page-header">
            {{$result->title}} [{{$result->id}}]
            <button type="button" name="url_720p" id="url_720p"
                data-clipboard-text="{{$result->title}} [{{$result->id}}]" class="btn btn-xs btn-primary btncopy">Copy
                Folder</button>
        </div>
        <div class="panel panel-primary">
            <div class="panel-heading">{{$result->title}} Command Action</div>
            <div class="panel-body">

                <!-- Button trigger modal -->
                <button type="button" id="btnModal" onclick="btnAdd()" class="btn btn-primary btn-sm"
                    data-toggle="modal" data-target="#modelId">
                    <i class="fa fa-plus fa-fw"></i> Drama Eps.
                </button>
                <!-- Modal -->
                <div class="modal fade" id="modelId" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
                    aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <form action='{{ route("epsPost" , $result->id) }}' method="post" id="formDrama" role="form">
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
                                            <label class="control-label" for="email">Jumlah Eps</label>
                                            <input type="number" value="1" class="form-control" name="totalEps"
                                                id="totalEps" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="type" class="form-label">Status</label>
                                            <select class="custom-select form-control" name="status" id="status"
                                                required>
                                                <option value="Hardsub" selected>Hardsub</option>
                                                <option value="SUB">SUB</option>
                                            </select>
                                        </div>
                                        <div class="form-group dynamicbox">
                                            <strong>Link Video 1:</strong>
                                            <div class="input-group control-group increment">
                                                {!! Form::text('links[0][link]', null, array('placeholder'=> 'Link Google Drive','class' => 'form-control col-lg-8') ) !!}
                                                {!! Form::text('links[0][kualitas]', null, array('placeholder' => 'Kualitas','class' => 'form-control ') ) !!}
                                                <div class="input-group-btn">
                                                    <button id="remove-tr" link="" class="btn btn-danger remove-tr"><i class="glyphicon glyphicon-trash"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <div id="addlinkDrive" link="" count="0" class="btn btn-success addlinkDrive"><i class="glyphicon glyphicon-plus"></i></div>
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
                <button type="button" id="btnSingkronWeb" data-title="{{$result->title}}"
                    class="btn btn-primary btn-sm">
                    <i class="fa fa-refresh fa-fw"></i> Singkron Wordpress
                </button>
                <!-- Button trigger modal -->
                <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" onclick="btnDetail()"
                    data-target="#modelId2">
                    <i class="fa fa-film fa-fw"></i> Detail Drama
                </button>
                @if($result->folderid =="")
                <button type="button" id="btnaddFolder" data-id="{{$result->id}}" data-status="{{$result->status}}"
                    data-torrentlink="' . $data->torrentlink . '" data-torrentlink="' . $data->subsceneslink . '"
                    data-folderid="{{$result->folderid}}" data-title="{{$result->title}}"
                    class="btn btn-sm btn-primary"><i class="glyphicon glyphicon-plus"></i> Create Folder</button>
                @else
                <a href="https://drive.google.com/drive/folders/{{$result->folderid}}" class="btn btn-sm btn-primary"
                    target="_blank">Folder</a>
                @endif
                <!-- Modal -->
                <div class="modal fade" id="modelId2" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
                    aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <form action='{{ route("preCreate") }}' method="post" id="formDrama2" role="form">

                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                    <h4 class="modal-title" id="modelTitleId">Tag Generator</h4>
                                </div>
                                <div class="modal-body" id='detail-drama'>
                                    <div class="form-group">
                                        <input type="text" class="form-control" name="urlLink" id="urlLink" required>
                                        <button type="button" id="btnGet" name="btnGet"
                                            class="btn btn-xs btn-primary"><i
                                                class="glyphicon glyphicon-search"></i></button>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label" for="email">Name</label>
                                        <input type="text" class="form-control" name="titleDetail" id="titleDetail"
                                            required>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label" for="email">Tags</label>
                                        <input type="text" class="form-control" name="post_tag" id="post_tag">
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label" for="email">Categories</label>
                                        <input type="text" class="form-control" name="categories" id="categories">
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label" for="email">Status</label>
                                        <input type="text" class="form-control" name="status" id="status">
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label" for="email">Genre</label>
                                        <input type="text" class="form-control" name="genre" id="genre">
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label" for="email">Country</label>
                                        <input type="text" class="form-control" name="country" id="country">
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label" for="email">Cast</label>
                                        <input type="text" class="form-control" name="cast" id="cast">
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label" for="email">Iframe</label>
                                        <textarea class="form-control" id="iframe"
                                            style="margin: 0px 3px 3px 0px; width: 566px; height: 146px;"
                                            name="iframe"></textarea>
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
                @if($result->torrentlink )
                <a href="{{$result->torrentlink}}" target="_blank" class="btn btn-sm btn-primary"><i
                        class="glyphicon glyphicon-eye-open"></i> Torrent Link</a>
                @endif
                @if($result->subsceneslink)
                <a href="{{$result->subsceneslink}}" target="_blank" class="btn btn-sm btn-primary"><i
                        class="glyphicon glyphicon-eye-open"></i> Subscenes</a>
                @endif
                <button type="button" id="btnEdit" data-toggle="modal" data-target="#modelId2343"
                    data-id="{{$result->id}}" data-status="{{$result->status}}" data-type_id="{{$result->type_id}}"
                    data-country_id="{{$result->country_id}}" data-torrentlink="{{$result->torrentlink}}"
                    data-subsceneslink="{{$result->subsceneslink}}" data-folderid="{{$result->folderid}}"
                    data-title="{{$result->title}}" class="btn btn-sm btn-primary"><i
                        class="glyphicon glyphicon-plus"></i> Edit Drama</button>
                <!-- Modal -->
                <div class="modal fade" id="modelId2343" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
                    aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <form action="{{ route("dramaPost") }}" method="post" id="formDramaEdit" role="form"
                            autocomplete="off">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                    <h4 class="modal-title" id="modelTitleId">Drama</h4>
                                </div>
                                <div class="modal-body">
                                    <div class="container-fluid">
                                        <input type="hidden" name="id" id="idDrama" hidden>
                                        {{ csrf_field() }}
                                        <div class="form-group">
                                            <label class="control-label" for="email">Name</label>
                                            <input type="text" class="form-control" name="title" id="titleDrama"
                                                required>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label" for="email">Folder ID</label>
                                            <input type="text" class="form-control" name="folderid" id="folderidDrama">
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label" for="email">torrentlink</label>
                                            <input type="text" class="form-control" name="torrentlink"
                                                id="torrentlinkDrama">
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label" for="email">Link Subscanes</label>
                                            <input type="text" class="form-control" name="subsceneslink"
                                                id="subsceneslinkDrama">
                                        </div>
                                        <div class="form-group">
                                            <label for="type" class="form-label">Status</label>
                                            <select class="custom-select form-control" name="status" id="statusDrama"
                                                required>
                                                <option selected>Select one</option>
                                                @foreach($status as $status){
                                                <option value="{{$status->status}}">{{$status->status}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="type" class="form-label">Country</label>
                                            <select class="custom-select form-control" name="country_id"
                                                id="country_idDrama" required>
                                                <option selected>Select one</option>
                                                @foreach($country as $country){
                                                <option value="{{$country->id}}">{{$country->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="type" class="form-label">Type</label>
                                            <select class="custom-select form-control" name="type_id" id="type_idDrama"
                                                required>
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
        <div id='content'>
        </div>
        <div class="alert alert-danger alert-dismissible" id="alert-danger" style="display:none"></div>
        <div class="alert alert-success alert-dismissible" id="alert-succes" style="display:none"></div>
        <table class="table table-striped table-bordered table-hover dataTable no-footer dtr-inline" style="width:100%"
            id="table-users">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Title</th>
                    <th>Links</th>
                    <th>Backups</th>
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
$(document).ready(function() {
    var dramaidKode = {!! $result->id !!};
    new Clipboard('.btncopy');
    $("#btnEdit").on("click", function() {
        $("input[id=idDrama]").val($(this).attr('data-id'));
        $("input[id=titleDrama]").val($(this).attr('data-title'));
        $("input[id=folderidDrama]").val($(this).attr('data-folderid'));
        $("select[id=statusDrama]").val($(this).attr('data-status'));
        $("select[id=type_idDrama]").val($(this).attr('data-type_id'));
        $("select[id=country_idDrama]").val($(this).attr('data-country_id'));
        $("input[id=subsceneslinkDrama]").val($(this).attr('data-subsceneslink'));
        $("input[id=torrentlinkDrama]").val($(this).attr('data-torrentlink'));
    });
    $("#formDramaEdit").on("submit", function() {
        event.preventDefault()
        $.ajax({
            type: "post",
            url: "{{ route('dramaPost') }}",
            data: $(this).serializeArray(),
            success: function(data) {
                Swal.fire({
                    position: 'top-end',
                    icon: 'success',
                    title: 'success',
                    showConfirmButton: false,
                    timer: 1500
                });
                $('#modelId2343').modal('hide');
            },
            error: function(data) {
                $('#modelId2343').modal('hide');
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
    $("#table-users").on("click", "#btnShow", function() {
        event.preventDefault()
        $('#modelId').modal('show');
        $("#formDrama")[0].reset()
        $("input[name=id]").val($(this).attr('data-id'));
        $("input[name=drama_id]").val($(this).attr('data-drama_id'));
        $("input[name=title]").val($(this).attr('data-title'));
        $("select[name=status]").val($(this).attr('data-status'));
        $("input[name=f720p]").val($(this).attr('data-f720p'));
        $("input[name=f360p]").val($(this).attr('data-f360p'));
    });
    $("#btnSingkronWeb").on("click", function() {
        event.preventDefault()

        var seacrh = $(this).attr("data-title");
        $.ajax({
            type: "get",
            url: "{{ route('webfrontSingkron') }}",
            success: function(data) {
                $("#content").html(data);
                $("input[name=searchKeyword]").val(seacrh);
            }
        });
    });
    $("#btnGet").on("click", function() {
        event.preventDefault()
        var urlSearch = $("#urlLink").val();
        $.ajax({
            url: "{{ route('dramacurl') }}" + "?source=" + urlSearch,
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
    });
        $("#content").on("click", '#btnaddPostWp', function() {

        window.open("{{ route('webfrontAddPost',$result->id) }}","_self");

    });
    $("#btnaddFolder").on("click", function() {
        var fn = $(this).attr('data-title');
        var ids = $(this).attr('data-id');
        if (confirm('Are you sure you want to Crete Folder ' + fn + ' [' + ids + '] in Drive?')) {
            $.ajax({
                url: "{{ route('createFolderDrive') }}",
                type: "get",
                data: {
                    id: $(this).attr('data-id')
                },
                success: function(data) {
                    Swal.fire({
                        position: 'top-end',
                        icon: 'success',
                        title: 'success',
                        showConfirmButton: false,
                        timer: 1500
                    });
                    $("#table-users").DataTable().ajax.reload(null, false);
                    document.location.reload();
                },
                error: function(data) {
                    Swal.fire({
                        position: 'top-end',
                        icon: 'error',
                        title: 'error',
                        showConfirmButton: false,
                        timer: 1500
                    });
                    $("#table-users").DataTable().ajax.reload(null, false);
                }
            });
        }
    });
    $("#formDrama2").on("submit", function() {
        event.preventDefault()
        $.ajax({
            type: "post",
            url: "{{ route('preCreate') }}",
            data: $(this).serializeArray(),
            success: function(data) {
                $("#table-users").DataTable().ajax.reload(null, false);
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

                $("#table-users").DataTable().ajax.reload(null, false);
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
    $("#formDrama").on("submit", function() {
        event.preventDefault()
        $.ajax({
            type: "post",
            url: "{{ route('epsPost', $result->id) }}",
            data: $(this).serializeArray(),
            success: function(data) {
                $("#table-users").DataTable().ajax.reload(null, false);
                $("#formDrama")[0].reset()
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

                $("#table-users").DataTable().ajax.reload(null, false);
                $("#formDrama")[0].reset()
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

    $("#content").on("click", '#btnSubmitSingkron', function() {
        event.preventDefault()
        var siteId = $('#siteid').val();
        var searchKeyword = $('#searchKeyword').val();
        var drama_id = '{{$result->id}}';
        $.ajax({
            type: "POST",
            url: "{{route('webfrontSingkronpost')}}",
            data: {
                "id": siteId,
                "seacrh": searchKeyword
            },
            success: function(data) {
                $('#contentSearch').html(data);
            }
        });

    });
    $("#table-users").on("click", "#btnDelete", function() {
        var fn = $(this).attr('data-title');
        var id = $(this).attr('data-id');
        if (confirm('Are you sure you want to delete ' + fn + '?')) {
            $.ajax({
                url: "{{ route('epsDelete',$result->id) }}",
                type: "delete",
                data: {
                    _method: 'delete',
                    id: id,
                },
                success: function(data) {
                    Swal.fire({
                        position: 'top-end',
                        icon: 'success',
                        title: 'success',
                        showConfirmButton: false,
                        timer: 1500
                    });

                    $("#table-users").DataTable().ajax.reload(null, false);
                },
                error: function(data) {
                    Swal.fire({
                        position: 'top-end',
                        icon: 'error',
                        title: 'error',
                        showConfirmButton: false,
                        timer: 1500
                    });

                    $("#table-users").DataTable().ajax.reload(null, false);

                }
            });
        }
    });
    $("#table-users").ready(function() {
        var oTable = $("#table-users").DataTable({
            "processing": true,
            "pageLength": 25,
            "serverSide": true,
            "ajax": "{{ route('epsData',$result->id) }}",
            "columns": [{
                    data: 'DT_Row_Index',
                    name: 'DT_Row_Index',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'title',
                    name: 'title'
                },
                {
                    data: 'links',
                    name: 'Links',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'backups',
                    name: 'Backups',
                    orderable: false,
                    searchable: false
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
    $('body').on('click', '.addlinkDrive', function(elemen) {
            elemen.preventDefault();
            var i = $(this).attr('count');
            ++i;
            j = i + 1;
            $(".dynamicbox").append(
                '<div class="form-group"><tr><strong>Link Video ' + j + ':</strong>' +
                '<div class = "input-group control-group increment"> ' +
                '<input type="text" name="links[' + i + '][link]" placeholder="Link Google Drive" class="form-control" /></td>' +
                '<input type="text" name="links[' + i + '][kualitas]" placeholder="Kualitas" class="form-control" /></td>' +
                '<div class="input-group-btn"><button type="button" link="" class="btn btn-danger remove-tr"><i class="glyphicon glyphicon-trash"></i></button></td>' +
                '</tr></div>');
            $(this).attr("count", i);

    });

    $('body').on('click', '.remove-tr', function(elemen) {
            elemen.preventDefault();
            const urlsdelete = $(this).attr('link');
            if (urlsdelete == "") {
                $(this).parent().parent().parent().remove();
            } else {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to delete this!",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.value) {
                        axios({
                            url: urlsdelete,
                            credentials: true,
                            method: "DELETE",
                        }).then(response => {
                            console.log(response.data.status);
                            $(this).parent().parent().parent().remove();
                            swal2(response.data.status, response.data.message);
                        }).catch(error => {
                            console.log(error.response);
                        });
                    }
                })
            }
    });
});



function btnAdd() {
    $("#formDrama")[0].reset()
    $("input[name=drama_id]").val('{{ $result->id}}');
    $("input[name=title]").val('{{ $result->title}} E');
};

function btnSingkronToweb(idPost, titlePost) {
    event.preventDefault()
    var siteId = $('#siteid').val();
    var siteName = $('#siteid').find('option:selected').text();
    var searchKeyword = $('#searchKeyword').val();
    var drama_id = '{{$result->id}}';
    $.ajax({
        type: "POST",
        url: "{{route('webfrontSingkronpost')}}/" + siteId,
        data: {
            "drama_id": drama_id,
            "idPost": idPost
        },
        success: function(data) {
            data = JSON.parse(data);
            if (data.id) {
                $(".alert-success").fadeIn().html("<a href='" + data.link + "' target='_blank'>" + data
                    .title.rendered + " " + siteName + "</a>").wait(20000).fadeOut('slow');
            } else {
                $(".alert-danger").fadeIn().html(data.massage).wait(20000).fadeOut('slow');
            }
        }
    });
};

function btnReloadTable() {
    $(".alert-success").fadeIn().html('Reload Success').wait(20000).fadeOut('slow');
    $("#table-users").DataTable().ajax.reload(null, false);
}
jQuery.fn.wait = function(MiliSeconds) {
    $(this).animate({
        opacity: '+=0'
    }, MiliSeconds);
    return this;
}

function btnSingkron() {
    $.ajax({
        url: "{{ route('driveEps',$result->id) }}",
        type: "get",
        success: function(data) {
            $("#table-users").DataTable().ajax.reload(null, false);
            $("#content").fadeOut(function() {
                setTimeout(function() {
                    $("#content").fadeIn().html(data).wait(20000).fadeOut('slow');
                }, 1000);
            })
        }
    });
}

function btnDetail() {
    $.ajax({
        type: "GET",
        url: "{{route('epsDetail',$result->id)}}",
        success: function(data) {
            $("input[name=titleDetail]").val(data.title);
            $("input[name=post_tag]").val(data.tag);
            $("input[name=status]").val(data.status);
            $("input[name=categories]").val(data.category);
            $("input[name=country]").val(data.country);
            $("textarea[name=iframe]").val(atob(data.iframe));
        }
    });
}
</script>
@endsection
