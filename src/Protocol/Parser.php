<?php
namespace Translation\Protocol;

interface Parser{
    
    public function process($content,callable $callback);
}