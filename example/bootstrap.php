<?php
require_once '../autoload.php';
require_once 'RedisStore.php';
require_once '../vendor/autoload.php';

use Translation\Parser\ParagraphParser;
use Translation\TranslationMemory;
use Translation\Parser\HTMLParser;
use Translation\Translator\Google;

//Create A Simple Redis Store
$redisStore = new RedisStore();
$tm = new TranslationMemory($redisStore);
$tm->setConfig('to', 'zh-cn');

//add two parser plugin in it
$para = new ParagraphParser();
$tm->addParser('paragraph', $para);
$htmlParser = new HTMLParser();
$tm->addParser('html', $htmlParser);

//Setup Google Sync Translator
$googleTranslator = new Google('Google Service Account JSON key','Google project id');
$tm->setTranslator($googleTranslator);