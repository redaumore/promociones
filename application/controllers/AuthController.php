<?php
class AuthController extends Zend_Controller_Action
{
    // Secret key to encrypt/decrypt with 
    protected $_key='promosalpasoclavesecretisima'; // 8-32 characters without spaces 

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
                    if($user->getStatus() == 'validated'){
                        $this->_redirect('branch/new');
                        return;    
                    }
                    if($user->getStatus() == 'charged'){
                        $this->_redirect('branch/categories');
                        return;    
                    }                    
                        
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
                $this->sendValidationEmail($data['email']);
                $this->_redirect('auth/showvalidationmessage');
            }
        }        
}

    public function logoutAction()
    {
        $this->_helper->Session->setUserSession(null);
        $storage = new Zend_Auth_Storage_Session();
        $storage->clear();
        Zend_Auth::getInstance()->clearIdentity();
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
        $param = $request->getParam("key");
        $email = $this->decrypt($param);
        if(isset($email)){
            $user = new PAP_Model_User();
            $user->loadByEmail($email);
            if(!isset($user))
                $this->_redirect('auth/notexist');    
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
        try {
        //DONE -o RED:  Envìo de email de validaciòn.
        $to = $email;
        $subject = "Activar tu cuenta en Promos al Paso";

        // compose headers
        $headers = "From: activaciones@promosalpaso.com\r\n";
        $headers .= "Reply-To: activaciones@promosalpaso.com\r\n";
        $headers .= "X-Mailer: PHP/".phpversion()."\r\n";
        // To send HTML mail, the Content-type header must be set
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=iso-8859-1\r\n";


        // compose message
        $message = "<html>
          <head>
            <title>Promos al Paso</title>
          </head>
          <body>
            <h1>Activá tu cuenta en Promos al Paso</h1>
            <p>
                Debes activar tu cuenta de Promos al paso para seguir cargando tus datos de Comercio y Promociones<br/>
                Para activarla solo tienes que hacer click en el siguiente link: 
                <a href='http://".$_SERVER['SERVER_NAME']."/auth/activate/key/".$this->encrypt($email)."'>Promos al Paso</a>
            </p>
            <p>
                Una vez que hayas activado tu cuenta, debes dar de alta los restantes datos de tu Comercio, por ejemplo dirección, teléfono, logo, etc.
            </p>
            <p>
                Si consideras que este email fue enviado por error y quieres contarnoslo, por favor hazlo a soporte@promosalpaso.com.
            </p>
            <p>
                Gracias!!
            </p>
          </body>
        </html>
        ";

        // send email
        ZC_FileLogger::info("Activando anunciante: ".$to);
        mail($to, $subject, $message, $headers);    
        }
        catch(Exception $ex){
            ZC_FileLogger::error($ex);
        }
    }
    
    // String EnCrypt + DeCrypt function 
    // Author: halojoy, July 2006 
    private function convert($str,$ky=''){ 
        if($ky=='')return $str; 
        $ky=str_replace(chr(32),'',$ky); 
        if(strlen($ky)<8)exit('key error'); 
        $kl=strlen($ky)<32?strlen($ky):32; 
        $k=array();
        for($i=0;$i<$kl;$i++){ 
            $k[$i]=ord($ky{$i})&0x1F;
        } 
        $j=0;
        for($i=0;$i<strlen($str);$i++){ 
            $e=ord($str{$i}); 
            $str{$i}=$e&0xE0?chr($e^$k[$j]):chr($e); 
            $j++;
            $j=$j==$kl?0:$j;
        } 
        return $str; 
    }
    
    private function encrypt($str){
        $clave = base64_encode(urlencode($str));
        return $clave;
    }
    
    private function decrypt($str){
        $clave = urldecode(base64_decode($str));
        return $clave;
    } 
}


