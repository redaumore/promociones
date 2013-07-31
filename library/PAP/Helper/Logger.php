<?php
class PAP_Helper_Logger{
    public static function writeLog($priority, $context, $message, $params = ""){
        $log = Zend_Registry::get("logDB");
        $user_id = 0;
        $auth=Zend_Auth::getInstance();
        if($auth->hasIdentity())
            $user_id = $auth->getIdentity()->user_id;
        if(isset($log)){
            $log->setEventItem('user', $user_id)
                ->setEventItem('priorityname', self::getPriorityString($priority))
                ->setEventItem('priority', $priority)
                ->setEventItem('context', $context)
                ->setEventItem('params', $params)
                ->setEventItem('message', $message)
                ->setEventItem('timestamp', date( 'Y-m-d H:i:s'));
            $log->log($message, $priority);    
        }
        else{
            Zend_Log::log($message, $priority);
        }
    }
    
    public static function writeDebug($context, $message, $params = ""){
        self::writeLog(Zend_Log::DEBUG, $context, $message, $params);
    }
    
    private static function getPriorityString($priority){
        $str = "";
        switch($priority){
            case Zend_Log::ALERT:
                $str = 'ALERT';
                break;
            case Zend_Log::CRIT:
                $str = 'CRIT';
                break;
            case Zend_Log::DEBUG:
                $str = 'DEBUG';
                break;
            case Zend_Log::EMERG:
                $str = 'EMERG';
                break;
            case Zend_Log::ERR:
                $str = 'ERR';
                break;
            case Zend_Log::INFO:
                $str = 'INFO';
                break;
        case Zend_Log::NOTICE:
                $str = 'NOTICE';
                break;
        case Zend_Log::WARN:
                $str = 'WARN';
                break;
        }
        return $str;
                
    }
}