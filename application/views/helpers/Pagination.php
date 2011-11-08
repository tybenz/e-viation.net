<?php 
class Zend_View_Helper_Pagination
{
    public $view;

	public function setView(Zend_View_Interface $view)
	{
		$this->view = $view;
	} 
	function pagination($options = array()) {
	    
	    $default_options = array(
	        'data_name' => 'Results',
	        'page_button_range' => 10
	    );
	    $options = array_merge($default_options, $options);
	    
        
	    $data_name = $options['data_name'];
        $table_name = $options['table_name'];
	    $page_num = $this->view->page_num[$table_name];
	    $prev_page_num = $page_num - 1;
	    $next_page_num = $page_num + 1;
	    
        
	    $num_per_page = $this->view->num_per_page[$table_name];
	    $datum_count = $this->view->datum_count[$table_name];
        $page_count = ceil($datum_count / $num_per_page);
	    $datum_start_num = ($page_num - 1) * $num_per_page + 1;
	    $datum_end_num = $datum_start_num + $num_per_page - 1;
	    
	    //if there is no data to display, dont show anything
	    if ($datum_count < 1){
	        return '';
        }
	    
	    if($datum_end_num > $datum_count){
	        $datum_end_num = $datum_count;
        }

        //create the urls used within
        $prev_page_url = $this->view->url(array('table_name' => $table_name, 'page_num' => $prev_page_num));
        $next_page_url = $this->view->url(array('table_name' => $table_name, 'page_num' => $next_page_num));
	    $first_page_url = $this->view->url(array('table_name' => $table_name, 'page_num' => 1));
	    $last_page_url = $this->view->url(array('table_name' => $table_name, 'page_num' => $page_count));
    
        //determine which pages to have buttons for
        $page_button_range = $options['page_button_range']; //number of buttons shown surrounding current page
        $page_button_start = $page_num - floor($page_button_range / 2);
	    if($page_button_start < 1){
	        $page_button_start = 1;
        }
        $page_button_end = $page_button_start + $page_button_range;
        if($page_button_end > $page_count){
	        $page_button_end = $page_count;
	        $page_button_start = ($page_button_end - $page_button_range > 1) ? $page_button_end - $page_button_range : 1;
        }
        
        $output = '';
        $output .= '<div class="page"><ul class="number-wrapper">';
        
        
        if($page_num > 1){
            //$output .= '     		<li><a href="' . $first_page_url . '" class="button pag_ends first"><span><span>First</span></span></a> </li>' . "\n";
            $output .= '     		<li><a href="' . $prev_page_num . '" ><</a> </li>' . "\n";
        }
        
        if($page_button_start > 1){
            $output .= '     		<li><a href="' . $first_page_url . '">1</a></li>' . "\n";
        }
        if($page_button_start > 2){
            $output .= '     		...' . "\n";
        }
        
        for($p = $page_button_start; $p <= $page_button_end; $p++){
            $temp_page_button_url = $this->view->url(array('table_name' => $table_name, 'page_num' => $p));
            $temp_page_button_class = '';
            if($p == $page_num){
                $temp_page_button_class = 'active';
            }
            $output .= '     		<li><a href="' . $temp_page_button_url . '" class="' . $temp_page_button_class . '">';
            $output .= $p . '</a></li>' . "\n";
        }
        
        
        if($page_button_end < $page_count - 1){
            $output .= '     		...' . "\n";
        }
        if($page_button_end < $page_count){
            $output .= '     		<li><a href="' . $last_page_url . '">' . $page_count . '</a></li>' . "\n";
        }
        if($page_num < $page_count){
            $output .= '     		<li><a href="' . $next_page_url . '">></a></li>' . "\n";
            //$output .= '     		<li><a href="' . $last_page_url . '" class="button pag_ends last"><span><span>Last</span></span></a> </li>' . "\n";
        }
        $output .= '     	</ul></div>';
        
        return $output;
	}

}