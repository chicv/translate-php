<?php
namespace Translation;

use Translation\Protocol\Parser;

class HTMLParser implements Parser{
    
    public function process($html,callable $callback){
        $dom = new \DOMDocument();
        $dom->loadHTML($html);
        $elements = $dom->getElementsByTagName('html');
        echo "<pre>";
        $this->throughHTML($elements->item(0));
        echo "</pre>";
        return '';
    }
    
    protected function throughHTML($element){
        if(!$element->childNodes) return;
        foreach($element->childNodes as $item){
            
            if($item->attributes && $item->hasAttribute('notranslate')){
                continue;
            }
            
            if($item instanceof \DOMText && trim($item->nodeValue)){
                print_r($item);
                echo "<br/><br/>";
                continue;
            }
            
            $this->throughHTML($item);
        }
    }
}