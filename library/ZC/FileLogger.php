<?php
class ZC_FileLogger{
    /**
    * @var Zend_Log
    */
    protected $logger;
    
    /**
    * @var ZC_FileLogger
    * 
    */
    static $fileLogger = null;
    public static function getInstance(){
        if(self::$fileLogger === null){
            self::$fileLogger = new self();
        }
        return self::$fileLogger;
    }
    
    public function getLog(){
        return $this->logger;
    }
    
    protected function __construct(){
        $this->logger = Zend_Registry::get('log');
    }
    
    public static function info($message){
        self::getInstance()->getLog()->info($message);
    }
    
}