<?php 
require_once 'bootstrap.php';

$html_content = file_get_contents('example.html');
echo 'original:<br/>';
echo $html_content;
echo 'translate:<br/>'.$tm->complexTranslate($html_content, 'html');
?>