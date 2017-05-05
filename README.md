# translate-php

This is not old style translate tool,it's modern and easy,send content to it,then get translation back.with plugin like localizejs and other thirdpart service to manage you translated content.

# Quick Start

## SHOW ME THE CODE

```php
//Create A Simple Redis Store
$redisStore = new RedisStore();
$tm = new TranslationMemory($redisStore);

//Setup Google Sync Translator
$googleTranslator = new Google('Google Service Account JSON key','Google project id');
$tm->setTranslator($googleTranslator);
$tm->t('hello world');
```

then it will output 

    你好，世界
    
## You may think "Are you kidding me ?!",so let's put a big meal on table


```php
//add html parser plugin in it
$htmlParser = new HTMLParser();
$tm->addParser('html', $htmlParser);
echo $html_content = file_get_contents('https://github.com/about');
```

wait a moment,then you will find a whole new world!

It's so easy right? more code will find in ```example``` folder



## Oh,Install

```shell
composer require "chicv/translate-php"
```


## Thanks

### [localizejs](https://localizejs.com/)
I just finish you work localizejs,I also wrote a toolkit to sync translations between localizejs and me

### [Machine Translation at Etsy](https://codeascraft.com/2016/03/22/building-a-translation-memory-to-improve-machine-translation-coverage-and-quality/)
this article tell me how to do this



