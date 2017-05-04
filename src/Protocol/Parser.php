<?php
namespace Translation\Protocol;

interface Parser{
    
    /**
     * Try to split the content to small unit
     * @param string $content content being process,Typically split by special rule
     * @param callable $callback Usually this is a translate function
     */
    public function process($content,callable $callback);
}