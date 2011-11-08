<?php
require_once('BaseController.php');

class PartsController extends BaseController
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        $this->setSessionData();
        $ads = new Application_Model_Ads();
        $params = $this->getRequest()->getParams();
        $this->view->active_tab = 'airframe';
    
        unset($params['controller']);
        unset($params['action']);
        unset($params['module']);
        if(count($params) > 0) {
            if(isset($params['table_name'])) {
                $this->view->active_tab = $params['table_name'];
            }
        }
        
        $all_ads = $ads->getAllAds(array('sort_by' => 'ads_id_pk', 'sort_direction' => 'DESC'));
        
        
        foreach($all_ads as $a) {
            $categorized_ads[$a['adc_category']][$a['asc_subcategory']][] = $a;
        }     
        
        
        $parts_ads = array();
        foreach($this->view->tabs['parts'] as $key => $t) {
            if(isset($categorized_ads['parts'][$key])) {
                $parts_ads[$key] = $categorized_ads['parts'][$key];
            } else {
                $parts_ads[$key] = array();
            }
            $parts_ads[$key] = $this->pageSetup($parts_ads[$key], array('table_name' => $key, 'num_per_page' => 5));
        }
        
        $this->view->ads = $parts_ads;
    }


}

