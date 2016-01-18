<?php

define('PUN_ROOT', './');
require PUN_ROOT.'/config2.php';
require PUN_ROOT.'/include/fonctions2.php';

$archive_file_name='backup.zip';
$file_path='./';
$file='db-backup.sql';
 $temp=backup_tables($db_host, $db_username, $db_password, $db_name );
$zip = new ZipArchive();
    //create the file and throw the error if unsuccessful
    if ($zip->open($archive_file_name, ZIPARCHIVE::CREATE )!==TRUE) {
        exit("cannot open <$archive_file_name>\n");
    }
 $zip->addFile($file_path.$file,$file);
$zip->close();
    //then send the headers to foce download the zip file
//    header("Content-type: application/zip"); 
//    header("Content-Disposition: attachment; filename=$archive_file_name"); 
//    header("Pragma: no-cache"); 
//    header("Expires: 0"); 
 //   readfile("$archive_file_name");
    
?>

