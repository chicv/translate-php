<?php
namespace Translation;

use Translation\Protocol\Parser;

class ParagraphParser implements Parser{
    
    public function process($content,callable $callback){
        $content = $this->pickLetter($content);
        //copy from http://stackoverflow.com/a/10494335
        $result = preg_split('/(?<=[.?!;:,])\s+/', $content[1]);
        
        $rtn = '';
        foreach($result as $str){
            if($this->filter($str)){
                $str = $this->pickLetter($str);
                $str = $str[0].$callback($str[1]).$str[2];
                $rtn .= $str;
            }else{
                $rtn .= $str;
            }
        }
        return $content[0].$rtn.$content[2];
    }
    
    protected function filter($content){
        //Check if content contain any letter in any language
        return preg_match('/\p{L}+/u', $content);
    }
    
    /**
     * 
     * @param array $content
     */
    protected function pickLetter($content){
        if(preg_match('/^(\P{L}*)(.+?)(\P{L}*)$/u',$content,$match)){
            return [$match[1],$match[2],$match[3]];
        }else{
            return ['',$content,''];
        }
    }
}