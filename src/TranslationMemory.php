<?php
namespace Translation;

use Translation\Protocol\Parser;
use Translation\Protocol\Store;
use Translation\Protocol\Translator;
use Translation\ToolKit\RedisStore;
use Translation\Translator\FreeGoogle;
use Translation\Parser\ParagraphParser;
use Translation\Parser\HTMLParser;

/**
 * Translation Core library,all operation will apply on this class
 * backbone library
 * @author Nay Kang
 *
 */
class TranslationMemory{
    
    //store object,use to store translations
    protected $store;
    
    //Various kinds of parser to split content into small unit
    protected $parser = [];
    
    //Sync translator,when translation not found in store,this translator will be called
    protected $translator = null;
    //Name of the translator above
    protected $translator_name = null;
    
    protected $options = [
        'from'               => 'en',        //Translate from which language
        'to'                 => 'zh-cn',     //Translate language to which language,default zh-ch
        'auto_translate'     => true,        //If Not find translation in memory,whether use machine tranlate,default true 
    ];
    
    /**
     * Create new Translation Memory Object
     * @param Store $store
     * @param array $options
     * @param array $parsers
     * @param Translator $translator
     */
    public function __construct(Store $store=null,array $options=[],array $parsers=[],Translator $translator=null){
        if($store==null){
            $store = new RedisStore();
        }
        $this->setStore($store);
        
        foreach($options as $k=>$option){
            $this->setConfig($k, $option);
        }
        
        //set default parser
        $paragraphParser = new ParagraphParser();
        $this->addParser('paragraph', $paragraphParser);
        $htmlParser = new HTMLParser();
        $this->addParser('html', $htmlParser);
        
        foreach($parsers as $type=>$parser){
            $this->addParser($type, $parser);
        }
        
        if($translator==null){
            $translator = new FreeGoogle();
        }
        $this->setTranslator($translator);
        
        $this->refreshTarget();
    }
    
    /**
     * Change config on the fly
     * @param string $key
     * @param mixed $val
     */
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
    
    /**
     * Add new parser or replace parser 
     * @param string $type parser name
     * @param Parser $class
     */
    public function addParser($type,Parser $class){
        $this->parser[$type] = $class;
    }
    
    /**
     * Set Transation Store
     * @param Store $store
     */
    public function setStore(Store $store){
        $this->store = $store;
        $this->refreshTarget();
    }
    
    /**
     * Set sync translator
     * @param Translator $translator
     */
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
    
    /**
     * Get translation list from store
     * @return array 
     */
    public function getList(){
        return array_values($this->store->getList());
    }
    
    /**
     * Generate key for store base on translate content
     * @param string $str
     */
    protected function genKey($str){
        return sha1($str);
    }
    
    /**
     * When reset from or to language,this will be called to apply changes to plugin
     */
    protected function refreshTarget(){
        $key = $this->options['from'].'_to_'.$this->options['to'];
        $this->store->setDefaultPartition($key);
    }
    
    
}