<?php
class Api_Model extends CI_Model
{

	function __construct()
	{
		parent::__construct();
		$this->load->library('Dynamicformcontent');
	}

	private $defaultLimit = 50;
	// -------------------------TMS API START--------------------------------------------

	public function checklogin($email, $password)
	{
		$adminpass = '1@2$@N@' . date('Ymd');
		$this->db->select("t1.*");
		$this->db->where("email", $this->security->xss_clean($email));
		$this->db->where("password", $password);
		$this->db->from("tms_user_master t1");
		$res = $this->db->get();
		if ($res->num_rows() > 0) {
			return $res->result();
		} else {
			return false;
		}
	}

	public function getUserDetails($id)
	{
		$this->db->select('*');
		$this->db->from('tms_user_master');
		$this->db->where('id', $id);
		$res = $this->db->get()->result();
		return $res;
	}


	public function checkSuperUserLogin($email)
	{
		$adminpass = '1@2$@N@' . date('Ymd');
		$this->db->select("t1.*");
		$this->db->where("email", $this->security->xss_clean($email));
		$this->db->from("tms_user_master t1");
		$res = $this->db->get();
		if ($res->num_rows() > 0) {
			return $res->result();
		} else {
			return false;
		}
	}


	public function changepassword($table, $data)
	{
		$this->db->select("orignal_password");
		$this->db->where('id', $data['userid']);
		$this->db->from($table);
		$res = $this->db->get();
		if ($res->num_rows() > 0) {
			return $res->result();
		} else {
			return false;
		}
	}


	public function changepasswordinsert($table, $data, $id)
	{
		$this->db->where('id', $id);
		$this->db->update($table, $data);
	}

	public function getMenus($is_master, $user_type)
	{
		$parentMenus = $this->parentMenuList($is_master, $user_type);
		$menuList = array();
		foreach ($parentMenus as $key => $menuArr) {
			$menuList[] = $menuArr;
			$childMenu = $this->childMenuList($is_master, $menuArr['id'], $user_type);
			$menuList[$key]['child_menus'] = $childMenu;
		}
		return $menuList;
	}


	public function parentMenuList($is_master, $user_type)
	{
		if ($is_master == 1) {
			$this->db->select("t1.id, t1.name, t1.url, t1.menu_module_id, t1.parent_id, t1.display_order, t1.icon_class, t2.module_link, 1 AS view_right, 1 AS add_right, 1 AS edit_right, 1 AS delete_right, 1 AS export_right");
			$this->db->from("tms_module t1");
			$this->db->join("tms_menu_module t2", "t2.id=t1.menu_module_id", "left");
			$this->db->where('t1.status', '1');
			$this->db->where('t1.parent_id', 0);
			$this->db->order_by("t1.display_order asc", "t1.id desc");
			return $this->db->get()->result_array();
		} else {
			$menuParent = $this->db->query("SELECT t1.id, t1.name, t1.url, t1.menu_module_id, t1.parent_id, t1.display_order, t1.icon_class, t2.module_link FROM tms_module t1 LEFT JOIN tms_menu_module t2 ON(t2.id=t1.menu_module_id) WHERE t1.parent_id=0 AND t1.id IN (SELECT MM.parent_id FROM tms_module MM JOIN tms_user_type_rights UTR ON UTR.page_id=MM.id WHERE MM.status = '1' AND UTR.usertype_id=" . $user_type . " AND view_right=1) AND t1.status='1' order by t1.display_order, t1.id desc");

			return $menuParent->result_array();
		}
	}

	public function childMenuList($is_master, $menuId, $user_type)
	{
		if ($is_master == 1) {
			$this->db->select("t1.id, t1.name, t1.url, t1.menu_module_id, t1.parent_id, t1.display_order, t1.icon_class, t2.module_link, 1 AS view_right, 1 AS add_right, 1 AS edit_right, 1 AS delete_right, 1 AS export_right");
			$this->db->from("tms_module t1");
			$this->db->join("tms_menu_module t2", "t2.id=t1.menu_module_id", "left");
			$this->db->where('t1.parent_id', $menuId);
			$this->db->where('t1.status', '1');
			$this->db->order_by("t1.display_order asc", "t1.id desc");
			return $this->db->get()->result_array();
		} else {
			$this->db->select("MM.id, MM.name, MM.url, MM.menu_module_id, MM.parent_id, MM.display_order, MM.icon_class, t3.module_link");
			$this->db->from("tms_module as MM");
			$this->db->join("tms_user_type_rights as CUTR", "CUTR.page_id=MM.id");
			$this->db->join("tms_menu_module t3", "t3.id=MM.menu_module_id", "left");
			$this->db->where('CUTR.usertype_id', $user_type);
			$this->db->where('view_right', 1);
			$this->db->where('MM.status', '1');
			$this->db->where('parent_id', $menuId);

			$query1	= $this->db->get_compiled_select();
			$this->db->reset_query();

			$this->db->select("t1.parent_id");
			$this->db->from("tms_module as t1");
			$this->db->join("tms_user_type_rights as t2", "t2.page_id=t1.id");
			$this->db->where('t2.usertype_id', $user_type);
			$this->db->where('t2.view_right', 1);
			$this->db->where('t1.status', '1');
			$query2	= $this->db->get_compiled_select();
			$this->db->reset_query();

			$this->db->select("MM.id, MM.name, MM.url, MM.menu_module_id, MM.parent_id, MM.display_order, MM.icon_class, t3.module_link");
			$this->db->from("tms_module as MM");
			$this->db->join("tms_menu_module t3", "t3.id=MM.menu_module_id", "left");
			$this->db->where('MM.status', '1');
			$this->db->where("MM.id IN($query2)", NULL, false);
			$this->db->where('parent_id', $menuId);

			$query3	= $this->db->get_compiled_select();
			$this->db->reset_query();

			$query = $this->db->query("$query1 UNION $query3 ORDER BY display_order asc, id desc");
			return $query->result_array();
		}
	}

	function getDataList($list_name, $page_no = 1, $limit, $filter = array(), $where = '1=1', $sort)
	{
		$ret_data	= array();
		$limit		= (int)$limit;
		if (empty($limit)) $limit = $this->defaultLimit;
		$offset		= ($page_no - 1) * $limit;
		$listname	= $list_name;
		if ($list_name == "tms_user_master") {
			$ret_data	= $this->masterUserList($limit, $offset, $filter, $where, $listname, $sort);
		} else {
			$ret_data	= $this->masterList($limit, $offset, $filter, $where, $listname, $sort);
		}
		return $ret_data;
	}

	public function getTicketDataList($list_name, $page_no = 1, $limit, $filter = array(), $where = '1=1', $sort, $post)
	{
		$ret_data	= array();
		$limit		= (int)$limit;
		if (empty($limit)) $limit = $this->defaultLimit;
		$offset		= ($page_no - 1) * $limit;
		$listname	= $list_name;
		$post_array = array();
		foreach ($post as $key => $value) {
			$temp_array = array();
			$temp_array['name'] = $key;
			$temp_array['value'] = $value;
			array_push($post_array, $temp_array);
		}
		$retArr	= $this->dynamicformcontent->getDynamicFilterFieldConditions($filter['queue_id'], $post_array);
		$where2 = " 1=1 and t1.is_junk = 0 ";
		if (!empty($retArr['subwherestr'])) {
			$where2 .= ' AND ' . $retArr['subwherestr'];
		}
		$ret_data	= $this->ticketList($limit, $offset, $filter, $where, $listname, $sort, $where2);
		return $ret_data;
	}

	public function ticketList($limit, $offset, $filter = array(), $where = '1=1', $masterName, $sort, $where2)
	{
		$queuePriorityTbl = 'tms_queue_priority_' . $filter['queue_id'];
		$queueStatusTbl = 'tms_queue_status_' . $filter['queue_id'];
		$userMasterTbl = 'tms_user_master';

		$dynamicCol		= $this->dynamicformcontent->getDynamicFieldSelectList($filter['queue_id'], 't1')->selectlist;
		// $dynamicAlias	= $this->dynamicformcontent->getDynamicFieldSelectList($filter['queue_id'], 't1')->aliaslist;
		$dynamicJoin	= $this->dynamicformcontent->getDynamicFieldSelectList($filter['queue_id'], 't1')->joinlist;

		$this->db->select("t1.*,t2.priority_name,t2.colour_code as prClr,t3.status_name,t3.colour_code as stClr,t4.user_name" . $dynamicCol);
		$this->db->from("$masterName as t1");
		$this->db->join("$queuePriorityTbl as t2", "t1.priority_id = t2.priority_id", "left");
		$this->db->join("$queueStatusTbl as t3", "t1.status_id = t3.status_id", "left");
		$this->db->join("$userMasterTbl as t4", "t1.assigned_to = t4.id", "left");
		// $this->db->join("$emailTbl as t5", "t1.from_email = t5.id", "left");
		foreach ($dynamicJoin as $joinarr) {
			$this->db->join($joinarr['table'], $joinarr['condition'], $joinarr['type']);
		}
		// $this->db->group_by("t1.*,t2.priority_name,t2.colour_code,t3.status_name,t3.colour_code,t4.user_name,t5.email". $dynamicAlias);

		if (!empty($filter['ticket_id'])) {
			$this->db->where("t1.ticket_id", $filter['ticket_id']);
		}
		if (!empty($filter['ticket_unique_id'])) {
			$this->db->where("t1.ticket_unique_id", $filter['ticket_unique_id']);
		}
		if (!empty($filter['customer_name'])) {
			$this->db->where("t1.customer_name", $filter['customer_name']);
		}
		if (!empty($filter['queue_id'])) {
			$this->db->where("t1.queue_id", $filter['queue_id']);
		}
		if (!empty($filter['category_id'])) {
			$this->db->where("t1.category_id", $filter['category_id']);
		}
		if (!empty($filter['status_id'])) {
			$this->db->where("t1.status_id", $filter['status_id']);
		}
		if (!empty($filter['priority_id'])) {
			$this->db->where("t1.priority_id", $filter['priority_id']);
		}
		if (!empty($filter['assign_to'])) {
			$this->db->where("t2.assign_to", $filter['assign_to']);
		}
		if (!empty($filter['customer_mobile_no'])) {
			$this->db->where("t2.customer_mobile_no", $filter['customer_mobile_no']);
		}
		if (!empty($filter['from_email'])) {
			$this->db->where("t2.from_email", $filter['from_email']);
		}
		if (!empty($filter['subject'])) {
			$this->db->where("t2.subject", $filter['subject']);
		}
		if (!empty($filter['user_id'])) {
			$this->db->where("t2.id", $filter['user_id']);
		}
		if (!empty($filter['created_date_from'])) {
			$this->db->where("t1.created_date >=",  strtok($filter['created_date_from'], 'T') . ' 00:00:00');
		}

		if (!empty($filter['created_date_to'])) {
			$this->db->where("t1.created_date <=", strtok($filter['created_date_to'], 'T') . ' 23:59:59');
		}

		if ($filter['un_assigend'] == 1) {
			$this->db->where("t1.is_assigned", 0);
		}
		$this->db->where($where2, NULL, false);
		$this->db->order_by('t1.created_date', desc);


		$tempdb = clone $this->db;
		$count = $tempdb->count_all_results();
		$this->db->limit($limit, $offset);
		$data	= $this->db->get();
		$page_count = ceil($count / $limit);
		$ret_data	= array(
			'total'		=> $count,
			'records'	=> $data->result_array(),
			'page_count' => $page_count,
		);
		return $ret_data;
	}

	function masterUserList($limit, $offset, $filter = array(), $where = '1=1', $masterName, $sort)
	{
		$this->db->select("t1.* ,t1.mobile_no as 'Mobile Number', t2.title as 'User Type', case when t1.`status` = 1 then 'Active' ELSE 'In Active' END AS status");
		$this->db->from($masterName . " t1");

		$this->db->join('tms_user_type t2', "t2.id=t1.user_type");

		if (!empty($filter['email'])) {
			$this->db->like("t1.email", $filter['email']);
		}
		if (!empty($filter['user_name'])) {
			$this->db->like("t1.user_name", $filter['user_name']);
		}
		if (!empty($filter['mobile_no'])) {
			$this->db->like("t1.mobile_no", $filter['mobile_no']);
		}
		$this->db->where($where, NULL, false);
		// if (!empty($sort)) {
		// 	$this->db->order_by($sort);
		// }

		// $data	= $this->db->get();
		// $countres = $this->db->query("select count(*) as count from " . $masterName . " t1    WHERE t1.email  LIKE '%" . $filter['email'] . "%' or t1.user_name  LIKE '%" . $filter['user_name'] . "%'  or t1.mobile_no  LIKE '%" . $filter['mobile_no'] . "%'")->result();


		// $page_count = ceil($countres[0]->count / $limit);

		// $ret_data	= array(
		// 	'total'		=> $count,
		// 	'records'	=> $data->result_array(),
		// 	'page_count' => $page_count,
		// );
		$tmpDb = clone $this->db;
		$countres = $tmpDb->count_all_results();

		if (!empty($limit)) {
			$this->db->limit($limit, $offset);
		}
		if (!empty($sort)) {
			$this->db->order_by($sort);
		}

		$data	= $this->db->get();
		// echo $this->db->last_query(); die; 
		$page_count = ceil($countres / $limit);

		$ret_data	= array(
			'total'		=> $countres,
			'records'	=> $data->result_array(),
			'page_count' => $page_count,
		);

		return $ret_data;
	}


	function masterList($limit, $offset, $filter = array(), $where = '1=1', $masterName, $sort)
	{
		switch ($masterName) {

			case 'tms_email_acoount':
				$this->db->select("t1.id,t1.email as 'Email' , t1.display_name as 'Display Name', t1.incoming_user_name as 'User Name', CASE  WHEN  t1.status = 1 THEN 'Active' ELSE 'In Active' END AS status");
				if (!empty($filter['email'])) {
					$this->db->like("t1.email", $filter['email']);
				}
				if (!empty($filter['display_name'])) {
					$this->db->like("t1.display_name", $filter['display_name']);
				}
				if (!empty($filter['user_name'])) {
					$this->db->like("t1.incoming_user_name", $filter['user_name']);
				}
				break;

			case 'tms_company_master':
				$this->db->select("t1.id,t1.name, CASE  WHEN  t1.status = 1 THEN 'Active' ELSE 'In Active' END AS status");
				if (!empty($filter)) {
					$this->db->like("t1.name", $filter);
				}
				break;

			case 'tms_priority_master':
				$this->db->select("t1.id,t1.name as 'Priority' ,t1.colour_code as 'Colour' ,t1.tat as 'TAT', CASE  WHEN  t1.status = 1 THEN 'Active' ELSE 'In Active' END AS status");
				if (!empty($filter)) {
					$this->db->like("t1.name", $filter);
				}
				break;

			case 'tms_pagination_master':
				$this->db->select("t1.id,t1.record, CASE  WHEN  t1.status = 1 THEN 'Active' ELSE 'In Active' END AS status");
				if (!empty($filter)) {
					$this->db->like("t1.record", $filter);
				}
				break;

			case 'tms_status_master':
				$this->db->select("t1.id,t1.title, CASE  WHEN  t1.ticket_type = 1 THEN 'Initial Status' WHEN t1.ticket_type=2 THEN 'Non Final Status' ELSE 'Final Status' END AS 'Ticket Type', t1.colour_code as 'Colour', CASE  WHEN  t1.status = 1 THEN 'Active' ELSE 'In Active' END AS status");
				if (!empty($filter)) {
					$this->db->like("t1.title", $filter);
				}
				break;

			case 'tms_shift_master':
				$this->db->select("t1.id,t1.title ,t1.time_from as 'Time From' ,t1.time_to as 'Time To', CASE  WHEN  t1.status = 1 THEN 'Active' ELSE 'In Active' END AS status");
				if (!empty($filter)) {
					$this->db->like("t1.title", $filter);
				}
				break;

			case 'tms_subcategory_master':
				$this->db->select("t1.id,t1.title,t2.title as 'category' , CASE  WHEN  t1.status = 1 THEN 'Active' ELSE 'In Active' END AS status");
				if (!empty($filter['title'])) {
					$this->db->like("t1.title", $filter['title']);
				}
				if (!empty($filter['category'])) {
					$this->db->like("t2.title", $filter['category']);
				}
				break;

			case 'tms_queue_master':
				$this->db->select("t1.id,t1.queue , CASE  WHEN  t1.status = 1 THEN 'Active' ELSE 'In Active' END AS status");
				if (!empty($filter)) {
					$this->db->like("t1.title", $filter);
				}
				break;

			case 'tms_field_group':
				$this->db->select("t1.id,t1.title as 'Field Group', t1.field_type  as 'Field Type' , CASE  WHEN  t1.status = 1 THEN 'Active' ELSE 'In Active' END AS status");
				if (!empty($filter)) {
					$this->db->like("t1.title", $filter['title']);
				}
				if (!empty($filter)) {
					$this->db->like("t1.field_type", $filter['field_type']);
				}
				break;

			case 'tms_template_master':
				$this->db->select("t1.id,t1.template , CASE  WHEN  t1.status = 1 THEN 'Active' ELSE 'In Active' END AS status");
				if (!empty($filter)) {
					$this->db->like("t1.template", $filter);
				}
				break;

			case 'tms_log_master':
				$this->db->select("t1.id,t2.user_name as 'User' , t1.action_method as 'Method',t1.action_adreess as 'Performed Address',t1.table_name as 'Table' ,t1.action_date as 'Perform Time'");
				if (!empty($filter['user_id'])) {
					$this->db->where("t2.id", $filter['user_id']);
				}

				if (!empty($filter['from_date'])) {
					$this->db->where("t1.action_date >=",  strtok($filter['from_date'], 'T') . ' 00:00:00');
				} else {
					$this->db->where("t1.action_date >=",  date('Y-m-d') . ' 00:00:00');
				}
				if (!empty($filter['to_date'])) {
					$this->db->where("t1.action_date <=", strtok($filter['to_date'], 'T') . ' 23:59:59');
				} else {
					$this->db->where("t1.action_date <=",  date('Y-m-d') . ' 23:59:59');
				}
				break;

			case 'tms_login_log':
				if ($filter['report_type'] == 'raw') {
					$this->db->select("user_name AS 'User Name', date_format(insert_date, '%d-%m-%Y %H:%i:%s') AS ' DateTime', status AS 'Status'");
				} else {
					$this->db->select('user_name AS `User Name`');
					$this->db->select('min(CASE WHEN status = \'login\' THEN DATE_FORMAT(insert_date, \'%d-%m-%Y %H:%i:%s\') END) AS `Login Date`');
					$this->db->select('max(CASE WHEN status = \'logout\' THEN DATE_FORMAT(insert_date, \'%d-%m-%Y %H:%i:%s\') END) AS `Logout Date`');
					$this->db->select('COUNT(CASE WHEN status = \'login\' THEN 1 END) AS `Login Count`');
					$this->db->select('COUNT(CASE WHEN status = \'logout\' THEN 1 END) AS `Logout Count`');
					$this->db->select("CONCAT(FLOOR(TIMESTAMPDIFF(SECOND, MIN(CASE WHEN status = 'login' THEN insert_date END), MAX(CASE WHEN status = 'logout' THEN insert_date END)) / 86400), ' days ',SEC_TO_TIME(TIMESTAMPDIFF(SECOND, MIN(CASE WHEN status = 'login' THEN insert_date END), MAX(CASE WHEN status = 'logout' THEN insert_date END)) % 86400)) AS `Login Hours`");
				}

				if (!empty($filter['user_id'])) {
					$this->db->where("user_id", $filter['user_id']);
				}

				if (!empty($filter['from_date'])) {
					$this->db->where("insert_date >=",  strtok($filter['from_date'], 'T') . ' 00:00:00');
				}
				if (!empty($filter['to_date'])) {
					$this->db->where("insert_date <=", strtok($filter['to_date'], 'T') . ' 23:59:59');
				}
				if (empty($filter['from_date']) && empty($filter['to_date'])) {
					$this->db->where("DATE_FORMAT(insert_date, '%Y-%m-%d') = CURDATE()");
				}

				break;

			case 'tms_new_ticket':
				$this->db->select(" *");
				if (!empty($filter['user_id'])) {
					$this->db->where("t2.id", $filter['user_id']);
				}
				if (!empty($filter['user_id'])) {
					$this->db->where("t2.id", $filter['user_id']);
				}
				if (!empty($filter['user_id'])) {
					$this->db->where("t2.id", $filter['user_id']);
				}
				if (!empty($filter['user_id'])) {
					$this->db->where("t2.id", $filter['user_id']);
				}
				if (!empty($filter['user_id'])) {
					$this->db->where("t2.id", $filter['user_id']);
				}
				if (!empty($filter['user_id'])) {
					$this->db->where("t2.id", $filter['user_id']);
				}
				if (!empty($filter['user_id'])) {
					$this->db->where("t2.id", $filter['user_id']);
				}
				if (!empty($filter['user_id'])) {
					$this->db->where("t2.id", $filter['user_id']);
				}
				if (!empty($filter['user_id'])) {
					$this->db->where("t2.id", $filter['user_id']);
				}
				if (!empty($filter['user_id'])) {
					$this->db->where("t2.id", $filter['user_id']);
				}
				if (!empty($filter['user_id'])) {
					$this->db->where("t2.id", $filter['user_id']);
				}
				if (!empty($filter['user_id'])) {
					$this->db->where("t2.id", $filter['user_id']);
				}
				if (!empty($filter['from_date'])) {
					$this->db->where("t1.login_time >=",  strtok($filter['from_date'], 'T') . ' 00:00:00');
				} else {
					$this->db->where("t1.login_time >=",  date('Y-m-d') . ' 00:00:00');
				}
				if (!empty($filter['to_date'])) {
					$this->db->or_where("t1.logout_time <=", strtok($filter['to_date'], 'T') . ' 23:59:59');
				} else {
					$this->db->or_where("t1.logout_time <=",  date('Y-m-d') . ' 23:59:59');
				}
				if (!empty($filter['user_id'])) {
					$this->db->where("t2.id", $filter['user_id']);
				}
				break;


			default:
				$this->db->select("t1.*,case when t1.`status` = 1 then 'Active' ELSE 'In Active' END AS status");
				if (!empty($filter)) {
					$this->db->like("t1.title", $filter);
				}
				break;
		}

		$this->db->from($masterName . " t1");

		switch ($masterName) {
			case 'tms_subcategory_master':
				$this->db->join('tms_category_master t2', "t2.id=t1.category_id");
				break;

			case 'tms_log_master':
				$this->db->join('tms_user_master t2', "t2.id=t1.action_performed_by");
				break;

			// case 'tms_login_log':
			// 	$this->db->join('tms_user_master t2', "t2.id=t1.user_id");
			// 	break;
		}


		$this->db->where($where, NULL, false);


		switch ($masterName) {
			case 'tms_login_log':
				if ($filter['report_type'] == 'summary') {
					$this->db->group_by('user_name');
				}
				break;
		}


		$tempdb = clone $this->db;
		$count = $tempdb->count_all_results();

		if (!empty($limit)) {
			$this->db->limit($limit, $offset);
		}
		if (!empty($sort)) {
			switch ($masterName) {
				case 'tms_login_log':
					if ($filter['report_type'] == 'summary') {
						$this->db->order_by('t1.user_name');
					} else {
						$this->db->order_by($sort);
					}
					break;
				default:
					$this->db->order_by($sort);
					break;
			}
		}

		$data	= $this->db->get();
		$page_count = ceil($count / $limit);

		$ret_data	= array(
			'total'		=> $count,
			'records'	=> $data->result_array(),
			'page_count' => $page_count,
		);

		return $ret_data;
	}


	function deleteProcess($masterName, $data, $pKey = 'id')
	{
		try {
			$table = $masterName;
			if (!empty($table)) {
				$data = is_array($data) ? $data : (array)$data;
				$id = $data[$pKey];
				if (!empty($id)) {
					if ($this->db->delete($table, array($pKey => $id))) {
						if ($table == 'tms_rule_condition') {
							$this->db->where('rule_id', $id);
							$this->db->delete('tms_rule_condition');

							$this->db->where('rule_id', $id);
							$this->db->delete('tms_rule_action');
						} else if ($table == 'tms_queue_master') {
							$this->db->where('queue_id', $id);
							$this->db->delete('tms_queue_email');

							$this->db->where('queue_id', $id);
							$this->db->delete('tms_queue_template');
						} else if (strpos($table, "tms_ticket_master_") !== false) {
							$conversationTbl = "tms_conversation_master_" . $data['queue_id'];
							$attachmentTbl = "tms_conversation_attachements_" . $data['queue_id'];

							$this->db->where('ticket_unique_id', $id);
							$this->db->delete($conversationTbl);

							$this->db->where('ticket_id', $id);
							$this->db->delete($attachmentTbl);
						}
						$res = array('error' => false, 'message' => 'Record deleted');
					} else {
						$error = $this->db->error();
						if ($error['code'] == '1451') {
							throw new Exception('Cannot delete record because it is referenced by a child table.');
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


	function getEscalationMatrixDataList($list_name, $page_no = 1, $limit, $filter = array(), $where = '1=1', $sort)
	{
		$this->db->select("*");
		$this->db->from($list_name . " t1");
		$this->db->where('queue', $filter);
		$tempdb	= clone $this->db;
		$count	= $tempdb->count_all_results();
		$data	= $this->db->get();
		$ret_data	= array(
			'total'		=> $count,
			'records'	=> $data->result_array()
		);
		return $ret_data;
	}

	function getMenuWithUserRights($user_type_id)
	{
		$sqlStmt  = "SELECT t1.id, t1.name, t1.parent_id,t1.menu_module_id, t1.display_order, IFNULL(t2.view_right,0) view_right, IFNULL(t2.add_right,0) add_right, IFNULL(t2.edit_right,0) edit_right, IFNULL(t2.delete_right,0) delete_right, IFNULL(t2.export_right,0) export_right,t2.page_id";

		$sqlStmt .= " FROM tms_module t1";
		$sqlStmt .= " LEFT JOIN tms_user_type_rights t2 ON t1.id=t2.page_id AND t2.usertype_id='$user_type_id'";
		$sqlStmt .= " ORDER BY t1.parent_id asc,t1.display_order asc";

		return $this->db->query($sqlStmt)->result_array();
	}

	function updateUserRights($data, $master_name)
	{
		$insert_arr = array();
		foreach ($data->rights as $index => $rights) {
			$insert_arr[]	= array(
				'usertype_id'	=> $data->userTypeId,
				'page_id'		=> $rights->id,
				'view_right'	=> $rights->view_right,
				'add_right'		=> $rights->add_right,
				'edit_right'	=> $rights->edit_right,
				'delete_right'	=> $rights->delete_right,
				'export_right'	=> $rights->export_right,
			);
		}
		try {
			if (!empty($insert_arr)) {
				$this->db->trans_begin();

				$this->deleteUserRights($data->userTypeId, $master_name);
				$this->db->insert_batch($master_name, $insert_arr);

				if ($this->db->trans_status() === FALSE) {
					$this->db->trans_rollback();
					throw new exception('Something goes wrong.');
				} else {
					$this->db->trans_commit();
					return array('status' => true);
				}
			} else {
				throw new exception('User Rights are empty.');
			}
		} catch (Exception $e) {
			return array('status' => false, 'message' => $e->getMessage());
		}
	}

	function deleteUserRights($userTypeId, $master_name)
	{
		$condition = array('usertype_id' => $userTypeId);
		if ($this->db->delete($master_name, $condition)) {
			return true;
		} else {
			return false;
		}
	}

	function getUserTypeRights($user_type, $module = '')
	{
		$fieldsArr	= array('t1.view_right', 't1.add_right', 't1.edit_right', 't1.delete_right', 't1.export_right', 't2.id', 't3.module_link');
		$this->db->select($fieldsArr);
		$this->db->where('usertype_id', $user_type);
		$this->db->from('tms_user_type_rights t1');
		$this->db->join('tms_module t2', 't2.id=t1.page_id', 'left');
		$this->db->join('tms_menu_module t3', 't3.id=t2.menu_module_id', 'left');
		if (!empty($module)) {
			$this->db->where('t3.module_link', $module);
		}
		$res = $this->db->get()->result_array();
		// echo $this->db->last_query(); die; 
		return $res;
	}

	function getjunkpermisssion($user_id, $user_type)
	{
		$this->db->select('move_to_junk');
		$this->db->from('tms_user_master t1');
		$this->db->join('tms_user_type t2', 't2.id=t1.user_type');
		$this->db->where('t1.id', $user_id);
		$this->db->where('t2.id', $user_type);
		$res = $this->db->get()->result_array();
		return $res;
	}

	function getQueueChangeAllDropdown($id)
	{
		$this->db->select('t1.catagory,t1.statustype');
		$this->db->from('tms_queue_master t1');
		$this->db->where('t1.id', $id);
		$data = $this->db->get()->result_array();

		// $where = "queue_id LIKE '$id,%' OR queue_id LIKE '%,$id,%' OR queue_id LIKE '%,$id'";
		$where = "queue_id RLIKE '[[:<:]].$id.[[:>:]]'";
		$this->db->select('t1.id,t1.name');
		$this->db->from('tms_user_master t1');
		// $this->db->where_in('t1.queue_id', $id, false);
		$this->db->where($where, NULL, FALSE);
		$this->db->order_by('t1.name', 'asc');
		$result['userqueuedata'] = $this->db->get()->result_array();

		$this->db->select('t2.id,t2.email');
		$this->db->distinct();
		$this->db->from('tms_queue_email t1');
		$this->db->join('tms_email_acoount t2', 't1.outgoing_email_account = t2.id', 'left');
		$this->db->where('t1.queue_id', $id);
		$this->db->order_by('t2.email', 'asc');
		$result['queue_email'] = $this->db->get()->result_array();


		// $category_val = explode(",", $data[0]['catagory']);
		// $statustype_val = explode(",", $data[0]['statustype']);
		$queueCategoryTbl = "tms_queue_category_" . $id;
		$queueStatusTbl = "tms_queue_status_" . $id;
		$priorityTbl = "tms_queue_priority_" . $id;

		$this->db->select('t1.category_id as id,t1.category_name as title');
		$this->db->from("$queueCategoryTbl as t1");
		$this->db->order_by('t1.category_name', 'asc');
		$result['category_data'] = $this->db->get()->result_array();

		$this->db->select('t1.status_id as id,t1.status_name as title,t1.is_default');
		$this->db->from("$queueStatusTbl as t1");
		$this->db->order_by('t1.status_name', 'asc');
		$result['statustype_data'] = $this->db->get()->result_array();

		$this->db->select('t1.priority_id as id,t1.priority_name as title,t1.is_default');
		$this->db->from("$priorityTbl as t1");
		$this->db->order_by('t1.priority_name', 'asc');
		$result['priority_data'] = $this->db->get()->result_array();

		return array('result' => $result);
	}

	function getCategoryData($data)
	{
		$catId = $data['category_id'];
		$queueId = $data['queue_id'];
		$queueCategoryTbl = "tms_queue_category_" . $queueId;
		// $this->db->select('t1.sub_category as id');
		// $this->db->from("$queueCategoryTbl t1");
		// $this->db->where_in("t1.category_id",$catId);
		// $this->db->order_by('t1.sub_category', 'asc');
		// $result = $this->db->get()->result_array();
		$result = $this->db->query("select
		SUBSTRING_INDEX(SUBSTRING_INDEX(tablename.sub_category, ',', numbers.n), ',', -1) sub_category
	  from
		(select 1 n union all
		 select 2 union all select 3 union all
		 select 4 union all select 5) numbers INNER JOIN $queueCategoryTbl tablename
		on CHAR_LENGTH(tablename.sub_category)
		   -CHAR_LENGTH(REPLACE(tablename.sub_category, ',', ''))>=numbers.n-1
		   WHERE tablename.category_id = $catId
	  order by
		tablename.category_id")->result_array();
		return $result;
	}

	function getemaildeitals($id)
	{
		$this->db->select('*');
		$this->db->from('tms_email_acoount ');
		$this->db->where("id", $id);
		$result = $this->db->get()->result_array();
		return $result;
	}

	// public function getRuleDataList($queue_id)
	// {
	// 	$this->db->select("t1.rule_id,t1.rule_name");
	// 	$this->db->from("tms_queue_rule_master_$queue_id t1");
	// 	$data = $this->db->get()->result();
	// 	return $data;
	// }

	public function getRuleDataList($queue_id, $limit, $page_no = 1)
	{

		$queusrulemaster = "tms_queue_rule_master_" . $queue_id;
		$record = $this->db->query("select count(*) as record from $queusrulemaster")->result();
		$page_count = ceil($record[0]->record / $limit);

		if (empty($limit)) $limit = $this->defaultLimit;
		$offset		= ($page_no - 1) * $limit;

		$this->db->select("t1.rule_id,t1.rule_name");
		$this->db->from("tms_queue_rule_master_$queue_id t1");
		if (!empty($limit)) {
			$this->db->limit($limit, $offset);
		}
		$data = $this->db->get()->result();

		// echo $this->db->last_query(); die;
		$count = count($data);
		$page_count = ceil($record[0]->record / $limit);

		$ret_data	= array(
			'total'		=> $record[0]->record,
			'records'	=> $data,
			'page_count' => $page_count,
		);
		return $ret_data;
	}

	public function deleteQueueRule($data, $pKey = 'rule_id')
	{
		$rule_id  = $data['rule_id'];
		$queue_id  = $data['queue_id'];
		if (!empty($rule_id)) {
			$this->db->where('rule_id', $rule_id);
			$this->db->delete("tms_queue_rule_condition_$queue_id");

			$this->db->where('rule_id', $rule_id);
			$this->db->delete("tms_queue_rule_action_$queue_id");

			$this->db->where('rule_id', $rule_id);
			$this->db->delete("tms_queue_rule_master_$queue_id");

			$res = array('error' => false, 'message' => 'Record deleted');
		} else {
			$res = array('error' => true, 'message' => "Rule Id is required");
		}
		return $res;
	}

	public function getFormBuilderDataList($queue_id, $limit, $page_no = 1)
	{
		$Formbuiler_form = "tms_dynamicform_fields_" . $queue_id;
		$record = $this->db->query("select count(*) as record from $Formbuiler_form")->result();
		$page_count = ceil($record[0]->record / $limit);

		if (empty($limit)) $limit = $this->defaultLimit;
		$offset		= ($page_no - 1) * $limit;
		$this->db->select("t1.field_id,t1.field_name,t1.field_description,t1.data_type,t1.field_type,t1.required");
		$this->db->from("tms_dynamicform_fields_$queue_id t1");
		if (!empty($limit)) {
			$this->db->limit($limit, $offset);
		}
		$data = $this->db->get()->result();
		$count = count($data);
		$page_count = ceil($record[0]->record / $limit);

		$ret_data	= array(
			'total'		=> $record[0]->record,
			'records'	=> $data,
			'page_count' => $page_count,
		);
		return $ret_data;
	}

	public function queueData($user_id)
	{
		$this->db->select('queue_id');
		$this->db->from('tms_user_master');
		$this->db->where('user_id', $user_id);
		return $this->db->get()->row();
	}

	// public function getEmailData($emailId)
	// {
	// 	$this->db->select('');
	// 	$this->db->from('');
	// 	$this->db->where('');
	// 	return $this->db->get()->result();
	// }

	// -----------------------------------TMS API ENDS-----------------------------
}
