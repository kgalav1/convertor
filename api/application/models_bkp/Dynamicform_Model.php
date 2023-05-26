<?php
class Dynamicform_Model extends CI_MODEL
{

    function __construct()
    {
        parent::__construct();
    }

    public function getData($id)
    {
        $this->db->where("t1.id", $id);
        $this->db->select("t1.*, t1.queue AS form_id");
        $this->db->from("tms_dynamicform_fields as t1");
        $result = $this->db->get()->row();

        return (object) array('status' => true, 'data' => $result);
    }

    public function getTableField($field_name, $formData)
    {
        $result = '';
            //SYSTEM EXISTING TABLE
            $frmSrc    = $this->getFormSourceInfo($formData->queue_id);
            if (!empty($frmSrc->data->table_name)) {
                $tblName    = $this->compDb . "." . $frmSrc->data->table_name;
                if ($this->isTableExist($frmSrc->data->table_name)) {
                    $sql    = "SHOW COLUMNS FROM " . $tblName . " WHERE field='" . $field_name . "';";
                    $res    = $this->db->query($sql);
                    $result    = $res->result();
                }
            }
        return $result;
    }

    public function getFormSourceInfo($id) {
		$this -> db -> where("t1.id", $id);
		$this->db->select("t1.*");
		$this->db->from("tms_dynamic_form_mapping as t1");
		$result = $this->db->get()->row();
		return (object) array('status' => true, 'data' => $result);
	}
}
