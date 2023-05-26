<?php
function set_db_connection() {
    $CI = get_instance();
    $controller = $CI->router->class;
    $compDbConf	= $CI->fx->compDbConf;
    $loadConfig = 'default';

    $controller_lists = array('shipper');
    $dbconfig	= array('masterms');
	if(!empty($compDbConf) && in_array($controller, $controller_lists)) {
		foreach($dbconfig as $config_name){
			if($compDbConf == $config_name) {
				$loadConfig = $config_name;
				break;
			}
		}
	}
	
    $CI->db = $CI->load->database($loadConfig, true);
}
?>
