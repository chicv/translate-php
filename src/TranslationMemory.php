<?php
namespace Translation;

use Translation\Protocol\Parser;
use Translation\Protocol\Store;
use Translation\Protocol\Translator;

class TranslationMemory{
    
    protected $store;
    protected $parser = [];
    protected $translator = null;
    protected $options = [
        'to'                    => 'zh-cn',     //Translate language to which language,default zh-ch
        'machine_translate'     => true,        //If Not find translation in memory,whether use machine tranlate,default true 
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
        
        $this->changeTarget($this->options['to']);
    }
    
    public function setConfig($key,$val){
        $this->options[$key] = $val;
        if($key=='to'){
            $this->changeTarget($val);
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
        $this->changeTarget($this->options['to']);
    }
    
    public function setTranslator(Translator $translator){
        $this->translator = $translator;
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
        }
        
        if(!$trans 
            && $this->options['machine_translate']
            && $this->translator){
            $trans = $this->translator->t($content);
        }
        
        if(!$trans){
            $trans = $content;
        }
        $this->setTranslation($content, $trans);
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
    public function setTranslation($original,$trans,$reset=false){
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
        $this->store->set($key, $body);
    }
    
    public function getList(){
        return array_values($this->store->getList());
    }
    
    protected function genKey($str){
        return sha1($str);
    }
    
    protected function changeTarget($code){
        $this->store->setDefaultPartition($code);
    }
    
    
}