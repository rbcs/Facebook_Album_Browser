<?php
require_once 'config.php';

$user = $facebook->getUser();

if ($user) // Logged in
{
	$_SESSION['fbobject'] = serialize($facebook);
    header('Location: http://rinkeshchauhanfb.comoj.com/rtcamp/fb_browser.php');
}
else
{
	$params = array(
		'scope' => 'user_photos, offline_access'
	);
	$loginUrl = $facebook->getLoginUrl($params);
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
	</head>
<body>
<div class="container">      
	<div class="links" >
		<div class="demo-headline">
        	<h1 class="demo-logo">
          		<div class="logo"></div>
          			Facebook Album Browser <small><a href="<?php echo $loginUrl ?>" title="Login"><img src="lib/images/login-with-facebook.jpg" alt=""></a></small> 
                    <small>Developed By: Rinkesh Chauhan</small>
            </h1>
      	</div>
	</div>
</div>
</body>
</html>
