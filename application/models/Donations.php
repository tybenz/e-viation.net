<?php

class Application_Model_Donations extends Application_Model_DefaultModel
{
    protected $_types;
    protected $_categories;
    protected $_subcategories;
    
    public function __construct()
    {
        parent::__construct();
        $this->_types = new Application_Model_DbTable_DonationsTypes();
    }
    
    public function getAllTypes($options = array())
    {
        $default_options = array(
            'dnt_id_pk' => NULL,
            'dnt_percent' => NULL,
            'dnt_value' => NULL,
            'sort_by' => 'dnt_id_pk',
            'sort_direction' => 'ASC'
        );
        $options = array_merge($default_options, $options);
        
        $select = $this->db->select()
            ->from($this->_types->getTableName())
            ->order($options['sort_by'] . ' ' . $options['sort_direction']);
        
        if(isset($options['dnt_id_pk'])) {
            $select->where('dnt_id_pk = ?', $options['dnt_id_pk']);
        }
        if(isset($options['dnt_percent'])) {
            $select->where('dnt_percent = ?', $options['dnt_percent']);
        }
        if(isset($options['dnt_value'])) {
            $select->where('dnt_value = ?', $options['dnt_value']);
        }
        
        $query = $this->db->query($select);
        $rows = $query->fetchAll();

        foreach($rows as &$r) {
            if($r['dnt_percent']) {
                $r['dnt_formatted_value'] = number_format($r['dnt_value'], 0) . '%';
            } else {
                $r['dnt_formatted_value'] = '$' . $this->format_money($r['dnt_value']);
            }
        }
        
        return $rows;
    }
}
