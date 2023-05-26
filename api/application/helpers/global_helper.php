<?php

	if (!function_exists('time_ago')) {

		function time_ago($date) { 
			if (empty($date)) {
				return "No date provided";
			}

			$time = date('H', strtotime($date)) != '00' ? '(' . date('h:i A', strtotime($date)) . ')' : '';
			$periods = array("second", "minute", "hour", "day");
			$lengths = array("60", "60", "24", "7");
			$now = time();
			$unix_date = strtotime($date);

			// check validity of date

			if (empty($unix_date)) {
				return "Bad date";
			}

			// is it future date or past date
			if ($now > $unix_date) {
				$difference = $now - $unix_date;
				$tense = "ago";
			} else {
				$difference = $unix_date - $now;
				$tense = "from now";
			}
			for ($j = 0; $difference >= $lengths[$j] && $j < count($lengths) - 1; $j++) {
				$difference /= $lengths[$j];
			}
			$difference = round($difference);
			if ($difference != 1) {
				$periods[$j] .= "s";
			}

			if ($difference >= 60 && $periods[$j] == 'days') {
				return date('d-M-Y H:i A', strtotime($date));
			} else {
				if ($tense != "from now") {
					return "$difference $periods[$j] {$tense} {$time}";
				} else {
					return "Just Now";
				}
			}
		}

	}

	if (!function_exists('site_date')) {

		function site_date($date = false, $if_not = 'Not Define!') {
			if ($date == '0000-00-00') {
				$date = false;
			}
			return $date ? date('d-m-Y', strtotime($date)) : date('d-m-Y');
		}

	}

	if (!function_exists('site_time')) {

		function site_time($time = null, $format = 1) { 
			date_default_timezone_set('Asia/Kolkata');
			if ($format == 1) {
				return $time?date('H:i', strtotime($time)):date('H:i');
			} elseif ($format == 2) {
				return $time?date('H:i A', strtotime($time)):date('H:i');
			}
		}

	}

	if (!function_exists('site_date_time')) {

		function site_date_time($site_date_time = false, $if_not = 'Not Define!') {
			if ($site_date_time == '0000-00-00') {
				$site_date_time = false;
			}
			return $site_date_time ? date('d-m-Y H:i A', strtotime($site_date_time)) : $if_not;
		}

	}

	if (!function_exists('db_date')) {

		function db_date($date = false) {
			return $date ? date('Y-m-d', strtotime($date)) : date('Y-m-d');
		}

	}

	 


	if (!function_exists('db_date_time')) {

		function db_date_time($date_time = false) {
			return $date_time ? date('Y-m-d H:i:s', strtotime($date_time)) : date('Y-m-d H:i:s');
		}

	}

	if (!function_exists('expired')) {

		function expired($date) {
			$date = strtotime($date);
			$curr_date = strtotime(date('d-m-Y'));
			if ($date > $curr_date) {
				return false; // not exp
			} else {
				return true; // exp
			}
		}

	}

	if (!function_exists('pr')) {

		function pr($arr = 'No Data') {
			echo "<pre>";
			print_r($arr);
			echo "</pre>";
		}

	}

	if (!function_exists('prd')) {

		function prd($arr = 'No Data') {
			echo "<pre>";
			print_r($arr);
			echo "</pre>";
			die;
		}

	} 

	if (!function_exists('encrypt')) {

		function encrypt($id) {
			$key = hash('sha256', 'asetrdtcb');
			$iv = substr(hash('sha256', 'jjedgdfvv'), 0, 16);
			$encrypted = openssl_encrypt($id, "AES-256-CBC", $key, 0, $iv);
			return base64_encode($encrypted);
		}

	}

	if (!function_exists('decrypt')) {

		function decrypt($encrypted) {
			$key = hash('sha256', 'asetrdtcb');
			$iv = substr(hash('sha256', 'jjedgdfvv'), 0, 16);
			$id = openssl_decrypt(base64_decode($encrypted), "AES-256-CBC", $key, 0, $iv);
			return $id;
		}

	}

	if (!function_exists('current_user')) { 
		function current_user() {
			$thiss = &get_instance();
			$current_user = $thiss->session->userdata('CLIENT');
			$current_user->id = $current_user->clientId;
			$current_user->email = $current_user->clientEmail;
			$current_user->name = $current_user->clientFirstname." ".$current_user->clientLastname; 
			return $current_user;
		} 
	}  
	
	function isAdmin(){
		$current_user = current_user(); 
		return (trim($current_user->role)=='admin')?true:false;
	}
	function auth(){
		if(!isAdmin()){
			redirect('home');
		}
	}

	if (!function_exists('current_user_plan')) { 
		function current_user_plan() {
			$thiss = &get_instance();
			$current_user =  $thiss->session->userdata('CLIENT');
			return $current_user->planDetails;
		} 
	}  
	if (!function_exists('current_user_company')) { 
		function current_user_company() {
			$thiss = &get_instance();
			$current_user =  $thiss->session->userdata('CLIENT');
			return $current_user->clientComp;
		} 
	}  

	if (!function_exists('read_more')) { 
		function read_more($string, $limit = 20, $dash = true, $read_more = false) {
			$real_conten = $string;
			$string = strip_tags($string);
			if (strlen($string) > $limit) {
				$string = substr($string, 0, $limit);
				// $string = substr($stringCut, 0, strrpos($stringCut, ' '));
				if ($dash) {
					$string .= '...';
				}
				$return_html = html_entity_decode($string);
				if ($read_more) {
					$return_html = $return_html . read_more_poup($real_conten);
				}
			} else {
				$return_html = html_entity_decode($string);
			}
			return $return_html;
		}

	}
 

	if (!function_exists('pagination_formatting')) {

		function pagination_formatting() {
			$CI = &get_instance();
			$CI->page_config['full_tag_open'] = '<div class="pagination pagination-small pagination-right"><ul class="pagination">';
			$CI->page_config['full_tag_close'] = '</ul></div>';
			$CI->page_config['first_link'] = true;
			$CI->page_config['last_link'] = true;
			$CI->page_config['first_tag_open'] = '<li>';
			$CI->page_config['first_tag_close'] = '</li>';
			$CI->page_config['prev_link'] = '&laquo';
			$CI->page_config['prev_tag_open'] = '<li class="prev">';
			$CI->page_config['prev_tag_close'] = '</li>';
			$CI->page_config['next_link'] = '&raquo';
			$CI->page_config['next_tag_open'] = '<li>';
			$CI->page_config['next_tag_close'] = '</li>';
			$CI->page_config['last_tag_open'] = '<li>';
			$CI->page_config['last_tag_close'] = '</li>';
			$CI->page_config['cur_tag_open'] = '<li class="active"><a href="#">';
			$CI->page_config['cur_tag_close'] = '</a></li>';
			$CI->page_config['num_tag_open'] = '<li>';
			$CI->page_config['num_tag_close'] = '</li>';
			return $CI->page_config;
		}

	}

	if (!function_exists('print_flash_message')) {

		function print_flash_message() {
			if (isset($_SESSION['flash_status']) and $_SESSION['flash_status']== 'success') {
				
				echo '<div class="alert alert-dismissable sugg_msg alert-success "><button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button> ';
				echo $_SESSION['flash_message'];
				echo '</div>';
			} elseif (isset($_SESSION['flash_status']) and $_SESSION['flash_status'] == 'error') {
				echo '<div class="alert alert-dismissable sugg_msg alert-danger"><button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>';
				echo $_SESSION['flash_message'];
				echo '</div>';
			}  
			unset($_SESSION['flash_status']);
			unset($_SESSION['flash_message']);
		}

	}


	if (!function_exists('set_flash_message')) { 
		function set_flash_message($type = 'error', $message = 'There is something wrong, Please contact to admin.') {
			
			$thiss = &get_instance();
			$thiss->session->set_flashdata(array('flash_message' => $message, 'flash_status' => $type));
		} 
	}	
	
	function flash_message_html($type = 'error', $message = 'There is something wrong, Please contact to admin.') {
		if ($type== 'success') { 
			$html = '<div class="alert alert-dismissable sugg_msg alert-success "><button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button> '.$message.'</div>';
		} elseif ($type== 'error') {
			$html = '<div class="alert alert-dismissable sugg_msg alert-danger"><button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>'.$message.'</div>';
		} 
		return $html;
	}   
   

	if (!function_exists('user_ip')) {

		function user_ip() {
			$ip = '';
			if (!empty($_SERVER["HTTP_CLIENT_IP"])) {
				//check for ip from share internet
				$ip = $_SERVER["HTTP_CLIENT_IP"];
			} elseif (!empty($_SERVER["HTTP_X_FORWARDED_FOR"])) {
				// Check for the Proxy User
				$ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
			} else {
				$ip = $_SERVER["REMOTE_ADDR"];
			}
			return $ip;
		}

	}     

	if (!function_exists('get_age')){ 
		function get_age($dob){ 
			$today = date('Y-m-d');
			$bday = date('Y-m-d', strtotime($dob));
			$diff = date_diff(date_create($bday), date_create($today));
			$total_days =   $diff->format("%a");  
			
			$total_months = ($total_days/30.5);
			$total_years = $total_months/12;
			return round($total_years,1);
		}
	}
	if (!function_exists('get_months')){
		 function get_months($dob){
			 $today = date('Y-m-d');
			 $bday = date('Y-m-d', strtotime($dob));
			 $diff = date_diff(date_create($bday), date_create($today));
			 $total_days =   $diff->format("%a");
			 
			 $total_months = ($total_days/30.5);
			 
			 return round($total_months,1);
		 }
	 }
   
	if (!function_exists('time_options')){ 
		function time_options($limit=false){  
				$hours = range("0", "9");
				$munits = array("00", "15","30","45");
				foreach($hours as $h){
					foreach($munits as $m){
						$arr['0'.$h.':'.$m] = '0'.$h.':'.$m; 
						if(('0'.$h.':'.$m) == $limit){ 
								goto stop; 
						}
					} 
				}
				stop:
				unset($arr["00:00"]); 
				return $arr;
		}
	}

	if (!function_exists('time_round')){ 
		function time_round($time='00:00',$return = ''){ 
			 if(!strpos($time,':')){
				 return $return;
			 } 
			 list($h,$m) = explode(':',$time);
			 $pointeer = round($m*1.67)/100;
			 $time = $h+$pointeer;
			 return $time;
		}
	}

	if (!function_exists('time_sum')){ 
		function time_sum($times=false) {
			if(!$times){ return "00:00"; }
			$minutes = 0; 
			foreach ($times as $time) {
				$time = explode(':', $time); 
				 $hour  = isset($time[0])?(int)$time[0]:0; 
				 $minute  = isset($time[1])?(int)$time[1]:0;  
				$minutes += $hour * 60;
				$minutes += $minute;
			} 
			$hours = floor($minutes / 60);
			$minutes -= $hours * 60; 
			// returns the time already formatted
			return sprintf('%02d:%02d', $hours, $minutes); 
		}
	}
	
	if (!function_exists('weekOfMonth')){ 
		function weekOfMonth($date) {
			//Get the first day of the month.
			$firstOfMonth = strtotime(date("Y-m-01", strtotime($date)));
			//Apply above formula.
			return intval(date("W", strtotime($date))) - intval(date("W", $firstOfMonth)) + 1;
		}
	}    
 
	  
	if (!function_exists('excel_column')){  
		function excel_column($limit = 27){
			$alphabets = $column_array = range('A','Z');
			if($limit<27){
				return range('A',$alphabets[$limit-1]);
			}
			
			$column_num = count($column_array);
			foreach($alphabets as $alphabet1){
				foreach($alphabets as $alphabet2){
					$column_array[]=$alphabet1.$alphabet2;
					$column_num++;
					if($column_num == $limit){
						break;
					}
				}
				if($column_num == $limit){
					break;
				}
			}
			// return array_combine(range(1, count($column_array)), array_values($column_array));
			return $column_array;
		}
	}
	 
	if (!function_exists('date_range')){  
		function date_range($start, $end, $format = 'd-M') {
			$array = array();
			$interval = new DateInterval('P1D');

			$realEnd = new DateTime($end);
			$realEnd->add($interval);

			$period = new DatePeriod(new DateTime($start), $interval, $realEnd);

			foreach($period as $date) { 
				$array[] = $date->format($format); 
			}

			return $array;
		}
	}
	  
	if (!function_exists('date_diffr')){  
		function date_diffr($start, $end, $is_str=true) {
				$datetime1 = new DateTime($start);
				$datetime2 = new DateTime($end);
				$interval = $datetime1->diff($datetime2);
				$data['days'] = $interval->format('%d');
				$data['month'] = $interval->format('%m');
				$data['years'] = $interval->format('%y');
				$str = $data['days']?$data['days']:'';
				if($data['days']){$str .= $data['days']>1?' Days ':' Day';}
				$str .= $data['month']?$data['month']:'';
				if($data['month']){$str .= $data['month']>1?:' month';}
				$str .= $data['years']?$data['years']:'';
				if($data['years']){$str .= $data['years']>1?' Years ':' year';} 
				return $is_str?$str:$data;
		}
	}
	 
	 
	if (!function_exists('month_options')){  
		function month_options($month) {
			$months = array('01' => 'January', '02' => 'February', '03' => 'March', '04' => 'April', '05' => 'May', '06' => 'June', '07' => 'July', '08' => 'August', '09' => 'September', 10 => 'October', 11 => 'November', 12 => 'December');
			return $months;
		}
	}

	if (!function_exists('month_shortname')){  
		function month_shortname($month) { 
			return date("F", mktime(0, 0, 0,$month, 10));
		}
	}

	if (!function_exists('gender')){  
		function gender() {
			$gender = array('m' => 'Male', 'f' => 'Female');
			return $gender;
		}
	}

	if (!function_exists('martial')){  
		function martial() {
			$martial = array(0 => 'Single', 1 => 'Marrried');
			return $martial;
		}
	}     
 

	if (!function_exists('print_status')) { //javascript href link print 
		function print_status($val) { 
			if ((int)$val ==1) {
				echo '<span class="label label-success">Active</span>';
			} else {
				echo '<span class="label label-warning">Inactive</span>';
			}
		} 
	}  
	
	if (!function_exists('version')){  
		function version() {
			// $version =  '?ver=1.0.1';
			$version =  '?ver=1.0.1'.time();
			return $version;
		}
	}
	
	if (!function_exists('multi_item_update_ids')){  
		function multi_item_update_ids($new,$exist, $base_column='id'){ 
			$data['delete'] = $data['insert'] = $data['update'] = array();			
			$new_ids = array_column($new,$base_column);
			$exist_ids = array_column($exist,$base_column); 
			$update = array_values(array_filter($new_ids));
			$data['delete'] = array_diff($exist_ids,$update); 
			foreach($new as $row){
				//insert
				if(!in_array($row[$base_column],$exist_ids)){ 
					$data['insert'][] = $row;
				} 
				//update
				elseif(in_array($row[$base_column],$update)){
					$data['update'][]=$row;
				}
			}
			return $data; 
		}
	}  
	
	function timeDiff($date1,$type="",$date2=false){
		$date2 = $date2?$date2:date("Y-m-d"); 
		$diff = abs(strtotime($date2) - strtotime($date1));

		$years = floor($diff / (365*60*60*24));
		$months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
		$days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24)); 
		 switch($type) {
			case 's': // seconds
				$difRes = 0;
				break;
			case 'm': // minutes
				$difRes = 0;
				break;
			case 'h'://hours
				$difRes = 0;
				break;
			case 'd': //days
				$difRes = $days+($months*30)+ ($years*12*30);
				break;  
			case 'm': //months
				$difRes = $months+($years*12);
				break;  
			case 'y': //years
				$difRes = $years;
				break;      
			default:
				if($years){
					$yTitle = $years>1?'years':'year';
					$mTitle = $months>1?'months':'month';
					$difRes = $years." $yTitle ".$months." $mTitle";
				}elseif($months){ 
					$mTitle = $months>1?'months':'month';
					$difRes = $months." $mTitle";
				}else{ 
					$dTitle = $days>1?'days':'day';
					$difRes = $days." $dTitle";
				}
				break;
		 }

		return $difRes;
	}
	
	function tableNextId($table, $id='id'){ 
		$thiss = &get_instance();
		$return = $thiss->db->select("max($id)+1 as nextId")->get($table)->row()->nextId;  
		return $return?$return:1; 
	}
	function numberTowords($num=1){ 
		$ones = array(
		0 =>"ZERO",
		1 => "ONE",
		2 => "TWO",
		3 => "THREE",
		4 => "FOUR",
		5 => "FIVE",
		6 => "SIX",
		7 => "SEVEN",
		8 => "EIGHT",
		9 => "NINE",
		10 => "TEN",
		11 => "ELEVEN",
		12 => "TWELVE",
		13 => "THIRTEEN",
		14 => "FOURTEEN",
		15 => "FIFTEEN",
		16 => "SIXTEEN",
		17 => "SEVENTEEN",
		18 => "EIGHTEEN",
		19 => "NINETEEN",
		"014" => "FOURTEEN"
		);
		$tens = array( 
		0 => "ZERO",
		1 => "TEN",
		2 => "TWENTY",
		3 => "THIRTY", 
		4 => "FORTY", 
		5 => "FIFTY", 
		6 => "SIXTY", 
		7 => "SEVENTY", 
		8 => "EIGHTY", 
		9 => "NINETY" 
		); 
		$hundreds = array( 
		"HUNDRED", 
		"THOUSAND", 
		"MILLION", 
		"BILLION", 
		"TRILLION", 
		"QUARDRILLION" 
		); /*limit t quadrillion */
		$num = number_format($num,2,".",","); 
		$num_arr = explode(".",$num); 
		$wholenum = $num_arr[0]; 
		$decnum = $num_arr[1]; 
		$whole_arr = array_reverse(explode(",",$wholenum)); 
		krsort($whole_arr,1); 
		$rettxt = ""; 
		foreach($whole_arr as $key => $i){
			
		while(substr($i,0,1)=="0")
				$i=substr($i,1,5);
		if($i < 20){ 
		/* echo "getting:".$i; */
		$rettxt .= $ones[$i]; 
		}elseif($i < 100){ 
		if(substr($i,0,1)!="0")  $rettxt .= $tens[substr($i,0,1)]; 
		if(substr($i,1,1)!="0") $rettxt .= " ".$ones[substr($i,1,1)]; 
		}else{ 
		if(substr($i,0,1)!="0") $rettxt .= $ones[substr($i,0,1)]." ".$hundreds[0]; 
		if(substr($i,1,1)!="0")$rettxt .= " ".$tens[substr($i,1,1)]; 
		if(substr($i,2,1)!="0")$rettxt .= " ".$ones[substr($i,2,1)]; 
		} 
		if($key > 0){ 
		$rettxt .= " ".$hundreds[$key]." "; 
		}
		} 
		if($decnum > 0){
		$rettxt .= " and ";
		if($decnum < 20){
		$rettxt .= $ones[$decnum];
		}elseif($decnum < 100){
		$rettxt .= $tens[substr($decnum,0,1)];
		$rettxt .= " ".$ones[substr($decnum,1,1)];
		}
		}
		return $rettxt;
		} 

