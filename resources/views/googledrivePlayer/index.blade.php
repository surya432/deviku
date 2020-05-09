@extends('parts.default')
@section('title-page')
Master Cookies
@endsection
@section('content')
<div class="panel panel-primary">
    <div class="panel-heading">
        List Player Cookies
    </div>
    <div class="panel-body">
         <div class="col-lg-12">
              <!-- Button trigger modal -->
          <button type="button" link="{{ route('cookies.create') }}" class="btn btn-primary btn-create btn-action btn-sm btn-flat " data-toggle="modal" data-target="#modelId">
            Create New Cookies
        </button>

        <!-- Modal -->
         </div>
     <div class="col-lg-12">   
         <div class="table-responsive">
            <div class="box-body">
                <table class="table table-striped table-hover table-responsive" id="table">
                    <thead>
                        <tr>
                            <th witdh="5%">No</th>
                            <th>Email</th>
                            <th width="25%">Key</th>
                            <th width="25%">Status</th>
                            <th width="10%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    </div>
</div>

<div class="modal" id="modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">

        </div>
    </div>
</div>
@endsection
@section('scripts')
<script src="https://unpkg.com/axios/dist/axios.min.js"></script>

<script type="text/javascript">
    $(document).ready(function() {
        $('body').on('click', '.btn-action', function(elemen) {
            elemen.preventDefault();
            $(".btn-action").attr("disabled", true);

            if ($(this).hasClass('btn-create')) {
                showModal($(this));

            } else if ($(this).hasClass('btn-detail')) {
                showModal($(this));
            } else if ($(this).hasClass('btn-edit')) {
                showModal($(this));
            } else if ($(this).hasClass('delete')) {
                const urlsdelete = $(this).attr('link');
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
                            credentials: false,
                            method: "DELETE",
                        }).then(response => {
                            console.log(response);
                            table.draw();
                            swal2(data.status, data.message);
                        }).catch(error => {
                            console.log(error.response);
                        });
                    }
                })
            }
            $(".btn-action").attr("disabled", false);

        });
        $(document).ajaxStart(function() {
            // Pace.restart();
        });



        function showModal(el) {
            var urls = el.attr('link'),
                title = el.attr('title');

            $('.modal-title').text(title);

            axios({
                url: urls,
                method: "GET",
            }).then(response => {
                // // console.log(response);
                $('.modal-content').html(response.data);
                // // initElem();
                $('#modal-button').text(el.hasClass('edit') ? 'Edit' : 'Simpan');
                if (el.hasClass('btn-addlink')) {
                    $('#invisible_id').val('1');

                }
                $('.modal').modal('show');

            }).catch(error => {
                console.log(error.response);
            });
        }
        $('body').on('click', '#saveBtn', function(e) {
            e.preventDefault();
            $(this).html('Sending..');
            $("#btnSubmit").attr("disabled", true);

            $.ajax({
                data: $('#my_form').serialize(),
                url: $('#my_form').attr("action"),
                type: $('#my_form').attr("method"),
                dataType: 'json',
                success: function(data) {

                    $('#my_form').trigger("reset");
                    $('.modal').modal('hide');
                    swal2("success", data.message);
                    table.draw();

                },
                error: function(data) {
                    swal2("error", data.statusText);

                    console.log('Error:', data);
                    $('#saveBtn').html('Save Changes');
                }
            });
            $("#btnSubmit").attr("disabled", false);

        });

        function swal2(types, titles) {
            Swal.fire({
                position: 'top-end',
                type: types,
                title: titles,
                showConfirmButton: false,
                timer: 2000
            })
        }
        table =
            $('#table').DataTable({
                //server-side
                processing: true,
                serverSide: true,
                ajax: {
                    'url': "{!! route('jsonDataTableCookies') !!}",
                    "type": "GET"
                },
                columns: [{
                        data: 'DT_Row_Index',
                        name: 'DT_Row_Index',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: 'cookiestext',
                        name: 'cookiestext'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'action',
                        name: 'action'
                    },
                ]
            });



    });
</script>
@endsection
