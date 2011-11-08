<?php

class Application_Model_Users extends Application_Model_DefaultModel
{
    protected $_users;
    
    public function __construct()
    {
        parent::__construct();
        $this->_users = new Application_Model_DbTable_Users();
    }
    
    public function addUser($data) 
    {
        foreach($data as &$d) {
            if($d === "") {
                $d = NULL;
            }
        }
        
        $user_data = $this->tableFilter('usr_', $data, true);
        return $this->_users->add($user_data);
    }
    
    public function editAccident($data)
    {	
    	if( $data['acc_accident_id_pk'] == '' ) {
            $accidents_data = $this->tableFilter('acc_', $data, true);
        } else {
            $accidents_data = $this->tableFilter('acc_', $data, false);
        }
        $retID = $this->_accidents_table->edit($accidents_data);
    }
    
    public function getUser($id) 
    {
        return $this->_users->get($id);
    }
    
    public function getAllUsers($options = array()) 
    {
        $default_options = array(
            'usr_id' => NULL,
            'usr_fname' => NULL,
            'usr_lname' => NULL,
            'usr_email' => NULL,
            'usr_password' => NULL,
            'usr_salt' => NULL,
            'sort_by' => 'usr_id',
            'sort_direction' => 'ASC'
        );
        $options = array_merge($default_options, $options);
        
        
        $select = $this->db->select()
            ->from($this->_users->getTableName())
            ->order($options['sort_by'] . ' ' . $options['sort_direction']);
        
        if(isset($options['usr_id'])) {
            $select->where('usr_id = ?', $options['usr_id']);
        }
        if(isset($options['usr_fname'])) {
            $select->where('usr_fname = ?', $options['usr_fname']);
        }
        if(isset($options['usr_lname'])) {
            $select->where('usr_lname = ?', $options['usr_lname']);
        }
        if(isset($options['usr_email'])) {
            $select->where('usr_email = ?', $options['usr_email']);
        }
        if(isset($options['usr_password'])) {
            $select->where('usr_password = ?', $options['usr_password']);
        }
        if(isset($options['usr_salt'])) {
            $select->where('usr_salt = ?', $options['usr_salt']);
        }
        
        $query = $this->db->query($select);
        $rows = $query->fetchAll();

        return $rows;
    }
}

