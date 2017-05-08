<?php
namespace Translation\Translator;

use Translation\Protocol\Translator;
use Stichoza\GoogleTranslate\TranslateClient;

class FreeGoogle implements Translator{
    
    protected $client = null;
    
    public function __construct(){
        $this->client = new TranslateClient();
    }
    
    public function t($content,$from,$to){
        return $this->client->setSource($from)->setTarget($to)->translate($content);
    }
}