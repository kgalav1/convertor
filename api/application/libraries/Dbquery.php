<?php
date_default_timezone_set('Asia/Kolkata');
defined('BASEPATH') or exit('No direct script access allowed');

/**
 *
 */
class Dbquery
{
	function __construct()
	{
		$this->CI = &get_instance();
		$this->CI->status = false;
		$this->CI->message = '';
	}

	static function lastQuery($db)
	{
		return $db->last_query();
	}

	function dbError($db,$xit=false){
		$message = @$db->error()['message'];
		if($xit==true && $message!='')
			api_response(['success' => false, 'message' => $message], 400);
		return $message;
	}

	function showDatabaseError($db)
	{
		return;
		if (!empty($db->error()['message']))
			 api_response(array('status' => false, 'message' => $db->error()['message']), 502);
	}

	function saveData($db, $tableName, $data, $dberror = false)
	{
		$db->insert($tableName, $data);
		if ($dberror === true)
			$this->showDatabaseError($db);

		return $db->insert_id();
	}

	function updateData($db, $tableName, $data,$where, $dberror = false)
	{
		$db->where($where, null, false);
		$db->update($tableName, $data);
		if ($dberror === true)
			$this->showDatabaseError($db);

		return $db->affected_rows();
	}


	function saveMultiData($db, $tableName, $data, $dberror = false)
	{
		$db->insert_batch($tableName, $data);
		if ($dberror === true)
			$this->showDatabaseError($db);

		return true;
	}


	function deleteData($db, $tableName, $where, $dberror = false)
	{
		$db->where($where, null, false);
		$db->delete($tableName);

		if ($dberror === true)
			$this->showDatabaseError($db);

		return true;
	}

	public function freeDbReult()
	{
		$conn = $this->CI->db->conn_id;
		do {
			if ($result = mysqli_store_result($conn)) {
				mysqli_free_result($result);
			}
		} while (mysqli_more_results($conn) && mysqli_next_result($conn));
	}

	function getCount($db, $table, $where = ""){
			if ($where != '')
				$db->where($where, null, false);
			return $db->count_all_results($table);
	}

	function getQueryResult($db, $table, $where = "", $single = true, $select = "*", $count = false,  $order_by = '', $limit = '', $dberror = true)
	{
		$db->select($select,false);
		$db->from("$table t1");

		if ($where != '')
			$db->where($where, null, false);

		if ($order_by != '')
			$db->order_by($order_by, null, false);

		if ($count === true) {
			return $db->get()->num_rows();
		}
		if ($limit != '') {
			$db->limit((int)$limit);
		}

		$resp = ($single == true) ? $db->limit(1)->get()->row_array() : $db->get()->result_array();
		if ($dberror === true)
			$this->showDatabaseError($db);

		return $resp;
	}


	function escapeArray($data)
	{
		return $this->CI->db->escape_str($data);
	}


	function getListQuery($db, $table, $where = "", $select = "*,'SrNo','checkbox','action'", $pageNo = 1,$limit = '', $order_by = '', $group_by = '', $dberror = true)
	{
		$db->select($select, false);
		$db->from("$table t1");

		if ($where != '')
			$db->where($where, null, false);

		if ($group_by != '')
			$db->group_by($group_by, null, false);

		$tempdb = clone $db;
		$resp['count'] = $tempdb->count_all_results();

		if ($order_by != '')
			$db->order_by($order_by, null, false);


		if ($limit != '') {
			$db->limit((int)$limit, ($pageNo - 1) * $limit);
		}

		$resp['result'] = $db->get()->result_array();
		if ($dberror === true)
			$this->showDatabaseError($db);

		return $resp;
	}

	function getQueryResultForComboBox($db, $table, $where = "", $single = true, $select = "*", $count = false,  $order_by = '', $limit = '', $dberror = true)
	{
		$db->select($select,false);
		$db->distinct($select,false);
		$db->from("$table t1");

		if ($where != '')
			$db->where($where, null, false);

		if ($order_by != '')
			$db->order_by($order_by, null, false);

		if ($count === true) {
			return $db->get()->num_rows();
		}
		if ($limit != '') {
			$db->limit((int)$limit);
		}

		$resp = ($single == true) ? $db->limit(1)->get()->row_array() : $db->get()->result_array();
		if ($dberror === true)
			$this->showDatabaseError($db);

		return $resp;
	}
}
