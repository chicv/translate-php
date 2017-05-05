<?php
namespace Translation\Translator;

use Translation\Protocol\Translator;
use Google\Cloud\Translate\TranslateClient;

/**
 * Simple Wrapper to Google Cloud Translate API
 * @author Nay Kang
 *
 */
class Google implements Translator{
    private $client = null;
    
    /**
     * Create Google Translator Instance
     * @param string $keyFilePath filepath point to google json key(absolute path needed)
     * @param string $projectId
     */
    public function __construct($keyFilePath,$projectId){
        $this->client = new TranslateClient([
            'keyFilePath' => $keyFilePath,
            'projectId' => $projectId,
        ]);
        
    }
    
    /**
     * 
     * {@inheritDoc}
     * @see \Translation\Protocol\Translator::t()
     */
    public function t($content,$from,$to){
        $result = $this->client->translate($content,[
            'source' => $from,
            'target' => $to,
        ]);
        return $result['text'];
    }
}