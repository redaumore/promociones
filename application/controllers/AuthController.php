<?php
class AuthController extends Zend_Controller_Action
{

    public function init(){
    
    }
    
    public function indexAction()
    {
        $this->_redirect('auth/login');        
    }

    public function loginAction()
    {
        $users = new PAP_Model_User();
        $form = new PAP_Form_LoginForm();
        $this->view->form = $form;
        if($this->getRequest()->isPost()){
            if($form->isValid($_POST)){
                $data = $form->getValues();
                $auth = Zend_Auth::getInstance();
                $authAdapter = new Zend_Auth_Adapter_DbTable(Zend_Db_Table::getDefaultAdapter(),'user');
                $authAdapter->setIdentityColumn('email')
                            ->setCredentialColumn('password');
                $authAdapter->setIdentity($data['email'])
                            ->setCredential($data['password']);
                $result = $auth->authenticate($authAdapter);
                if($result->isValid()){
                    $user = new PAP_Model_User();
                    $user->loadByEmail($data['email']);
                    $this->_helper->Session->setUserSession($user);
                    if($user->getStatus() == 'pending')
                        $this->_redirect('auth/resendemail');
                    $storage = $auth->getStorage();
                    $storage->write($authAdapter->getResultRowObject());
                    if($user->getStatus() != 'active')                    
                        $this->_redirect('branch/new');
                    $this->_redirect('promotion/index');
                } else {
                    $form->addError("Invalid username or password. Please try again.");
                }         
            }
        }
        else{
            $request = $this->getRequest();
            $email = $request->getParam("user");
            if(isset($email))
                $this->view->assign('user', $email);
            else
                $this->view->assign('user', '');
        }
    }

    public function signupAction()
    {
        $users = new PAP_Model_User();
        $form = new PAP_Form_RegistrationForm();
        $this->view->form=$form;
        if($this->getRequest()->isPost()){
            if($form->isValid($_POST)){
                $data = $form->getValues();
                if($data['password'] != $data['confirmPassword']){
                    //$this->view->errorMessage = "Password and confirm password don't match.";
                    $form->addError("Password and confirm password don't match.");
                    return;
                }
                if($users->checkUnique($data['email'])){
                    $form->addError("Email already taken. Please choose another one.");
                    return;
                }
                unset($data['confirmPassword']);
                $data["priceRuleId"] = 1;
                $data["status"] = 'pending';
                $data["rol"] = 2; //1:reseler, 2:customer, 3:admin
                $users->insert($data);
                $this->_redirect('auth/showvalidationmessage');
            }
        }        
}

    public function logoutAction()
    {
        $storage = new Zend_Auth_Storage_Session();
        $storage->clear();
        $this->_redirect('auth/login');
    }

    public function homeAction()
    {
        $storage = new Zend_Auth_Storage_Session();
        $data = $storage->read();
        if(!$data){
            $this->_redirect('auth/login');
        }
        $this->view->username = $data->username;
    }

    public function showvalidationmessageAction()
    {
    }

    public function activateAction()
    {
        $request = $this->getRequest();
        $email = $request->getParam("user");
        if(isset($email)){
            $user = new PAP_Model_User();
            $user->loadByEmail($email);
            if($user->getStatus() == 'pending'){
                $user->setStatus('validated');
                $user->update();
                $this->view->assign('name', $user->getName());
            }
            else{
                //Si el user ya fue validado
                if($user->getStatus()!= null)
                    $this->_redirect('auth/login/user/'.$email);
                else
                    $this->_redirect('auth/login');
            }
        }
        else{
            $this->_redirect('auth/login');    
        }
            
        //
    }

    public function resendemailAction()
    {
        $this->view->form = new PAP_Form_ResendEmailForm();
        $form = $this->view->form;
        $user = $this->_helper->Session->getUserSession();
        
        if($this->getRequest()->isPost()){
            if($form->isValid($_POST)){
                $data = $form->getValues();
                $this->sendValidationEmail($data['email']);
                $this->_redirect('auth/showvalidationmessage');
            }
        }
        else{
            if(isset($user))
            $this->view->form->email->setValue($user->email);
        else
            $this->_redirect('/');    
        }
    }
    
    private function sendValidationEmail($email)
    {
        //TODO -o RED:  Envìo de email de validaciòn.    
    }
}


