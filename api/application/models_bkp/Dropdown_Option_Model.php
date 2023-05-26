<?php
class Dropdown_Option_Model extends CI_Model{

    function __construct()
	{
		parent::__construct();
	}

    function getOptionList($option_type_arr = array())
	{

		$ret_data	= array();
		foreach ($option_type_arr as $key => $option_type) {
			$type = $option_type;
			switch ($type) {
                case"tms_user_type":
                    $this->db->select('id, title');
					$this->db->where('status', 1);
                    $this->db->order_by('title','asc');
					$ret_data[$option_type] = $this->db->get('tms_user_type')->result_array();
					break;

				case"manager":
					$this->db->select('t1.id, name');
					$this->db->from('tms_user_master t1');
					$this->db->join('tms_user_type t2','t1.user_type = t2.id','left');
					$this->db->where('t2.id',2);
					$this->db->where('t1.status', 1);
					$this->db->order_by('name','asc');
					$ret_data[$option_type] = $this->db->get()->result_array();
					break;

				case"tms_action_master":
					$this->db->select('id, title');
					$this->db->where('status', 1);
                    $this->db->order_by('title','asc');
					$ret_data[$option_type] = $this->db->get('tms_action_master')->result_array();
					break;

				case"tms_priority_master":
					$this->db->select('id, name as "title"  ');
					$this->db->where('status', 1);
					$this->db->order_by('name','asc');
					$ret_data[$option_type] = $this->db->get('tms_priority_master')->result_array();
					break;

				case"tms_subcategory_master":
					$this->db->select('id, title');
					$this->db->where('status', 1);
					$this->db->order_by('title','asc');
					$ret_data[$option_type] = $this->db->get('tms_subcategory_master')->result_array();
					break;

				case"tms_status_master":
					$this->db->select('id, title');
					$this->db->where('status', 1);
					$this->db->order_by('title','asc');
					$ret_data[$option_type] = $this->db->get('tms_status_master')->result_array();
					break;

				case"tms_user_master":
					$this->db->select('id, name as "title"');
					$this->db->where('status', 1);
					$this->db->order_by('name','asc');
					$ret_data[$option_type] = $this->db->get('tms_user_master')->result_array();
					break;	
														
				case"tms_shift_master":
					$this->db->select("t1.id,CONCAT(t1.title,'(',SUBSTRING(t1.time_from,1,5),'-',SUBSTRING(t1.time_to,1,5),')') AS title");
					$this->db->where('status', 1);
                    $this->db->order_by('title','asc');
					$ret_data[$option_type] = $this->db->get('tms_shift_master t1')->result_array();
					break;

				case"tms_category_master":
					$this->db->select("t1.id,title");
					$this->db->where('status', 1);
					$this->db->order_by('title','asc');
					$ret_data[$option_type] = $this->db->get('tms_category_master t1')->result_array();
					break;

				case"tms_pagination_master":
						$this->db->select("t1.record,t1.status");
						// $this->db->where('status', 1);
						$this->db->order_by('record','asc');
						$ret_data[$option_type] = $this->db->get('tms_pagination_master t1')->result_array();
				        break;	

				case"tms_email_acoount":
					$this->db->select("t1.email,t1.id");
					$this->db->where('status', 1);
					$this->db->order_by('email','asc');
					$ret_data[$option_type] = $this->db->get('tms_email_acoount t1')->result_array();
					break;	

				case"tms_queue_master":
					$this->db->select("t1.queue,t1.id");
					$this->db->where('status', 1);
					$this->db->order_by('queue','asc');
					$ret_data[$option_type] = $this->db->get('tms_queue_master t1')->result_array();
					break;
				
				case"tms_template_master":
					$this->db->select("t1.template,t1.id");
					$this->db->where('status', 1);
					$this->db->order_by('template','asc');
					$ret_data[$option_type] = $this->db->get('tms_template_master t1')->result_array();
					break;

				case"tms_status_master":
					$this->db->select("t1.title,t1.id");
					$this->db->where('status', 1);
					$this->db->order_by('title','asc');
					$ret_data[$option_type] = $this->db->get('tms_status_master t1')->result_array();
					break;

				case "dynamicFormQueue":
					$this->db->select("t1.queue,t1.id");
					$this->db->from('tms_queue_master t1');
					$this->db->join('tms_dynamic_form_mapping t2','t1.id = t2.queue_id','left');
					$this->db->where('t1.status', 1);
					$this->db->where('t2.id', NULL);
					$this->db->order_by('t1.queue','asc');
					$ret_data[$option_type] = $this->db->get()->result_array();
					break;

					
                default:
					break;
            }
        }
        return $ret_data;
    }
}
?>