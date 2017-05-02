<?php
namespace Translation\Protocol;

interface Store{
    
    /**
     * Set Default partition for all sub execution
     * @param string $partition a prefix or a database schema
     */
    public function setDefaultPartition($partition);
    /**
     * set a content in store
     * @param string $key
     * @param string $content
     */
    public function set($key,$content);
    
    /**
     * get a content by key
     * @param string $key
     * @return string content
     */
    public function get($key);
    
    /**
     * get all items
     * @return array all items
     */
    public function getList();
}