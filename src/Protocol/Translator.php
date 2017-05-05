<?php
namespace Translation\Protocol;

/**
 * For thirdpart translation service wrap
 * @author Nay Kang
 *
 */
interface Translator{
    
    /**
     * Translate $content from language $from to language $to
     * @param string $content
     * @param string $from
     * @param string $to
     */
    public function t($content,$from,$to);
}