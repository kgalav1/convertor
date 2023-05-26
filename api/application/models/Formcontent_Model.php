<?php
class Formcontent_Model extends CI_Model
{

	function __construct()
	{
		parent::__construct();
	}

	public function getFormFieldsByFormId($queue_id, $serach_fields = array())
	{
		$masterName = "tms_dynamicform_fields_" . $queue_id;
		$this->db->select("t1.*");
		if (isset($serach_fields['primary_field'])) {
			$this->db->where('primary_field', $serach_fields['primary_field']);
		}
		if (isset($serach_fields['filter'])) {
			$this->db->where('filter', $serach_fields['filter']);
		}
		if (isset($serach_fields['list_column'])) {
			$this->db->where('list_column', $serach_fields['list_column']);
		}
		if (isset($serach_fields['excel_export'])) {
			$this->db->where('excel_export', $serach_fields['excel_export']);
		}
		if (isset($serach_fields['print_pdf'])) {
			$this->db->where('print_pdf', $serach_fields['print_pdf']);
		}
		if (isset($serach_fields['values_from_db'])) {
			$this->db->where('values_from_db', $serach_fields['values_from_db']);
		}
		if (isset($serach_fields['field_name'])) {
			$this->db->where('field_name', $serach_fields['field_name']);
		}

		$this->db->from("$masterName as t1");
		$result = $this->db->get()->result();
		return (object) array('status' => true, 'data' => $result);
	}

	function checkDataDedupe($table_name, $field_name, $value, $pimary_key, $pimary_key_val) {
		if(empty($value)){
			return false;
		}
		
		$tblName	= $table_name;
		
		$this->db->select($field_name);
		$this->db->from($tblName);
		$this->db->where($field_name, $value);
		if(!empty($pimary_key) && !empty($pimary_key_val)) {
			$this->db->where(array($pimary_key.' !=' => $pimary_key_val));
		}
		$count = $this->db->count_all_results();
		
		if($count > 0)
			return true;
		else
			return false;
	}
}
?>
