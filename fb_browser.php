<?php
ini_set('max_execution_time', 300);
require_once 'config.php';

$facebook = unserialize($_SESSION['fbobject']);
$_SESSION['fbobject'] = serialize($facebook);
$user = $facebook->getUser();	//Get facebook User Id

if ($user) 
{
		$logoutUrl = $facebook->getLogoutUrl(array(
			'next'=>'http://rinkeshchauhanfb.comoj.com/rtcamp/logout.php'
		));
}
	

if ($user) {
	try {
	$me = $facebook->api('/me');
		$user_albums = $facebook->api('/me/albums');
		$albums = array();
		if(!empty($user_albums['data'])) {
			foreach($user_albums['data'] as $album) {
				$temp = array();
				$temp['id'] = $album['id'];
				$temp['name'] = $album['name'];
				$temp['thumb'] = "https://graph.facebook.com/{$album['id']}/picture?type=album&access_token={$facebook->getAccessToken()}";
				$temp['count'] = (!empty($album['count'])) ? $album['count']:0;
				if($temp['count']>1 || $temp['count'] == 0)
					$temp['count'] = $temp['count'] . " photos";
				else
					$temp['count'] = $temp['count'] . " photo";
				$albums[] = $temp;
			}
		}
	} catch (FacebookApiException $e) {
		error_log($e);
		var_dump($e);
		$user = null;
	}
}

?>



<!DOCTYPE html>
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
    <script type="text/javascript" src="lib/js/jquery-1.10.2.js"></script> 
	</head>
<body>
<form action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" method="post" name="form" >
<div class="container">
	<div class="links" >
		<div class="span12">
        	<div id="header">
          		<div id="title" >
                	<h1 style="color:#FFFFFF">My Facebook Album Browser</h1>
                </div>
                <div id="user-info" > 
                	<div>
                     	<p style="color:#FFFFFF"><?php echo $me['name']?></p>
                      	<p><input type="button" value="Download Selected" id="btntest" onClick="check()"/><input type="button" value="Download All" id="checkall" /><a href="<?php echo $logoutUrl;?>" title="Click to logout">Logout</a></p>
                        </div>
          		</div>
                <div class="clearfix"></div>
     	 	</div>
     
	 <?php 
	 foreach($albums as $album) {
	 ?>
     <div class="album-list span3">
     	<div class="album tile">
        	<a href="album.php?id=<?=$album['id']?>">
            	<img src="<?=$album['thumb']?>" alt="<?=$album['id'] ?>" class="<?php if($album['count']>0) echo "view-album" ?>" title="View album">
            </a>
           
            <p class="album-title" title="<?=$album['count']?>">
            	<?=$album['count']?>
            </p>
            <p class="album-title" title="<?=$album['name']?>">
            
              	<?=substr($album['name'], 0, 21)?>
            </p>
           
            <div>
               <input name="chb[]" type="checkbox" value="<?php echo $album['id']; ?>" >&nbsp;&nbsp;<a href="http://rinkeshchauhanfb.comoj.com/rtcamp/download.php?album_id=<?php echo $album['id']?>">Download</a>
            </div>
           
         </div>
     </div>
     
	 <?php }
	 ?>
  
     <script type="text/javascript">
// Returns an array with values of the selected (checked) checkboxes in "form"
function getSelectedChbox(frm) {
  var selchbox = [];        // array that will store the value of selected checkboxes

  // gets all the input tags in frm, and their number
  var inpfields = frm.getElementsByTagName('input');
  var nr_inpfields = inpfields.length;

  // traverse the inpfields elements, and adds the value of selected (checked) checkbox in selchbox
  for(var i=0; i<nr_inpfields; i++) {
    if(inpfields[i].type == 'checkbox' && inpfields[i].checked == true) selchbox.push(inpfields[i].value);
	
  }
  

  return selchbox;
}



var checkAll = document.getElementById("checkall");

checkAll.onclick = function(){
    [].forEach.call(
        document.forms['form'].querySelectorAll("input[type='checkbox']"),
        function(el){
            el.checked=true;
			 var selchbox = getSelectedChbox(this.form);     // gets the array returned by getSelectedChbox()
  window.location.href='http://rinkeshchauhanfb.comoj.com/rtcamp/selected_download.php?album='+selchbox;
			
    });
}
// When click on #btntest, alert the selected values
document.getElementById('btntest').onclick = function(){
  var selchbox = getSelectedChbox(this.form);     // gets the array returned by getSelectedChbox()
  if(selchbox == "")
  {
  alert ("please select atleast one checkbox to download!!!");
  }
  else
  {
  window.location.href='http://rinkeshchauhanfb.comoj.com/rtcamp/selected_download.php?album='+selchbox;
  }
}
//-->
</script>
    
	    </div>
	</div>
</div>
</form>
</body>
</html>
