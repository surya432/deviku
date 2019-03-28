<div class="panel panel-primary copyright-wrap" id="copyright-wrap">
        <div class="panel-heading">Singkron Wordpress
            <button type="button" class="close" data-target="#copyright-wrap" data-dismiss="alert"> <span aria-hidden="true">&times;</span><span class="sr-only">Close</span>
            </button>
        </div>
        <div class="panel-body">
        <form action="{{route('webfrontSingkronpost')}}" id="formSingkron" method="POST" class="form-inline">
                <div class="form-group">
                    <label for="">Keywords</label>
                    <input type="hidden" id="idDrama" name="idDrama" class="form-control" hidden>
                    <input type="text" id="searchKeyword" name="searchKeyword" class="form-control">
                    <label for="">Site</label>
                    <select name="siteid" id="siteid" class="form-control">
                        @foreach($site as $site)
                            <option value="{{$site->id}}">{{$site->site}}</option>
                        @endforeach
                    </select>
                    <input type="button" id="btnSubmitSingkron"value="Submit">
                </div>
            </form>
            <div id="contentSearch">
            </div>    
        </div>
</div>