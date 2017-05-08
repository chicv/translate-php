<?php
namespace Translation\ToolKit;

use Translation\Protocol\Store;
use Redis;

/**
 * a simple impliments of translation memory store
 * @author Nay Kang
 *
 */
class RedisStore implements Store{
    protected $prefix = 'trans_';
    protected $partition = '';
    protected $redis = null;
    
    /**
     * Create a new store instance
     * @param array $options redis connect config and prefix
     */
    public function __construct(array $options = []){
        $this->redis = new Redis();
        $default = [
            'ip' => '127.0.0.1',
            'port' => '6379',
            'database' => '0',
            'auth' => false,
            'prefix' => $this->prefix
        ];
        $options = array_merge($default,$options);
        
        $this->redis->connect($options['ip'],$options['port']);
        if($options['database']){
            $this->redis->select($options['database']);
        }
        if($options['auth']){
            $this->redis->auth($options['auth']);
        }
        $this->prefix = $options['prefix'];
    }

    protected function wrapKey($key){
        return $this->prefix.$this->partition.':'.$key;
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