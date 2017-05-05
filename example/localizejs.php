<?php
require_once 'bootstrap.php';

use Translation\ToolKit\Localizejs;

$localizejs = new Localizejs('localizejs public key', 'localizejs project key');

header('content-type:application/json');

//Test refresh localizejs to local translation memory 
//$localizejs->refreshLocalStore($tm, 'zh-cn');
//echo 'refresh finish';

//Test give back new phraser to localizejs
//$count = $localizejs->createPhraseFromLocal($tm);
//echo $count;
