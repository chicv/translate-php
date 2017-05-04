<?php
namespace Translation\Protocol;

/**
 * For thirdpart translation service wrap
 * @author Nay Kang
 *
 */
interface Translator{
    
    /**
     * 
     * @param string $content
     * @param string $from
     * @param string $to
     */
    public function t($content,$from,$to);
}