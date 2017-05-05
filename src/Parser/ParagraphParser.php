<?php
namespace Translation\Parser;

use Translation\Protocol\Parser;

/**
 * Break large paragraph into small sentences 
 * @author Nay Kang
 *
 */
class ParagraphParser implements Parser{
    
    /**
     * 
     * {@inheritDoc}
     * @see \Translation\Protocol\Parser::process()
     */
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
    
    /**
     * Test if content need translate,eg. "123" do not need be translated
     * @param string $content
     * @return bool true means need translate,false means no translate needed
     */
    protected function filter($content){
        //Check if content contain any letter in any language
        return preg_match('/\p{L}+/u', $content);
    }
    
    /**
     * break a sentence into array with [prefix,content,sufix]
     * only content in the middle need translate
     * @param string $content
     * @return array
     */
    protected function pickLetter($content){
        if(preg_match('/^(\P{L}*)(.+?)(\P{L}*)$/u',$content,$match)){
            return [$match[1],$match[2],$match[3]];
        }else{
            return ['',$content,''];
        }
    }
}