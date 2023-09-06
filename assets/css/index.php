<?php
//generate css styles using php so it is abled to be enqueued.
$absolute_path = explode('wp-content', $_SERVER['SCRIPT_FILENAME']);
$wp_load = $absolute_path[0] . 'wp-load.php';
require_once($wp_load);
require_once('../../admin-interface-changer.php');


header('Content-type: text/css');
header('Cache-control: must-revalidate');
echo AdminInterface::get_style();