<?php
class Dynamicform_Model extends CI_MODEL
{
    public $dynamicFormTbl;
    public $myforge;
    public $compDbConf;

    function __construct()
    {
        parent::__construct();
        $this->compDb        = 'tms';
        // $this->compDbConf = array(
        //     'dsn'    => '',
        //     'hostname' => $this->db->hostname,
        //     'username' => $this->db->username,
        //     'password' => $this->db->password,
        //     'database' => $this->compDb,
        //     'dbdriver' => 'mysqli',
        //     'dbprefix' => '',
        //     'pconnect' => FALSE,
        //     'db_debug' => true,
        //     'cache_on' => FALSE,
        //     'cachedir' => '',
        //     'char_set' => 'utf8',
        //     'dbcollat' => 'utf8_general_ci',
        //     'swap_pre' => '',
        //     'encrypt' => FALSE,
        //     'compress' => FALSE,
        //     'stricton' => FALSE,
        //     'failover' => array(),
        //     'save_queries' => TRUE
        // );
        // $this->db    = $this->load->database($this->compDbConf, true);
        $this->myforge = $this->load->dbforge($this->db, TRUE);
        // $this->db = $this->load->database('default', TRUE);
        $this->uploadPath    = "uploads/";
        $this->phoneNo_length    = 16;
        $this->emailId_length    = 150;
        $this->date_length        = 10;
        $this->dateTime_length    = 19;
        $this->fileName_length    = 255;
        $this->textArea_maxLine    = 4;
        $this->dataTypeList        = array("integer" => "Numbers", "phone" => "Phone", "email" => "Email", "string" => "Text", "decimal" => "Decimal", "date" => "Date", "datetime" => "Date &amp; Time", "text" => "Big Text", "attachment" => "Attachment", "linkurl" => "Link");
        $this->fieldTypeList    = array("text" => "Text Box", "dropdown" => "Dropdown", "multiselect" => "Multi Select Dropdown", "checkbox" => "Checkbox", "radio" => "Radio Buttons", "textarea" => "Big Text", "attachment" => "Attachment", "linkurl" => "Link");
    }

    public function getData($id)
    {
        $this->db->where("t1.id", $id);
        $this->db->select("t1.*, t1.queue AS form_id");
        $this->db->from("tms_dynamicform_fields as t1");
        $result = $this->db->get()->row();

        return (object) array('status' => true, 'data' => $result);
    }

    public function getTableField($field_name, $ticketTblName)
    {
        $result = '';
        $sql    = "SHOW COLUMNS FROM " . $ticketTblName . " WHERE field='" . $field_name . "';";
        $res    = $this->db->query($sql);
        $result    = $res->result();
        return $result;
    }

    public function checkDataValuesQuery($sql)
    {
        $chk    = true;
        $msg    = '';
        $db_debug = $this->db->db_debug;
        $this->db->db_debug = FALSE;
        $this->db->db_debug = FALSE;
        try {
            if (!$this->db->query($sql)) {
                throw new Exception("Please check SQL Query");
            }
        } catch (Exception $ex) {
            $msg    = '<br>' . $ex->getMessage();
            $sqlErr    = $this->db->error();
            $sqlStmt = $this->db->last_query();
            if (!empty($sqlErr)) {
                $error_code    = isset($sqlErr['code']) ? $sqlErr['code'] : 0;
                $error_msg    = isset($sqlErr['message']) ? $sqlErr['message'] : 0;
                $msg    .= '<br>Error Code: ' . $error_code . '<br>Message: ' . $error_msg;
                $msg    .= '<br>SQL Query: ' . $sqlStmt;
            }
            $chk    = false;
        }

        $this->db->db_debug = $db_debug;
        return array('check' => $chk, 'message' => $msg);
    }

    public function getDataCountThroughValuesQuery($selectQry)
    {
        $select    = $this->db->query("SELECT COUNT(*) AS rowcnt FROM (" . $selectQry . ") AS derivedtbl");
        return $select->result();
    }

    public function getDataByQuery($selectQry)
    {
        $select    = $this->db->query("SELECT * FROM (" . $selectQry . ") AS derivedtbl LIMIT 0, 1");
        return $select->result_array();
    }

    public function setFormFieldData($rawData)
    {
        $data    = array();
        $data['field_description']    = !empty($rawData['field_description']) ? $rawData['field_description'] : NULL;
        $data['field_name']            = !empty($rawData['field_name']) ? $rawData['field_name'] : NULL;
        $data['data_type']            = !empty($rawData['data_type']) ? $rawData['data_type'] : NULL;

        if (in_array(strtolower($data['data_type']), array('datetime', 'date', 'email', 'phone', 'decimal', 'integer'))) {
            $data['field_type']        = 'text';
        } else if (strtolower($data['data_type']) == 'text') {
            $data['field_type']        = 'textarea';
        } else if (strtolower($data['data_type']) == 'attachment') {
            $data['field_type']        = 'attachment';
        } else if (strtolower($data['data_type']) == 'linkurl') {
            $data['field_type']        = 'linkurl';
        } else {
            $data['field_type']        = !empty($rawData['field_type']) ? $rawData['field_type'] : NULL;
        }
        if (strtolower($data['data_type']) == 'attachment') {
            $data['field_length']        = $this->fileName_length;
        } else if (strtolower($data['data_type']) == 'linkurl') {
            $data['field_length']        = $this->fileName_length;
        } else if (strtolower($data['data_type']) == 'datetime') {
            $data['field_length']        = $this->dateTime_length;
        } else if (strtolower($data['data_type']) == 'date') {
            $data['field_length']        = $this->date_length;
        } else if (strtolower($data['data_type']) == 'email') {
            $data['field_length']        = $this->emailId_length;
        } else if (strtolower($data['data_type']) == 'phone') {
            $data['field_length']        = $this->phoneNo_length;
        } else if (strtolower($data['data_type']) == 'text') {
            $data['field_length']        = NULL;
        } else {
            $data['field_length']        = !empty($rawData['field_length']) ? $rawData['field_length'] : NULL;
        }
        if (strtolower($data['data_type']) == 'decimal') {
            $data['decimal_length']        = !empty($rawData['decimal_length']) ? $rawData['decimal_length'] : NULL;
        } else {
            $data['decimal_length']        = NULL;
        }

        if (in_array(strtolower($data['field_type']), array('dropdown', 'multiselect', 'checkbox', 'radio'))) {
            $data['values_from_db']        = !empty($rawData['values_from_db']) ? 1 : 0;
            $data['unique_field']        = 0;
            $data['field_values']        = !empty($rawData['field_values']) ? addslashes(trim($rawData['field_values'])) : NULL;
        } else {
            $data['values_from_db']        = 0;
            $data['unique_field']        = !empty($rawData['unique_field']) ? 1 : 0;
            $data['field_values']        = NULL;
        }
        $data['width']                = !empty($rawData['width']) ? $rawData['width'] : NULL;
        $data['filter']                = !empty($rawData['filter']) ? 1 : 0;
        $data['excel_export']        = !empty($rawData['excel_export']) ? 1 : 0;
        $data['print_pdf']            = !empty($rawData['print_pdf']) ? 1 : 0;
        $data['required']            = !empty($rawData['required']) ? 1 : 0;
        $data['list_column']        = !empty($rawData['list_column']) ? 1 : 0;
        $data['max_line']            = NULL;

        if (strtolower($data['field_type']) == 'textarea') {
            $data['max_line']        = !empty($rawData['max_line']) ? $rawData['max_line'] : $this->textArea_maxLine;
        }
        $data['onchange']            = !empty($rawData['onchange']) ? $rawData['onchange'] : NULL;
        $data['onkeypress']            = !empty($rawData['onkeypress']) ? $rawData['onkeypress'] : NULL;
        $data['onclick']            = !empty($rawData['onclick']) ? $rawData['onclick'] : NULL;
        $data['db_val_column']        = !empty($rawData['db_val_column']) ? $rawData['db_val_column'] : NULL;
        $data['db_txt_column']        = !empty($rawData['db_txt_column']) ? $rawData['db_txt_column'] : NULL;

        return $data;
    }

    public function createFormField($insData, $masterName)
    {
        // $insData['cr_usr']		= $this -> fx -> clientId;
        // $insData['cr_date']		= date('Y-m-d H:i:s');
        $inserted = $this->db->insert($masterName, $insData);
        if ($inserted == 1) {
            $id = $this->db->insert_id();
            return $id;
        } else {
            return 0;
        }
    }

    public function createTableIndex($table_name, $column_name, $key_length = 0)
    {
        if (!empty($table_name)) {
            // $tblName    = $this->tblNamePrefix . $table_name;
            // if (!empty($form_source)) {
            //     $tblName = $table_name;
            // }

            if ($this->isTableExist($table_name)) {
                $sql    = "CREATE INDEX " . str_replace('_', '', $column_name) . " ON " . $this->compDb . "." . $table_name;
                if (!empty($key_length)) {
                    $sql    .= " (" . $column_name . "(" . $key_length . "))";
                } else {
                    $sql    .= " (" . $column_name . ")";
                }
                $this->db->query($sql);
            }
        }
        return true;
    }


    public function addTableColumn($table_name, $field)
    {
        $column        = array();
        $column[$field['field_name']]    = array();
        // $tblName    = $this->tblNamePrefix . $table_name;
        // if (!empty($form_source)) {
        //     $tblName    = $table_name;
        // }
        if (in_array($field['data_type'], array('integer', 'string', 'phone', 'email', 'attachment', 'linkurl'))) {
            $column[$field['field_name']]['type']    = 'VARCHAR';
        } else if ($field['data_type'] == 'datetime') {
            $column[$field['field_name']]['type']    = 'DATETIME';
        } else {
            $column[$field['field_name']]['type']    = $field['data_type'];
        }
        if (!empty($field['field_length']) && !empty($field['decimal_length'])) {
            $column[$field['field_name']]['constraint']    = $field['field_length'] . ',' . $field['decimal_length'];
        } else if (!empty($field['field_length'])) {
            $column[$field['field_name']]['constraint']    = $field['field_length'];
        }
        if (in_array($field['data_type'], array('text', 'date', 'datetime'))) {
            $column[$field['field_name']]['constraint']    = '';
        }
        $column[$field['field_name']]['null']    = true;
        if (strtolower($field['data_type']) == 'date') {
            $column[$field['field_name']]['default'] = (isset($field['default_value']) && ($field['default_value'] != '')) ? date('Y-m-d', strtotime($field['default_value'])) : NULL;
        } else if (strtolower($field['data_type']) == 'datetime') {
            $column[$field['field_name']]['default'] = (isset($field['default_value']) && ($field['default_value'] != '')) ? date('Y-m-d H:i:s', strtotime($field['default_value'])) : NULL;
        } else {
            $column[$field['field_name']]['default'] = (isset($field['default_value']) && ($field['default_value'] != '')) ? $field['default_value'] : NULL;
        }
        if ($this->isTableExist($table_name)) {
            if (!$this->myforge->add_column($table_name, $column)) {
                return false;
            }
        }
        return true;
    }

    public function isTableExist($table_name)
    {
        $restbl    = $this->db->query("SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_NAME = '" . $table_name . "' AND TABLE_SCHEMA='" . $this->compDb . "'");
        if ($restbl->num_rows() > 0) {
            return true;
        }
        return false;
    }

    public function deleteDynFieldData($data, $pKey = 'field_id')
    {
        $field_id  = $data['field_id'];
        $queue_id  = $data['queue_id'];
        if (!empty($field_id)) {
            $fieldData    = $this->getFieldData($field_id, $queue_id);
            $ticketTableName = "tms_ticket_master_" . $queue_id;
            $field_name    = $fieldData->data->field_name;
            if (!empty($ticketTableName) && !empty($field_name)) {
                //DROP TABLE COLUMN 
                $this->dropTableColumn($ticketTableName, $field_name);
            }

            $this->db->where('field_id', $field_id);
            $this->db->delete("tms_dynamicform_fields_$queue_id");

            $res = array('error' => false, 'message' => 'Record deleted');
        } else {
            $res = array('error' => true, 'message' => "Field Id is required");
        }
        return $res;
    }

    public function getFieldData($field_id, $queue_id)
    {
        $this->db->select("t1.*");
        $this->db->where("t1.field_id", $field_id);
        $this->db->from("tms_dynamicform_fields_$queue_id as t1");
        $result = $this->db->get()->row();
        return (object) array('status' => true, 'data' => $result);
    }


    public function dropTableColumn($table_name, $field_name)
    {
        $tblName    = $table_name;
        if ($this->isTableExist($tblName)) {
            if ($this->db->field_exists($field_name, $tblName)) {
                if (!$this->myforge->drop_column($tblName, $field_name)) {
                    return false;
                }
            }
        }
        return true;
    }

    public function getTableFieldMaxLengthRecord($table_name, $field_name)
    {
        $result = '';
        if (!empty($table_name) && !empty($field_name)) {
            $tblName    = $table_name;

            if ($this->isTableExist($tblName)) {
                $sql    = "SELECT `$field_name`, MAX(LENGTH(`$field_name`)) AS maxlen FROM " . $this->compDb . "." . $tblName . " GROUP BY `$field_name` ORDER BY `$field_name` DESC LIMIT 1";
                $res    = $this->db->query($sql);

                $result    = $res->result();
            }
        }
        return $result;
    }

    public function getTableFieldDedupeRecord($table_name, $field_name)
    {
        $result = '';
        if (!empty($table_name) && !empty($field_name)) {
            $tblName    = $table_name;
            $sql    = "SELECT COUNT(" . $field_name . ") AS dedupe_cnt FROM " . $tblName . " WHERE " . $field_name . " <> '' GROUP BY " . $field_name . " HAVING dedupe_cnt > 1;";
            $res    = $this->db->query($sql);
            $result    = $res->result();
        }
        return $result;
    }

    public function getTableData($table_name, $fieldName = '')
    {
        $count    = 0;

        $this->db->select("*");
        $this->db->from($table_name);
        if (!empty($fieldName)) {
            $where = "$fieldName IS NOT NULL AND $fieldName <> '' AND $fieldName <> '0000-00-00' AND $fieldName <> '0000-00-00 00:00:00'";
            $this->db->where($where, NULL, FALSE);
        }
        $count    = $this->db->count_all_results();

        return $count;
    }

    public function updateFormField($updateData, $field_id, $masterName)
    {
        $this->db->where("field_id", $field_id);
        $updated = $this->db->update($masterName, $updateData);
        if ($updated == 1) {
            return $field_id;
        } else {
            return 0;
        }
    }

    public function modifyTableColumn($table_name, $field, $oldField, $form_source = 0)
    {
        $tblName    = $table_name;
        $column    = array();
        $column[$oldField->field_name]    = array();
        $column[$oldField->field_name]['name']    = $field['field_name'];
        if (in_array($field['data_type'], array('integer', 'string', 'phone', 'email', 'attachment', 'linkurl'))) {
            $column[$oldField->field_name]['type']    = 'VARCHAR';
        } else if ($field['data_type'] == 'datetime') {
            $column[$oldField->field_name]['type']    = 'DATETIME';
        } else {
            $column[$oldField->field_name]['type']    = $field['data_type'];
        }
        if (!empty($field['field_length']) && !empty($field['decimal_length'])) {
            $column[$oldField->field_name]['constraint']    = $field['field_length'] . ',' . $field['decimal_length'];
        } else if (!empty($field['field_length'])) {
            $column[$oldField->field_name]['constraint']    = $field['field_length'];
        }
        if (in_array($field['data_type'], array('text', 'date', 'datetime'))) {
            $column[$oldField->field_name]['constraint']    = '';
        }
        $column[$oldField->field_name]['null']    = true;

        if (strtolower($field['data_type']) == 'date') {
            $column[$oldField->field_name]['default'] = (isset($field['default_value']) && ($field['default_value'] != '')) ? date('Y-m-d', strtotime($field['default_value'])) : NULL;
        } else if (strtolower($field['data_type']) == 'datetime') {
            $column[$oldField->field_name]['default'] = (isset($field['default_value']) && ($field['default_value'] != '')) ? date('Y-m-d H:i:s', strtotime($field['default_value'])) : NULL;
        } else {
            $column[$oldField->field_name]['default'] = (isset($field['default_value']) && ($field['default_value'] != '')) ? $field['default_value'] : NULL;
        }

        if ($this->isTableExist($tblName)) {
            if (!$this->myforge->modify_column($tblName, $column)) {
                return false;
            }
        }

        return true;
    }

    public function getTableIndex($table_name, $field_name, $finYrWise = 0, $form_source = 0)
    {
        $result = '';
        if (!empty($table_name) && !empty($field_name)) {
            $tblName    = table_name;

            if ($this->isTableExist($tblName)) {
                $sql    = "SHOW INDEXES FROM " . $tblName . " WHERE column_name='" . $field_name . "';";
                $res    = $this->db->query($sql);
                $result    = $res->result();
            }
        }
        return $result;
    }

    public function dropTableIndex($table_name, $index_name)
    {
        if (!empty($table_name)) {

            $tblName    = $table_name;

            if ($this->isTableExist($tblName)) {
                $this->db->query("ALTER TABLE " . $tblName . " DROP INDEX " . $index_name);
            }
        }
        return true;
    }

    // public function getFormSourceInfo($id) {
    // 	$this -> db -> where("t1.id", $id);
    // 	$this->db->select("t1.*");
    // 	$this->db->from("tms_dynamic_form_mapping as t1");
    // 	$result = $this->db->get()->row();
    // 	return (object) array('status' => true, 'data' => $result);
    // }
}
