<?php

class PAP_Model_User
{
    protected $_user_id;
    protected $_email;
    protected $_name;
    protected $_password;
    protected $_price_rule_id;
    protected $_cuit;
    protected $_rol;
    protected $_created;
    protected $_status;
    protected $_billingAddress;
    protected $_customer_list;
    
    
    public function __construct(array $options = null)
    {
        if (is_array($options)) {
            $this->setOptions($options);
        }
    }
    
    public function __set($name, $value)
    {
        $method = 'set' . $name;
        if (('mapper' == $name) || !method_exists($this, $method)) {
            throw new Exception('Invalid user property');
        }
        $this->$method($value);
    }
 
    public function __get($name)
    {
        $method = 'get' . $name;
        if (('mapper' == $name) || !method_exists($this, $method)) {
            throw new Exception('Invalid user property');
        }
        return $this->$method();
    }
    
    public function checkUnique($email){
        $userMapper = new PAP_Model_UserMapper();
        return $userMapper->existByEmail($email);
    }
    
    public function loadByEmail($email){
        $result = false;
        $userMapper = new PAP_Model_UserMapper();
        $result = $userMapper->loadByEmail($email, $this);
        if($result)
            $this->loadBillingAddress();
        return $result;
    }
    
    public function loadById($id){
        $userMapper = new PAP_Model_UserMapper();
        $userMapper->find($id, $this);
        //$this->loadBillingAddress();
    }
    
    public function insert(array $options){
        $this->setOptions($options);
        $userMapper = new PAP_Model_UserMapper();
        $userMapper->save($this);
    }
    
    public function update(){
        $userMapper = new PAP_Model_UserMapper();
        $userMapper->save($this);    
    }
 
    public function setOptions(array $options)
    {
        $methods = get_class_methods($this);
        foreach ($options as $key => $value) {
            $method = 'set' . ucfirst($key);
            if (in_array($method, $methods)) {
                $this->$method($value);
            }
            if($key == 'user_id')
                $this->setId($value);
            if($key == 'price_rule_id')
                $this->setPriceRuleId($value);
            if($key == 'customer_list')
                $this->setCustomerList($value);
        }
        return $this;
    }
    
    public function getPriceRulesItems(){
        $mapper = new PAP_Model_UserMapper();
        $rules = $mapper->getPriceRulesItems($this->getPriceRuleId()); 
        return $rules;   
    }
    
    public function getBranch($branch_order = 0){
        $branch = new PAP_Model_Branch();
        $branchMapper = new PAP_Model_BranchMapper();
        $branchMapper->findByUserId($this, $branch_order, $branch);
        return $branch;
    }
    
    public function getBranches(){
        $branches = array();
        $branchMapper = new PAP_Model_BranchMapper();
        $branches = $branchMapper->findAllByUserId($this);
        return $branches;
    }
    
    public function getCategories(){
        $userMap = new PAP_Model_UserMapper();
        $categories = $userMap->getCategories($this);
        return $categories;
    }
    
    public function getPaymentMethods(){
        $payment_methods = array();
        if(!isset($this->_billingAddress))
            if(!$this->loadBillingAddress())
                return $payment_methods;
        $payment_methods = $this->_billingAddress->getCity()->getPaymentMethods();
        
        /*$paymentMethods = array();
        $paymentMethods[] = 'E';
        $paymentMethods[] = 'MP';*/
        return $payment_methods;
    }
    
    public function setCategories($categories){
        $userMap = new PAP_Model_UserMapper();
        $categories = $userMap->setCategories($this, $categories);
        return;    
    }
    
    public function refreshStatus(){
        $my_info = PAP_Model_Charge::getDebtorsInfo($this);
        if(isset($my_info)){
             if ($this->getStatus() == 'active'){
                $this->setStatus('debtor');
                $this->update();
            }   
        }
        else{
            if ($this->getStatus() == 'debtor'){
                $this->setStatus('active');
                $this->update();
            }
        }
    }
    
    private function loadBillingAddress(){
        $mainBranch = $this->getBranch();
        if(isset($mainBranch)){
            $this->_billingAddress = $mainBranch->getAddress();
            return true;
        }
        return false;
    }
 
    public function setId($text)
    {
        $this->_user_id = (string) $text;
        return $this;
    }
 
    public function getId()
    {
        return $this->_user_id;
    }
    
    public function setEmail($text)
    {
        $this->_email = (string) $text;
        return $this;
    }
 
    public function getEmail()
    {
        return $this->_email;
    }
    
    public function setName($text)
    {
        $this->_name = (string) $text;
        return $this;
    }
 
    public function getName()
    {
        return $this->_name;
    }
    
    public function setPassword($text)
    {
        $this->_password = (string) $text;
        return $this;
    }
 
    public function getPassword()
    {
        return $this->_password;
    }
    
    public function setPriceRuleId($text)
    {
        $this->_price_rule_id = (string) $text;
        return $this;
    }
 
    public function getPriceRuleId()
    {
        return $this->_price_rule_id;
    }
    
    public function setCuit($text)
    {
        $this->_cuit = (string) $text;
        return $this;
    }
 
    public function getCuit()
    {
        return $this->_cuit;
    }
    
    public function setCreated($text)
    {
        $this->_created = (string) $text;
        return $this;
    }
 
    public function getCreated()
    {
        return $this->_created;
    }
    
    public function setStatus($text)
    {
        $this->_status = (string) $text;
        return $this;
    }
    public function getStatus()
    {
        return $this->_status;
    }
    
     public function setCustomerList($text)
    {
        $this->_customer_list = (string) $text;
        return $this;
    }
    public function getCustomerList()
    {
        //TODO 5: Devolver objeto y no id. 
        return $this->_customer_list;
    }
    
    public function setRol($text)
    {
        $this->_rol = (string) $text;
        return $this;
    }
 
    public function getRol()
    {
        return $this->_rol;
    }
    
    public function resetPassword(){
        $newPassword = PAP_Helper_Tools::generateRandomString();
        $this->setPassword($newPassword);
        $this->update();
        return $this->sendResetPasswordEmail($this->getEmail(), $newPassword);
    }
    
    private function sendResetPasswordEmail($email, $newpassword){
        try {
            //DONE -o RED:  Envìo de email de validaciòn.
            $to = $email;
            $subject = "Tu contraseña en Promos al Paso ha cambiado";

            // compose headers
            $headers = "From: soporte@promosalpaso.com\r\n";
            $headers .= "Reply-To: soporte@promosalpaso.com\r\n";
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
                <h1>Su contraseña en Promos al Paso ha cambiado.</h1>
                <p>
                    Se ha pedido que cambiemos la contraseña para el backend de Promos al Paso. Estos son los nuevos datos.<br/>
                    Usuario: ".$email.
                    "Nueva Contraseña: ".$newpassword.
                "</p>
                <p>
                    Por favor cambiala una vez que hayas ingresado al sistema.
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
            mail($to, $subject, $message, $headers);
            return true;    
        }
        catch(Exception $ex){
            PAP_Helper_Logger::writeLog(Zend_Log::ERR, 'User->sendValidationEmail()',$ex, $_SERVER['REQUEST_URI']);
            return false;
        }
    }
    
    public function validatePassword($id, $password){
        $mapper = new PAP_Model_UserMapper();
        $mapper->find($id, $this);
        if($this->getPassword() == $password)
            return true;
        return false;
    }
    
    public static function getUsersByAntiquity($days){
        $mapper = new PAP_Model_UserMapper();
        $users = $mapper->getUsersByAntiquity($days);
        return $users;    
    }
    
    public static function changePriceRulesToUsers($fromPR, $toPR, $users = null){
        $usersArr = array();
        foreach($users as $user){
            $usersArr[] = $user->getId();
        }
        $mapper = new PAP_Model_UserMapper();
        $pricerule = new PAP_Model_PriceRule();
        $pricerule->loadByCode($fromPR);
        $fromPR = $pricerule->getId();
        $pricerule->loadByCode($toPR);
        $toPR = $pricerule->getId();
        
        $mapper->changePriceRulesToUsers($fromPR, $toPR, $usersArr);        
    }
    
    public static function changePriceRulesToNewUsers(){
        $days = PAP_Helper_Config::getDaysAsNew();
        $users = PAP_Model_User::getUsersByAntiquity($days);
        if($users != null)
            PAP_Model_User::changePriceRulesToUsers("C1", "C2", $users);        
    }
}

