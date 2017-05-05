<?php
require_once '../autoload.php';

use Translation\Protocol\Store;
use Redis;

class RedisStore implements Store{
    const PREFIX = 'trans_';
    protected $partition = '';
    protected $redis = null;
    
    public function __construct(){
        $this->redis = new Redis();
        $this->redis->connect('127.0.0.1');
    }

    protected function wrapKey($key){
        return self::PREFIX.$this->partition.':'.$key;
    }

    public function setDefaultPartition($partition){
        $this->partition = $partition;
    }

    public function set($key,$content){
        $content = is_numeric($content) ? $content : serialize($content);
        $this->redis->set($this->wrapKey($key),$content);
    }

    public function get($key){
        $value = $this->redis->get($this->wrapKey($key));
        return is_numeric($value) ? $value : unserialize($value);
    }

    public function getList(){
        $list = $this->redis->keys($this->wrapKey('*'));
        $rlist = [];
        foreach($list as $item){
            $item = str_replace($this->wrapKey(''), '', $item);
            $rlist[$item] = $this->get($item);
        }
        return $rlist;
    }
}