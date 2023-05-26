<?php
class Api_Model extends CI_Model
{

	function __construct()
	{
		parent::__construct();
	}

	private $defaultLimit = 50;
	// -------------------------TMS API START--------------------------------------------

	public function checklogin($email, $password)
	{
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


	public function changepasswordinsert($table, $data,$id)
	{
		$this->db->where('id', $id);
		$this->db->update($table, $data);
	}
	// public function getMenus($parent_user_id) {
	// 	$parentMenus = $this->parentMenuList();
	// 	$menuList = array();
	// 	foreach($parentMenus as $key=>$menuArr) {
	// 		$menuList[] = $menuArr;
	// 		$childMenu = $this->childMenuList('0', $menuArr['id']);
	// 		$menuList[$key]['child_menus'] = $childMenu;
	// 		// if(!empty($childMenu)) {
	// 		// 	foreach($childMenu as $chky=>$chmenu) {
	// 		// 		$tempMenuList = $chmenu;
	// 		// 		$secChildMenu = $this->childMenuList('0', $chmenu['parent_id']);
	// 		// 		if(!empty($secChildMenu)) {
	// 		// 			$tempMenuList['child_menus'] = $secChildMenu;
	// 		// 			foreach($secChildMenu as $chchkey=>$chchmenu) {
	// 		// 				$chchMenu = $this->childMenuList('0', $chchmenu['parent_id']);
	// 		// 				if(!empty($chchMenu)) {
	// 		// 					$tempMenuList['child_menus'][$chchkey]['child_menus']	= $chchMenu;
	// 		// 				} else {
	// 		// 					$tempMenuList['child_menus'][$chchkey]['child_menus']	= '';
	// 		// 				}
	// 		// 			}
	// 		// 		} else {
	// 		// 			$tempMenuList['child_menus'] = '';
	// 		// 		}
	// 		// 		$menuList[] = $tempMenuList;
	// 		// 	}
	// 		// }
	// 	}
	// 	// print_r($menuList);die;
	// 	return $menuList;
	// }

	// public function parentMenuList($parent_id=0,$id=0,$user_name='admin') {
	// 	$master_user='yes';
	// 	$this->db->select("tms_module.*");
	// 	if($parent_id==0 && $master_user!='yes' && $user_name!='admin')
	// 	{
	// 		$this->db->where("(parent_id='".$parent_id."' OR (parent_id NOT IN (select module_id from user_module where user_id='".$user_id."') and parent_id NOT IN (select module_id from user_group_module u_g_m JOIN user u on u_g_m.user_group_id=u.user_group_id and u.id='".$user_id."')))",null,false);

	// 	}
	// 	else
	// 	{
	// 		$this->db->where("parent_id",$parent_id);
	// 	}

	// 	if($id!=0)
	// 	{
	// 		$this->db->where("tms_module.id",$id);
	// 	}
	// 	$this->db->order_by("tms_module.display_order","asc");
	// 	if($master_user!='yes' and $user_name!='admin')
	// 	{

	// 		$this->db->where("(id in(select module_id from user_module where user_id='".$user_id."') or id in(select module_id from user_group_module u_g_m JOIN user u on u_g_m.user_group_id=u.user_group_id and u.id='".$user_id."'))",null,false);
	// 		$this->db->from('tms_module');
	// 	}
	// 	else
	// 	{
	// 		$this->db->from('tms_module');
	// 	}
	// 	if($master_user != 'yes'){
	// 		$this->db->where("is_registered",1);
	// 	}
	// 	$this->db->group_by("tms_module.id");
	// 	$select=$this->db->get();
	// 	// echo $this->db->last_query();
	// 	return $select->result_array();
	// }

	public function getMenus($is_master,$user_type)
	{
		$parentMenus = $this->parentMenuList($is_master,$user_type);
		$menuList = array();
		foreach ($parentMenus as $key => $menuArr) {
			$menuList[] = $menuArr;
			$childMenu = $this->childMenuList($is_master, $menuArr['id'],$user_type);
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

	// public function childMenuList($id = 0, $parent_id = 0, $user_name = 'admin')
	// {
	// 	$master_user = 'yes';
	// 	$this->db->select("tms_module.*");
	// 	if ($parent_id == 0 && $master_user != 'yes' && $user_name != 'admin') {
	// 		$this->db->where("(parent_id='" . $parent_id . "' OR (parent_id NOT IN (select module_id from user_module where user_id='" . $user_id . "') and parent_id NOT IN (select module_id from user_group_module u_g_m JOIN user u on u_g_m.user_group_id=u.user_group_id and u.id='" . $user_id . "')))", null, false);
	// 	} else {
	// 		$this->db->where("parent_id", $parent_id);
	// 	}

	// 	if ($id != 0) {
	// 		$this->db->where("tms_module.id", $id);
	// 	}
	// 	$this->db->order_by("tms_module.display_order", "asc");
	// 	if ($master_user != 'yes' and $user_name != 'admin') {

	// 		$this->db->where("(id in(select module_id from user_module where user_id='" . $user_id . "') or id in(select module_id from user_group_module u_g_m JOIN user u on u_g_m.user_group_id=u.user_group_id and u.id='" . $user_id . "'))", null, false);
	// 		$this->db->from('tms_module');
	// 	} else {
	// 		$this->db->from('tms_module');
	// 	}
	// 	if ($master_user != 'yes') {
	// 		$this->db->where("is_registered", 1);
	// 	}
	// 	$this->db->group_by("tms_module.id");
	// 	$select = $this->db->get();
	// 	// echo $this->db->last_query();
	// 	return $select->result_array();
	// }

	public function childMenuList($is_master,$menuId,$user_type) {
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

	function getDataList($list_name, $page_no = 1, $limit, $filter = array(), $where = '1=1')
	{
		$ret_data	= array();
		$limit		= (int)$limit;
		if (empty($limit)) $limit = $this->defaultLimit;
		$offset		= ($page_no - 1) * $limit;
		$listname	= $list_name;
		if($list_name == "tms_user_master"){
			$ret_data	= $this->masterUserList($limit, $offset, $filter, $where, $listname);
		}else{
			$ret_data	= $this->masterList($limit, $offset, $filter, $where, $listname);
		}
		return $ret_data;
	}

	function masterUserList($limit, $offset, $filter = array(), $where = '1=1', $masterName)
	{
		$this->db->select("t1.*,case when t1.`status` = 1 then 'Active' ELSE 'In Active' END AS status");
		$this->db->from($masterName . " t1");
		// if (!empty($filter['id'])) {
		// 	$this->db->where("t1.id", $filter['id']);
		// }
		// if (!empty($filter['title'])) {
		// 	$this->db->like("t1.title", $filter['title']);
		// }
		if (!empty($filter)) {
			$this->db->like("t1.title", $filter);
		}
		$this->db->where($where, NULL, false);
		$this->db->order_by('id');
		$tempdb	= clone $this->db;
		$count	= $tempdb->count_all_results();
		$data	= $this->db->get();
		$ret_data	= array(
			'total'		=> $count,
			'records'	=> $data->result_array()
		);
		return $ret_data;
	}

	// function masterList($limit, $offset, $filter = array(), $where = '1=1', $masterName)
	// {
	// 	$this->db->select("t1.id, t1.title,case when t1.`status` = 1 then 'Active' ELSE 'In Active' END AS status");
	// 	$this->db->from($masterName . " t1");
	// 	// if (!empty($filter['id'])) {
	// 	// 	$this->db->where("t1.id", $filter['id']);
	// 	// }
	// 	// if (!empty($filter['title'])) {
	// 	// 	$this->db->like("t1.title", $filter['title']);
	// 	// }
	// 	if (!empty($filter)) {
	// 		$this->db->like("t1.title", $filter);
	// 	}
	// 	$this->db->where($where, NULL, false);
	// 	$this->db->order_by('id');
	// 	$tempdb	= clone $this->db;
	// 	$count	= $tempdb->count_all_results();
	// 	$data	= $this->db->get();

	// 	$ret_data	= array(
	// 		'total'		=> $count,
	// 		'records'	=> $data->result_array()
	// 	);
	// 	return $ret_data;
	// }

	function masterList($limit, $offset, $filter = array(), $where = '1=1', $masterName)
	{
		if($masterName=='tms_company_master'||$masterName =='tms_priority_master' )
		{
		$this->db->select('t1.id,t1.name');
		if (!empty($filter)) {
			$this->db->like("t1.name", $filter);
		}
		}else{
		$this->db->select("t1.id, t1.title,case when t1.`status` = 1 then 'Active' ELSE 'In Active' END AS status");
		if (!empty($filter)) {
			$this->db->like("t1.title", $filter);
		}
		}
		$this->db->from($masterName . " t1");
		
		$this->db->where($where, NULL, false);
		$this->db->order_by('id');
		$tempdb	= clone $this->db;
		$count	= $tempdb->count_all_results();
		$data	= $this->db->get();

		$ret_data	= array(
			'total'		=> $count,
			'records'	=> $data->result_array()
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

	function getMenuWithUserRights($user_type_id) {
		$sqlStmt  = "SELECT t1.id, t1.name, t1.parent_id,t1.menu_module_id, t1.display_order, IFNULL(t2.view_right,0) view_right, IFNULL(t2.add_right,0) add_right, IFNULL(t2.edit_right,0) edit_right, IFNULL(t2.delete_right,0) delete_right, IFNULL(t2.export_right,0) export_right,t2.page_id";

		$sqlStmt .= " FROM tms_module t1";
		$sqlStmt .= " LEFT JOIN tms_user_type_rights t2 ON t1.id=t2.page_id AND t2.usertype_id='$user_type_id'";
		$sqlStmt .= " ORDER BY t1.parent_id asc,t1.display_order asc";

		return $this->db->query($sqlStmt)->result_array();
	}

	function updateUserRights($data,$master_name) {
		$insert_arr = array();
		foreach($data->rights as $index=>$rights) {
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
		try{
			if(!empty($insert_arr)) {
				$this->db->trans_begin();

				$this->deleteUserRights($data->userTypeId,$master_name);
				$this->db->insert_batch($master_name, $insert_arr);

				if ($this->db->trans_status() === FALSE) {
					$this->db->trans_rollback();
					throw new exception('Something goes wrong.');
				} else {
					$this->db->trans_commit();
					return array('status'=>true);
				}
			} else {
				throw new exception('User Rights are empty.'); 
			}
		} catch(Exception $e) {
			return array('status'=>false, 'message'=>$e->getMessage());
		}
	}

	function deleteUserRights($userTypeId,$master_name) {
		$condition = array('usertype_id'=>$userTypeId);
		if($this->db->delete($master_name, $condition)) {
			return true;
		} else {
			return false;
		}
	}

	// -----------------------------------TMS API ENDS--------------------------------------------
}
