<?php
class Dropdown_Option_Model extends CI_Model{

    function __construct()
	{
		parent::__construct();
	}

    function getOptionList($option_type_arr = array(),$user_id)
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
					$this->db->where('t1.status', 1);
					$this->db->where('t1.id !=' ,$user_id);
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

				case "ticket_status_type":
					$this->db->select("t1.statustype_id,t1.status_type");
					$this->db->order_by('status_type','asc');
					$ret_data[$option_type] = $this->db->get('statustype_master t1')->result_array();
					break;

				case "tms_conversation_type_master":
					$this->db->select("t1.id,t1.title,con_type_img");
					$this->db->order_by('title','asc');
					$ret_data[$option_type] = $this->db->get('tms_conversation_type_master t1')->result_array();
					break;

                default:
					break;
            }
        }
        return $ret_data;
    }

	public function getQueuePriorityOptionList($option_type = "")
	{
		$ret_data = array();
		if(!empty($option_type)){
			$this->db->select("t1.priority_id,t1.priority_name");
			$this->db->order_by('priority_name','asc');
			$ret_data['queue_priority'] = $this->db->get("$option_type t1")->result_array();
		}
		return $ret_data;
	}

	public function getQueueOptionList($option_type_arr = array(),$queue_id)
	{
		$ret_data	= array();
		foreach ($option_type_arr as $key => $option_type) {
			$type = $option_type;
			switch ($type) {
				case"tms_queue_priority":
					$this->db->select('priority_id as id, priority_name as title');
					$this->db->order_by('priority_name','asc');
					$ret_data[$option_type] = $this->db->get("tms_queue_priority_$queue_id")->result_array();
					break;
				case"tms_queue_category":
					$ret_data[$option_type] = $this->db->query("SELECT
					SUBSTRING_INDEX(SUBSTRING_INDEX(tablename.sub_category, ',', numbers.n), ',', -1) AS id,
					SUBSTRING_INDEX(SUBSTRING_INDEX(tablename.sub_category, ',', numbers.n), ',', -1) AS title
				  from
					(select 1 n union all
					 select 2 union all select 3 union all
					 select 4 union all select 5) numbers INNER JOIN tms_queue_category_$queue_id tablename
					on CHAR_LENGTH(tablename.sub_category)
					   -CHAR_LENGTH(REPLACE(tablename.sub_category, ',', ''))>=numbers.n-1
				  order by
					tablename.sub_category")->result_array();;
					break;
				case"tms_queue_status":
					$this->db->select('status_id as id, status_name as title');
					$this->db->order_by('status_name','asc');
					$ret_data[$option_type] = $this->db->get("tms_queue_status_$queue_id")->result_array();
					break;
				case"user":
					$this->db->select('id, name as title');
					$this->db->where('status', 1);
					$this->db->order_by('title','asc');
					$ret_data[$option_type] = $this->db->get('tms_user_master')->result_array();
					break;					
				default:
					break;
			}
		}
		return $ret_data;

	}

}
?>
