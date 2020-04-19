{!! Form::open(array('route' => ['cookies.update', $googleDrivePlayer->id],'method'=>'PATCH','role' => 'form', 'id' => 'my_form')) !!}
{{ Form::hidden('id', $googleDrivePlayer->id, array('id' => 'invisible_id')) }}

<div class="modal-header">
    <h5 class="modal-title">Modal title</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>Email:</strong>
                {!! Form::text('email', $googleDrivePlayer->email, array('placeholder' => 'Email','class' => 'form-control')) !!}
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>Json cookies:</strong>
                {!! Form::textarea('cookiestext', $googleDrivePlayer->cookiestext, array('placeholder' => 'Json Cookies','rows' => 4, 'cols' => 54,'class' => 'form-control')) !!}
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>Status:</strong>
                <select class="form-control" name="status">
                    <option value="active">Active</option>
                    <option value="disable">Disable</option>
                </select>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
    <button type="submit" id="saveBtn" value="create" class="btn btn-primary btn-submit btn-action">Save changes</button>
</div>
{!! Form::close() !!}