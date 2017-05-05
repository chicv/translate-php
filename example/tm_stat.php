<?php
require_once 'bootstrap.php';

echo '<h2>stat</h2>';
$list = $tm->getList();
foreach($list as $item){
    echo 'source:'.$item['original'];
    echo '<br/>';
    echo 'translate:'.$item['value'];
    echo '<br/>';
    echo 'translator:'.$item['translator'];
    echo '<br/>';
    echo 'hits:'.$item['hits'];
    echo '<br/><br>';
}