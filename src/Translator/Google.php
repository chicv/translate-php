<?php
namespace Translation\Translator;

use Translation\Protocol\Translator;
use Google\Cloud\Translate\TranslateClient;

class Google implements Translator{
    private $client = null;
    
    public function __construct($keyFilePath,$projectId){
        $this->client = new TranslateClient([
            'keyFilePath' => $keyFilePath,
            'projectId' => $projectId,
        ]);
        
    }
    
    public function t($content,$from,$to){
        $result = $this->client->translate($content,[
            'source' => $from,
            'target' => $to,
        ]);
        return $result['text'];
    }
}