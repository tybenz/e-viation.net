<?php
require_once('BaseController.php');

class EverythingElseController extends BaseController
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
        $this->view->active_tab = 'flight-gear';
    
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
        
        
        $everything_else_ads = array();
        foreach($this->view->tabs['everything-else'] as $key => $t) {
            if(isset($categorized_ads['everything-else'][$key])) {
                $everything_else_ads[$key] = $categorized_ads['everything-else'][$key];
            } else {
                $everything_else_ads[$key] = array();
            }
            $everything_else_ads[$key] = $this->pageSetup($everything_else_ads[$key], array('table_name' => $key, 'num_per_page' => 5));
        }
        
        $this->view->ads = $everything_else_ads;
    }


}

