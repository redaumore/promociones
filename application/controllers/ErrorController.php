<?php

class ErrorController extends Zend_Controller_Action
{
    public function init(){
        // Add the context to the error action
        //$this->_helper->contextSwitch()->addActionContext('error', 'json');
    }
    
    public function errorAction()
    {
        $errors = $this->_getParam('error_handler');
        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->contextSwitch()->initJsonContext();
            //$this->_helper->contextSwitch()->initContext('json');
            $response = array('success' => false);

            if ($this->getInvokeArg('displayExceptions') == true) {
                // Add exception error message
                $response['message'] = $errors->exception->getMessage();

                // Send stack trace
                $response['code'] = $errors->exception->getCode();

            }

            echo Zend_Json::encode($response);
            return;
        }
        
        if (!$errors || !$errors instanceof ArrayObject) {
            $this->view->message = 'You have reached the error page';
            return;
        }
        
        switch ($errors->type) {
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
                // 404 error -- controller or action not found
                $this->getResponse()->setHttpResponseCode(404);
                $priority = Zend_Log::NOTICE;
                $this->view->message = 'Page not found';
                break;
            default:
                // application error
                $this->getResponse()->setHttpResponseCode(500);
                $priority = Zend_Log::CRIT;
                $this->view->message = 'Application error';
                break;
        }
        
        // Log exception, if logger available
        if ($log = $this->getLog()) {
            $auth=Zend_Auth::getInstance();
            $log->setEventItem('user', $auth->getIdentity()->user_id)
                ->setEventItem('priorityname', 'CRIT')
                ->setEventItem('context', $this->view->message)
                ->setEventItem('timestamp', date( 'Y-m-d H:i:s'));
            $log->crit($errors->exception);
        }
        // conditionally display exceptions
        if ($this->getInvokeArg('displayExceptions') == true) {
            $this->view->exception = $errors->exception;
        }
        $this->view->request   = $errors->request;
    }

    public function getLog()
    {
        return Zend_Registry::get("logDB");
    }


}

