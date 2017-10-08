<?php
namespace Translation\Parser;

use Translation\Protocol\Parser;

/**
 * Parse html,and pick content from it
 * use ParagraphParser to break the content from html into small sentences
 *
 * html tag has attribute "notranslate" will be pass
 * @author Nay Kang
 *
 */
class HTMLParser implements Parser{

    protected $paragraphParser = null;

    public function __construct(){
        $this->paragraphParser = new ParagraphParser();
    }

    /**
     *
     * {@inheritDoc}
     * @see \Translation\Protocol\Parser::process()
     */
    public function process($html,callable $callback){
        $html = $this->syntaxFix($html);
        $dom = new \DOMDocument();
        $dom->loadHTML($html);
        $elements = $dom->getElementsByTagName('html');
        $this->throughHTML($elements->item(0),$callback);
        return $dom->saveHTML();
    }

    /**
     * Go through all html nodes,and pick content
     * @param \DOMElement $element
     * @param callable $callback
     */
    protected function throughHTML($element,callable $callback){
        if(!$element->childNodes) return;
        foreach($element->childNodes as $item){
            if(in_array($item->nodeName, ['style','script']) || $item instanceof \DOMCdataSection)
                continue;

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

    /**
    * Because the poor of php inner html parser,some spell mistake should fix
    * eg: & => &amp;
    * @param string $html
    * @return string $html
    */
    public function syntaxFix($html){
      //only under php7,symtax error will occur
      //and javascript will be problem
      if (version_compare(PHP_VERSION, '7.0.0') < 0){
          $html = preg_replace('/\&(?!\w+;|#|&)/','&amp;',$html);
      }
      return $html;
    }
}
