<?php
require_once('../../../wp-load.php');
$imagePull[0] = get_field('event_image', 'option');
$imagePull[1] = get_field('event_link', 'option');
echo $imagePull[0].' '.$imagePull[1];
?>