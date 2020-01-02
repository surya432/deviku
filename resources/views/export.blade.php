<table>
    <thead>
        <tr>
            <th>title</th>
            <th>episode</th>
            <th>episode_slug</th>
            <th>episode_link</th>
            <th>episode_kualitas</th>
            <th>episode_apikey</th>
            <th>episode_link2</th>
            <th>episode_kualitas2</th>
            <th>episode_apikey2</th>
            <th>episode_link3</th>
            <th>episode_kualitas3</th>
            <th>episode_apikey3</th>
            <th>episode_link4</th>
            <th>episode_kualitas4</th>
            <th>episode_apikey4</th>

        </tr>
    </thead>
    <tbody>
        @foreach($pegawai as $p)
            @if (count ($p->episode)> 0)
                @foreach($p->episode as $episode)

                <tr>
                    <td>{{ $p->title }}</td>
                    <td>{{ $episode->title }}</td>
                    <td>{{ $episode->url }}</td>
                    @if (count ($episode->links)> 0)
                    @foreach($episode->links as $link)
                    <td>{{ $link->drive }}</td>
                    <td>{{ $link->kualitas }}</td>
                    <td>{{ $link->apikey }}</td>
                    @endforeach
                    @endif
                    @if (count ($episode->backup)> 0)
                    @foreach($episode->backup as $backup)
                    <td>{{ $backup->f720p }}</td>
                    <td>{{ $backup->title }}</td>
                    <td>{{ $backup->tokenfcm }}</td>
                    @endforeach
                    @endif
                    
                </tr>
                @endforeach
            @endif
        @endforeach
    </tbody>
</table>