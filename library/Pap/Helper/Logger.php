<?php
class PAP_Helper_Logger{
    public static function writeLog($priority, $context, $message){
        $log = Zend_Registry::get("logDB");
        $auth=Zend_Auth::getInstance();
        $log->setEventItem('user', (isset($auth))?$auth->getIdentity()->user_id:0)
            ->setEventItem('priorityname', self::getPriorityString($priority))
            ->setEventItem('priority', $priority)
            ->setEventItem('context', $this->view->message)
            ->setEventItem('timestamp', date( 'Y-m-d H:i:s'));
        $log->log($errors->exception);    
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