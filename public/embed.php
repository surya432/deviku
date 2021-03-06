<!doctype html>
<html lang="en">

<head>
	<!-- Required meta tags -->
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<!-- Plyr.io Player -->
	<!-- <link rel="stylesheet" href="https://cdn.plyr.io/3.3.12/plyr.css">
	<script type="text/javascript" src="/js/jwplayer.js"></script> -->
	<link href="https://stream.ksplayer.com/templates/jwplayer/skin/asset/css/kunamthemes.css" rel="stylesheet">
	<script type="text/javascript" src="https://ssl.p.jwpcdn.com/player/v/8.6.2/jwplayer.js"></script>
	<script type="text/javascript">
		jwplayer.key = "cLGMn8T20tGvW+0eXPhq4NNmLB57TrscPjd1IyJF84o=";
	</script>
	<!-- <script type="text/javascript" src="//content.jwplatform.com/libraries/0P4vdmeO.js"></script> -->
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.0/sweetalert.min.js"></script>

	<!-- <script type="text/javascript" src="//cdn.jsdelivr.net/jwplayer/5.10/jwplayer.js"></script> -->
	<!-- <script>
		jwplayer.key = "zGhSOpbt7hbdG53nW3nDZE0vdyyjy0cNdaQNfA==";
	</script> -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
	<?php //include_once('popads.php');
	?>
</head>

<body style="margin:0px;">
	<script type="text/javascript">
		var bttCount = 4;
		var h = 35;
		//if(bttCount<2) h=1; else {if($(window).width()>(100*bttCount)) h=35; else h=100;}
		var video = {
			width: $(window).width(),
			height: $(window).height()
		};

		$(window).resize(function() {
			video.width = $(window).width(),
				video.height = $(window).height(),
				jwplayer("myElement").resize(video.width, video.height)
		});
	</script>

	<div id="myElement" style="width:100%!important;height:100%!margin-bottom:0px;"></div>
	<?php
	error_reporting(0);
	include "curl_gd.php";
	$base_url = 'http://demo.filedeo.stream/drive';
	function url()
	{
		if (isset($_SERVER['HTTPS'])) {
			$protocol = ($_SERVER['HTTPS'] && $_SERVER['HTTPS'] != "off") ? "http" : "http";
		} else {
			$protocol = 'http';
		}
		return $protocol . "://gd." . $_SERVER['HTTP_HOST'];
	}
	if (isset($_GET['id'])) {
		$gid = htmlspecialchars($_GET['id']);
		$gid = my_simple_crypt($gid, 'd');
		// $results = file_get_contents('https://gd.nontonindrama.xyz/Player-Script/json.php?url=https://drive.google.com/file/d/' . $gid . '/preview');
		$gid = get_drive_id("https://drive.google.com/file/d/$gid/view");
		$sourcesvideo = GoogleDrive($gid);
		?>

	<script type="text/javascript">
		var currentTime = 0;
		var hsdsd = 35;
		var sources_video = <?php
								if ($sourcesvideo) {
									echo $sourcesvideo;
								} else {
									echo  '[{"label":"undefined","type":"video\/mp4","file":"undefined"}]';
								} ?>

		var daplayer = jwplayer("myElement").setup({
			controls: true,
			displaytitle: true,
			width: video.width,
			aspectratio: "16:9",
			height: video.height,
			fullscreen: "true",
			// skin: {
			// 	"name": "customs",
			// 	"url": "/jw/prime.min.css"
			// },
			captions: {
				color: "#ffffff",
				fontSize: 18,
				backgroundOpacity: 50,
				edgeStyle: "dropshadow",
			},
			autostart: false,
			"primary": "html5",
			// "advertising": {
			// 	"tag": "https://www.movcpm.com/watch.xml?key=590b107a0857e0fb7cb70f5a0e73aff2",
			// 	"client": "vast",
			// 	"vpaidmode": "insecure",
			// 	"companiondiv": {
			// 		"id": "sample-companion-div",
			// 		"height": 250,
			// 		"width": 300,
			// 	}
			// },
			abouttext: "nontonindrama.xyz",
			aboutlink: "http://nontonindrama.xyz",
			sources: sources_video
		}).addButton(
			//"//i.imgur.com/cAHz5k9.png",
			"//i.imgur.com/bfcWPdI.png",
			"Download Video",
			function() {
				showPlayer('download_links');
				var kI = daplayer.getPlaylistItem(),
					kcQ = daplayer.getCurrentQuality();
				if (kcQ < 0) {
					kcQ = 0;
				}
				if (kI.sources[kcQ].file.lastIndexOf('googlevideo.com') > 0) {
					var kF = kI.sources[kcQ].file + "&title=<?php echo htmlspecialchars_decode($urldownload, ENT_QUOTES); ?>";
				} else {
					var kF = kI.sources[kcQ].file + "&title=NontonOnlineDrama.co-<?php echo htmlspecialchars_decode($urldownload, ENT_QUOTES); ?>-" + kI.sources[kcQ].label + ".mp4";
					var kF1 = kF.replace("video.mp4", "<?php echo htmlspecialchars_decode($urldownload, ENT_QUOTES); ?>.mp4");
					//kF1= kF1.replace("/pd2/","/pd/");
					//kF= kF1.replace("/index.m3u8","");
					kF = kF1.replace("e=view", "e=download");
				}
				jwplayer("myElement").pause(true);
				window.open(kF, '_blank');

			},
			"download"
		);
		daplayer.on("setupError", function() {
			swal("Server Error!", "Please contact us to fix it asap. Thank you!", "error");
			$("#myElement").html('<iframe id="playerEmbed" src="https://drive.google.com/file/d/<?php echo $gid; ?>/preview" frameborder=0 marginwidth=0 marginheight=0 scrolling=no width="' + video
				.width + '" height="' + video.height + '" allowfullscreen></iframe>');
		});
		daplayer.on("error", function() {
			swal("Server Error!", "Please contact us to fix it asap. Thank you!", "error");
			$("#myElement").html('<iframe id="playerEmbed" src="https://drive.google.com/file/d/<?php echo $gid; ?>/preview" frameborder=0 marginwidth=0 marginheight=0 scrolling=no width="' + video
				.width + '" height="' + video.height + '" allowfullscreen></iframe>');
		});
	</script><?php
					// $results = file_get_contents(url() . '/json.php?url=https://drive.google.com/file/d/' . $gid . '/view');
					// $results = json_decode($results, true);
					// if ($results['file'] == 1) {
					// 	echo '<center>Sorry, the owner hasn\'t given you permission to download this file.</center>';
					// 	exit;
					// } elseif ($results['file'] == 2) {
					// 	echo '<center>Error 404. We\'re sorry. You can\'t access this item because it is in violation of our Terms of Service.</center>';
					// 	exit;
					// }
					if (isset($results)) {
						echo $results;
					}
				} else {
					echo "nothing";
				}

				?>
</body>

</html>