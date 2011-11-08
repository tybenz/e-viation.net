<?php
require_once('BaseController.php');


class IndexController extends BaseController
{

    public function init()
    {
        parent::init();
        /* Initialize action controller here */
    }
    
    public function indexAction()
    {
        $ads = new Application_Model_Ads();
        $this->setSessionData();
        //$users_table = new Application_Model_Users();
        
        $post = $this->getRequest()->getPost();

        
        if(isset($post['email']) && isset($post['password']) && isset($post['token'])) {
            if($post['email'] == 'tabenziger@ucdavis.edu' && $post['password'] == 'integ3R#') {
                $token = Zend_Registry::get('login_token');
                $token_arr = explode('-', $token);
                $post_token_arr = explode('-', $post['token']);                
                if($token_arr[1] == $post_token_arr[1] && ((strtotime('now') - $token_arr[0]) < 10)) {
                    $_SESSION['email'] = $post['email'];
                    $this->view->message = 'test';
                }
            }
        }

    	if(isset($_SESSION['email'])) {
            //personalized page
    	}
        
        
        $all_ads = $ads->getAllAds(array('sort_by' => 'ads_id_pk', 'sort_direction' => 'DESC'));
        
        $recent_ads = array();
        foreach($all_ads as &$a) {
            $a['ads_timestamp'] = date('m/d/y', strtotime($a['ads_timestamp']));
            $recent_ads[$a['ads_timestamp']][] = $a;
        }     
        
        $today = date('m/d/y', strtotime('today'));

        
        $recent_ads = $this->pageSetup($recent_ads[$today], array('table_name' => 'recent', 'num_per_page' => 5));
        
        
        
        $this->view->ads = $recent_ads;
        
    }
    
    public function postAdAction()
    {
        $ads = new Application_Model_Ads();
        $images = array();
        foreach($_FILES as $key => $file) {
            $file_name = $file['name'];
            $type = explode('.', $file_name);
            if(isset($type[1]) && $type[1] != 'jpg') {
                $type = $type[1];
            } else {
                $type = 'jpeg';
            }
            if($file['name'] != '') {
                $_POST[$key] = 'data:image/' . $type . ';base64,' . base64_encode(@file_get_contents($file['tmp_name']));
            } else {
                $_POST[$key] = '';
            }
        }
        
        
        $_POST['ads_timestamp'] = date('Y-m-d H:i:s', strtotime('now'));
        
        $result = $ads->addAd($_POST);
        
        if($result) {
            echo 'good';
            exit;
        } else {
            echo 'bad';
            exit;
        }

        sleep(1);
    }
    
    public function loginAction()
    {
        $post = $this->getRequest()->getPost();
        $users = new Application_Model_Users();
        
        
        if(isset($post['usr_email']) && isset($post['usr_password']) && isset($post['token'])) {
            $token = Zend_Registry::get('login_token');
            $token_arr = explode('-', $token);
            $post_token_arr = explode('-', $post['token']);                
            if($token_arr[1] == $post_token_arr[1] && ((strtotime('now') - $token_arr[0]) < 600)) {
                //token is valid check password
                $user = $users->getAllUsers(array('usr_email' => $post['usr_email']));
                if(count($user) != 1) {
                    //error - no user with matching email or more than one user with matching email
                } else {
                    $user = reset($user);
                    $password = md5($user['usr_salt'] . $post['usr_password']);
                    if($password == $user['usr_password']) {
                        //send messages to the flashMessenger view helper
                        
                        //set session data
                        $session = new Zend_Session_Namespace('User_Data');
                        $session->usr_email = $post['usr_email'];
                        echo('#8fbeea|Welcome back, ' . $user['usr_fname'] . '!');
                        exit;
                    } else {
                        //send messages to the flashMessenger view helper
                        echo('#f00|Invalid email and/or password.');
                        exit;
                    }
                }
            }
        }
        
        echo('#f00|Invalid email and/or password.');
        exit;
    }
    
    public function logoutAction()
    {
        Zend_Session::namespaceUnset('User_Data');
        
        echo 'success';
        exit;
    }
    
    public function registerAction()
    {
        $session = Zend_Registry::get('session');
        $post = $this->getRequest()->getPost();
        $users = new Application_Model_Users();
        
        $conflicts = $users->getAllUsers(array('usr_email' => $post['usr_email']));
        
        
        if($post['usr_password'] == $post['usr_password2']) {
            if($post['usr_password'] == '' || count(preg_split('//', $post['usr_password'])) < 8 || count(preg_split('//', $post['usr_password'])) > 14) {
                echo('#f00|Password must be 6-12 characters');
                exit;
            }
        } else {
            echo('#f00|Passwords do not match');
            exit;
        }
        if(!preg_match('/[A-z0-9]*@[A-z0-9]*\.[A-z0-9]*/', $post['usr_email']) || preg_match_all('/@/', $post['usr_email'], $out) > 1) {
            echo('#f00|Email invalid');
            exit;
        }
        if(count($conflicts) > 0) {
            echo('#f00|Email already in use');
            exit;
        } 
           
        $post['usr_salt'] = md5(uniqid(rand(), TRUE));
        $post['usr_password'] = md5($post['usr_salt'] . $post['usr_password']);
        unset($post['usr_password2']);
        $response = $users->addUser($post);

        //set session data
        $session = new Zend_Session_Namespace('User_Data');
        $session->usr_email = $post['usr_email'];
        echo('#8fbeea|Welcome to e-viation.net');
        exit;
    
    }


}

