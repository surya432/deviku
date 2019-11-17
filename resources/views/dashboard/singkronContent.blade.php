<div class="panel panel-primary copyright-wrap" id="copyright-wrap">
    <div class="panel-heading">Singkron Info
        <button type="button" class="close" data-target="#copyright-wrap" data-dismiss="alert"> <span
                aria-hidden="true">&times;</span><span class="sr-only">Close</span>
        </button>
    </div>
    <div class="panel-body">
        <div class="list-group">
            @if(!$url)
            Tidak ada Singkron
            @else
            <div class="list-group">
                @foreach($url as $a =>$b)
                <a href="#" class="list-group-item">{{$b}}</a>
                @endforeach
            </div>
            @endif

        </div>
    </div>
</div>
