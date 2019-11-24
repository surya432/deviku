Result:
<div class="list-group">
    @if(!$url)
    keyword not found
    <button type="button" id="btnaddPostWp" name="btnaddPostWp" class="btn btn-md btn-primary">Create New Post</button>
    @endif
    @foreach($url as $post)

    <a data-id="{{ $post['id'] }}" id="singkronPost"
        onclick="btnSingkronToweb('{{ $post['id'] }}','{{ str_replace('"', "",json_encode($post['title']['rendered']) )}}');"
        class="list-group-item list-group-item-action">{{ str_replace('"', "",json_encode($post['title']['rendered']) ) }}
        {{ $post['guid']['rendered'] }}</a>
    @endforeach
</div>