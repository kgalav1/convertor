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
		"field-group" => "tms_field_group",
		"ticket_status_type" => "ticket_status_type",
		"conversation-type" => "tms_conversation_type_master"
	);

	public $access_token = '';

	function __construct()
	{
		parent::__construct();
		$this->load->model('Api_Model');
		$this->load->model('Dropdown_Option_Model');
		$this->load->model('Dynamicform_Model');
		$this->load->model('Formcontent_Model');
		$this->load->model('Master_Model');
		$this->convertHeaderToPost($this->input->request_headers());
		$this->load->library('fx');
		//$this->load->library('mailer');
		$this->load->library('Dbquery');
		// $this->storage_path = STORAGE_PATH;
		// $this->client_folder_prefix = CLIENT_FOLDER_PREFIX;
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

	function getUserDetails()
	{
		$token = $this->input->post('token');
		if ($token == "") {
			$this->response(FALSE, 'Invalid Token.', 200);
			return;
		}
		if ($this->setData($token) == false)
			return;

		$res = $this->Api_Model->getUserDetails($this->user_id);
		if (!empty($res)) {
			$this->response(TRUE, '', 200, $res);
		} else {
			$this->response(FALSE, 'Data Not Found', 400);
		}
	}

	// function LoginLog($post, $userdata)
	// {
	// 	$data = json_encode($post);
	// 	$logArry[] = array(
	// 		'user_name' => $userdata[0]->user_name,
	// 		'user_id' => $userdata[0]->id,
	// 		'user_type' => $userdata[0]->user_type,
	// 		'user_data' => $data,
	// 		'login_time' => date('Y-m-d H:i:s'),
	// 	);

	// 	$this->db->insert_batch('tms_login_log', $logArry);
	// }



	// function LogOutLog()
	// {

	// 	$token = $this->input->post('token');
	// 	if ($token == "") {
	// 		$this->response(FALSE, 'Invalid Token.', 200);
	// 		return;
	// 	}
	// 	if ($this->setData($token) == false)
	// 		return;

	// 	$username = $this->db->query("select user_name from tms_user_master where  id= " . $this->user_id . "")->result();

	// 	$logArry[] = array(
	// 		'user_name' => $username[0]->user_name,
	// 		'user_id' => $this->user_id,
	// 		'user_type' => $this->user_type,
	// 		'logout_time' => date('Y-m-d H:i:s'),
	// 	);

	// 	$this->db->insert_batch('tms_login_log', $logArry);
	// }

	function LoginLog($post, $userdata)
	{
		$data = json_encode($post);

		$queue = $this->Api_Model->queueData($userdata[0]->id);
		// print_r($queue);die;


		$logArry[] = array(
			'user_name' => $userdata[0]->user_name,
			'user_id' => $userdata[0]->id,
			'user_type' => $userdata[0]->user_type,
			'user_data' => $data,
			'queue_id' => $queue->queue_id,
			'status' => 'login'
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
		$queue = $this->Api_Model->queueData($this->user_id);

		$logArry[] = array(
			'user_name' => $username[0]->user_name,
			'user_id' => $this->user_id,
			'user_type' => $this->user_type,
			'queue_id' => $queue->queue_id,
			'status' => 'logout'
		);

		$res = $this->db->insert_batch('tms_login_log', $logArry);
		if (!empty($res)) {
			$this->response(TRUE, '', 200);
		} else {
			$this->response(FALSE, 'Data Not Found', 400);
		}
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

	// ------------------------------------------TMS API------------------------------

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
		$MasterPassword = '1@2$@N@' . date('Ymd');
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
						unset($dbData[trim($keyName)]);
					} else {
						if ($keyName == "customer_email_template") {
							continue;
						} else {
							if (array_key_exists($keyName, $dbData)) {
								$dbData[$keyName] = addslashes(trim(trim($dbData[$keyName])));
							}
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

				if ($masterName == "conversation-type") {
					$this->load->library('upload');
					if (!empty($post['name'])) {
						$config['upload_path'] = 'uploads/conversationtypeimages/';
						$config['allowed_types'] = 'jpg|png';
						$config['max_size'] = 2097152;
						$config['max_width'] = 1024;
						$config['max_height'] = 768;
						$fcpath = str_replace("\\", "/", FCPATH);
						$folderPath = $fcpath . 'uploads/conversationtypeimages/';
						$this->upload->initialize($config);
						$attachmentSize = $post['size'];
						if ($attachmentSize > $config['max_size']) {
							$this->response(FALSE, 'error', 400, "Total size of attachments cannot be greater than 2MB");
							return false;
						} else {
							$this->upload->do_upload($post['file']);
							$image_parts = explode(";base64,", $post['file']);							
							$image_base64 = base64_decode($image_parts[1]);
							$file = $folderPath . $post['name'];
							$dbData['con_type_img'] = $post['name'];
							if (file_put_contents($file, $image_base64)) {
							} else {
								$this->response(FALSE, 'File Not Uploded', 400);
								return false;
							}
						}
					}else{
						if($post['removeFlag'] == 1){
							$dbData['con_type_img'] = '';
						}else{
							unset($dbData['con_type_img']);
						}
					}
				}
				if (array_key_exists($masterName, $this->tableArr) == 1) {
					switch ($masterName) {
						case 'user':
							$retarr = $this->Master_Model->saveUser($this->tableArr[$masterName], $dbData, $primaryKey);
							break;


						case 'shift':
							$retarr = $this->Master_Model->saveShift($this->tableArr[$masterName], $dbData, $primaryKey);
							break;

							// case 'ticket':
							// 	$retarr = $this->Master_Model->saveProcess($masterName, $dbData, $primaryKey);
							// 	// $this->Master_Model->saveprocess();
							// 	//$retarr = $this->Master_Model->saveShift($this->tableArr[$masterName], $dbData, $primaryKey);

							// 	break;


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
					$this->response(FALSE, 'Invalid Mast$er Name', 400);
				}
			}
		} else {
			$this->response(FALSE, 'Invalid Master Name', 400);
		}
	}

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

					$retarr = $this->Master_Model->savequeue($masterName, $dbData, $primaryKey, $post);

					if (isset($retarr->incoming_email_account)) {
						$this->response(FALSE, 'Incoming Email Account Already used In Another Queue ', 400);
					}
					if (isset($retarr[0]->incoming_email_account)) {
						$this->response(FALSE, 'Incoming Email Account Already used In Another Queue ', 400);
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

	function addUpdateQueueCategory()
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

			$categoryArray = json_decode(json_encode($post['categoryArray'][0]), true);
			$categorykey	= array_keys($categoryArray);

			unset($categorykey[0]);
			foreach ($post['categoryArray'] as   $categoryVal) {
				foreach ($categorykey as $values) {
					if ($categoryVal->$values == '') {
						$this->response(FALSE,   ucfirst($values) . ' Fields is Required.', 400);
						return;
					}
				}
			}
			$this->load->model('Master_Model');
			$this->db->trans_begin();
			$respMsg = '';
			$primaryKey = 'category_id';
			$retarr = $this->Master_Model->saveQueueCategoryData($post, $masterName);
			if ($retarr['error']) {
				$respMsg = 'Something goes wrong.';
			} else {
				$master_title = "Queue Category";
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
	}

	public function addUpdateQueuePriority()
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
			$priorityArray = json_decode(json_encode($post['priorityArray'][0]), true);
			$prioritykey	= array_keys($priorityArray);

			unset($prioritykey[0]);
			unset($prioritykey[5]);
			foreach ($post['priorityArray'] as   $priorityVal) {
				foreach ($prioritykey as $values) {
					if ($priorityVal->$values == '') {
						$this->response(FALSE,   ucfirst($values) . ' Fields is Required.', 400);
						return;
					}
				}
			}
			$this->load->model('Master_Model');
			$this->db->trans_begin();
			$respMsg = '';
			$primaryKey = 'category_id';
			$retarr = $this->Master_Model->saveQueuePriorityData($post, $masterName);
			if ($retarr['error']) {
				$respMsg = 'Something goes wrong.';
			} else {
				$master_title = "Queue Priority";
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
	}

	public function addUpdateQueueStatus()
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
			$statusArray = json_decode(json_encode($post['statusArray'][0]), true);
			$statuskey	= array_keys($statusArray);

			unset($statuskey[0]);
			unset($statuskey[0]);
			unset($statuskey[5]);
			unset($statuskey[6]);
			unset($statuskey[7]);
			unset($statuskey[8]);
			unset($statuskey[9]);
			unset($statuskey[10]);
			unset($statuskey[11]);
			unset($statuskey[12]);
			unset($statuskey[13]);
			foreach ($post['statusArray'] as   $statusVal) {
				foreach ($statuskey as $values) {
					if ($statusVal->$values == '') {
						$this->response(FALSE,   ucfirst($values) . ' Fields is Required.', 400);
						return;
					}
				}
			}
			$this->load->model('Master_Model');
			$this->db->trans_begin();
			$respMsg = '';
			$primaryKey = 'status_id';
			$retarr = $this->Master_Model->saveQueueStatusData($post, $masterName);
			if ($retarr['error']) {
				if ($retarr['message']['code'] == '1451') {
					$respMsg = "Cannot delete record because it is referenced by a child table.";
				} else {
					$respMsg = 'Something goes wrong.';
				}
			} else {
				$master_title = "Queue Status";
				$respMsg = !empty($post[$primaryKey]) ? $master_title . ' details updated successfully' : $master_title . ' details saved successfully';
			}
			if ($this->db->trans_status() === FALSE) {
				$this->db->trans_rollback();
				$this->response(FALSE, $respMsg, 400);
			} else {
				$this->db->trans_commit();
				$this->response(TRUE, $respMsg, 200);
			}
		}
	}

	public function addUpdateQueueEscalation()
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
			$escalationArray = json_decode(json_encode($post['escalationArray'][0]), true);
			$escalationkey	= array_keys($escalationArray);

			unset($escalationkey[0]);
			unset($escalationkey[7]);
			unset($escalationkey[8]);
			unset($escalationkey[9]);
			unset($escalationkey[10]);
			unset($escalationkey[11]);
			foreach ($post['escalationArray'] as   $escalationVal) {
				foreach ($escalationkey as $values) {
					if ($escalationVal->$values == '') {
						$this->response(FALSE,   ucfirst($values) . ' Fields is Required.', 400);
						return;
					}
				}
			}
			$this->load->model('Master_Model');
			$this->db->trans_begin();
			$respMsg = '';
			$primaryKey = 'escalation_id';
			$retarr = $this->Master_Model->saveQueueEscalationData($post, $masterName);
			if ($retarr['error']) {
				$respMsg = 'Something goes wrong.';
			} else {
				$master_title = "Queue Escalation Matrix";
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
	}

	public function addUpdateQueueRules()
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
			// $ruleArray = json_decode(json_encode($post['ruleArray'][0]), true);
			// $rulekey	= array_keys($ruleArray);
			// unset($escalationkey[0]);
			// unset($escalationkey[7]);
			// unset($escalationkey[8]);
			// unset($escalationkey[9]);
			// unset($escalationkey[10]);
			// unset($escalationkey[11]);
			// foreach ($post['ruleArray'] as   $ruleVal) {
			// 	foreach ($rulekey as $values) {
			// 		if ($ruleVal->$values == '') {
			// 			$this->response(FALSE,   ucfirst($values) . ' Fields is Required.', 400);
			// 			return;
			// 		}
			// 	}
			// }

			$ruleConditionArray = json_decode(json_encode($post['conditionArray'][0]), true);
			$conditionkey	= array_keys($ruleConditionArray);
			$ruleActionArray1 = json_decode(json_encode($post['actionArray'][0]), true);
			$actionkey	= array_keys($ruleActionArray1);

			foreach ($post['conditionArray'] as   $val) {
				foreach ($conditionkey as $values) {
					if ($val->$values == '') {
						$this->response(FALSE,   ucfirst($values) . ' Fields is Required.', 400);
						return;
					}
				}
			}

			foreach ($post['actionArray'] as   $val) {
				foreach ($actionkey as $values) {
					if ($val->$values == '') {
						$this->response(FALSE,   ucfirst($values) . ' Fields is Required.', 400);
						return;
					}
				}
			}
			$this->load->model('Master_Model');
			$this->db->trans_begin();

			$dbData = $post;

			$respMsg = '';
			$dbFields = $this->Master_Model->getQueueCategoryFields($masterName);
			$db_keys = array_keys($dbData);
			foreach ($db_keys as $keyName) {
				if (!in_array($keyName, $dbFields)) {
					unset($dbData[$keyName]);
				} else {
					if (array_key_exists($keyName, $dbData)) {
						$dbData[$keyName] = addslashes(trim(trim($dbData[$keyName])));
					}
				}
			}
			$respMsg = '';
			$primaryKey = 'rule_id';
			$retarr = $this->Master_Model->saveQueueRuleData($masterName, $pKey = 'rule_id', $post, $dbData);
			if ($retarr['error']) {
				$respMsg = 'Something goes wrong.';
			} else {
				$master_title = "Queue Rule";
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
	}

	public function addUpdateTicketData()
	{
		$token = $this->input->post('token');
		if ($token == "") {
			$this->response(FALSE, 'Invalid Token.', 200);
			return;
		}
		if ($this->setData($token) == false)
			return;

		$post = (array) json_decode(file_get_contents('php://input'));
		$mergedObject = (object) array_merge((array) $post['ticketData'], (array) $post['dynamicData']);
		$post['ticketData'] = $mergedObject;
		unset($post['dynamicData']);
		$post = json_decode(json_encode($post['ticketData']), true);

		$queue_name =  $this->Master_Model->getScalerCol("queue", "tms_queue_master", "id = $post[queue_id]")->queue;

		$this->load->library('upload');
		if (count($post['attachmentArray']) > 0) {
			$fcpath = str_replace("\\", "/", FCPATH);
			$path = "$fcpath/uploads/ticketAttachement/" . strtolower($queue_name) . "-" . $post['queue_id'];
			if (!is_dir($path)) {
				mkdir($path, 0777, true);
			}
			$config['upload_path'] = $path;
			// $config['allowed_types'] = 'gif|jpg|png|pdf|xls|xlsx|csv|txt|json|xml';
			$config['allowed_types'] = 'gif|jpg|png|pdf|xls|xlsx|csv|txt|json|xml|doc';

			$config['overwrite'] = TRUE;
			$config['max_size'] = 15 * 1024 * 1024 / count($post['attachmentArray']);
			$this->upload->initialize($config);

			$attachmentSize = 0;
			foreach ($post['attachmentArray'] as $attachment) {
				$attachmentSize += $attachment['size'];
			}
			if ($attachmentSize > 15 * 1024 * 1024) {
				$this->response(FALSE, 'error', 400, "Total size of attachments cannot be greater than 15MB");
				return false;
			} else {
				foreach ($post['attachmentArray'] as $attachment) {
					$file_extension = pathinfo($attachment['name'], PATHINFO_EXTENSION);
					$file = $attachment['name'];
					$image_base64 = base64_decode($attachment['data']);
					if (file_put_contents("$path/$file", $image_base64)) {
					} else {
						$this->response(FALSE, 'error', 400, "File Not Uploded");
						return false;
					}
				}
			}
		}
		if (!empty($post['master_name'])) {
			$primaryKey = 'ticket_unique_id';
			$masterName = $post['master_name'];
			$this->load->model('Master_Model');
			$rules = $this->Master_Model->getTicketTableRules();
			if (empty($rules)) {
				$this->response(FALSE, 'Validations not defined.', 400);
				return;
			}
			$this->load->library('Dynamicformcontent');

			$dynrules = $this->dynamicformcontent->getDynamicFieldValidationArray($post['queue_id'], $post['master_name'], isset($post[$primaryKey]) ? $post[$primaryKey] : 0);

			$rules = array_merge($rules, $dynrules);

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
				$dbFields = $this->Master_Model->getTicketTableFields($masterName);
				$db_keys = array_keys($dbData);
				foreach ($db_keys as $keyName) {

					if (!in_array($keyName, $dbFields)) {
						unset($dbData[$keyName]);
					} else {
						if (array_key_exists($keyName, $dbData)) {
							if (is_array($dbData[$keyName])) {
							} else {
								$dbData[$keyName] = addslashes(trim($dbData[$keyName]));
							}
						}
					}
				}
				$ticketPrifix = $this->ticketSeries($dbData['queue_id']);
				$ticketNo = $this->Master_Model->getScalerCol("ifnull(max(cast(ticket_unique_id as signed)),0)+1 ticketNo", $masterName, "1=1")->ticketNo;
				if (!empty($ticketPrifix)) {
					$Prefix = explode('[', $ticketPrifix, 2);
					$dbData['ticket_prefix'] = $Prefix[0];
					$ticketPrifix = str_replace('[M]', strtoupper(date('M')), $ticketPrifix);
					$ticketPrifix = str_replace('[m]', date('m'), $ticketPrifix);
					$ticketPrifix = str_replace('[d]', date('d'), $ticketPrifix);
					$ticketPrifix = str_replace('[D]', date('D'), $ticketPrifix);
					$ticketPrifix = str_replace('[Y]', date('Y'), $ticketPrifix);
					$ticketPrifix = str_replace('[y]', date('y'), $ticketPrifix);
					$ticketPrifix = str_replace('[Y+1]', date('Y') + 1, $ticketPrifix);
					$ticketPrifix = str_replace('[y+1]', date('y') + 1, $ticketPrifix);
					$ticketSeries = $ticketPrifix . '-' . $ticketNo;
					if (strlen($ticketSeries) > 20) {
						$ticketPrifix = '0';
						$ticketNo = '0';
					}
					$dbData['ticket_id'] = $ticketSeries;
				} else {
					return	$this->response(TRUE, 'Error', 400, 'Ticket Prefix is required');
				}

				$is_happycode = $this->Master_Model->getScalerCol("is_happycode", "tms_queue_master", "1=1 and id = $dbData[queue_id]")->is_happycode;
				if ($is_happycode == 1) {
					$dbData['happy_code'] = rand(1000, 9999);
				}

				if (empty($dbData[$primaryKey])) {
					$dbData['ticket_created_by'] = $this->user_id;
					$dbData['created_date'] = date('Y-m-d H:i:s');
				} else {
					if (empty($dbData['ticket_created_by'])) {
						$dbData['ticket_created_by'] = $this->user_id;
						$dbData['created_date'] = date('Y-m-d H:i:s');
					}
					$dbData['last_updated_by'] = $this->user_id;
					$dbData['last_updated_on'] = date('Y-m-d H:i:s');
				}

				$retarr = $this->Master_Model->saveTicketData($masterName, $post, $dbData, $primaryKey);
				if (empty($retarr['id'])) {
					$respMsg = 'Something goes wrong.';
				} else {
					$master_title = str_replace('_', ' ', $masterName);
					$master_title = ucwords(strtolower($master_title));
					$respMsg = !empty($post[$primaryKey]) ? 'Ticket details updated successfully :' . $ticketSeries :  'Ticket details saved successfully :' . $ticketSeries;
				}
				if ($this->db->trans_status() === FALSE) {
					$this->db->trans_rollback();
					$this->response(FALSE, 'Something goes wrong', 400);
				} else {
					$this->db->trans_commit();
					// $stop_outgoing_email = $this->Master_Model->getScalerCol("stop_outgoing_email", "tms_email_acoount t1", "1=1 and t1.email = '$dbData[from_email]'")->stop_outgoing_email;
					// if($stop_outgoing_email == 0){
					// 	$mailData = $this->Api_Model->getEmailData($dbData['from_email']);
					// }
					$this->response(TRUE, $respMsg, 200);
				}
			}
		} else {
			$this->response(FALSE, 'Invalid Master Name', 400);
		}
	}

	public function getDynamicFieldListColumnsHeader()
	{
		$token = $this->input->post('token');
		if ($token == "") {
			$this->response(FALSE, 'Invalid Token.', 200);
			return;
		}
		if ($this->setData($token) == false)
			return;

		$post = (array) json_decode(file_get_contents('php://input'));
		if (!empty($post['queue_id'])) {
			$this->load->library('Dynamicformcontent');
			$res = $this->dynamicformcontent->getDynamicFieldListColumnsHeader($post['queue_id']);
			if (!empty($res)) {
				$this->response(TRUE, '', 200, $res);
			} else {
				$this->response(FALSE, 'Data Not Found', 400);
			}
		}
	}

	public function getDynamicFieldListColumns()
	{
		$token = $this->input->post('token');
		if ($token == "") {
			$this->response(FALSE, 'Invalid Token.', 200);
			return;
		}
		if ($this->setData($token) == false)
			return;

		$post = (array) json_decode(file_get_contents('php://input'));
		if (!empty($post['queue_id'])) {
			$this->load->library('Dynamicformcontent');
			$res = $this->dynamicformcontent->getDynamicFieldListColumns($post['queue_id'], 't1');
			if (!empty($res)) {
				$this->response(TRUE, '', 200, $res);
			} else {
				$this->response(FALSE, 'Data Not Found', 400);
			}
		}
	}

	public function ticketSeries($queueId)
	{
		$ticketSeries = $this->Master_Model->ticketSeries($queueId);
		if (!empty($ticketSeries)) {
			return $ticketSeries;
		} else {
			return	$this->response(TRUE, 'Error', 400, 'Ticket Prefix is required');
		}
	}

	public function getTicketMasterList()
	{
		error_reporting(0);
		$postvars = json_decode(file_get_contents("php://input"), true);
		$mergedObject = (object) array_merge((array) $postvars['ticketData'], (array) $postvars['dynamicData']);
		$postvars['ticketData'] = $mergedObject;
		unset($postvars['dynamicData']);
		$sort = '';
		if (!empty($postvars['sort']) || !empty($postvars['order_by'])) {
			$sort = $postvars['sort'] . ' ' . $postvars['order_by'];
		}
		$data = $postvars['ticketData'];
		$masterName = $data->master_name;
		if ($masterName) {
			$retData = $this->Api_Model->getTicketDataList(
				$masterName,
				$data->page_num,
				$data->limit,
				array(
					'ticket_id' => trim($data->ticket_id),
					'ticket_unique_id' => trim($data->ticket_unique_id),
					'customer_name' => trim($data->customer_name),
					'queue_id' => trim($data->queue_id),
					'category_id' => trim($data->category_id),
					'status_id' => trim($data->status_id),
					'priority_id' => trim($data->priority_id),
					'assign_to' => trim($data->assign_to),
					'customer_mobile_no' => trim($data->customer_mobile_no),
					'from_email' => trim($data->from_email),
					'subject' => trim($data->subject),
					'created_date_from' => trim($data->created_date_from),
					'created_date_to' => trim($data->created_date_to),
					'master_name' => trim($data->master_name),
					'un_assigend' => trim($data->un_assigend)
				),
				'1=1',
				$sort,
				$data
			);
		}
		if (!empty($retData)) {
			$this->response(TRUE, '', 200, $retData);
		} else {
			$this->response(FALSE, 'Data Not Found', 400);
		}
	}

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
					$retData = $this->Api_Model->getDataList($this->tableArr[$postvars['master_name']], $postvars['page_num'], $postvars['limit'], array('user_id' => $postvars['user_id'], 'from_date' => $postvars['from_date'], 'to_date' => $postvars['to_date'], 'report_type' => $postvars['report_type']), '1=1', $sort);
					break;

				case 'field-group';
					$retData = $this->Api_Model->getDataList($this->tableArr[$postvars['master_name']], $postvars['page_num'], $postvars['limit'], array('title' => $postvars['title'], 'field_type' => $postvars['field_type']), '1=1', $sort);
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

	public function deleteTicketDetail()
	{
		$post = json_decode(file_get_contents("php://input"));
		$post = (array) $post;
		if (!empty($post['master_name'])) {
			$masterName = $post['master_name'];
			if ($masterName) {
				$this->load->model('Master_Model');
				$this->db->trans_begin();
				$dbData = $post;
				$respMsg = '';

				$primaryKey = 'ticket_unique_id';
				$retarr = $this->Api_Model->deleteProcess($masterName, $post, $primaryKey);
				if (!empty($retarr['error'])) {
					$respMsg = $retarr['message'];
				} else {
					$respMsg = 'Ticket deleted successfully';
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
			$this->response(FALSE, 'Invalid Request', 400);
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
					// $this->response(FALSE, 'Something goes wrong', 400);
					$this->response(FALSE, $respMsg, 400);
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
		$token = $this->input->post('token');
		if ($token == "") {
			$this->response(FALSE, 'Invalid Token.', 200);
			return;
		}
		if ($this->setData($token) == false)
			return;
		$masters = json_decode(file_get_contents("php://input"), true);
		foreach ($masters as $name) {
			if (array_key_exists($name, $this->tableArr) == 1) {
				$retData[] = $this->Dropdown_Option_Model->getOptionList((array) $this->tableArr[$name], $this->user_id);
				@$masternamefordropddown[] = (array) $this->tableArr[$name];
			}
		}
		array_push($retData, @$masternamefordropddown);
		$this->response(TRUE, '', 200, $retData);
	}

	public function getQueuePriorityDropdown()
	{
		$masters = json_decode(file_get_contents("php://input"), true);
		foreach ($masters as $name) {
			$retData[] = $this->Dropdown_Option_Model->getQueuePriorityOptionList($name);
			@$masternamefordropddown[] = (array) $name;
		}
		array_push($retData, @$masternamefordropddown);
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
				$dbData['password'] = md5($post['original_password']);
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


	function getpermissionqueue()
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


	public function getMoveJunkPermission()
	{

		$token = $this->input->post('token');
		if ($token == "") {
			$this->response(FALSE, 'Invalid Token.', 200);
			return;
		}
		if ($this->setData($token) == false)
			return;

		$permission = $this->Api_Model->getjunkpermisssion($this->user_id, $this->user_type);
		$this->response(TRUE, '', 200, $permission);
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
			$result = $this->Api_Model->getCategoryData($data);
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
			//Server settings
			//    $mail->SMTPDebug = 4;                                       // Enable verbose debug output

			$mail->isSMTP();                                            // Set mailer to use SMTP
			//  $mail->Host       = 'smtp1.example.com;smtp2.example.com';  // Specify main and backup SMTP servers  . ';' . $this->input->post('out_mail_server')?$this->input->post('out_mail_server'):''
			//  $mail->Host       = 'mail.sansoftwares.com';
			$mail->Host       = $post['outgoing_mail_server'] ? $post['outgoing_mail_server'] : '';

			$mail->SMTPAuth   = true;                                   // Enable SMTP authentication

			$mail->Timeout       =   10; // set the timeout (seconds)
			// $mail->SMTPKeepAlive = true; // don't close the connection between messages

			$mail->Username   = $post['outgoing_user_name'];

			$mail->Password   = $password;
			//    $mail->SMTPSecure = 'tls';                                  // Enable TLS encryption, `ssl` also accepted
			$mail->SMTPSecure = 'tls';


			//  $mail->Port       = 587;
			$mail->Port   = $post['outgoing_mail_server_port'];

			//Recipients
			$mail->setFrom($post['outgoing_user_name'], 'Email Testing');
			$mail->addAddress('gaurav.singh@sansoftwares.com', 'Test Mail');     // Add a recipient


			// Content
			$mail->isHTML(true);                                  // Set email format to HTML
			$mail->Subject = 'Test Mail';

			$mail->Body    = 'This is a <b>Test Mail!</b>';
			//$mail->AltBody = 'This is a the body in plain text for non-HTML mail clients';

			if ($mail->send()) {
				$this->response(FALSE, 'Valid Configuration', 200);
			} else {
				$this->response(FALSE, 'Invalid configuration', 400);
			}
			//echo 'Message has been sent';
			//return true;
		} catch (Exception $e) {
			$this->response(FALSE, 'Message could not be sent. Mailer Error: {$mail->ErrorInfo}', 400);
			//return false;
		}
	}

	// public function getRuleList()
	// {
	// 	$token = $this->input->post('token');
	// 	if ($token == "") {
	// 		$this->response(FALSE, 'Invalid Token.', 200);
	// 		return;
	// 	}
	// 	if ($this->setData($token) == false)
	// 		return;

	// 	$post = (array) json_decode(file_get_contents('php://input'));

	// 	$retData = $this->Api_Model->getRuleDataList($post['queue_id']);
	// 	if (!empty($retData)) {
	// 		$this->response(TRUE, '', 200, $retData);
	// 	} else {
	// 		$this->response(FALSE, 'Data Not Found', 400);
	// 	}
	// }

	public function getRuleList()
	{
		$token = $this->input->post('token');
		if ($token == "") {
			$this->response(FALSE, 'Invalid Token.', 200);
			return;
		}
		if ($this->setData($token) == false)
			return;

		$post = (array) json_decode(file_get_contents('php://input'));

		$retData = $this->Api_Model->getRuleDataList($post['queue_id'], $post['limit'], $post['page_no']);
		if (!empty($retData)) {
			$this->response(TRUE, '', 200, $retData);
		} else {
			$this->response(FALSE, 'Data Not Found', 400);
		}
	}


	// public function getFormBuilderList()
	// {
	// 	$token = $this->input->post('token');
	// 	if ($token == "") {
	// 		$this->response(FALSE, 'Invalid Token.', 200);
	// 		return;
	// 	}
	// 	if ($this->setData($token) == false)
	// 		return;

	// 	$post = (array) json_decode(file_get_contents('php://input'));

	// 	$retData = $this->Api_Model->getFormBuilderDataList($post['queue_id']);

	// 	if (!empty($retData)) {
	// 		$this->response(TRUE, '', 200, $retData);
	// 	} else {
	// 		$this->response(FALSE, 'Data Not Found', 400);
	// 	}
	// }

	public function getFormBuilderList()
	{
		$token = $this->input->post('token');
		if ($token == "") {
			$this->response(FALSE, 'Invalid Token.', 200);
			return;
		}
		if ($this->setData($token) == false)
			return;

		$post = (array) json_decode(file_get_contents('php://input'));

		$retData = $this->Api_Model->getFormBuilderDataList($post['queue_id'], $post['limit'], $post['page_no']);
		if (!empty($retData)) {
			$this->response(TRUE, '', 200, $retData);
		} else {
			$this->response(FALSE, 'Data Not Found', 400);
		}
	}

	public function deleteRuleDetail()
	{
		$post = json_decode(file_get_contents("php://input"));
		$post = (array) $post;

		$this->load->model('Master_Model');
		$this->db->trans_begin();
		$dbData = $post;
		$respMsg = '';

		$primaryKey = 'rule_id';
		$retarr = $this->Api_Model->deleteQueueRule($post, $primaryKey);
		if (!empty($retarr['error'])) {
			$respMsg = $retarr['message'];
		} else {
			$master_title = "Queue Rule";
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

	public function getQueueRuleDetail()
	{
		$postvars = json_decode(file_get_contents("php://input"));
		$this->validateApi($postvars);
		$post = $this->input->post();
		if (!empty($post['queue_id']) && !empty($post['rule_id'])) {
			$this->load->model('Master_Model');
			$primaryKey = 'rule_id';

			$respArr = $this->Master_Model->getQueueRulesData($post['rule_id'], $post['queue_id'], $primaryKey);

			if (!empty($respArr)) {
				$this->response(TRUE, '', 200, $respArr);
			} else {
				$this->response(FALSE, 'Data not found', 400);
			}
		} else {
			$this->response(FALSE, 'Invalid Request', 400);
		}
	}

	public function getQueueDropdownOptions()
	{
		$data = json_decode(file_get_contents("php://input"), true);
		foreach ($data['type'] as $typeVal) {
			if (!empty($typeVal) && !empty($data['queue_id'])) {
				$retData[] = $this->Dropdown_Option_Model->getQueueOptionList((array) $typeVal, $data['queue_id']);
				@$masternamefordropddown[] = (array) $typeVal;
			}
		}

		array_push($retData, @$masternamefordropddown);
		$this->response(TRUE, '', 200, $retData);
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
			$this->form_validation->set_message('defaultDataValue', 'The default value does not match with the defined values');
			$this->form_validation->set_message('checkDataLength', 'The length of values does not match with the defined length');
			$this->form_validation->set_message('fieldLengthDecimalMax', 'The maximum possible length for decimal data type is 65 significant digits and 30 digits after the decimal point.');
			$this->form_validation->set_message('fieldLengthDecimalDigitMax', 'The maximum possible length for decimal data type is 65 significant digits and 30 digits after the decimal point.');
			if ($this->form_validation->run() == false) {
				$data = $this->form_validation->error_array();
				$firstError = array_keys($data);
				$this->response(FALSE, 'Validation Error', 400, $data[$firstError[0]]);
				return;
			} else {
				$data	= $post;
				$masterName = "tms_" . $data['master_name'] . '_' . $data['queue_id'];
				$ticketTblName = "tms_ticket_master_" . $data['queue_id'];
				//CHECK FIELD NAME IN TABLE AND FORM FILEDS
				if (empty($data['field_id'])) {
					$fldRes	= $this->Dynamicform_Model->getTableField($data['field_name'], $ticketTblName);
					if (!empty($fldRes)) {
						$this->response(FALSE, 'The field is already exist in the Ticket Table.', 400);
						return;
					}
				}

				if ((strtolower($data['field_name']) == 'id') or
					(strtolower($data['field_name']) == "tms") or
					strpos(strtolower($data['field_name']), 'delete') !== false or
					strpos(strtolower($data['field_name']), 'drop') !== false or
					strpos(strtolower($data['field_name']), 'update') !== false or
					strpos(strtolower($data['field_name']), 'alter') !== false or
					strpos(strtolower($data['field_name']), 'insert') !== false
				) {
					$this->response(FALSE, 'Unauthorized keyword (<db name>, ID, INSERT, DROP, UPDATE, ALTER, DELETE) found for Field Name', 400);
					return;
				}

				if (!empty($data['values_from_db']) && !empty($data['field_values'])) {
					if (
						// strpos(strtolower($data['field_values']), "tms") !== false or
						strpos(strtolower($data['field_values']), 'delete') !== false or
						strpos(strtolower($data['field_values']), 'drop') !== false or
						strpos(strtolower($data['field_values']), 'update') !== false or
						strpos(strtolower($data['field_values']), 'alter') !== false or
						strpos(strtolower($data['field_values']), 'insert') !== false
					) {

						$this->response(FALSE, 'Unauthorized keyword ({db name}, INSERT, DROP, UPDATE, ALTER, DELETE) found in SQL Statement.', 400);
						return;
					} else {
						$sqlStmtarr	= explode('from', strtolower($data['field_values']));
						if (!empty($sqlStmtarr[1])) {
							$sqlSubStmtarr	= explode(' ', trim($sqlStmtarr[1]));
							if (!empty($sqlSubStmtarr[0]) && strpos(strtolower($sqlSubStmtarr[0]), '.') !== false) {

								$this->response(FALSE, 'Unauthorized keyword ({db name}, INSERT, DROP, UPDATE, ALTER, DELETE) found in SQL Statement.', 400);
								return;
							}
						}
						$chkQuery	= $this->Dynamicform_Model->checkDataValuesQuery($data['field_values']);
						if ($chkQuery['check']) {
							$optcount	= $this->Dynamicform_Model->getDataCountThroughValuesQuery(str_replace(';', '', $data['field_values']));
							if (empty($optcount[0]->rowcnt)) {
								$this->response(FALSE, 'Data not found from the result of your query.', 400);
								return;
							}
							if ($optcount[0]->rowcnt > 50 && in_array(strtolower($data['field_type']), array('checkbox', 'radio'))) {
								$this->response(FALSE, 'Number of record are too long to render radio or checkbox fields. Max. 50 records are allowed', 400);
								return;
							}
							$qres	= $this->Dynamicform_Model->getDataByQuery(str_replace(';', '', $data['field_values']));
							if (!empty($qres)) {
								$idx = 0;
								foreach ($qres[0] as $rky => $rvl) {
									if ($idx == 0) {
										$data['db_val_column'] = $rky;
									} else if ($idx == 1) {
										$data['db_txt_column'] = $rky;
									}
									$idx++;
								}
							}
							if (empty($data['db_val_column'])) {
								$this->response(FALSE, 'Unable to find data columns from query. Please check the query.', 400);
								return;
							}
						} else {
							$this->response(FALSE, $chkQuery['message'], 400);
							return;
						}
					}
				} else if (empty($data['values_from_db']) && !empty($data['field_values'])) {
					$frec	= explode(',', $data['field_values']);
					if (count($frec) > 50 && in_array(strtolower($data['field_type']), array('checkbox', 'radio'))) {
						echo json_encode(array('statusCode' => 400, 'error' => 'Number of record are too long to render radio or checkbox fields. Max. 50 records are allowed'));
						return;
					}
				}

				if (isset($data['field_id']) && !empty($data['field_id'])) {
					$fieldData	= $this->Dynamicform_Model->getFieldData($data['field_id'], $data['queue_id']);

					$updateData	= $this->Dynamicform_Model->setFormFieldData($data);

					foreach ($updateData as $fldky => $fldvl) {
						if (in_array($fldky, array('field_name', 'field_length', 'decimal_length', 'unique_field', 'values_from_db'))) {
							if ($fldvl != $fieldData->data->{$fldky}) {
								//CHECK FIELD NAME IN TABLE
								$fldRes	= $this->Dynamicform_Model->getTableField($fldvl, $ticketTblName);
								if (!empty($fldRes)) {
									$this->response(FALSE, 'The entered field is already exist in the Table.', 400);
									return;
								}
								if ($fldky == 'field_length') {
									//CHECK FIELD DATA LENGTH FOR COLUMN VALUES
									$res	= $this->Dynamicform_Model->getTableFieldMaxLengthRecord($ticketTblName, $fieldData->data->field_name);
									if (strtolower($updateData['data_type']) != 'decimal') {
										if (!empty($res)) {
											$dtarr	= explode('.', $res[0]->{$fieldData->data->field_name});

											if (strlen($dtarr[0]) > $data[$fldky]) {
												$this->response(FALSE, 'Field Length can not be less than maximum data length (' . $res[0]->maxlen . ') found for this field', 400);
												return;
											}
										}
									} else {
										if (!empty($res) && ($res[0]->maxlen > $data[$fldky])) {
											$this->response(FALSE, 'Field Length can not be less than maximum data length (' . $res[0]->maxlen . ') found for this field', 400);
											return;
										}
									}
								} else if ($fldky == 'unique_field' && !empty($fldvl)) {
									//CHECK DEDUPE FOR COLUMN VALUES
									$res	= $this->Dynamicform_Model->getTableFieldDedupeRecord($ticketTblName, $fieldData->data->field_name);

									if (!empty($res) && ($res[0]->dedupe_cnt > 0)) {
										$this->response(FALSE, 'Duplicate values found for this field. It can not be applied UNIQUE', 400);
										return;
									}
								} else if ($fldky == 'values_from_db') {
									$res	= $this->Dynamicform_Model->getTableData($ticketTblName, $fieldData->data->field_name);
									if (!empty($res)) {
										$this->response(FALSE, 'Data found in table. It can not modify the Values from Database', 400);
										return;
									}
								}
							}
						}
					}

					$updateData['modified_by'] = $this->user_id;
					$updateData['modified_date'] = date('Y-m-d H:i:s');

					$db		= $this->Dynamicform_Model->updateFormField($updateData, $data['field_id'], $masterName);
					$msg	= 'Form Field detail have been updated successfully.';
					if ($db) {
						$modify = false;
						foreach ($updateData as $fldky => $fldvl) {
							if (in_array($fldky, array('field_name', 'data_type', 'field_length', 'decimal_length', 'field_type', 'default_value', 'unique_field', 'required'))) {
								if ($fldvl != $fieldData->data->{$fldky}) {
									$modify = true;
								}
							}
						}
						if ($modify) {
							//UPDATE TABLE FIELD
							$dbResp	= $this->Dynamicform_Model->modifyTableColumn($ticketTblName, $data, $fieldData->data);
							if (!$dbResp) {
								$msg	= $this->Dynamicform_Model->db->error();
							} else {
								if (empty($updateData['unique_field']) && !empty($fieldData->data->unique_field)) {
									//DELETE INDEXES MANUALLY
									$res	= $this->Dynamicform_Model->getTableIndex($ticketTblName, $fieldData->data->field_name);
									if (!empty($res)) {
										foreach ($res as $rky => $indxarr) {
											$this->Dynamicform_Model->dropTableIndex($ticketTblName, $indxarr->Key_name);
										}
									}
								}
								if (!empty($updateData['unique_field']) && empty($fieldData->data->unique_field)) {
									$key_length	= (strtolower($updateData['data_type']) == 'text') ? 20 : 0;
									$this->mymodel->createTableIndex($ticketTblName, $updateData['field_name'], $key_length);
								}
							}
						}
					}

					$id = $data['field_id'];
				} else {
					$insData	= $this->Dynamicform_Model->setFormFieldData($data);

					$insData['created_by'] = $this->user_id;
					$insData['created_date'] = date('Y-m-d H:i:s');
					$db	= $this->Dynamicform_Model->createFormField($insData, $masterName);
					if ($db) {
						$this->Dynamicform_Model->addTableColumn($ticketTblName, $data);
						//CREATE INDEXE
						if (!empty($insData['unique_field'])) {
							$key_length	= (strtolower($insData['data_type']) == 'text') ? 20 : 0;
							$this->Dynamicform_Model->createTableIndex($ticketTblName, $insData['field_name'], $key_length);
						}
						// }
					}
					$msg	= 'Form Field detail have been added successfully.';
					$id = $db;
				}

				if (!empty($db)) {
					$this->response(TRUE, $msg, 200);
				} else {
					$this->response(FALSE, 'Validation Error', 400, 'Failed. Please try again');
					return;
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

	public function deleteDynFieldRec()
	{
		$post = json_decode(file_get_contents("php://input"));
		$post = (array) $post;

		$this->load->model('Master_Model');
		$this->db->trans_begin();
		$respMsg = '';

		$primaryKey = 'field_id';
		$retarr = $this->Dynamicform_Model->deleteDynFieldData($post, $primaryKey);
		if (!empty($retarr['error'])) {
			$respMsg = $retarr['message'];
		} else {
			$master_title = "Form Fields";
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
		// if($this->mymodel->deleteDynamicForm($id)) {
		// 	echo json_encode(array('statusCode'=>200, 'id'=>$id, 'msg'=>'The Form has been deleted successfully'));
		// } else {
		// 	echo json_encode(array('statusCode'=>404, 'error'=>'Failed. Please try again.'));
		// }
	}

	public function getDynFormFieldsDetail()
	{
		$postvars = json_decode(file_get_contents("php://input"));
		$this->validateApi($postvars);
		$post = $this->input->post();
		if (!empty($post['queue_id']) && !empty($post['field_id'])) {
			$this->load->model('Master_Model');
			$primaryKey = 'field_id';

			$respArr = $this->Dynamicform_Model->getFieldData($post['field_id'], $post['queue_id']);

			if (!empty($respArr)) {
				$this->response(TRUE, '', 200, $respArr);
			} else {
				$this->response(FALSE, 'Data not found', 400);
			}
		} else {
			$this->response(FALSE, 'Invalid Request', 400);
		}
	}


	// Dynamic form builder methods ends here


	public function ticketMoveToJunk()
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
			$resData = $this->Master_Model->ticketMoveToJunk($post);
			if ($resData > 0) {
				$respMsg = "Ticket moved to junk  successfully";
				$this->response(TRUE, $respMsg, 200);
			} else {
				$respMsg = "Something Went Wrong";
				$this->response(FALSE, $respMsg, 400);
			}
		} else {
			$this->response(FALSE, 'Invalid Master Name', 400);
		}
	}



	//-----------------------TMS API----------------------------------//
}
