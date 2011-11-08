<?php
//required for CAS authentication 
//located in library/CAS


class BaseController extends Zend_Controller_Action
{
	public $user = array();
	
	
    public function init()
    {
    	//setting namespace for the session
    	$default_session_namespace = new Zend_Session_Namespace('Default');
    	Zend_Registry::set('session', $default_session_namespace);
              
        $front = Zend_Controller_Front::getInstance();
		$request = $front->getRequest();
        
        //passing current controller and action for use in view
        $temp_controller = $request->getControllerName();
		$temp_action = $request->getActionName();

		Zend_Registry::set('c_controller', $temp_controller);
		Zend_Registry::set('c_action', $temp_action);
		$this->view->c_controller = $temp_controller;
		$this->view->c_action = $temp_action;
    	
    	//flashMessenger used to pass messages between actions
    	$this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
    	$this->_flashMessenger->setNamespace('error');
    	$this->view->error_messages = $this->_flashMessenger->getMessages();
    	
    	$this->_flashMessenger->setNamespace('info');
    	$this->view->info_messages = $this->_flashMessenger->getMessages();
    	
    	$this->_flashMessenger->setNamespace('message');
    	$this->view->message_messages = $this->_flashMessenger->getMessages();
    	
    	$this->_flashMessenger->setNamespace('confirmation');
    	$this->view->confirmation_messages = $this->_flashMessenger->getMessages();
    	
    	$this->_flashMessenger->resetNamespace();
    }
    
    public function preDispatch()
    {
        $users = new Application_Model_Users();
        $ads = new Application_Model_Ads();
        $donations = new Application_Model_Donations();
        
        date_default_timezone_set('America/Los_Angeles');
        $register_form = new Application_Form_Register();
        $this->view->register_form = $register_form;

        if(!isset($_POST['token'])) {
            $token = strtotime('now') . '-' . md5(uniqid(rand(), TRUE));
            Zend_Registry::set('login_token', $token);
            $this->view->login_token = $token;
        } else {
            Zend_Registry::set('login_token', $_POST['token']);
        }
        
        //set user for view with session
        $session = new Zend_Session_Namespace('User_Data');
        $this->view->usr_email = $session->usr_email;
        if($session->usr_email != NULL) {
            $user = $users->getAllUsers(array('usr_email' => $session->usr_email));
            $this->view->user = reset($user);
        } else {
            $this->view->user = array('usr_id' => '', 'usr_email' => '');
        }
                
        $categories = $ads->getAllCategories(array());
        foreach($categories as &$c) {
            $label = explode('_', $c['adc_category']);
            $label = implode(' ', $label);
            $c['adc_label'] = ucwords($label);
        }
        $this->view->categories = $categories;
        
        $subcategories = $ads->getAllSubcategories(array());
        foreach($subcategories as &$s) {
            $label = explode('_', $s['asc_subcategory']);
            $label = implode(' ', $label);
            $s['asc_label'] = ucwords($label);
        }
        $this->view->subcategories = $subcategories;
        
        $this->view->donations_types = $donations->getAllTypes();
        
        
        
        $front = $this->getFrontController();
        $acl = array();

        //getting the directory
        foreach ($front->getControllerDirectory() as $module => $path) {
            //scanning every path, looking for Controller files
            foreach (scandir($path) as $file) {
                if (strstr($file, "Controller.php") !== false) {
                    include_once($path . DIRECTORY_SEPARATOR . $file);

                    foreach (get_declared_classes() as $class) {
                        if (is_subclass_of($class, 'Zend_Controller_Action')) {
                            $controller = substr($class, 0, strpos($class, "Controller"));
                            $actions = array();

                            foreach (get_class_methods($class) as $action) {
                                //looking for Actions
                                if (strstr($action, "Action") !== false) {
                                    $actions[] = $action;
                                }
                            }
                        }
                    }

                    $acl[$module][$controller] = $actions;
                }
            }
        }
        
        $sitemap = array();
        
        foreach($acl['default'] as $key => &$a) { 
            foreach($a as $key2 => &$act) {
                if($act == 'indexAction') {
                    unset($a[$key2]);
                } else {
                    $pos = strpos($act, 'Action');
                    $act = substr($act, 0, $pos);
                    $pattern = "/(.)([A-Z])/";
                    $replacement = "\\1-\\2";
                    $act = strtolower(preg_replace($pattern, $replacement, $act));
                    $act = array('link' => $act, 'label' => ucwords(implode(' ', explode('-', $act))));
                }
            }
            $new_key = $key;$pattern = "/(.)([A-Z])/";
            $replacement = "\\1-\\2";
            $new_key = strtolower(preg_replace($pattern, $replacement, $new_key));
            $sitemap[$new_key] = $a;
        }
        
        $this->view->sitemap = $sitemap;
        
        
        $this->view->tabs = array(
            'aircraft' => array(
                'single-engine' => array('active' => true, 'label' => 'Single Engine'),
                'multi-engine' => array('active' => false, 'label' => 'Multi Engine'),
                'jet' => array('active' => false, 'label' => 'Jet'),
                'helicopter' => array('active' => false, 'label' => 'Helicopter'),
                'light_sport' => array('active' => false, 'label' => 'Light Sport'),
            ),
            'project' => array(
                'single-engine' => array('active' => true, 'label' => 'Single Engine'),
                'multi-engine' => array('active' => false, 'label' => 'Multi Engine'),
                'jet' => array('active' => false, 'label' => 'Jet'),
                'helicopter' => array('active' => false, 'label' => 'Helicopter'),
                'light-sport' => array('active' => false, 'label' => 'Light Sport'),
            ),
            'parts' => array(
                'airframe' => array('active' => true, 'label' => 'Airframe'),
                'engine' => array('active' => false, 'label' => 'Engine'),
                'avionics' => array('active' => false, 'label' => 'Avionics'),
                'props' => array('active' => false, 'label' => 'Props'),
            ),
            'everything-else' => array(
                'flight-gear' => array('active' => true, 'label' => 'Flight Gear'),
                'property' => array('active' => false, 'label' => 'Property'),
                'services-provided' => array('active' => false, 'label' => 'Services Provided'),
            ),
        );
    }
    
    public function setSessionData()
    {
        if(Zend_Registry::isRegistered('session')) {
            $session = Zend_Registry::get('session');
            $session->referrer_url = $this->view->url();
        }
    }

    
    
    /********************************************************************************
     *                          Helper functions                                      *
     ********************************************************************************/
    
    //convert a numeric representation of the day of the week (dow) into a textual representation
    protected function dowToDay( $dow ) {
        switch( $dow ) {
            case 1 :
                return 'Sunday';
            case 2 :
                return 'Monday';
            case 3 :
                return 'Tuesday';
            case 4 :
                return 'Wednesday';
            case 5 :
                return 'Thursday';
            case 6 :
                return 'Friday';
            case 7 :
                return 'Saturday';
            default :
                return $dow;
        }
    }
    
    //converts oracle's datetime into a date string acceptable for the form
    protected function formDate($oracleDate)
    {
        //When the date string is null this means the db does not contain a date.
    	//Returning null prevents the date() function from defaulting to 12-31-1969.
    	//This also allows us to test vs. null in our code.
    	if ($oracleDate == null || $oracleDate == '') {
    		return null;
    	}
    	
    	//Returns date in yyyy-MM-dd format.
    	//This format is used because Dojo DateTextBox on populates data in this format.
    	else {
    		$formDate = date('Y-m-d', strtotime($oracleDate));
    		return $formDate;
    	}
    }
    
    //converts oracle's datetime into a string which represents both date and time
    protected function formatOracleDateTime($format, $oracleDateTime)
    {
        
        //When the date string is null this means the db does not contain a date.
    	//Returning null prevents the date() function from defaulting to 12-31-1969.
    	//This also allows us to test vs. NULL in our code.
    	if ($oracleDateTime == null || $oracleDateTime == '') {
    		return null;
    	} else {
    		$datetime = explode(' ', $oracleDateTime);
    		$date = $datetime[0];
    		$time = $datetime[1];
    		$time_suffix = $datetime[2];
    		$date = explode('-', $date);
    		$day = (int)$date[0];
    		$year= (int)($date[2]);
    		if ( $year > 50 ) {
    			$year += 1900;
    		} else {
    			$year += 2000;
    		}
    		//adding more than just MONTH data to strtotime to deal with errors with leap years
    		$month = (int)date('n', strtotime($date[1] . ' ' . $day . ' ' . $year ));
    		
    		$time = explode('.', $time);
    		$hour = $time[0];
    		if ( $time_suffix == 'PM' && $hour != 12 ) {
    			$hour = (int)$hour + 12;
    		}
    		$minute = (int)$time[1];
    		$seconds = (int)$time[2];
    		return date($format,mktime($hour, $minute, $seconds, $month, $day, $year));
    	}
    }

    //converts oracle's time into a time string acceptable for the form
    protected function formTime($format, $oracleTime)
    {
    	//When the time string is null this means the db does not contain a time.
    	//Returning null prevents the date() function from defaulting to 16:00.
    	//This also allows us to test vs. NULL in our code.
    	if ($oracleTime == null || $oracleTime == '') {
    		return null;
    	} else {
            $time_array = explode(' ', $oracleTime);
            $time = $time_array[0];
            $time_suffix = $time_array[1];
            $time = explode('.', $time);
            $hour = $time[0];
            if ( $time_suffix == 'PM' && $hour != 12 ) {
                    $hour = (int)$hour + 12;
            }
            $minute = (int)$time[1];
            $seconds = (int)$time[2];
            return date($format,mktime($hour, $minute, $seconds));
        }
    }
    
    //this takes in an array of data that is passed to the tablehelper
    //and does all necessary logic 
    public function pageSetup($data, $options = array())
    {
        if($data === NULL) {
            $data = array();
        }
        $default_options = array(
            'page_num' => 1,
            'num_per_page' => 100
        );
        $table_name = $options['table_name'];
        
        $options = array_merge($default_options, $options);
        
        $request = $this->getRequest();
        
        //if the url has page num, use that instead of defaults
        if($request->getParam('page_num')){
            $page_num = $request->getParam('page_num');
        } else {
            $page_num = $options['page_num'];
        }
        if($request->getParam('table_name')) {
            $table_name_param = $request->getParam('table_name');
        } else {
            $table_name_param = $table_name;
        }
        
        if($table_name_param == $table_name) {
            if(!isset($this->view->page_num)) {
                $this->view->page_num = array($table_name => $page_num);
            } else {
                $this->view->page_num[$table_name] = $page_num;
            }
        } else {
            if(!isset($this->view->page_num)) {
                $this->view->page_num = array($table_name => $options['page_num']);
            } else {
                $this->view->page_num[$table_name] = $options['page_num'];
            }
        }
            
        //if the url has num per page, use that instead of defaults
        if( $request->getParam('num_per_page') ) {
            $num_per_page = $request->getParam($options['num_per_page']);
        } else {
            $num_per_page = $options['num_per_page'];
        }
        if(!isset($this->view->num_per_page)) {
            $this->view->num_per_page = array($table_name => $num_per_page);
        } else {
            $this->view->num_per_page[$table_name] = $num_per_page;
        }
        
        
        //now calculate additional paging values and pass to view for use by page view helper
        $datum_count = count($data);
        if(!isset($this->view->datum_count)) {
            $this->view->datum_count = array($table_name => $datum_count);
        } else {
            $this->view->datum_count[$table_name] = $datum_count;
        }
            
        //return the array with just the slice determined by above
        return array_slice($data, ($page_num - 1) * $num_per_page, $num_per_page, true);
    }
    
    
    //this function takes in the file name of a csv file and returns an assoc array of
    // rows representing the csv
    protected function processCsv($file_name)
    {
        $handle = fopen($file_name, "r");
        
        //get first row, which contains field names, indexed by location
        $header_row = fgetcsv($handle);

        $return_array = array();
        
        //now go through rest of csv document and create array indexed by field names
        while (($data = fgetcsv($handle)) !== FALSE) {
            $num = count($data);
            $temp_row = array();
            for ($c=0; $c < $num; $c++) {
                $temp_row[$header_row[$c]] = $data[$c];
            }
            $return_array[] = $temp_row;
        }
                
        fclose($handle);
        return $return_array;
    }
    
    //this function takes a default URL as a string
    //if the session variable referrer_url is set, it will redirect to the referrer, 
    //otherwise redirect to the passed value.
    //If redirect is true, the fuction will perform the redirect, otherwise it will 
    //return the URL to be redirected to as a string.
    public function referrerRedirect( $redirect = true, $default_url = '' ) {
    	$session = Zend_Registry::get('session');
    	
    	if ( $redirect == true ) {
	    	if ( isset($session->referrer_url) && $session->referrer_url != '' ) {
	    		$referrer_url = $session->referrer_url;
	    		unset($session->referrer_url);
	    		$this->_redirect($referrer_url);
	    	} else {
	    		$this->_redirect($default_url);
	    	}
    	} else {
    		return $session->referrer_url;
    	}
    }
    
    //takes care of sorting for a controller action's table
    public function sortSetup(
        $options = array(
            'sort_by' => 'sort_by',
            'sort_direction' => 'sort_direction'
        ),
        $defaults = array(
            'sort_by' => NULL,
            'sort_direction' => 'ASC'
        )
    )
    {
        $request = $this->getRequest();
        $sort_options = array();
        
        //checks if the user supplied a column for the
        //table to sort
        if( $request->getParam($options['sort_by']) ) {
            $sort_by = $request->getParam($options['sort_by']);
        } else {
            $sort_by = $defaults['sort_by'];
        }
        $sort_options['sort_by'] = $sort_by;

        //checks if the user supplied a direction for the
        //table to sort
        if( $request->getParam($options['sort_direction']) ) {
            $sort_direction = $request->getParam($options['sort_direction']);
        } else {
            $sort_direction = $defaults['sort_direction'];
        }
        $sort_options['sort_direction'] = $sort_direction;
        
        return $sort_options;
    }
    

}

