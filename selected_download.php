<?php

require_once 'config.php';

ini_set('max_execution_time', 300);
$facebook = unserialize($_SESSION['fbobject']) ;
$_SESSION['fbobject'] = serialize($facebook);
$album_id=array();
$album_id = $_GET['album'];

$data = explode(',', $album_id);
$folderName = time();
if (file_exists($folderName)) {
    $folderName = time();
    mkdir($folderName);
} else {
    $folderName = time();
    mkdir($folderName);
}
foreach ($data as $d) {
    $albumnm = $facebook->api(array(
        'method' => 'fql.query',
        'query' => 'SELECT name FROM album where object_id="' . $d . '";',
    ));
    foreach ($albumnm as $albumName) {
        
        if (file_exists($albumName['name'])) 
        {
           mkdir($folderName . '/' . $albumName['name']);
           rename($albumName['name'],$albumName['name'].'(1)'); 
        } 
        else 
        {
           mkdir($folderName . '/' . $albumName['name']); 
        }
        $albumph = $facebook->api(array(
            'method' => 'fql.query',
            'query' => 'SELECT src_big FROM photo where album_object_id="' . $d . '";',
        ));
        foreach ($albumph as $photo) {
            $fp = fopen($folderName . '/' . $albumName['name'] . '/' . basename($photo['src_big']), 'w');
            fwrite($fp, file_get_contents($photo['src_big']));
            fclose($fp);
        }
    }
    unset($albumph);
}
if(extension_loaded('zip'))
{
$sourcefolder = "./";
$zipfilename = "MyAlbums.zip";
$fileslist = new RecursiveIteratorIterator($dirlist);
$dirlist = new RecursiveDirectoryIterator($folderName);
$zip = new ZipArchive();
  if ($zip->open("$zipfilename", ZipArchive::CREATE) !== TRUE) 
	{
    	die("Could not open archive");
	}
	foreach ($filelist as $key => $value) 
	{
    	$zip->addFile(realpath($key), $key) or die("ERROR: Could not add file: $key");
	}
$zip->close();

if(file_exists($zipfilename))
	{
	
		header('Content-Description: File Transfer');
	    header('Content-Type: application/zip');
	    header('Content-Disposition: attachment; filename="'.$zipfilename.'"');
		$file_name = basename($zipfilename);
        $size = filesize($zipfilename);
	    readfile($zipfilename);
		unlink($zipfilename);
		exit;
	}
	else
	{	
		$error .= "* Please select file to zip ";
	}
}
?>
