<?php
namespace Translation;

use Translation\Protocol\Parser;

class ParagraphParser implements Parser{
    
    public function process($content,callable $callback){
        //copy from http://stackoverflow.com/a/10494335
        $result = preg_split('/(?<=[.?!;:,])\s+/', $content, -1, PREG_SPLIT_NO_EMPTY);
        
        $rtn = '';
        foreach($result as $str){
            $rtn .= $callback($str);
        }
        return $rtn;
    }
}