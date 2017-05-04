<?php
namespace Translation;

use Translation\Protocol\Parser;
use Translation\Protocol\Store;
use Translation\Protocol\Translator;

class TranslationMemory{
    
    protected $store;
    protected $parser = [];
    protected $translator = null;
    protected $translator_name = null;
    
    protected $options = [
        'from'                  => 'en',
        'to'                    => 'zh-cn',     //Translate language to which language,default zh-ch
        'auto_translate'     => true,        //If Not find translation in memory,whether use machine tranlate,default true 
    ];
    
    
    public function __construct(Store $store,array $options=[],array $parsers=[],Translator $translator=null){
        $this->setStore($store);
        
        foreach($options as $k=>$option){
            $this->setConfig($k, $option);
        }
        foreach($parsers as $type=>$parser){
            $this->addParser($type, $parser);
        }
        
        if($translator)
            $this->setTranslator($translator);
        
        $this->refreshTarget();
    }
    
    public function setConfig($key,$val){
        $this->options[$key] = $val;
        if(in_array($key, ['from','to'])){
            $this->refreshTarget();
        }
    }
    
    private static $_instance;
    public static function getInstance(){
        if(!static::$_instance){
            static::$_instance = new self();
        }
        return static::$_instance;
    }
    
    public function addParser($type,Parser $class){
        $this->parser[$type] = $class;
    }
    
    public function setStore(Store $store){
        $this->store = $store;
        $this->refreshTarget();
    }
    
    public function setTranslator(Translator $translator){
        $this->translator = $translator;
        $className = get_class($this->translator);
        $className = explode('\\', $className);
        $this->translator_name = array_pop($className);
    }
    
    /**
     * Translate a single phrase
     * @param string $content
     * @return string translated phrase 
     */
    public function t($content){
        $key = $this->genKey($content);
        $trans = null;
        if($result = $this->store->get($key)){
            $trans = $result['value'];
            $result['hits']++;
            $this->store->set($key, $result);
            return $trans;
        }
        
        if(!$trans 
            && $this->options['auto_translate']
            && $this->translator){
            $trans = $this->translator->t($content,$this->options['from'],$this->options['to']);
            
            $this->setTranslation($content, $trans,$this->translator_name);
        }
        
        if(!$trans){
            $trans = $content;
            $this->setTranslation($content, $trans,'None');
        }
        
        return $trans;
    }
    
    /**
     * Translate a complex content,like html,paragraph
     * which need split into phrase
     * @param string $content
     * @param string $type
     * @return
     */
    public function complexTranslate($content,$parser){
        $trans = $this->parser[$parser]->process($content,function($str){
            return $this->t($str);
        });
        return $trans;
    }
    
    /**
     * update or add translation in memory
     * @param string $original original source language
     * @param string $trans target translated language
     * @param bool $reset whether reset the hits stats
     */
    public function setTranslation($original,$trans,$translator,$reset=false){
        $key = $this->genKey($original);
        $body = $this->store->get($key);
        if($body && $reset===false){
            // out of my mind (＞﹏＜) 
        }else{
            $body = [
                'hits'  => 1,
            ];
        }
        $body['original'] = $original;
        $body['value'] = $trans;
        if($translator){
            $body['translator'] = $translator;
        }
        $this->store->set($key, $body);
    }
    
    public function getList(){
        return array_values($this->store->getList());
    }
    
    protected function genKey($str){
        return sha1($str);
    }
    
    protected function refreshTarget(){
        $key = $this->options['from'].'_to_'.$this->options['to'];
        $this->store->setDefaultPartition($key);
    }
    
    
}