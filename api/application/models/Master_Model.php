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
		"field-group" => "tms_field_group",
		"conversation-type" =>"tms_conversation_type_master"
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


		$select = array('*');
		$this->db->select($select);
		$this->db->from('tms_queue_category_' . $id);
		$categoryquery = $this->db->get();
		$categoryresult = $categoryquery->result_array();

		$select = array('*');
		$this->db->select($select);
		$this->db->from('tms_queue_priority_' . $id);
		$priorityquery = $this->db->get();
		$priorityresult = $priorityquery->result_array();

		$select = array('*');
		$this->db->select($select);
		$this->db->from('tms_queue_status_' . $id);
		$statusquery = $this->db->get();
		$statusresult = $statusquery->result_array();

		$select = array('*');
		$this->db->select($select);
		$this->db->from('tms_queue_escalationmatrix_' . $id);
		$escalationquery = $this->db->get();
		$escalationresult = $escalationquery->result_array();

		return  array('result' => $result, 'result1' => $result1, 'result2' => $result2, 'categoryResult' => $categoryresult, 'priorityResult' => $priorityresult, 'statusResult' => $statusresult, 'escalationResult' => $escalationresult);
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

	public function getTicketTableFields($masterName)
	{
		$sql = "SHOW COLUMNS FROM " . $masterName;
		$exec = $this->db->query($sql);
		$result = $exec->result_array();
		$retArr = array();
		foreach ($result as $key => $value) {
			$retArr[] = $value['Field'];
		}
		return $retArr;
	}

	public function getQueueCategoryFields($masterName)
	{
		$sql = "SHOW COLUMNS FROM " . $masterName;
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
			// if(isset($data['customer_email_template'])){
			// 	$data['customer_email_template'] = json_encode($data['customer_email_template']);
			// }
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

	// public function saveQueueCategoryData($masterName, $post)
	// {
	// if (isset($post['categoryArray'])) {
	// 	foreach ($post['categoryArray'] as $key => $val) {
	// 		// $categoryArray[] = array(
	// 		// 	'categor_id' => $val->category_id,
	// 		// 	'category_name' => $val->category,
	// 		// 	'sub_category' => $val->subcategory,
	// 		// );
	// 	}
	// $this->db->insert_batch($masterName, $categoryArray);
	// $id =  $this->db->insert_id();
	// if($id > 0){
	// 	$res = array('error' => false, 'id' => $id);
	// }else{
	// 	$res = array('error' => true, 'id' => '');
	// }
	// return $res;
	// }
	// if (isset($post['categoryArray'])) {
	// 	$success_count = 0;
	// 	$error_count = 0;
	// 	foreach ($post['categoryArray'] as $key => $val) {
	// 		$data = array(
	// 			'category_name' => $val->category,
	// 			'sub_category' => $val->subcategory,
	// 		);
	// 		$category_id = $val->category_id;
	// 		if (!empty($category_id)) {
	// 			// Update existing category
	// 			$this->db->where('category_id', $category_id);
	// 			$this->db->update($masterName, $data);
	// 			$affected_rows = $this->db->affected_rows();
	// 			if ($affected_rows >= 0) {
	// 				$success_count++;
	// 			} else {
	// 				$error_count++;
	// 			}
	// 		} else {
	// 			// Insert new category
	// 			$this->db->insert($masterName, $data);
	// 			$insert_id = $this->db->insert_id();
	// 			if ($insert_id > 0) {
	// 				$success_count++;
	// 			} else {
	// 				$error_count++;
	// 			}
	// 		}
	// 	}
	// 	if ($error_count == 0) {
	// 		// All operations were successful
	// 		return array('error' => false);
	// 	} else {
	// 		// There were some errors
	// 		return array('error' => true);
	// 	}
	// }

	public function saveQueueCategoryData($post, $masterName, $idField = 'category_id')
	{
		$categoryArray = array();
		$error_count = 0;
		$deleted_count = 0;

		// Get an array of existing IDs
		$existingIds = $this->db->select($idField)->get($masterName)->result_array();
		$existingIds = array_column($existingIds, $idField);

		// Build an array of data to insert or update
		foreach ($post['categoryArray'] as $key => $val) {
			$categoryArray[] = array(
				$idField => $val->category_id,
				'category_name' => $val->category,
				'sub_category' => $val->subcategory,
			);
		}

		// Update existing records
		foreach ($categoryArray as $category) {
			$id = $category[$idField];
			unset($category[$idField]);

			if (!empty($id)) {
				$this->db->where($idField, $id);
				$this->db->update($masterName, $category);

				// if ($this->db->affected_rows() == 0) {
				// 	$error_count++;
				// }
			} else {
				$this->db->insert($masterName, $category);

				if ($this->db->affected_rows() == 0) {
					$error_count++;
				}
			}

			// Remove ID from existing IDs array if it is present
			if (($key = array_search($id, $existingIds)) !== false) {
				unset($existingIds[$key]);
			}
		}

		// Delete records that were not included in the update
		if (!empty($existingIds)) {
			$this->db->where_in($idField, $existingIds);
			$this->db->delete($masterName);
			if ($this->db->affected_rows() == 0) {
				$error_count++;
			}
		}

		// Determine success or failure based on error and delete counts
		if ($error_count > 0) {
			$res = array('error' => true);
		} else {
			$res = array('error' => false);
		}

		return $res;
	}

	public function saveQueuePriorityData($post, $masterName, $idField = 'priority_id')
	{
		$priorityArray = array();
		$error_count = 0;
		$deleted_count = 0;

		// Get an array of existing IDs
		$existingIds = $this->db->select($idField)->get($masterName)->result_array();
		$existingIds = array_column($existingIds, $idField);

		// Build an array of data to insert or update
		foreach ($post['priorityArray'] as $key => $val) {
			$priorityArray[] = array(
				$idField => $val->priority_id,
				'priority_name' => $val->priority_name,
				'colour_code' => $val->colour_code,
				'tat' => $val->tat_time,
				'order_no' => $val->order_no,
				'is_default' => ($val->is_default == "") ? 0 : 1,
			);
		}

		// Update existing records
		foreach ($priorityArray as $priority) {
			$id = $priority[$idField];
			unset($priority[$idField]);

			if (!empty($id)) {
				$this->db->where($idField, $id);
				$this->db->update($masterName, $priority);

				// if ($this->db->affected_rows() == 0) {
				// 	$error_count++;
				// }
			} else {
				$this->db->insert($masterName, $priority);

				if ($this->db->affected_rows() == 0) {
					$error_count++;
				}
			}

			// Remove ID from existing IDs array if it is present
			if (($key = array_search($id, $existingIds)) !== false) {
				unset($existingIds[$key]);
			}
		}

		// Delete records that were not included in the update
		if (!empty($existingIds)) {
			$this->db->where_in($idField, $existingIds);
			$this->db->delete($masterName);

			if ($this->db->affected_rows() == 0) {
				$error_count++;
			}
		}

		// Determine success or failure based on error and delete counts
		if ($error_count > 0) {
			$res = array('error' => true);
		} else {
			$res = array('error' => false);
		}

		return $res;
	}

	public function saveQueueStatusData($post, $masterName, $idField = 'status_id')
	{
		$statusArray = array();
		$error_count = 0;

		// Get an array of existing IDs
		$existingIds = $this->db->select($idField)->get($masterName)->result_array();
		$existingIds = array_column($existingIds, $idField);

		// Build an array of data to insert or update
		foreach ($post['statusArray'] as $key => $val) {
			$statusArray[] = array(
				$idField => $val->status_id,
				'status_name' => $val->status_name,
				'colour_code' => $val->colour_code,
				'ticket_status_type' => $val->ticket_status_type,
				'order_no' => $val->order_no,
				'is_default' => ($val->is_default == "") ? 0 : 1,
				'is_close_status' => ($val->is_close_status == "") ? 0 : 1,
				'template_id' => $val->template_id,
				'customer_email' => ($val->customer_email == "") ? 0 : 1,
				'customer_sms' => ($val->customer_sms == "") ? 0 : 1,
				'customer_wp' => ($val->customer_wp == "") ? 0 : 1,
				'user_email' => ($val->user_email == "") ? 0 : 1,
				'user_sms' => ($val->user_sms == "") ? 0 : 1,
				'user_wp' => ($val->user_wp == "") ? 0 : 1,
			);
		}

		// Update existing records
		foreach ($statusArray as $status) {
			$id = $status[$idField];
			unset($status[$idField]);

			if (!empty($id)) {
				$this->db->where($idField, $id);
				$this->db->update($masterName, $status);

				// if ($this->db->affected_rows() == 0) {
				// 	$error_count++;
				// }
			} else {
				$this->db->insert($masterName, $status);

				if ($this->db->affected_rows() == 0) {
					$error_count++;
				}
			}

			// Remove ID from existing IDs array if it is present
			if (($key = array_search($id, $existingIds)) !== false) {
				unset($existingIds[$key]);
			}
		}

		// Delete records that were not included in the update
		if (!empty($existingIds)) {
			$this->db->where_in($idField, $existingIds);
			$this->db->delete($masterName);

			if ($this->db->affected_rows() == 0) {
				$error_count++;
			}
		}

		// Determine success or failure based on error and delete counts
		if ($error_count > 0) {
			$res = array('error' => true, 'message' => $this->db->error());
		} else {
			$res = array('error' => false, 'message' => '');
		}

		return $res;
	}

	public function saveQueueEscalationData($post, $masterName, $idField = 'escalation_id')
	{
		$escalationArray = array();
		$error_count = 0;

		// Get an array of existing IDs
		$existingIds = $this->db->select($idField)->get($masterName)->result_array();
		$existingIds = array_column($existingIds, $idField);

		// Build an array of data to insert or update
		foreach ($post['escalationArray'] as $key => $val) {
			$escalationArray[] = array(
				$idField => $val->escalation_id,
				'priority_id' => $val->priority_id,
				'days' => $val->days,
				'hours' => $val->hours,
				'minute' => $val->minute,
				'emails' => $val->email,
				'mobile_no' => $val->mobile_no,
				'shift_id' => $val->shift_id,
				'template_id' => $val->template_id,
				'send_email' => ($val->send_email != "") ? 1 : 0,
				'send_sms' => ($val->send_sms != "") ? 1 : 0,
				'active' => ($val->active != "") ? 1 : 0,
				'level' => $val->level,
			);
		}
		// Update existing records
		foreach ($escalationArray as $escalation) {
			$id = $escalation[$idField];
			unset($escalation[$idField]);

			if (!empty($id)) {
				$this->db->where($idField, $id);
				$this->db->update($masterName, $escalation);

				// if ($this->db->affected_rows() == 0) {
				// 	$error_count++;
				// }
			} else {
				$this->db->insert($masterName, $escalation);

				if ($this->db->affected_rows() == 0) {
					$error_count++;
				}
			}

			// Remove ID from existing IDs array if it is present
			if (($key = array_search($id, $existingIds)) !== false) {
				unset($existingIds[$key]);
			}
		}

		// Delete records that were not included in the update
		if (!empty($existingIds)) {
			$this->db->where_in($idField, $existingIds);
			$this->db->delete($masterName);

			if ($this->db->affected_rows() == 0) {
				$error_count++;
			}
		}

		// Determine success or failure based on error and delete counts
		if ($error_count > 0) {
			$res = array('error' => true);
		} else {
			$res = array('error' => false);
		}

		return $res;
	}

	public function saveQueueRuledata($masterName, $pKey = 'rule_id', $post, $data)
	{
		$data = is_array($data) ? $data : (array) $data;
		$id = $data[$pKey];
		$queue_id = $post['queue_id'];
		unset($post['queue_id']);
		unset($post[$pKey]);
		if (!empty($masterName)) {
			if (empty($id)) { // create 
				$this->db->insert($masterName, $data);
				$id = $this->db->insert_id();

				foreach ($post['conditionArray'] as $key => $val) {
					$conditionarray[] = array(
						'evaluate_on' => $val->evaluate_on,
						'operator' => $val->operator,
						'evaluate_value' => $val->evaluate_value,
						'rule_id' =>  $id,
					);
				}
				$this->db->insert_batch("tms_queue_rule_condition_$queue_id", $conditionarray);

				foreach ($post['actionArray'] as $key => $val) {
					$actionnarray[] = array(
						'action' => $val->action,
						'action_value' => $val->action_value,
						'rule_id' =>  $id,
					);
				}
				$this->db->insert_batch("tms_queue_rule_action_$queue_id", $actionnarray);
			} else { // edit 
				$this->db->where($pKey, $id)->update($masterName, $data);

				$this->db->where('rule_id', $id);
				$this->db->delete("tms_queue_rule_condition_$queue_id");

				$this->db->where('rule_id', $id);
				$this->db->delete("tms_queue_rule_action_$queue_id");

				foreach ($post['conditionArray'] as $key => $val) {
					$conditionarray[] = array(
						'evaluate_on' => $val->evaluate_on,
						'operator' => $val->operator,
						'evaluate_value' => $val->evaluate_value,
						'rule_id' =>  $id,
					);
				}
				$this->db->insert_batch("tms_queue_rule_condition_$queue_id", $conditionarray);

				foreach ($post['actionArray'] as $key => $val) {
					$actionnarray[] = array(
						'action' => $val->action,
						'action_value' => $val->action_value,
						'rule_id' =>  $id,
					);
				}
				$this->db->insert_batch("tms_queue_rule_action_$queue_id", $actionnarray);
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



	// function savequeue($masterName, $data, $pKey = 'id', $post)
	// {
	// 	$table = array_key_exists($masterName, $this->tableName) ? $this->tableName[$masterName] : '';
	// 	if (!empty($table)) {
	// 		$data = is_array($data) ? $data : (array) $data;
	// 		$id = $data[$pKey];
	// 		unset($data[$pKey]);
	// 		if (empty($id)) { // create 
	// 			$this->db->insert($table, $data);
	// 			$id = $this->db->insert_id();

	// 			$sql = file_get_contents(FCPATH . "/assets/queue_db_script/queue_db_script.sql");
	// 			$sqls = explode(';', $sql);
	// 			array_pop($sqls);
	// 			foreach ($sqls as $statement) {
	// 				$query = str_replace('##QID##', $id, $statement);
	// 				$query = str_replace('##QID##', $id, $query) . ";";
	// 				$this->db->query($query);
	// 			}
	// 			$this->db->close();
	// 			$this->load->database();

	// 			// $DynamicData['queue_name'] = $data['queue'];
	// 			// $DynamicData['queue_id'] = $id;
	// 			// $DynamicData['status'] = $data['status'];
	// 			// $DynamicData['createdOn'] = date('Y-m-d H:i:s');
	// 			// $DynamicData['createdBy'] = $data['created_by'];


	// 			// $this->db->insert('tms_dynamic_form', $DynamicData);

	// 			foreach ($post['emailArray'] as $key => $val) {
	// 				$emailarray[] = array(
	// 					'incoming_email_account' => $val->incoming_email_account,
	// 					'outgoing_email_account' => $val->outgoing_email_account,
	// 					'queue_id' =>  $id,
	// 				);
	// 			}
	// 			$this->db->insert_batch('tms_queue_email', $emailarray);

	// 			foreach ($post['templateArray'] as $key => $val) {
	// 				$templatearray[] = array(
	// 					'action' => $val->action,
	// 					'template' => $val->template,
	// 					'customer_email' => $val->customer_email,
	// 					'consumer_sms' => $val->consumer_sms,
	// 					'customer_wp' => $val->customer_wp,
	// 					'user_email' => $val->user_email,
	// 					'user_sms' => $val->user_sms,
	// 					'user_wp' => $val->user_wp,
	// 					'queue_id' =>  $id,
	// 				);
	// 			}
	// 			$this->db->insert_batch('tms_queue_template', $templatearray);
	// 		} else { // edit 
	// 			$this->db->where($pKey, $id)->update($table, $data);


	// 			// $DynamicData['queue_name'] = $data['queue'];
	// 			// $DynamicData['queue_id'] = $id;
	// 			// $DynamicData['status'] = $data['status'];
	// 			// $DynamicData['createdOn'] = date('Y-m-d H:i:s');
	// 			// $DynamicData['createdBy'] = $data['modified_by'];

	// 			// $this->db->where('queue_id', $id)->update('tms_dynamic_form', $DynamicData);

	// 			// $this->db->insert_batch('tms_dynamic_form', $DynamicData);

	// 			$this->db->where('queue_id', $id);
	// 			$this->db->delete('tms_queue_email');

	// 			$this->db->where('queue_id', $id);
	// 			$this->db->delete('tms_queue_template');

	// 			foreach ($post['emailArray'] as $key => $val) {
	// 				$emailarray[] = array(
	// 					'incoming_email_account' => $val->incoming_email_account,
	// 					'outgoing_email_account' => $val->outgoing_email_account,
	// 					'queue_id' =>  $id,
	// 				);
	// 			}
	// 			$this->db->insert_batch('tms_queue_email', $emailarray);

	// 			foreach ($post['templateArray'] as $key => $val) {
	// 				$templatearray[] = array(
	// 					'action' => $val->action,
	// 					'template' => $val->template,
	// 					'customer_email' => $val->customer_email,
	// 					'consumer_sms' => $val->consumer_sms,
	// 					'customer_wp' => $val->customer_wp,
	// 					'user_email' => $val->user_email,
	// 					'user_sms' => $val->user_sms,
	// 					'user_wp' => $val->user_wp,
	// 					'queue_id' =>  $id,
	// 				);
	// 			}
	// 			$this->db->insert_batch('tms_queue_template', $templatearray);
	// 		}
	// 		$res = array('error' => false, 'id' => $id);
	// 	} else {
	// 		$res = array('error' => true, 'id' => 0);
	// 	}
	// 	return $res;
	// }

	public function savequeue($masterName, $data, $pKey = 'id', $post)
	{
		$this->db->trans_start();

		// Perform database operations here
		$table = array_key_exists($masterName, $this->tableName) ? $this->tableName[$masterName] : '';
		if (!empty($table)) {
			$data = is_array($data) ? $data : (array) $data;
			$id = $data[$pKey];
			unset($data[$pKey]);
			if (empty($id)) { // create 
				$this->db->insert($table, $data);
				$id = $this->db->insert_id();
				$sql = file_get_contents(FCPATH . "/assets/queue_db_script/queue_db_script.sql");
				$sqls = explode(';', $sql);
				array_pop($sqls);

				$emailArray = $post['emailArray'];
				$count = count($emailArray);

				for ($i = 0; $i < $count; $i++) {
					for ($j = $i + 1; $j < $count; $j++) {
						if ($emailArray[$i]->incoming_email_account == $emailArray[$j]->incoming_email_account) {
							return array($emailArray[$i]);
						}
					}
				}


				foreach ($post['emailArray'] as $key => $val) {
					$this->db->select('*');
					$this->db->from('tms_queue_email');
					$this->db->where('incoming_email_account', (int)$val->incoming_email_account);
					$res = $this->db->get()->row();
					if (isset($res->incoming_email_account)) {
						return $res;
					}
				}


				foreach ($sqls as $statement) {
					$query = str_replace('##QID##', $id, $statement);
					$query = str_replace('##QID##', $id, $query) . ";";
					$this->db->query($query);
				}
				$this->db->close();
				$this->load->database();

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

				$emailArray = $post['emailArray'];
				$count = count($emailArray);

				for ($i = 0; $i < $count; $i++) {
					for ($j = $i + 1; $j < $count; $j++) {
						if ($emailArray[$i]->incoming_email_account == $emailArray[$j]->incoming_email_account) {
							return array($emailArray[$i]);
						}
					}
				}

				foreach ($post['emailArray'] as $key => $val) {
					$this->db->select('*');
					$this->db->from('tms_queue_email');
					$this->db->where('incoming_email_account', (int)$val->incoming_email_account);
					$res = $this->db->get()->row();
					if (isset($res->incoming_email_account)) {
						return $res;
					}
				}

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

		// End the transaction
		if ($this->db->trans_status() === false) {
			$this->db->trans_rollback();
			$res = array('error' => true, 'id' => 0);
		} else {
			$this->db->trans_commit();
			$res = array('error' => false, 'id' => $id);
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
		// $rules[] = array('field' => 'outgoing_mail_server_port', 'label' => 'Outgoing Mail Server  Port', 'rules' => 'required');
		// $rules[] = array('field' => 'incoming_mail_server_port', 'label' => 'Incoming Mail  Port', 'rules' => 'required');
		$rules[] = array('field' => 'outgoing_mail_server', 'label' => 'Outgoing  Mail Server', 'rules' => 'required');
		// $rules[] = array('field' => 'outgoing_mail_server_port', 'label' => 'Outgoing Mail Server  Port', 'rules' => 'required');
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
			case 'field-group':
				return $this->masterFieldGroupRules();
				break;
			case 'conversation-type':
				return $this->masterConversationTypeRules();
				break;
			default:
				return $this->masterRules();
				break;
		}
	}

	public function getTicketTableRules()
	{
		$rules[] = array('field' => 'customer_name', 'label' => 'Customer Name', 'rules'  => 'required');
		$rules[] = array('field' => 'customer_mobile_no', 'label' => 'Customer Mobile No', 'rules'  => 'required');
		$rules[] = array('field' => 'queue_id', 'label' => 'Queue', 'rules' => 'required');
		$rules[] = array('field' => 'from_email', 'label' => 'From Email', 'rules' => 'required');
		$rules[] = array('field' => 'to_email', 'label' => 'Email To', 'rules' => 'required');
		$rules[] = array('field' => 'subject', 'label' => 'Subject', 'rules' => 'required');
		$rules[] = array('field' => 'priority_id', 'label' => 'Priority', 'rules' => 'required');
		$rules[] = array('field' => 'conversation', 'label' => 'Conversation', 'rules' => 'required');
		$rules[] = array('field' => 'status_id', 'label' => 'Ticket Status', 'rules' => 'required');
		return $rules;
	}


	function masterFieldGroupRules()
	{

		$rules[] = array(
			'field' => 'title',
			'label' => '',
			'rules' => array('required', array('msg', array($this, 'uniqueTitleValidate'))),
			'errors' => array('msg' => 'Field Group  already exists')
		);
		$rules[] = array(
			'field' => 'order_no',
			'label' => '',
			'rules' => array('required', array('msg', array($this, 'uniqueOrdeValidate'))),
			'errors' => array('msg' => 'Order No already exists')
		);
		$rules[] = array('field' => 'field_type', 'label' => 'Field Type', 'rules' => 'required');
		return $rules;
	}

	// function masterTicketRules()
	// {
	// 	$rules[] = array('field' => 'queue_id', 'label' => 'Queue', 'rules' => 'required');
	// 	$rules[] = array('field' => 'email_from', 'label' => ' Email From', 'rules' => 'required');
	// 	$rules[] = array('field' => 'email_to', 'label' => 'Email To', 'rules' => 'required');
	// 	$rules[] = array('field' => 'subject', 'label' => 'Subject', 'rules' => 'required');
	// 	$rules[] = array('field' => 'priority_id', 'label' => 'Priority', 'rules' => 'required');
	// 	$rules[] = array('field' => 'conversation', 'label' => 'Conversation', 'rules' => 'required');
	// 	$rules[] = array('field' => 'ticket_status', 'label' => 'Ticket Status', 'rules' => 'required');
	// 	return $rules;
	// }


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

	public function masterConversationTypeRules()
	{
		$rules[] = array(
			'field' => 'title',
			'label' => '',
			'rules' => array('required', array('msg', array($this, 'uniqueTitleValidate'))),
			'errors' => array('msg' => 'Conversation Type already exists')
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
		// $rules[] = array('field' => 'priority_id', 'label' => 'Priority', 'rules' => 'required');
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
			'rules' => array('required', array('msg', array($this, 'uniqueEmailValidateforuser'))),
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
			$is_exist = $this->db->where('user_name', trim($post['user_name']))->where_not_in('id', array(trim($post['id'])))->get($tableName)->row();
		} else {
			$is_exist = $this->db->where('user_name', trim($post['user_name']))->get($tableName)->row();
		}
		return ($is_exist) ? false : true;
	}

	public function uniqueNameValidate()
	{
		$post = (array) json_decode(file_get_contents('php://input'));
		$masterName = $post['master_name'];
		$tableName = array_key_exists($masterName, $this->tableName) ? $this->tableName[$masterName] : '';
		if (!empty($post['id'])) {
			$is_exist = $this->db->where('name', trim($post['name']))->where_not_in('id', array(trim($post['id'])))->get($tableName)->row();
		} else {
			$is_exist = $this->db->where('name', trim($post['name']))->get($tableName)->row();
		}
		return ($is_exist) ? false : true;
	}

	public function uniqueEmailValidate()
	{
		$post = (array) json_decode(file_get_contents('php://input'));
		$masterName = $post['master_name'];
		$tableName = array_key_exists($masterName, $this->tableName) ? $this->tableName[$masterName] : '';
		if (!empty($post['id'])) {
			$is_exist = $this->db->where('email', trim($post['email']))->where_not_in('id', array(trim($post['id'])))->get($tableName)->row();
		} else {
			$is_exist = $this->db->where('email', trim($post['email']))->get($tableName)->row();
		}
		return ($is_exist) ? false : true;
	}


	public function uniqueEmailValidateforuser()
	{
		$post = (array) json_decode(file_get_contents('php://input'));
		$masterName = $post['master_name'];
		$tableName = array_key_exists($masterName, $this->tableName) ? $this->tableName[$masterName] : '';
		if (!empty($post['id'])) {
			$is_exist = $this->db->where('email', trim($post['email']))->where('id !=', trim($post['id']))->get($tableName)->row();
		} else {
			$is_exist = $this->db->where('email', trim($post['email']))->get($tableName)->row();
		}
		return ($is_exist) ? false : true;
	}

	public function uniqueMobilenoValidate()
	{
		$post = (array) json_decode(file_get_contents('php://input'));
		$masterName = $post['master_name'];
		$mobile_no =  $post['mobile_no'];
		$tableName = array_key_exists($masterName, $this->tableName) ? $this->tableName[$masterName] : '';
		if (!empty($post['id'])) {
			$is_exist = $this->db->where('mobile_no', trim($post['mobile_no']))->where_not_in('id', array(trim($post['id'])))->get($tableName)->row();
		} else {
			$is_exist = $this->db->where('mobile_no', trim($post['mobile_no']))->get($tableName)->row();
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
			$is_exist = $this->db->where('title', trim($post['title']))->where_not_in('id', array(trim($post['id'])))->get($tableName)->row();
		} else {
			$is_exist = $this->db->where('title', trim($post['title']))->get($tableName)->row();
		}

		// print_r($is_exist); die; 
		return ($is_exist) ? false : true;
	}

	function uniquePaginationValidate()
	{
		$post = (array) json_decode(file_get_contents('php://input'));
		if (!empty($post['id'])) {
			$is_exist = $this->db->where('record', trim($post['record']))->where_not_in('id', array(trim($post['id'])))->get('tms_pagination_master')->row();
		} else {
			$is_exist = $this->db->where('record', trim($post['record']))->get('tms_pagination_master')->row();
		}
		return ($is_exist) ? false : true;
	}

	function uniqueCategoryValidate()
	{
		$post = (array) json_decode(file_get_contents('php://input'));
		$masterName = $post['master_name'];
		$category_id = trim($post['category_id']);
		$tableName = array_key_exists($masterName, $this->tableName) ? $this->tableName[$masterName] : '';
		if (!empty($post['id'])) {
			$is_exist = $this->db->where("title = '$post[title]' and category_id = '$category_id'")->where_not_in('id', array(trim($post['id'])))->get($tableName)->row();
		} else {
			$is_exist = $this->db->where("title = '$post[title]' and category_id = '$category_id'")->get($tableName)->row();
		}
		return ($is_exist) ? false : true;
	}


	function uniqueOrdeValidate()
	{
		$post = (array) json_decode(file_get_contents('php://input'));
		$masterName = $post['master_name'];
		$orderno = trim($post['order_no']);
		$tableName = array_key_exists($masterName, $this->tableName) ? $this->tableName[$masterName] : '';
		if (!empty($post['id'])) {
			$is_exist = $this->db->where("order_no = '$orderno'")->where_not_in('id', array(trim($post['id'])))->get($tableName)->row();
		} else {
			$is_exist = $this->db->where("order_no = '$orderno'")->get($tableName)->row();
		}
		return ($is_exist) ? false : true;
	}


	function uniqueQueueValidate()
	{
		$post = (array) json_decode(file_get_contents('php://input'));
		$masterName = $post['master_name'];
		$queuename = trim($post['queue']);
		$tableName = array_key_exists($masterName, $this->tableName) ? $this->tableName[$masterName] : '';
		if (!empty($post['id'])) {
			$is_exist = $this->db->where("queue = '$queuename'")->where_not_in('id', array(trim($post['id'])))->get($tableName)->row();
		} else {
			$is_exist = $this->db->where("queue = '$queuename'")->get($tableName)->row();
		}
		return ($is_exist) ? false : true;
	}

	function uniquetemplateValidate()
	{
		$post = (array) json_decode(file_get_contents('php://input'));
		$masterName = $post['master_name'];
		$templatename = trim($post['template']);
		$tableName = array_key_exists($masterName, $this->tableName) ? $this->tableName[$masterName] : '';
		if (!empty($post['id'])) {
			$is_exist = $this->db->where("template = '$templatename'")->where_not_in('id', array(trim($post['id'])))->get($tableName)->row();
		} else {
			$is_exist = $this->db->where("template = '$templatename'")->get($tableName)->row();
		}
		return ($is_exist) ? false : true;
	}

	public function getQueueRulesData($rule_id, $queue_id, $primaryKey = 'rule_id')
	{
		$select = array('*');
		$this->db->select($select);
		$this->db->where(array($primaryKey => $rule_id));
		$this->db->from("tms_queue_rule_master_$queue_id");
		$query = $this->db->get();
		$result = $query->row_array();


		$select = array('*');
		$this->db->select($select);
		$this->db->where('rule_id', $rule_id);
		$this->db->from("tms_queue_rule_condition_$queue_id");
		$ruleConditionQry = $this->db->get();
		$ruleConditionResult = $ruleConditionQry->result_array();

		$select = array('*');
		$this->db->select($select);
		$this->db->where('rule_id', $rule_id);
		$this->db->from("tms_queue_rule_action_$queue_id");
		$ruleActionQry = $this->db->get();
		$ruleActionResult = $ruleActionQry->result_array();

		return  array('result' => $result, 'ruleConditionResult' => $ruleConditionResult, 'ruleActionResult' => $ruleActionResult);
	}

	function saveTicketData($masterName, $postData, $data, $pKey = 'ticket_unique_id')
	{
		$this->load->library('Dynamicformcontent');
		$table = $masterName;
		if (!empty($table)) {
			$this->db->trans_begin();
			$data = is_array($data) ? $data : (array) $data;
			$queue_id = $data['queue_id'];
			$dynData	= $this->dynamicformcontent->setDynamicFieldDataArray($queue_id, $data);
			$data = array_merge($data, $dynData['data']);

			if (!empty($data['assigned_to'])) {
				$data['is_assigned'] = 1;
			} else {
				$data['is_assigned'] = 0;
			}
			$id = $data[$pKey];
			unset($data[$pKey]);
			if (empty($id)) { // create 
				$this->db->insert($table, $data);
				$id = $this->db->insert_id();
				$this->insertConversationData($postData, $data, $id);
				$conversation_id = $this->db->insert_id();
				$this->insertAttachmentData($postData, $data, $id, $conversation_id);
			} else { // edit 
				$this->db->where($pKey, $id)->update($table, $data);
			}
			if ($this->db->trans_status() === FALSE) {
				$this->db->trans_rollback();
			} else {
				$this->db->trans_commit();
			}
			$res = array('error' => false, 'id' => $id);
		} else {
			$res = array('error' => true, 'id' => 0);
		}
		return $res;
	}

	public function insertConversationData($postData, $data, $ticket_id)
	{
		$masterName = "tms_conversation_master_" . $data['queue_id'];
		$postDataArr = array();
		$postDataArr[] = $postData;
		$conversationData = array();
		foreach ($postDataArr as $key => $val) {
			$conversationData[] = array(
				'conversation_date' => date('Y-m-d H:i:s'),
				'conversation_type' => 'new ticket',
				'ticket_unique_id' => $ticket_id,
				'ticket_id' => $data['ticket_id'],
				'customer_mobile_no' => $val['customer_mobile_no'],
				'email_uid' => (isset($val['email_uid']) ? $val['email_uid'] : NULL),
				'account_id' => (isset($val['account_id']) ? $val['account_id'] : NULl),
				'from_email' => $val['from_email'],
				'to_email' => (isset($val['to_email']) ? $val['to_email'] : NULl),
				'cc_email' => (isset($val['cc_email']) ? $val['cc_email'] : NULl),
				'bcc_email' => (isset($val['bcc_email']) ? $val['bcc_email'] : NULl),
				'subject' => (isset($val['subject']) ? $val['subject'] : NULl),
				'sms_conversation' => NULl,
				'category_id' => (isset($val['category_id']) ? $val['category_id'] : NULl),
				'subcategory_id' => (isset($val['subcategory_id']) ? $val['subcategory_id'] : NULl),
				'status_id' => (isset($val['status_id']) ? $val['status_id'] : NULl),
				'priority_id' => (isset($val['priority_id']) ? $val['priority_id'] : NULl),
				'queue_id' => (isset($val['queue_id']) ? $val['queue_id'] : NULl),
				'login_id' => (isset($val['assigned_to']) ? $val['assigned_to'] : NULl),
				'email_status' => NULL,
				'sms_status' => NULL,
				'no_of_try' => NULL,
				'max_try' => NULL,
				'email_message_id' => NULL,
				'sms_no_of_try' => NULL,
				'sms_max_try' => NULL,
				'fetch_xml_uid' => NULL,
				'hold_date' => NULL,
				'json_file_name' => NULL
			);
		}
		return $this->db->insert_batch($masterName, $conversationData);
	}

	public function insertAttachmentData($postData, $data, $ticket_id, $conversation_id)
	{
		$masterName = "tms_conversation_attachements_" . $data['queue_id'];
		$postDataArr = array();
		$postDataArr[] = $postData;
		$conversationAttachmentData = array();
		$queue_name =  $this->getScalerCol("queue", "tms_queue_master", "id = $postData[queue_id]")->queue;
		$fcpath = str_replace("\\", "/", FCPATH);
		$path = "$fcpath/uploads/ticketAttachement/" . strtolower($queue_name) . "-" . $postData['queue_id'];

		if (count($postDataArr[0]['attachmentArray']) > 0) {
			foreach ($postDataArr[0]['attachmentArray'] as $key => $val) {
				$conversationAttachmentData[] = array(
					'ticket_id' => $data['ticket_id'],
					'ticket_unique_id' => $ticket_id,
					'conversation_id' => $conversation_id,
					'content_type' => $val['type'],
					'attachement_filepath' => $path . $val['name']
				);
			};
		}
		if (count($conversationAttachmentData) > 0) {
			return $this->db->insert_batch($masterName, $conversationAttachmentData);
		}
	}

	public function getScalerCol($column, $table, $where, $masterDb = NULL)
	{
		$this->db->select($column);
		if ($masterDb == 1) {
			$this->db->from($table);
		} else {
			$this->db->from($table);
		}
		$this->db->where($where, NULL, false);
		return $this->db->get()->row();
	}

	public function ticketSeries($queue_id)
	{
		$this->db->select('ticket_prefix');
		$this->db->from('tms_queue_master');
		$this->db->where('id', $queue_id);
		return $this->db->get()->row()->ticket_prefix;
	}

	public function ticketMoveToJunk($data)
	{
		$this->db->where(array('ticket_unique_id' => $data['ticket_unique_id']));
		$this->db->update($data['master_name'], array('is_junk' => 1));
		return $this->db->affected_rows();
	}
}
