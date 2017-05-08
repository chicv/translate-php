<?php
require_once '../autoload.php';
require_once '../vendor/autoload.php';

use Translation\TranslationMemory;
use Translation\Translator\Google;

//Create A Simple Redis Store
$tm = new TranslationMemory();
//how to set target language
//$tm->setConfig('to', 'zh-cn');

//add custom parser plugin in it
//$tm->addParser('myparser', $myParser);

//Setup Google Translator Services
//We have built in free translator service for test,but not for production
//$googleTranslator = new Google('Google Service Account JSON key','Google project id');
//$tm->setTranslator($googleTranslator);