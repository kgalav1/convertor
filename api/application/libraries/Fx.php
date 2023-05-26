<?php
date_default_timezone_set('Asia/Kolkata');
defined('BASEPATH') OR exit('No direct script access allowed');
class Fx {
	public $userId;
	public $roleId;
	public $userDetails;
	public $userChilds;
	public $userChildsWithParent;
	public $isMasterRole = false;

	public $templateTypeArray = array(
		'sms_for_otp'							=> 'OTP SMS',
		'email_for_otp' 						=> 'OTP Email',
		'email_for_new_lead' 					=> 'Acknowledgement Email for New Lead',
		'email_for_new_client_registration' 	=> 'Acknowledgement Email for New Client Registration',
		'email_for_plan_renewable' 				=> 'Acknowledgement Email for Plan Renewable',
		'email_for_plan_change' 				=> 'Acknowledgement Email for Plan Change',
		'email_before_due_date_for_plan_renewable' => 'Reminder Email for Plan Renewal Before Due Date',
		'email_on_due_date_for_plan_renewable' 	=> 'Reminder Email for Plan Renewal on Due Date'
	);

	public $templateArray = array(
		'common' => array(
			array(
				'{{client_name}}'		=> 'Client Name',
				'{{client_phone}}'		=> 'Mobile No.',
				'{{client_email}}'		=> 'Email Id',
				'{{client_pan}}'		=> 'Client PAN',
				'{{client_gstin}}'		=> 'Client GSTIN',
				'{{client_company}}'	=> 'Client Company',
				'{{client_address}}'	=> 'Client Address',
				'{{prev_plan_name}}'	=> 'Previous Plan Name',
				'{{plan_name}}'			=> 'Plan Name',
				'{{plan_price}}'		=> 'Plan Price',
				'{{plan_start_date}}'	=> 'Plan Start Date',
				'{{plan_valid_date}}'	=> 'Plan Valid UpTo',
				'{{plan_storage_size}}'	=> 'Plan Storage Size',
				'{{plan_backup_period}}'=> 'Plan Backup Period',
				'{{plan_user_count}}'	=> 'Plan Users',
				'{{plan_validity}}'		=> 'Plan Validity',
				'{{sms_otp}}'			=> 'SMS OTP',
				'{{email_otp}}'			=> 'Email OTP',
				'{{payment_id}}'		=> 'Payment Id',
				'{{plan_renew_link}}'	=> 'Plan Renew Link',
			)
		)
	);

	public $notInSessionApi = array('api', 'login', 'logout');
	function __construct() {
		
	}

	static function pr($ar, $ex = 0) {
		echo '<pre>';
		print_r($ar);
		echo '</pre>';
		if ($ex == 1) {
			exit ;
		}
	}

	public function validate() {
		$this->CI = &get_instance();
		$data_1 = json_decode(file_get_contents("php://input"));
		$data_1 = (array)$data_1;
		$data_2 = $this->CI->input->post();
		
		if(!is_array($data_2))
			$data_2 = (array) $data_2;
		$post = array_merge($data_1, $data_2);
		
		$this->apiName			= $this->CI->uri->segment(1);
		$this->apiMethodName	= $this->CI->uri->segment(2);
		
		if(empty($post['token']) && (strtolower($this->apiName) == 'api' && !in_array($this->apiMethodName, $this->notInSessionApi))){
			return false;
		} else if(!in_array($this->apiMethodName, $this->notInSessionApi)){
			$this->CI->load->model('Users_Model');
			$userData = $this->CI->Users_Model->getUserByToken($post['token']);
			
			if(!empty($userData['user_role_id'])) {
				$this->userId = $userData['user_id'];
				$this->roleId = $userData['user_role_id'];
				$this->isMasterRole = empty($userData['parent_user_id'])?true:false;
				if($this->isMasterRole) {
					$childData = $this->CI->Users_Model->getAllUsers();
					$this->userChilds = $childData['all_users'];
					$this->userChildsWithParent = $this->userChilds;
				} else {
					$childData = $this->CI->Users_Model->getChildUsers($this->userId);
					if(!empty($childData)) {
						$this->userChilds = $childData[0]['@chuser'];
						$this->userChildsWithParent = $this->userChilds.','.$this->userId;
					}
				}
				return true;
			} else {
				return false;
			}
		}
	}
	
	public function encrypt_decrypt($action, $string) {
		$output = false;
		$encrypt_method = "AES-256-CBC";
		$secret_key = 'sansoft@1806';
		$secret_iv = 'san13122004';
		$key = hash('sha256', $secret_key);
		// iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
		$iv = substr(hash('sha256', $secret_iv), 0, 16);
		if ($action == 'encrypt') {
			$output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
			$output = base64_encode($output);
		} elseif ($action == 'decrypt') {
			$output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
		}
		return $output;
	}
	
	function generateTokenForPayment($ref_type='', $ref_id='', $order_id=''){
		$str	= "$ref_type-$ref_id-$order_id";
		return $this->encrypt_decrypt('encrypt', $str);
	}
	
	public function setEailSMSTemplate($type = 'common', $templatedbArray, $dataArray, $itemArray = array()) {
		// replace first string and replaced string array ************ Start
		$baseStringKeys = array_keys($this->templateArray[$type][0]);
		foreach ($baseStringKeys as $baseKey) {
			$key = trim(str_replace(array('{{', '}}'), array('', ''), $baseKey));
			$replacvalue[] = (isset($dataArray["$key"])) ? $dataArray["$key"] : '';
		}
		// replace first string and replaced string array ************* ENDS

		$emilSubject = $emilBody = $smsBody = '';
		foreach ($templatedbArray as $temp) {
			if ($temp['temp_type'] == TEMPLATE_TYPE_EMAIL) {
				$emilSubject = str_replace($baseStringKeys, $replacvalue, $temp['subject']);
				$emilBody = str_replace($baseStringKeys, $replacvalue, $temp['template_body']);
			} else if ($temp['temp_type'] == TEMPLATE_TYPE_SMS) {
				$smsBody = str_replace($baseStringKeys, $replacvalue, strip_tags($temp['template_body']));
			}
		}
		return array('emilSubject' => $emilSubject, 'emilBody' => $emilBody, 'smsBody' => $smsBody);
	}

	function runPhpCommand($command) {
		try{
			if (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN') {
				exec('php ' . $command);
			} else {
				exec('php ' . $command . ' >  /dev/null 2>&1 &');
			}
		} catch(Exception $e) {
			echo $e->getMessage();
			return false;
		}
		return true;
	}
}
?>
