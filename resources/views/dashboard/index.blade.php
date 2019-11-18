@extends('parts.default')
@section('title-page')
Dahsboard
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
                <!-- Button trigger modal -->
                <button type="button" id="btnSingkron" onclick="btnSingkron()" class="btn btn-primary btn-sm">
                    <i class="fa fa-refresh fa-fw"></i> Singkron Folder
                </button>
                <button type="button" id="btnReloadTable" onclick="btnReloadTable()" class="btn btn-primary btn-sm">
                    <i class="fa fa-refresh fa-fw"></i> Reload Table
                </button>
            </div>
        </div>
        <div id='content'>
        </div>
        <div class="alert alert-danger alert-dismissible" id="alert-danger" style="display:none"></div>
        <div class="alert alert-success alert-dismissible" id="alert-succes" style="display:none"></div>

    </div>

    <div class="col-lg-12">
        <table class="table table-striped table-bordered table-hover dataTable no-footer dtr-inline" style="width:100%" id="table-users">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Title</th>
                    <th>Status</th>
                    <th>Updated At</th>
                    <th>Action</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
@endsection
@section('scripts')

<script type="text/javascript">
    function btnReloadTable() {
        $(".alert-success").fadeIn().html('Reload Success').wait(20000).fadeOut('slow');
        $("#table-users").DataTable().ajax.reload(null, false);
    }

    function btnSingkronToweb(idPost, titlePost) {
        event.preventDefault()
        var siteId = $('#siteid').val();
        var siteName = $('#siteid').find('option:selected').text();
        var searchKeyword = $('#searchKeyword').val();
        var drama_id = $('#idDrama').val();
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
        });
        $("#content").on("click", '#btnSubmitSingkron', function() {
            event.preventDefault()
            var siteId = $('#siteid').val();
            var searchKeyword = $('#searchKeyword').val();
            var drama_id = $('idDrama').val();
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
        $("#table-users").on("click", "#btnSingkronWeb", function() {

            event.preventDefault()
            var dmaIDD = $(this).attr("data-drama_id");
            var seacrh = $(this).attr("data-title");
            $.ajax({
                type: "get",
                url: "{{ route('webfrontSingkron') }}",
                success: function(data) {
                    $("#content").html(data);
                    $("input[name=searchKeyword]").val(seacrh);
                    $("input[name=idDrama]").val(dmaIDD);
                }
            });
        });
        $("#formDrama").on("submit", function() {
            event.preventDefault()
            $.ajax({
                type: "post",
                url: "{{ route('dramaPost') }}",
                data: $(this).serializeArray(),
                success: function(data) {
                    swal("success", data, "success")
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
                    swal("error", data, "error")
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
                        $("#table-users").DataTable().ajax.reload(null, false);
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
        $("#table-users").ready(function() {
            oTable = $("#table-users").DataTable({
                "processing": true,
                "serverSide": true,
                "order": [
                    [3, "desc"]
                ],
                "ajax": "{{ route('dramaDataUpdate') }}",
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
        jQuery.fn.wait = function(MiliSeconds) {
            $(this).animate({
                opacity: '+=0'
            }, MiliSeconds);
            return this;
        }
    });

    function btnSingkron() {
        $.ajax({
            url: "{{ route('driveEps','0') }}",
            type: "get",

            success: function(data) {
                $('#alert-succes').html(data);
                $("#table-users").DataTable().ajax.reload(null, false);
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

    function btnAdd() {
        $("#formDrama")[0].reset()
    };
</script>
@endsection