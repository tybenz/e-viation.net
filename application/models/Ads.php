<?php

class Application_Model_Ads extends Application_Model_DefaultModel
{
    protected $_ads;
    protected $_categories;
    protected $_subcategories;
    protected $_donations_types;
    protected $_types;
    
    public function __construct()
    {
        parent::__construct();
        $this->_ads = new Application_Model_DbTable_Ads();
        $this->_categories = new Application_Model_DbTable_AdsCategories();
        $this->_subcategories = new Application_Model_DbTable_AdsSubcategories();
        $this->_donations_types = new Application_Model_DbTable_DonationsTypes();
        $this->_types = new Application_Model_DbTable_AdsTypes();
    }
    
    public function addAd($data) 
    {
        foreach($data as $key => $d) {
            if($d === "") {
                unset($data[$key]);
            }
        }
        
        $ads_data = $this->tableFilter('ads_', $data, true);
        return $this->_ads->add($ads_data);
    }
    
    public function getAllAds($options = array()) 
    {
        $default_options = array(
            'ads_id_pk' => NULL,
            'ads_user' => NULL,
            'ads_title' => NULL,
            'ads_description' => NULL,
            'ads_image1' => NULL,
            'ads_image2' => NULL,
            'ads_image3' => NULL,
            'ads_image4' => NULL,
            'ads_image5' => NULL,
            'ads_price' => NULL,
            'ads_donation' => NULL,
            'ads_category' => NULL,
            'sort_by' => 'asc_id_pk',
            'sort_direction' => 'ASC'
        );
        $options = array_merge($default_options, $options);
        
        
        $select = $this->db->select()
            ->from($this->_ads->getTableName())
            ->joinLeft($this->_subcategories->getTableName(), 'ads_subcategory_id = asc_id_pk')
            ->joinLeft($this->_categories->getTableName(), 'asc_category_id = adc_id_pk')
            ->joinLeft($this->_donations_types->getTableName(), 'ads_donations_type_id = dnt_id_pk')
            ->joinLeft($this->_types->getTableName(), 'ads_type = adt_id_pk')
            ->order($options['sort_by'] . ' ' . $options['sort_direction']);
        
        
        if(isset($options['ads_id_pk'])) {
            $select->where('ads_id_pk = ?', $options['ads_id_pk']);
        }
        if(isset($options['ads_user'])) {
            $select->where('ads_user = ?', $options['ads_user']);
        }
        if(isset($options['ads_title'])) {
            $select->where('ads_title = ?', $options['ads_title']);
        }
        if(isset($options['ads_description'])) {
            $select->where('ads_description = ?', $options['ads_description']);
        }
        if(isset($options['ads_image1'])) {
            $select->where('ads_image1 = ?', $options['ads_image1']);
        }
        if(isset($options['ads_image2'])) {
            $select->where('ads_image2 = ?', $options['ads_image2']);
        }
        if(isset($options['ads_image3'])) {
            $select->where('ads_image3 = ?', $options['ads_image3']);
        }
        if(isset($options['ads_image4'])) {
            $select->where('ads_image4 = ?', $options['ads_image4']);
        }
        if(isset($options['ads_image5'])) {
            $select->where('ads_image5 = ?', $options['ads_image5']);
        }
        if(isset($options['ads_price'])) {
            $select->where('ads_price = ?', $options['ads_price']);
        }
        if(isset($options['ads_donation'])) {
            $select->where('ads_donation = ?', $options['ads_donation']);
        }
        if(isset($options['ads_category'])) {
            $select->where('ads_category = ?', $options['ads_category']);
        }
        
        
        $query = $this->db->query($select);
        $rows = $query->fetchAll();
        
        foreach($rows as &$r) {
            if($r['dnt_percent']) {
                $r['dnt_formatted_value'] = number_format($r['dnt_value'], 0) . '%';
            } else {
                $r['dnt_formatted_value'] = '$' . $this->format_money($r['dnt_value']);
            }
            $r['ads_price'] = $this->format_money($r['ads_price']);
        }
        
        return $rows;
    }
    
    
    
    public function getAllCategories($options = array())
    {
        $default_options = array(
            'adc_id_pk' => NULL,
            'adc_category' => NULL,
            'sort_by' => 'adc_id_pk',
            'sort_direction' => 'ASC'
        );
        $options = array_merge($default_options, $options);
        
        
        $select = $this->db->select()
            ->from($this->_categories->getTableName())
            ->order($options['sort_by'] . ' ' . $options['sort_direction']);
        
        if(isset($options['adc_id_pk'])) {
            $select->where('adc_id_pk = ?', $options['adc_id_pk']);
        }
        if(isset($options['adc_category'])) {
            $select->where('adc_category = ?', $options['adc_category']);
        }
        
        $query = $this->db->query($select);
        $rows = $query->fetchAll();

        return $rows;
    }
    
    public function getAllSubcategories($options = array())
    {
        $default_options = array(
            'asc_id_pk' => NULL,
            'asc_subcategory' => NULL,
            'asc_category_id' => NULL,
            'sort_by' => 'asc_id_pk',
            'sort_direction' => 'ASC'
        );
        $options = array_merge($default_options, $options);
        
        
        $select = $this->db->select()
            ->from($this->_subcategories->getTableName())
            ->order($options['sort_by'] . ' ' . $options['sort_direction']);
        
        if(isset($options['asc_id_pk'])) {
            $select->where('asc_id_pk = ?', $options['asc_id_pk']);
        }
        if(isset($options['asc_subcategory'])) {
            $select->where('asc_subcategory = ?', $options['asc_subcategory']);
        }
        if(isset($options['asc_category_id'])) {
            $select->where('asc_category_id = ?', $options['asc_category_id']);
        }
        
        $query = $this->db->query($select);
        $rows = $query->fetchAll();

        return $rows;
    }
    
}