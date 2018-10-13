<div class="panel panel-primary copyright-wrap" id="copyright-wrap">
        <div class="panel-heading">Singkron Info
            <button type="button" class="close" data-target="#copyright-wrap" data-dismiss="alert"> <span aria-hidden="true">&times;</span><span class="sr-only">Close</span>
            </button>
        </div>
        <div class="panel-body">
                <div class="list-group">
                    @if(!$url)
                        Tidak ada Singkron
                    @endif
                    <ul>
                        @foreach($url as $url)
                            <li><a href="{{ route('viewEps',$url )}}" class="list-group-item list-group-item-action">{{$url}}</a></li>
                        @endforeach
                    </ul>
                </div>
        </div>
</div>