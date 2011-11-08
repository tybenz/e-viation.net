<?php

class Application_Model_DefaultModel
{
    protected $db; //db adapter
    
    public function __construct()
    {
        //get resource to setup db adapters (default, legacy, ucd)
        $this->db = Zend_Registry::get('db');
    }
    
    protected function format_money($string)
    {
        $price = preg_split("//", $string);
        $counter = -1;
        $formatted_price = '';
        for($i = sizeof($price) - 1; $i >= 1; $i--) {
            if($counter % 3 == 0 && $counter > 0) {
                $formatted_price = ',' . $formatted_price;
            }
            $formatted_price = $price[$i] . $formatted_price;
            $counter++;
        }
        return $formatted_price;
    }
    
    protected function tableFilter($prefix, $data, $removeEmpty = false)
    {
        $filtered_data = array();
        foreach($data as $key => $value){
            //if the prefix is found at the begining of key
            // === checks for equal value AND type (so false does not equal 0)
            if (strpos($key, $prefix) === 0) {
                //if removeEmpty is true but the value does exist
                if (!($removeEmpty && ($value === "" || $value === null))){
                    $filtered_data[$key] = $value;
                }
            }
        }
        return $filtered_data;
    }
    
    /*
    
    public function arrayIndexReplace($data, $newIndex, $ignore = false)
    {
        $new_array = array();
        
        foreach($data as $row) {
            if(isset($new_array[$row[$newIndex]]) && $ignore === false) {
                throw new Exception('arrayIndexReplace: New indices must be unique.');
            } else if( !isset($new_array[$row[$newIndex]]) ) {
                $new_array[$row[$newIndex]] = $row;
            }
        }
        
        return $new_array;
    }
    
	public function editPrintHistory( $options = array() )
    {
    	$default_options = array(
				'prh_print_history_id_pk' => NULL,
    			'prh_date_printed' => NULL,
				'prh_item_printed_id' => NULL,
				'prh_item_printed_type' => NULL,
    			'timestamp' => NULL,
    			'prh_printed_text' => NULL,
    	);
    	$options = array_replace($default_options, $options);
    	
    	if( $options['prh_print_history_id_pk'] == '' ) {
            $print_history_data = $this->tableFilter('prh_', $options, true);
            if ( $options['timestamp'] == NULL ) {
            	$print_history_data['prh_date_printed'] = date('d-M-y h.i.s.u A');
            } else {
            	$print_history_data['prh_date_printed'] = $options['timestamp'];
            }
            
        } else {
            $print_history_data = $this->tableFilter('prh_', $options, false);
        }
        
         $retID = $this->_print_history_table->edit($print_history_data);
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
    		$month = (int)date('n', strtotime($date[1]));
    		$year= (int)($date[2]);
    		if ( $year > 50 ) {
    			$year += 1900;
    		} else {
    			$year += 2000;
    		}
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
    
	//converts oracle's datetime into a string acceptable for the form
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
    
	public function getAllPrintHistory()
	{
		$select = $this->db->select()
    		->from('print_history');
    	
    	$query = $this->db->query($select);
    	$rows = $query->fetchAll();
    	
    	return $rows;
	}
	
	public function getPrintHistory( $options = array() )
	{
		$default_options = array(
				'prh_print_history_id_pk' => NULL,
				'sort_by' => NULL,
				'sort_direction' => NULL,
				'since' => NULL,
				'not_since' => NULL,
				'prh_item_printed_id' => NULL,
				'prh_item_printed_type' => NULL,
    	);
    	$options = array_replace($default_options, $options);
    	
    	if( !array_key_exists('sort_by', $options) ) {
    		$options['sort_by'] = 'prh_print_history_id_pk';
    	}
    	if( !array_key_exists('sort_direction', $options) ) {
    		$options['sort_direction'] = 'DESC';
    	}
    	
    	$select = $this->db->select()
    		->from('print_history')
    		->order( $options['sort_by'] . ' ' . $options['sort_direction'] );
    		
		if( $options['prh_print_history_id_pk'] != NULL ) {
            $select->where('prh_print_history_id_pk = ' . $options['prh_print_history_id_pk'] );
        }
		if( $options['since'] != NULL ) {
            $select->where("prh_date_printed >= '" . $options['since'] . "'" );
        }
		if( $options['not_since'] != NULL ) {
            $select->where("prh_date_printed < '" . $options['not_since'] . "'" );
        }
		if( $options['prh_item_printed_id'] != NULL ) {
            $select->where('prh_item_printed_id = ' . $options['prh_item_printed_id'] );
        }
		if( $options['prh_item_printed_id'] != NULL ) {
            $select->where("prh_item_printed_type = '" . $options['prh_item_printed_id'] . "'" );
        }
        
        $query = $this->db->query($select);
    	$rows = $query->fetchAll();
    	
    	return $rows;
	}
    
    protected function oracleTime($time)
    {
        $datetime = date('d-M-y h.i.s.u A', strtotime('1980-01-01 ' .  $time));
        return $datetime;
    }
    
    protected function oracleDate($date)
    {
    	//When the date string is empty this means the user did not enter a date.
    	//Returning null prevents the date() function from defaulting to 12-31-1969.
    	//This also allows us to test vs. null in our code.
    	if ($date == '' || $date == null) {
    		return null;
    	}
    	
    	else {
    		$oracleDate = date('d-M-y', strtotime($date));
    		return $oracleDate;
    	}
    }
	
    //Sets the default transport for mail sent from the website
    protected function smtpSetup(){
        $site_setting = new Model_SiteSetting();
        $site_settings = $site_setting->getAllSiteSettings();
        foreach ( $site_settings as $ss ) {
        	if ( $ss['sst_setting_name'] == 'sendingSMTP' ) {
        		$smtp = $ss['sst_setting_value'];
        	}
        }
        
        $tr = new Zend_Mail_Transport_Smtp($smtp);
      	//$tr = new Zend_Mail_Transport_Sendmail();
        Zend_Mail::setDefaultTransport($tr);
    }
    
    protected function tableFilter($prefix, $data, $removeEmpty = false)
    {
        $filtered_data = array();
        foreach($data as $key => $value){
            //if the prefix is found at the begining of key
            // === checks for equal value AND type (so false does not equal 0)
            if (strpos($key, $prefix) === 0) {
                //if removeEmpty is true but the value does exist
                if (!($removeEmpty && ($value === "" || $value === null))){
                    $filtered_data[$key] = $value;
                }
            }
        }
        return $filtered_data;
    }
    
    */
}