<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Embed {{ $url->url }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}" />


    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script type="text/javascript" src="{{URL::asset('js/jwplayer.js')}}"></script>
    <script type="text/javascript" src="//cdn.jsdelivr.net/jwplayer/5.10/jwplayer.js"></script>
    <script>
        jwplayer.key = "zGhSOpbt7hbdG53nW3nDZE0vdyyjy0cNdaQNfA==";
    </script>
    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <style>
        body {
            overflow: hidden;
            background-color: #111;
        }

        .spinner {
            margin: 150px auto 0;
            width: 70px;
            text-align: center;
        }

        .spinner>div {
            width: 28px;
            height: 28px;
            background-color: #337ab7;

            border-radius: 100%;
            display: inline-block;
            -webkit-animation: sk-bouncedelay 1.4s infinite ease-in-out both;
            animation: sk-bouncedelay 1.4s infinite ease-in-out both;
        }

        .spinner .bounce1 {
            -webkit-animation-delay: -0.32s;
            animation-delay: -0.32s;
        }

        .spinner .bounce2 {
            -webkit-animation-delay: -0.16s;
            animation-delay: -0.16s;
        }

        @-webkit-keyframes sk-bouncedelay {

            0%,
            80%,
            100% {
                -webkit-transform: scale(0)
            }

            40% {
                -webkit-transform: scale(1.0)
            }
        }

        @keyframes sk-bouncedelay {

            0%,
            80%,
            100% {
                -webkit-transform: scale(0);
                transform: scale(0);
            }

            40% {
                -webkit-transform: scale(1.0);
                transform: scale(1.0);
            }
        }

        .jw-background-color {
            background-color: rgba(0, 0, 0, 0.2);
        }

        .jw-button-color {
            color: #fff;
        }
    </style>
    {!! $pad_code !!}
</head>

<body>
    <script type="text/javascript">
        var bttCount = 4;
        var h = 35;
        //if(bttCount<2) h=1; else {if($(window).width()>(100*bttCount)) h=35; else h=100;}
        var video = {
            width: $(window).width(),
            height: $(window).height() - h
        };
        $(window).resize(function() {
            video.width = $(window).width(), video.height = $(window).height() - h, jwplayer().resize(video.width,
                video.height)
        });
    </script>
    <div id="server" class="text-left" style="padding-top:5px;">
        <!-- <button class="btn btn-sm btn-primary" disabled>Server:</button> -->

        @if($setting->folder360p == "true")
            <button class="btn btn-sm btn-primary" qtyLink="gd360" onclick="showPlayer('gd360')">B.Fs<sup>SD</sup></button>
        @endif
        @if($setting->folder720p == "true")
        <button class="btn btn-sm btn-primary" qtyLink="gd360" id="btnDefault" onclick="showPlayer('gd720')">B.Fs<sup>HD</sup></button>
        @endif
        @if(!empty($fembed))
        <button class="btn btn-sm btn-primary" qtyLink="mirror1" onclick="showPlayer('mirror1')">Fembed</button>
        @endif
        @if(!empty($rapidvideo))
        <button class="btn btn-sm btn-primary" qtyLink="mirror2" onclick="showPlayer('mirror2')">RapidVideo</button>
        @endif
        @if(!empty($openload))
        <button class="btn btn-sm btn-primary" qtyLink="mirror3" onclick="showPlayer('mirror3')">Openload</button>
        @endif
        <button class="btn btn-sm btn-primary" onclick=showPlayer('download_links')>Download</button>
    </div>
    <div id="myElement" style="width:100%!important;height:100%!important">
        <div class="spinner">
            <div class="bounce1"></div>
            <div class="bounce2"></div>
            <div class="bounce3"></div>
        </div>
        <div id="notif" class="text-center">
            <p style="color: blue;"> Pilih Server Di atas!!!</br> Jangan Gunakan UCbrowser atau Browser mini
                lainnya!!!</br> Jika Error Cepat Lapor Mimin atau Komentar di bawah... :D :)</p>
        </div>
    </div>

    <script type="text/javascript">
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            },
        });
    </script>
    <script type="text/javascript">
     $(document).ready(function() {
        var coek = document.getElementsByClassName("btn-sm")[0];
        var firstImg = document.getElementsByClassName("btn-sm")[0].getAttribute("qtylink");
        var data = showPlayer(firstImg);
        coek.classList.remove('btn-primary');
        coek.classList.add('btn-danger');
     });
    </script>
    <script type="text/javascript">
        function showPlayer(link_id) {
            $("#myElement").html(
                '<div class="spinner"><div class="bounce1"></div> <div class="bounce2"></div> <div class="bounce3"></div></div><div id="notif" class="text-center"><p style="color: blue;">Tunggu Sebentar Ya... :D :)</p></div>'
            );
            var data = 'videos={{ $url->url }}&player=' + link_id;
            $.ajax({
                async: true,
                url: "{{ route('ajaxEps',$url->url) }}",
                type: "POST",
                data: data,
                cache: false,
                beforeSend: function() {
                    // setting a timeout
                    $("#myElement").html(
                        '<div class="spinner"><div class="bounce1"></div> <div class="bounce2"></div> <div class="bounce3"></div></div><div id="notif" class="text-center"><p style="color: blue;">Tunggu Sebentar Ya... :D :)</p></div>'
                    );
                },
                success: function(html) {
                    if (html) {
                        if (html.match(/^http/g)) {
                            $("#myElement").html('<iframe src="' + html +
                                '" frameborder=0 marginwidth=0 marginheight=0 scrolling=no width="' + video
                                .width + '" height="' + video.height + '" allowfullscreen></iframe>');
                        } else
                            $("#myElement").html(html);
                        //                            console.log(html)
                    } else {
                        alert('Sorry, unexpected error. Please try again later.');
                    }
                },
            });
            return false;
        }
        $('.btn').click(function() {
            // daplayer.remove();
            $("#myElement").html(
                '<div class="spinner"><div class="bounce1"></div> <div class="bounce2"></div> <div class="bounce3"></div></div><div id="notif" class="text-center"><p style="color: blue;">Tunggu Sebentar Ya... :D :)</p></div>'
            );
            $('.btn').removeClass('btn-danger');
            $('.btn').addClass('btn-primary');
            $(this).removeClass('btn-primary').addClass('btn-danger');
        });
		var meta = document.createElement('meta');
meta.name = "referrer";
meta.content = "no-referrer";
document.getElementsByTagName('head')[0].appendChild(meta);
    </script>
    <!-- Histats.com  START  (aync)-->
    <script type="text/javascript">
        var _Hasync = _Hasync || [];
        _Hasync.push(['Histats.start', '1,3848851,4,604,110,55,00011111']);
        _Hasync.push(['Histats.fasi', '1']);
        _Hasync.push(['Histats.track_hits', '']);
        (function() {
            var hs = document.createElement('script');
            hs.type = 'text/javascript';
            hs.async = true;
            hs.src = ('//s10.histats.com/js15_as.js');
            (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(hs);
        })();
    </script>
    <noscript><a href="/" target="_blank"><img src="//sstatic1.histats.com/0.gif?3848851&101" alt="" border="0"></a></noscript>
    <!-- Histats.com  END  -->
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous">
    </script>
</body>

</html>
