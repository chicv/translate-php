<?php
namespace Translation;

use Translation\Protocol\Parser;

class HTMLParser implements Parser{
    
    protected $paragraphParser = null;
    
    public function __construct(){
        $this->paragraphParser = new ParagraphParser();
    }
    
    public function process($html,callable $callback){
        $dom = new \DOMDocument();
        $dom->loadHTML($html);
        $elements = $dom->getElementsByTagName('html');
        //echo "<pre>";
        $this->throughHTML($elements->item(0),$callback);
        //echo "</pre>";
        return $dom->saveHTML();
    }
    
    protected function throughHTML($element,callable $callback){
        if(!$element->childNodes) return;
        foreach($element->childNodes as $item){
            
            if($item->attributes && $item->hasAttribute('notranslate')){
                continue;
            }
            
            if($item instanceof \DOMText && trim($item->nodeValue)){
                $content = $item->nodeValue;
                $content = $this->paragraphParser->process($content, $callback);
                $item->nodeValue = $content;
                continue;
            }
            $this->throughHTML($item,$callback);
        }
    }
}