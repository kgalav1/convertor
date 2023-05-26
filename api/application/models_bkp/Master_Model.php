<?php
class Master_Model extends CI_Model
{
	//MASTER DETAIL
	private $tableName = array(
		'user_type' => 'tms_user_type',
		"action" => "tms_action_master",
		"shift" => "tms_shift_master",
		"user" => "tms_user_master",
		"company" => "tms_company_master",
		"priority" => "tms_priority_master",
		"emailAccount" => "tms_email_acoount",
		"pagination" => "tms_pagination_master",
		"category" => "tms_category_master",
		"sub-category" => "tms_subcategory_master",
		"status" => "tms_status_master",
		"rule" => "tms_rule_master",
		"rule_condition" => "tms_rule_condition",
		"queue" => "tms_queue_master",
		"template" => "tms_template_master",
		"escalation-matrix" => "tms_escalationmatrix_master",
		"user_log" => "tms_log_master",
		"login_log" => "tms_login_log",
		"ticket" => "tms_new_ticket",
		"dynamicFormQueue" => "dynamicFormQueue",
		"form-builder" => "tms_dynamic_form_mapping",
		"dynamicform_fields" => "tms_dynamicform_fields"
	);

	function __construct()
	{
		parent::__construct();
	}

	function getMasterData($id, $masterName, $primaryKey = 'id')
	{
		$select = array('*');
		$this->db->select($select);
		$this->db->where(array($primaryKey => $id));
		$this->db->from($masterName);
		$query = $this->db->get();
		$result = $query->row_array();
		return $result;
	}

	function getMasterUserData($id, $masterName, $primaryKey = 'id')
	{
		$select = array('*');
		$this->db->select($select);
		$this->db->where(array($primaryKey => $id));
		$this->db->from($masterName);
		$query = $this->db->get();
		$result = $query->row_array();
		$result['queue_id'] = explode(",", $result['queue_id']);
		$result['action_id'] = explode(",", $result['action_id']);
		return  array('result' => $result);
	}


	function getMasterRulesData($id, $masterName, $primaryKey = 'id')
	{
		$select = array('*');
		$this->db->select($select);
		$this->db->where(array($primaryKey => $id));
		$this->db->from($masterName);
		$query = $this->db->get();
		$result = $query->row_array();


		$select = array('*');
		$this->db->select($select);
		$this->db->where('rule_id', $id);
		$this->db->from('tms_rule_condition');
		$query1 = $this->db->get();
		$result1 = $query1->result_array();

		$select = array('*');
		$this->db->select($select);
		$this->db->where('rule_id', $id);
		$this->db->from('tms_rule_action');
		$query2 = $this->db->get();
		$result2 = $query2->result_array();

		return  array('result' => $result, 'result1' => $result1, 'result2' => $result2);
	}

	function getMasterQueueData($id, $masterName, $primaryKey = 'id')
	{
		$select = array('*');
		$this->db->select($select);
		$this->db->where(array($primaryKey => $id));
		$this->db->from($masterName);
		$query = $this->db->get();
		$result = $query->row_array();
		$result['catagory'] = explode(",", $result['catagory']);
		$result['statustype'] = explode(",", $result['statustype']);


		$select = array('*');
		$this->db->select($select);
		$this->db->where('queue_id', $id);
		$this->db->from('tms_queue_email');
		$query1 = $this->db->get();
		$result1 = $query1->result_array();

		$select = array('*');
		$this->db->select($select);
		$this->db->where('queue_id', $id);
		$this->db->from('tms_queue_template');
		$query2 = $this->db->get();
		$result2 = $query2->result_array();



		return  array('result' => $result, 'result1' => $result1, 'result2' => $result2);
	}


	function getMasterTableFields($masterName)
	{
		$sql = "SHOW COLUMNS FROM " . $this->tableName[$masterName];
		$exec = $this->db->query($sql);
		$result = $exec->result_array();
		$retArr = array();
		foreach ($result as $key => $value) {
			$retArr[] = $value['Field'];
		}
		return $retArr;
	}


	function preSaveProcess($masterName, $data)
	{
		$table = array_key_exists($masterName, $this->tableName) ? $this->tableName[$masterName] : '';
		if ($masterName == 'MASTER_PAGINATION') {
			if (!empty($table)) {
				if (!empty($data['is_default'])) {
					$this->db->update($table, array('is_default' => 0));
				}
			}
		}
		return true;
	}

	function saveProcess($masterName, $data, $pKey = 'id')
	{
		$table = array_key_exists($masterName, $this->tableName) ? $this->tableName[$masterName] : '';
		if (!empty($table)) {
			$data = is_array($data) ? $data : (array) $data;
			$id = $data[$pKey];
			unset($data[$pKey]);
			if (empty($id)) { // create 
				$this->db->insert($table, $data);
				$id = $this->db->insert_id();
			} else { // edit 
				$this->db->where($pKey, $id)->update($table, $data);
			}
			$res = array('error' => false, 'id' => $id);
		} else {
			$res = array('error' => true, 'id' => 0);
		}
		return $res;
	}


	function saverule($masterName, $data, $pKey = 'id', $post)
	{
		$table = array_key_exists($masterName, $this->tableName) ? $this->tableName[$masterName] : '';
		if (!empty($table)) {
			$data = is_array($data) ? $data : (array) $data;
			$id = $data[$pKey];
			unset($data[$pKey]);
			if (empty($id)) { // create 
				$this->db->insert($table, $data);
				$id = $this->db->insert_id();

				foreach ($post['conditionArray'] as $key => $val) {
					$conditionarray[] = array(
						'evaluate_on' => $val->evaluate_on,
						'operator' => $val->operator,
						'evaluate_value' => $val->evaluate_value,
						'rule_id' =>  $id,
					);
				}
				$this->db->insert_batch('tms_rule_condition', $conditionarray);

				foreach ($post['actionArray'] as $key => $val) {
					$actionnarray[] = array(
						'action' => $val->action,
						'action_value' => $val->action_value,
						'rule_id' =>  $id,
					);
				}
				$this->db->insert_batch('tms_rule_action', $actionnarray);
			} else { // edit 
				$this->db->where($pKey, $id)->update($table, $data);

				$this->db->where('rule_id', $id);
				$this->db->delete('tms_rule_condition');

				$this->db->where('rule_id', $id);
				$this->db->delete('tms_rule_action');

				foreach ($post['conditionArray'] as $key => $val) {
					$conditionarray[] = array(
						'evaluate_on' => $val->evaluate_on,
						'operator' => $val->operator,
						'evaluate_value' => $val->evaluate_value,
						'rule_id' =>  $id,
					);
				}
				$this->db->insert_batch('tms_rule_condition', $conditionarray);

				foreach ($post['actionArray'] as $key => $val) {
					$actionnarray[] = array(
						'action' => $val->action,
						'action_value' => $val->action_value,
						'rule_id' =>  $id,
					);
				}
				$this->db->insert_batch('tms_rule_action', $actionnarray);
			}
			$res = array('error' => false, 'id' => $id);
		} else {
			$res = array('error' => true, 'id' => 0);
		}
		return $res;
	}



	function savequeue($masterName, $data, $pKey = 'id', $post)
	{
		$table = array_key_exists($masterName, $this->tableName) ? $this->tableName[$masterName] : '';
		if (!empty($table)) {
			$data = is_array($data) ? $data : (array) $data;
			$id = $data[$pKey];
			unset($data[$pKey]);
			if (empty($id)) { // create 
				$this->db->insert($table, $data);
				$id = $this->db->insert_id();

				foreach ($post['emailArray'] as $key => $val) {
					$emailarray[] = array(
						'incoming_email_account' => $val->incoming_email_account,
						'outgoing_email_account' => $val->outgoing_email_account,
						'queue_id' =>  $id,
					);
				}
				$this->db->insert_batch('tms_queue_email', $emailarray);

				foreach ($post['templateArray'] as $key => $val) {
					$templatearray[] = array(
						'action' => $val->action,
						'template' => $val->template,
						'customer_email' => $val->customer_email,
						'consumer_sms' => $val->consumer_sms,
						'customer_wp' => $val->customer_wp,
						'user_email' => $val->user_email,
						'user_sms' => $val->user_sms,
						'user_wp' => $val->user_wp,
						'queue_id' =>  $id,
					);
				}
				$this->db->insert_batch('tms_queue_template', $templatearray);
			} else { // edit 
				$this->db->where($pKey, $id)->update($table, $data);

				$this->db->where('queue_id', $id);
				$this->db->delete('tms_queue_email');

				$this->db->where('queue_id', $id);
				$this->db->delete('tms_queue_template');

				foreach ($post['emailArray'] as $key => $val) {
					$emailarray[] = array(
						'incoming_email_account' => $val->incoming_email_account,
						'outgoing_email_account' => $val->outgoing_email_account,
						'queue_id' =>  $id,
					);
				}
				$this->db->insert_batch('tms_queue_email', $emailarray);

				foreach ($post['templateArray'] as $key => $val) {
					$templatearray[] = array(
						'action' => $val->action,
						'template' => $val->template,
						'customer_email' => $val->customer_email,
						'consumer_sms' => $val->consumer_sms,
						'customer_wp' => $val->customer_wp,
						'user_email' => $val->user_email,
						'user_sms' => $val->user_sms,
						'user_wp' => $val->user_wp,
						'queue_id' =>  $id,
					);
				}
				$this->db->insert_batch('tms_queue_template', $templatearray);
			}
			$res = array('error' => false, 'id' => $id);
		} else {
			$res = array('error' => true, 'id' => 0);
		}
		return $res;
	}

	function saveUser($masterName, $data, $pKey = 'id')
	{
		if (!empty($masterName)) {
			$data = is_array($data) ? $data : (array) $data;
			$password = md5($data['original_password']);
			$data += ['password' => $password];
			$id = $data[$pKey];
			unset($data[$pKey]);
			if (empty($id)) { // create 
				$this->db->insert($masterName, $data);
				$id = $this->db->insert_id();
				$this->db->set('user_id', $id);
				$this->db->where('id', $id)->update($masterName);
			} else { // edit 
				$this->db->where($pKey, $id)->update($masterName, $data);
			}
			$res = array('error' => false, 'id' => $id);
		} else {
			$res = array('error' => true, 'id' => 0);
		}
		return $res;
	}

	function saveShift($masterName, $data, $pKey = 'id')
	{
		if (!empty($masterName)) {
			$data = is_array($data) ? $data : (array) $data;
			$id = $data[$pKey];
			unset($data[$pKey]);
			if (empty($id)) { // create 
				$this->db->insert($masterName, $data);
				$id = $this->db->insert_id();
			} else { // edit 
				$this->db->where($pKey, $id)->update($masterName, $data);
			}
			$res = array('error' => false, 'id' => $id);
		} else {
			$res = array('error' => true, 'id' => 0);
		}
		return $res;
	}

	function deleteProcess($masterName, $data, $pKey = 'id')
	{
		try {
			$table = array_key_exists($masterName, $this->tableName) ? $this->tableName[$masterName] : '';
			if (!empty($table)) {
				$data = is_array($data) ? $data : (array) $data;
				$id = $data[$pKey];
				if (!empty($id)) {
					if ($this->db->delete($table, array($pKey => $id))) {
						$res = array('error' => false, 'message' => 'Record deleted');
					} else {
						$error = $this->db->error();
						if ($error['code'] == '') {
							throw new Exception('Something goes wrong.');
						} else {
							throw new Exception('Something goes wrong.');
						}
					}
				} else {
					throw new Exception('Invalid Request');
				}
			} else {
				throw new Exception('Invalid Request');
			}
		} catch (Exception $e) {
			$res = array('error' => true, 'message' => $e->getMessage());
		}
		return $res;
	}

	function masterEmailAccountRules()
	{
		$rules[] = array(
			'field' => 'email',
			'label' => '',
			'rules' => array('required', array('msg', array($this, 'uniqueEmailValidate'))),
			'errors' => array('msg' => 'Email already exists')
		);
		$rules[] = array('field' => 'server_type', 'label' => 'Server type', 'rules' => 'required');
		$rules[] = array('field' => 'display_name', 'label' => 'Display Name ', 'rules' => 'required');


		$rules[] = array('field' => 'incoming_user_name', 'label' => 'Incoming User Name', 'rules' => 'required');
		$rules[] = array('field' => 'incoming_password', 'label' => 'Incoming Password', 'rules' => 'required');
		$rules[] = array('field' => 'outgoing_user_name', 'label' => 'Outgoing User Name', 'rules' => 'required');
		$rules[] = array('field' => 'outgoing_password', 'label' => 'Outgoing Password', 'rules' => 'required');

		$rules[] = array('field' => 'incoming_mail_server', 'label' => 'Incoming Mail Server', 'rules' => 'required');
		$rules[] = array('field' => 'outgoing_mail_server_port', 'label' => 'Outgoing Mail Server  Port', 'rules' => 'required');
		$rules[] = array('field' => 'incoming_mail_server_port', 'label' => 'Incoming Mail  Port', 'rules' => 'required');
		$rules[] = array('field' => 'outgoing_mail_server', 'label' => 'Outgoing  Mail Server', 'rules' => 'required');
		$rules[] = array('field' => 'outgoing_mail_server_port', 'label' => 'Outgoing Mail Server  Port', 'rules' => 'required');
		return $rules;
	}

	function getMasterTableRules($masterName)
	{
		switch ($masterName) {
			case 'user':
				return $this->masterUserRules();
				break;
			case 'shift':
				return $this->masterShiftRules();
				break;
			case 'company':
				return $this->masterComapnyRules();
				break;
			case 'priority':
				return $this->masterPriorityRules();
				break;
			case 'emailAccount':
				return $this->masterEmailAccountRules();
				break;
			case 'pagination':
				return $this->masterPaginationRules();
				break;
			case 'sub-category':
				return $this->masterSubCategoryRules();
				break;
			case 'status':
				return $this->masterStatusRules();
				break;
			case 'rule':
				return $this->masterRuleRules();
				break;
			case 'queue':
				return $this->masterQueueRules();
				break;
			case 'template':
				return $this->masterTemplateRules();
				break;
			case 'ticket':
				return $this->masterTicketRules();
				break;
			case 'form-builder':
				return $this->masterFormBuilderRules();
				break;
			case 'dynamicform_fields':
				return $this->masterDynamicFormFieldsRules();
				break;
			default:
				return $this->masterRules();
				break;
		}
	}

	public function masterDynamicFormFieldsRules()
	{
		$rules[] = array(
			'field' => 'field_description',
			'label' => 'Form Description',
			'rules' => 'trim|required'
		);
		$rules[] = array(
			'field' => 'field_name',
			'label' => 'Field Name',
			'rules' => 'trim|required'
		);
		$rules[] = array(
			'field' => 'data_type',
			'label' => 'Data Type',
			'rules' => 'trim|required'
		);
		$rules[] = array(
			'field' => 'field_length',
			'label' => 'Field Length',
			'rules' => 'trim|callback_fieldLengthRequired|greater_than_equal_to[1]|callback_fieldLengthVarCharMax|callback_fieldLengthDecimalMax'
		);
		$rules[] = array(
			'field' => 'decimal_length',
			'label' => 'Decimal Length',
			'rules' => 'trim|callback_decimalLengthRequired|greater_than_equal_to[1]|callback_fieldLengthDecimalDigitMax'
		);
		$rules[] = array(
			'field' => 'field_type',
			'label' => 'Field Type',
			'rules' => 'trim|callback_fieldTypeRequired'
		);
		$rules[] = array(
			'field' => 'field_values',
			'label' => 'Field Values',
			'rules' => 'trim|callback_dataRequired'
		);
		$rules[] = array(
			'field' => 'default_value',
			'label' => 'Default Values',
			'rules' => 'trim|callback_defaultDataLength|callback_defaultDataValue'
		);
		$rules[] = array(
			'field' => 'width',
			'label' => 'Width',
			'rules' => 'trim|greater_than_equal_to[25]|less_than_equal_to[1100]'
		);
		return $rules;
	}
	

	function masterTicketRules()
	{
		$rules[] = array('field' => 'queue_id', 'label' => 'Queue', 'rules' => 'required');
		$rules[] = array('field' => 'email_from', 'label' => ' Email From', 'rules' => 'required');
		$rules[] = array('field' => 'email_to', 'label' => 'Email To', 'rules' => 'required');
		$rules[] = array('field' => 'subject', 'label' => 'Subject', 'rules' => 'required');
		$rules[] = array('field' => 'priority_id', 'label' => 'Priority', 'rules' => 'required');
		$rules[] = array('field' => 'conversation', 'label' => 'Conversation', 'rules' => 'required');
		$rules[] = array('field' => 'ticket_status', 'label' => 'Ticket Status', 'rules' => 'required');
		return $rules;
	}

	public function masterFormBuilderRules()
	{
		$rules[] = array('field' => 'queue_id', 'label' => 'Queue', 'rules' => 'required');
		return $rules;
	}


	function masterTemplateRules()
	{
		$rules[] = array(
			'field' => 'template',
			'label' => '',
			'rules' => array('required', array('msg', array($this, 'uniquetemplateValidate'))),
			'errors' => array('msg' => 'Template already exists')
		);
		return $rules;
	}

	function masterQueueRules()
	{

		$rules[] = array(
			'field' => 'queue',
			'label' => '',
			'rules' => array('required', array('msg', array($this, 'uniqueQueueValidate'))),
			'errors' => array('msg' => 'Queue already exists')
		);
		$rules[] = array('field' => 'priority_id', 'label' => 'Priority', 'rules' => 'required');
		$rules[] = array('field' => 'incomming_male_status', 'label' => 'Incoming Mail Status', 'rules' => 'required');
		$rules[] = array('field' => 'before_assign_shift', 'label' => 'Before Assign Shift', 'rules' => 'required');
		$rules[] = array('field' => 'dont_read_mail_before', 'label' => 'Dont Read Mail Before', 'rules' => 'required');
		// $rules[] = array('field' => 'incoming_email_account', 'label' => 'Incoming Email Account', 'rules' => 'required');
		// $rules[] = array('field' => 'outgoing_email_account', 'label' => 'Outgoing Email Account', 'rules' => 'required');
		return $rules;
	}


	function masterRuleRules()
	{

		$rules[] = array(
			'field' => 'title',
			'label' => '',
			'rules' => array('required', array('msg', array($this, 'uniqueTitleValidate'))),
			'errors' => array('msg' => 'Rule already exists')
		);


		$rules[] = array(
			'field' => 'order_no',
			'label' => '',
			'rules' => array('required', array('msg', array($this, 'uniqueOrdeValidate'))),
			'errors' => array('msg' => 'Order No already exists')
		);
		return $rules;
	}

	function masterPriorityRules()
	{
		$rules[] = array(
			'field' => 'name',
			'label' => '',
			'rules' => array('required', array('msg', array($this, 'uniqueNameValidate'))),
			'errors' => array('msg' => 'Name already exists')
		);

		$rules[] = array(
			'field' => 'colour_code',
			'label' => '',
			'rules' => array('required', array('msg', array($this, 'uniqueColourCodeValidate'))),
			'errors' => array('msg' => 'Colour Code already exists')
		);
		$rules[] = array('field' => 'tat', 'label' => 'TaT', 'rules' => 'required');
		$rules[] = array('field' => 'order_no', 'label' => 'Order Number', 'rules' => 'required');

		return $rules;
	}

	public function masterStatusRules()
	{
		$rules[] = array(
			'field' => 'title',
			'label' => '',
			'rules' => array('required', array('msg', array($this, 'uniqueTitleValidate'))),
			'errors' => array('msg' => 'Ticket Status Already exists')
		);

		$rules[] = array(
			'field' => 'colour_code',
			'label' => '',
			'rules' => array('required', array('msg', array($this, 'uniqueColourCodeValidate'))),
			'errors' => array('msg' => 'Colour Code already exists')
		);

		$rules[] = array('field' => 'ticket_type', 'label' => 'Ticket Status Type', 'rules' => 'required');
		return $rules;
	}

	public function uniqueColourCodeValidate()
	{
		$post = (array) json_decode(file_get_contents('php://input'));
		$masterName = $post['master_name'];
		$tableName = array_key_exists($masterName, $this->tableName) ? $this->tableName[$masterName] : '';
		if (!empty($post['id'])) {
			$is_exist = $this->db->where('colour_code', $post['colour_code'])->where_not_in('id', array($post['id']))->get($tableName)->row();
		} else {
			$is_exist = $this->db->where('colour_code', $post['colour_code'])->get($tableName)->row();
		}
		return ($is_exist) ? false : true;
	}

	function masterComapnyRules()
	{
		$rules[] = array(
			'field' => 'name',
			'label' => '',
			'rules' => array('required', array('msg', array($this, 'uniqueNameValidate'))),
			'errors' => array('msg' => 'Name already exists')
		);
		$rules[] = array(
			'field' => 'email',
			'label' => '',
			'rules' => array('required', array('msg', array($this, 'uniqueEmailValidate'))),
			'errors' => array('msg' => 'Email already exists')
		);
		// print_r($rules);die;
		$rules[] = array('field' => 'emailserver', 'label' => 'Email Server', 'rules' => 'required');
		$rules[] = array('field' => 'pagesize', 'label' => 'Page Size', 'rules' => 'required');
		$rules[] = array('field' => 'mailassignlimit', 'label' => 'Assign Limit', 'rules' => 'required');
		$rules[] = array('field' => 'assigntimegap', 'label' => 'Assign Time Gap', 'rules' => 'required');

		return $rules;
	}

	function companysaveProcess($masterName, $data, $pKey = 'id')
	{
		$table = array_key_exists($masterName, $this->tableName) ? $this->tableName[$masterName] : '';
		if (!empty($table)) {
			$data = is_array($data) ? $data : (array) $data;
			$id = $data[$pKey];
			unset($data[$pKey]);
			if (empty($id)) { // create 
				$this->db->insert($table, $data);
				$id = $this->db->insert_id();
			} else { // edit 
				$this->db->where($pKey, $id)->update($table, $data);
			}
			$res = array('error' => false, 'id' => $id);
		} else {
			$res = array('error' => true, 'id' => 0);
		}
		return $res;
	}

	function validateForm($post, $rules)
	{

		$this->form_validation->set_data((array) $post);
		$this->form_validation->set_rules($rules);

		if ($this->form_validation->run() === FALSE) {
			$data = $this->form_validation->error_array();
			$data['error'] = true;
			echo json_encode($data);
			die;
		}
	}

	function masterUserRules()
	{
		$rules[] = array(
			'field' => 'name',
			'label' => '',
			'rules' => array('required', array('msg', array($this, 'uniqueNameValidate'))),
			'errors' => array('msg' => 'Name already exists')
		);
		$rules[] = array(
			'field' => 'email',
			'label' => '',
			'rules' => array('required', array('msg', array($this, 'uniqueEmailValidate'))),
			'errors' => array('msg' => 'Email already exists')
		);
		$rules[] = array(
			'field' => 'mobile_no',
			'label' => '',
			'rules' => array('required', array('msg', array($this, 'uniqueMobilenoValidate'))),
			'errors' => array('msg' => 'Mobile No already exists')
		);
		$rules[] = array(
			'field' => 'user_name',
			'label' => '',
			'rules' => array('required', array('msg', array($this, 'uniqueUsernameValidate'))),
			'errors' => array('msg' => 'User Name already exists')
		);
		return $rules;
	}

	function masterSubCategoryRules()
	{
		$rules[] = array('field' => 'category_id', 'label' => 'Category', 'rules' => 'required');

		$rules[] = array(
			'field' => 'title',
			'label' => '',
			'rules' => array('required', array('msg', array($this, 'uniqueCategoryValidate'))),
			'errors' => array('msg' => 'Sub Category already exists')
		);
		return $rules;
	}


	function masterRules()
	{
		$rules[] = array('field' => 'title', 'label' => 'Title', 'rules' => 'required|min_length[2]|max_length[100]');
		$rules[] = array(
			'field' => 'title',
			'label' => '',
			'rules' => array('required', array('msg', array($this, 'uniqueTitleValidate'))),
			'errors' => array('msg' => 'Already exists')
		);
		// print_r($rules); die; 
		return $rules;
	}

	public function masterShiftRules()
	{
		$rules[] = array('field' => 'title', 'label' => 'Title', 'rules' => 'required|min_length[2]|max_length[100]');
		$rules[] = array(
			'field' => 'title',
			'label' => '',
			'rules' => array('required', array('msg', array($this, 'uniqueTitleValidate'))),
			'errors' => array('msg' => 'Already exists')
		);
		$rules[] = array('field' => 'time_from', 'label' => 'Time_From', 'rules' => 'required');
		$rules[] = array('field' => 'time_to', 'label' => 'Time To', 'rules' => 'required');

		return $rules;
	}


	public function uniqueUsernameValidate()
	{
		$post = (array) json_decode(file_get_contents('php://input'));
		$masterName = $post['master_name'];
		$tableName = array_key_exists($masterName, $this->tableName) ? $this->tableName[$masterName] : '';
		if (!empty($post['id'])) {
			$is_exist = $this->db->where('user_name', $post['user_name'])->where_not_in('id', array($post['id']))->get($tableName)->row();
		} else {
			$is_exist = $this->db->where('user_name', $post['user_name'])->get($tableName)->row();
		}
		return ($is_exist) ? false : true;
	}

	public function uniqueNameValidate()
	{
		$post = (array) json_decode(file_get_contents('php://input'));
		$masterName = $post['master_name'];
		$tableName = array_key_exists($masterName, $this->tableName) ? $this->tableName[$masterName] : '';
		if (!empty($post['id'])) {
			$is_exist = $this->db->where('name', $post['name'])->where_not_in('id', array($post['id']))->get($tableName)->row();
		} else {
			$is_exist = $this->db->where('name', $post['name'])->get($tableName)->row();
		}
		return ($is_exist) ? false : true;
	}

	public function uniqueEmailValidate()
	{
		$post = (array) json_decode(file_get_contents('php://input'));
		$masterName = $post['master_name'];
		$tableName = array_key_exists($masterName, $this->tableName) ? $this->tableName[$masterName] : '';
		if (!empty($post['id'])) {
			$is_exist = $this->db->where('email', $post['email'])->where_not_in('id', array($post['id']))->get($tableName)->row();
		} else {
			$is_exist = $this->db->where('email', $post['email'])->get($tableName)->row();
		}
		return ($is_exist) ? false : true;
	}

	public function uniqueMobilenoValidate()
	{
		$post = (array) json_decode(file_get_contents('php://input'));
		$masterName = $post['master_name'];
		$tableName = array_key_exists($masterName, $this->tableName) ? $this->tableName[$masterName] : '';
		if (!empty($post['id'])) {
			$is_exist = $this->db->where('mobile_no', $post['mobile_no'])->where_not_in('id', array($post['id']))->get($tableName)->row();
		} else {
			$is_exist = $this->db->where('mobile_no', $post['mobile_no'])->get($tableName)->row();
		}
		return ($is_exist) ? false : true;
	}

	function masterPaginationRules()
	{
		$rules[] = array(
			'field' => 'record',
			'label' => '',
			'rules' => array('required', array('msg', array($this, 'uniquePaginationValidate'))),
			'errors' => array('msg' => 'Value already exists')
		);

		$rules[] = array('field' => 'status', 'label' => 'Status', 'rules' => 'required|min_length[1]|max_length[1]|numeric');
		return $rules;
	}

	function uniqueTitleValidate()
	{
		$post = (array) json_decode(file_get_contents('php://input'));
		$masterName = $post['master_name'];
		$tableName = array_key_exists($masterName, $this->tableName) ? $this->tableName[$masterName] : '';
		if (!empty($post['id'])) {
			$is_exist = $this->db->where('title', $post['title'])->where_not_in('id', array($post['id']))->get($tableName)->row();
		} else {
			$is_exist = $this->db->where('title', $post['title'])->get($tableName)->row();
		}
		return ($is_exist) ? false : true;
	}

	function uniquePaginationValidate()
	{
		$post = (array) json_decode(file_get_contents('php://input'));
		if (!empty($post['id'])) {
			$is_exist = $this->db->where('record', $post['record'])->where_not_in('id', array($post['id']))->get('tms_pagination_master')->row();
		} else {
			$is_exist = $this->db->where('record', $post['record'])->get('tms_pagination_master')->row();
		}
		return ($is_exist) ? false : true;
	}

	function uniqueCategoryValidate()
	{
		$post = (array) json_decode(file_get_contents('php://input'));
		$masterName = $post['master_name'];
		$tableName = array_key_exists($masterName, $this->tableName) ? $this->tableName[$masterName] : '';
		if (!empty($post['id'])) {
			$is_exist = $this->db->where("title = '$post[title]' and category_id = '$post[category_id]'")->where_not_in('id', array($post['id']))->get($tableName)->row();
		} else {
			$is_exist = $this->db->where("title = '$post[title]' and category_id = '$post[category_id]'")->get($tableName)->row();
		}
		return ($is_exist) ? false : true;
	}


	function uniqueOrdeValidate()
	{
		$post = (array) json_decode(file_get_contents('php://input'));
		$masterName = $post['master_name'];
		$tableName = array_key_exists($masterName, $this->tableName) ? $this->tableName[$masterName] : '';
		if (!empty($post['id'])) {
			$is_exist = $this->db->where("order_no = '$post[order_no]'")->where_not_in('id', array($post['id']))->get($tableName)->row();
		} else {
			$is_exist = $this->db->where("order_no = '$post[order_no]'")->get($tableName)->row();
		}
		return ($is_exist) ? false : true;
	}


	function uniqueQueueValidate()
	{
		$post = (array) json_decode(file_get_contents('php://input'));
		$masterName = $post['master_name'];
		$tableName = array_key_exists($masterName, $this->tableName) ? $this->tableName[$masterName] : '';
		if (!empty($post['id'])) {
			$is_exist = $this->db->where("queue = '$post[queue]'")->where_not_in('id', array($post['id']))->get($tableName)->row();
		} else {
			$is_exist = $this->db->where("queue = '$post[queue]'")->get($tableName)->row();
		}
		return ($is_exist) ? false : true;
	}

	function uniquetemplateValidate()
	{
		$post = (array) json_decode(file_get_contents('php://input'));
		$masterName = $post['master_name'];
		$tableName = array_key_exists($masterName, $this->tableName) ? $this->tableName[$masterName] : '';
		if (!empty($post['id'])) {
			$is_exist = $this->db->where("template = '$post[template]'")->where_not_in('id', array($post['id']))->get($tableName)->row();
		} else {
			$is_exist = $this->db->where("template = '$post[template]'")->get($tableName)->row();
		}
		return ($is_exist) ? false : true;
	}


}
