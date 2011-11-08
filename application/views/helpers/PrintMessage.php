<?php 
class Zend_View_Helper_PrintMessage
{ 
    function printMessage($messages, $type = 'message', $message_title = '') 
    { 
        $messages = (array)$messages;
    	
    	switch($type){
            case 'error':
                $message_class = 'red';
                break;
            case 'info':
                $message_class = 'white';
                break;
            case 'confirmation':
            	$message_class = 'green';
            	break;
            case 'message':
            default:
                $message_class = 'yellow';
        }
        
	    $output = '';

	    foreach( $messages as $message) {
	        $output .= '<!--[if !IE]>start system messages<![endif]-->' . "\n";
	        $output .= '    <ul class="system_messages">' ."\n";
	        
	        $output .= '        <li class="'. $message_class .'">' . "\n";
	        if(isset($message_title) && $message_title != ''){
	            $output .= '            <strong class="system_title">' . "\n";
	            $output .= '                ' . $message_title . "\n";
	            $output .= '            </strong>' . "\n";
	        }
	        $output .= '            ' . $message . "\n";
	        $output .= '        </li>' . "\n";
	        
	        $output .= '    </ul>' ."\n";
	        $output .= '<!--[if !IE]>end system messages<![endif]-->' . "\n";
	    }
        
        return $output; 
    } 
}

