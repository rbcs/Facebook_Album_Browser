<?php
if( !isset($_GET['id']) )
	die("No direct access allowed!");
	
require_once 'config.php';

$facebook = unserialize($_SESSION['fbobject']);
$_SESSION['fbobject'] = serialize($facebook);

$user = $facebook->getUser();
$returnAlbumPhotos = array();
if ($user) 
{
		$logoutUrl = $facebook->getLogoutUrl(array(
			'next'=>'http://rinkeshchauhanfb.comoj.com/rtcamp/logout.php'
		));
}


if ($user) {
	try {
		
		
		
		$me = $facebook->api('/me');
		$params = array();
		if( isset($_GET['offset']) )
			$params['offset'] = $_GET['offset'];
		if( isset($_GET['limit']) )
			$params['limit'] = $_GET['limit'];
		$params['fields'] = 'name,source,images';
		$params = http_build_query($params, null, '&');
		$album_photos = $facebook->api("/{$_GET['id']}/photos?$params");
		if( isset($album_photos['paging']) ) {
			if( isset($album_photos['paging']['next']) ) {
				$next_url = parse_url($album_photos['paging']['next'], PHP_URL_QUERY) . "&id=" . $_GET['id'];
			}
			if( isset($album_photos['paging']['previous']) ) {
				$pre_url = parse_url($album_photos['paging']['previous'], PHP_URL_QUERY) . "&id=" . $_GET['id'];
			}
		}
		$photos = array();
		if(!empty($album_photos['data'])) {
			foreach($album_photos['data'] as $photo) {
				$temp = array();
				$temp['id'] = $photo['id'];
				$temp['name'] = (isset($photo['name'])) ? $photo['name']:'';
				$temp['picture'] = $photo['images'][1]['source'];
				$temp['source'] = $photo['source'];
				$photos[] = $temp;
			}
		}
		
		
		
	} catch (FacebookApiException $e) {
		error_log($e);
		$user = null;
	}
} else {
	header("Location: http://rinkeshchauhanfb.comoj.com/rtcamp/index.php");
}
?>
<!doctype html>

<html lang="en">
	<head>
	<meta charset="utf-8">
	<title>FB Album</title>

	<link href="lib/Flat-UI/css/bootstrap.css" rel="stylesheet">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="lib/Flat-UI/css/bootstrap-responsive.css" rel="stylesheet">
	<link href="lib/Flat-UI/css/flat-ui.css" rel="stylesheet">
    <link type="text/css" href="lib/fancybox/jquery.fancybox.css" rel="stylesheet" />
    <link href="lib/css/style.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="lib/images/camera.png" />
    <link href="lib/css/jquery.fancybox-1.3.4.css" media="screen" type="text/css" rel="stylesheet">


	</head>
<body>
<div class="container">
	<div class="links" >
		<div class="span12">
        	<div id="header">
          		<div id="title" >
                	<a href="http://rinkeshchauhanfb.comoj.com/rtcamp/fb_browser.php" title="Back To Album Browser"><h1 style="color:#FFFFFF">My Facebook Album</h1></a>
                </div>
                <div id="user-info" > 
                	<div>
                    	<p style="color:#FFFFFF"><?php echo $me['name']?></p>
                      	<p><a href="<?php echo $logoutUrl;?>" title="Click to logout">Logout</a></p>
                  	</div>
          		</div>
                <div class="clearfix"></div>
     	 	</div>
	
	<?php if(!empty($photos)) { ?>
	
	<?php
	$count = 0;
	foreach($photos as $photo) {
		$lastChild = "";
		
	?>
		
    <div class="album-list span3">
    	<div class="album tile">   
        	<a href="<?=$photo['source']?>" title="<?=$photo['name']?>" rel="pic_gallery">
           		<img src="<?=$photo['picture']?>"/>
                                           <a target="blank" href="https://plus.google.com/share?url=<?php echo $photo['source']; ?>" class="btn btn-primary">Share on Google+</a>
            </a>
         </div>
    </div>
	<?php }} ?>
    <?php if(isset($album_photos['paging'])) { ?>
	<div class="paging">
		<?php if(isset($next_url)) { echo "<a class='next' href='album.php?$next_url'>Next</a>"; } ?>
		<?php if(isset($pre_url)) { echo "<a class='prev' href='album.php?$pre_url'>Previous</a>"; } ?>
	</div>
	<?php }
	 ?>
		</div>
	</div>
</div>


<script type="text/javascript" src="lib/js/jquery-1.6.1.min.js"></script>
<script type="text/javascript" src="lib/js/jquery.fancybox-1.3.4.pack.js"></script>
<script>
$(function() {
	$("a[rel=pic_gallery]").fancybox({
		'titlePosition' 	: 'over',
		'titleFormat'		: function(title, currentArray, currentIndex, currentOpts) {
			return '<span id="fancybox-title-over">Image ' + (currentIndex + 1) + ' / ' + currentArray.length + (title.length ? ' &nbsp; ' + title : '') + '</span>';
		}
	});
});
</script>
</body>
</html>
