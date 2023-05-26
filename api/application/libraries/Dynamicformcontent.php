<?php
date_default_timezone_set('Asia/Kolkata');
defined('BASEPATH') or exit('No direct script access allowed');
class Dynamicformcontent
{
    public function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->load->model('Formcontent_Model');
        // $this->CI->load->model("Acc_Model");
        // $this->CI->load->model("Dynamictable_Model");
        // $this->CI->load->model("Dynamicform_Model");
        // $this->CI->load->model("Formdesigner_Model");
        // $this->CI->load->library('upload');
        $this->filesize_allowed = 3;
    }

    // public function getSystemFormDetail($system_form_name) {
    // 	if(!empty($system_form_name)) {
    // 		$row	= $this->CI->Dynamicform_Model->db->where('name', $system_form_name)->get('acc_dynamic_form_mapping')->row();
    // 		if(!empty($row)) {
    // 			return $row->id;
    // 		}
    // 	}
    // 	return 0;
    // }
    // public function getPrimaryKey($system_form_name) {
    // 	switch($system_form_name) {
    // 		case 'lead':return 'lead_id';
    // 		case 'invoice':return 'sale_id';
    // 		case 'saleorder':return 'so_id';
    // 		case 'portallead':return 'id';
    // 		case 'contract':return 'contract_id';
    // 		default: return '';
    // 	}
    // }

    // public function getDynamicFormElements($system_form_name, $filterField=false) {
    // 	$system_form_id	= $this->getSystemFormDetail($system_form_name);
    // 	$formList		= $this->CI->Dynamicform_Model->getAllFormList(array('t1.system_form_mapping_id'=>$system_form_id));
    // 	$dataArr 		= array();
    // 	if(!empty($formList['data'])) {
    // 		$form_id		= $formList['data'][0]->id;
    // 		if($filterField) {
    // 			$resArr		= $this->CI->Dynamicform_Model->getFormFieldsByFormId($form_id, array('filter'=>1));
    // 			$fieldsArr	= $resArr->data;
    // 		} else {
    // 			$fieldsArr	= $this->CI->Formdesigner_Model->getFormDesignerFields($form_id);
    // 		}

    // 		$fetchdetailArr	= array("design_field_id", "field_group_id", "group_type", "group_name", "row_no", "colspan", "field_id", "field_name", "field_description", "data_type", "field_type", "max_line", "width", "field_length", "decimal_length", "field_type", "default_value", "values_from_db", "required");
    // 		$fieldDet		= array();
    // 		foreach($fieldsArr as $indx=>$fldDet) {
    // 			foreach($fldDet as $tblprop=>$propval) {
    // 				if(in_array($tblprop, $fetchdetailArr)) {
    // 					$fieldDet[$indx][$tblprop]	= !empty($propval)?$propval:'';
    // 				}
    // 				$fieldDet[$indx]['option_from']	= 0;
    // 				if(in_array(strtolower($fldDet->field_type), array('dropdown', 'multiselect', 'checkbox', 'radio'))) {
    // 					$fieldDet[$indx]['option_from']	= 1;
    // 				}
    // 			}
    // 		}
    // 		$dataArr		= array(
    // 			'formData'		=> $formList['data'][0],
    // 			'formFields'	=> $fieldDet,
    // 		);
    // 	}
    // 	return $dataArr; 
    // }

    // public function getDynamicFieldValues($system_form_name, $field_name) {
    // 	$system_form_id	= $this->getSystemFormDetail($system_form_name);
    // 	$formList		= $this->CI->Dynamicform_Model->getAllFormList(array('t1.system_form_mapping_id'=>$system_form_id));
    // 	$dataArr 		= array();
    // 	if(!empty($formList['data'])) {
    // 		$form_id		= $formList['data'][0]->id;
    // 		$formFields		= $this->CI->Dynamicform_Model->getFormFieldsByFormId($form_id, array('field_name'=>$field_name));
    // 		if(!empty($formFields->data)) {
    // 			$field	= $formFields->data[0];
    // 			if(!empty($field->values_from_db) && !empty($field->field_values)) {
    // 				$options	= $this->Formdesigner_Model->getDataThrughQuery($field->field_values);
    // 				foreach ($options as $Optkey => $Optvalue) {
    // 					$dataArr[]	= array('value'=> $Optvalue[$field->db_val_column], 'text'=>$Optvalue[$field->db_txt_column]);
    // 				}
    // 			} else if(!empty($field->field_values)) {
    // 				$options	= explode(',', $field->field_values);
    // 				foreach ($options as $Optvalue) {
    // 					$dataArr[]	= array('value'=> $Optvalue, 'text'=>$Optvalue);
    // 				}
    // 			}
    // 		}
    // 	}
    // 	return $dataArr; 
    // }

    // public function getDynamicFormElementsView($system_form_name, $dbData, $divStyle="") {
    // 	$system_form_id	= $this->getSystemFormDetail($system_form_name);
    // 	$primary_key	= $this->getPrimaryKey($system_form_name);
    // 	$formList	= $this->CI->Dynamicform_Model->getAllFormList(array('t1.system_form_mapping_id'=>$system_form_id));
    // 	$envArr = array();
    // 	if(!empty($formList['data'])) {
    // 		$form_id		= $formList['data'][0]->id;
    // 		$formFields		= $this->CI->Dynamicform_Model->getFormFieldsByFormId($form_id);
    // 		$designFields	= $this->CI->Formdesigner_Model->getFormDesignerFields($form_id);

    // 		$envArr			= array(
    // 			'form_id'		=> $form_id,
    // 			'formData'		=> $formList['data'][0],
    // 			'formFields'	=> $formFields->data,
    // 			'designFields'	=> $designFields,
    // 			'dbData'		=> $dbData,
    // 			'primary_key'	=> $primary_key,
    // 			'divStyle'		=> $divStyle
    // 		);
    // 	}
    // 	$this->CI->load->view("dynamictable/system_form_field_view", $envArr);
    // }

    // public function getDynamicSearchElementsView($system_form_name) {
    // 	$system_form_id	= $this->getSystemFormDetail($system_form_name);
    // 	$formList	= $this->CI->Dynamicform_Model->getAllFormList(array('t1.system_form_mapping_id'=>$system_form_id));
    // 	$envArr		= array();
    // 	if(!empty($formList['data'])) {
    // 		$form_id		= $formList['data'][0]->id;
    // 		$searchFields	= $this->CI->Dynamicform_Model->getFormFieldsByFormId($form_id, array('filter'=>1));
    // 		$envArr			= array(
    // 			'searchFields'	=> $searchFields->data
    // 		);
    // 	}
    // 	$this->CI->load->view("dynamictable/system_search_field_view", $envArr);
    // }

    // public function getAttachmentDetail($field_name, $table_name, $finYrWise, $primary_key, $pimary_val, $uploadFolder) {
    // 	$this->CI->Dynamictable_Model->db->where("t1.".$primary_key, $pimary_val);
    // 	$this->CI->Dynamictable_Model->db->select("t1.".$field_name);
    // 	if($finYrWise) {
    // 		$table_name	.= '_'.$this->CI->fx->clientFinYr; 
    // 	}
    // 	$this->CI->Dynamictable_Model->db->from($this->CI->fx->clientCompDb . ".".$table_name." AS t1");
    // 	$result = $this->CI->Dynamictable_Model->db->get()->row();

    // 	if(!empty($result->{$field_name})) {
    // 		$file_path = "./". $uploadFolder .$result->{$field_name};
    // 		if(file_exists($file_path)) {
    // 			return array('status'=>true, 'file_name'=>$result->{$field_name});
    // 		}
    // 	}
    // 	return array('status'=>false, 'file_name'=>'');
    // }

    public function getDynamicFieldValidationArray($queueId, $table_name, $primary_key_val)
    {
        // $system_form_id	= $this->getSystemFormDetail($system_form_name);
        $primary_key    = 'ticket_unique_id';
        // $formList    = $this->CI->Formcontent_Model->getFormFieldsByFormId($queueId);
        // fx::pr($formList,1);
        $rules            = array();

        if (!empty($queueId)) {
            $form_id        = $queueId;
            $formFields        = $this->CI->Formcontent_Model->getFormFieldsByFormId($form_id);
            // $uploadFolder    = $this->CI->Dynamictable_Model->uploadPath . 'comp' . $this->CI->fx->clientCompId . '/dynamicform/' . $form_id . '/';

            foreach ($formFields->data    as $fky => $fkval) {
                $fldRule    = array();
                if (in_array(strtolower($fkval->field_type), array('multiselect', 'checkbox'))) {
                    $fldRule['field']    = $fkval->field_name . '[]';
                } else {
                    $fldRule['field']    = $fkval->field_name;
                }
                $fldRule['label']    = $fkval->field_description;
                $frules        = array();
                $dataType    = strtolower($fkval->data_type);
                // if(!empty($fkval->required) && $dataType == 'attachment') {
                // 	if(!empty($primary_key_val)) {
                // 		$atchRes	= $this->getAttachmentDetail($fkval->field_name, $table_name, $finYrWise, $primary_key, $primary_key_val, $uploadFolder);
                // 		if(empty($atchRes['status']) && (empty($_FILES[$fkval->field_name]) || empty($_FILES[$fkval->field_name]['name']))) {
                // 			$frules[]	= 'required';
                // 		}
                // 	} else if(empty($_FILES[$fkval->field_name]) || empty($_FILES[$fkval->field_name]['name'])){
                // 		$frules[]	= 'required';
                // 	}
                // } else 
                if (!empty($fkval->required)) {
                    $frules[]    = 'required';
                }
                // }
                if (in_array($dataType, array('integer', 'phone'))) {
                    $frules[]    = 'integer';
                    $frules[]    = 'regex_match[/^[0-9]+$/]';
                } else if ($dataType == 'email') {
                    $frules[]    = 'valid_email';
                } else if ($dataType == 'linkurl') {
                    $frules[]    = 'valid_url';
                } else if ($dataType == 'decimal') {
                    $frules[]    = 'decimal';
                }
                //     else if ($dataType == 'date') {
                //     $frules[]    = 'regex_match[/^(\d{2})\/(\d{2})\/(\d{4})+$/]';
                // } else if ($dataType == 'datetime') {
                //     $frules[]    = 'regex_match[/^(\d{2})-(\d{2})-(\d{4}) (\d{2}):(\d{2})+$/]';
                // } 
                else if ($dataType == 'srting') {
                    if (in_array(strtolower($fkval->field_type), array('dropdown', 'multiselect', 'checkbox', 'radio'))) {
                        if (empty($fkval->values_from_db) && !empty($fkval->field_values)) {
                            $fldvals    = str_replace(' ', '', $fkval->field_values);
                            $frules[]    = 'in_list[' . $fldvals . ']';
                        }
                    }
                }
                if (!empty($fkval->field_length) && !empty($fkval->decimal_length)) {
                    $total_field_length    = $fkval->field_length + $fkval->decimal_length + 1;
                    $frules[]    = 'max_length[' . $total_field_length . ']';
                } else if (!empty($fkval->field_length) && in_array(strtolower($fkval->field_type), array('multiselect', 'checkbox'))) {
                    $fldMaxLen    = $fkval->field_length;
                    $fldname    = $fkval->field_name;
                    $frules[]    =    array('maxlength_callable', function ($value) use ($fldMaxLen, $fldname) {
                        if (!empty($_REQUEST[$fldname])) {
                            $tmpVal    = is_array($_REQUEST[$fldname]) ? implode(',', $_REQUEST[$fldname]) : $_REQUEST[$fldname];
                            if (strlen($tmpVal) > $fldMaxLen) {
                                return false;
                            }
                        }
                        return true;
                    });
                    $fldRule['errors'] = array("maxlength_callable" => 'Data length exhaust for ' . $fkval->field_description);
                } else if (!empty($fkval->field_length)) {
                    if (!in_array(strtolower($dataType), array('date', 'datetime'))) {
                        $frules[]    = 'max_length[' . $fkval->field_length . ']';
                    }
                }
                if (!empty($fkval->unique_field)) {
                    $fldname    = $fkval->field_name;
                    $frules[]    =   array('unique_callable', function ($value) use ($table_name, $fldname, $primary_key, $primary_key_val, $dataType) {
                        if ($dataType == 'date') {
                            $value    = !empty($value) ? date('Y-m-d', strtotime($value)) : '';
                        } else if ($dataType == 'datetime') {
                            $value    = !empty($value) ? date('Y-m-d H:i:s', strtotime($value)) : '';
                        }
                        if ($this->CI->Formcontent_Model->checkDataDedupe($table_name,  $fldname, $value, $primary_key, $primary_key_val)) {
                            return false;
                        } else {
                            return true;
                        }
                    });
                    $fldRule['errors'] = array("unique_callable" => 'Duplicate value found for ' . $fkval->field_description);
                }
                if (!empty($frules)) {
                    if (!empty($fkval->unique_field) || (!empty($fkval->field_length) && in_array(strtolower($fkval->field_type), array('multiselect', 'checkbox')))) {
                        $fldRule['rules']    = $frules;
                    } else {
                        $fldRule['rules']    = implode('|', $frules);
                    }
                    $rules[] = $fldRule;
                }
            }
        }

        return  $rules;
    }

    public function setDynamicFieldDataArray($queueId, $data)
    {
        // $system_form_id	= $this->getSystemFormDetail($system_form_name);
        $primary_key    = 'ticket_unique_id';
        // $formList	= $this->CI->Dynamicform_Model->getAllFormList(array('t1.system_form_mapping_id'=>$system_form_id));
        $pdata            = array();
        if (!empty($queueId)) {
            $form_id        = $queueId;
            // $table_name		= $formList['data'][0]->table_name;
            // $finYrWise		= $formList['data'][0]->financial_year_wise;
            $formFields        = $this->CI->Formcontent_Model->getFormFieldsByFormId($form_id);
            // $uploadFolder	= $this->CI->Dynamictable_Model->uploadPath.'comp'.$this->CI->fx->clientCompId.'/dynamicform/'.$form_id.'/'                 
            foreach ($formFields->data    as $fky => $fkval) {
                $data_type    = strtolower($fkval->data_type);
                $field_type    = strtolower($fkval->field_type);
                $NULL        = NULL;
                if (!empty($fkval->required)) {
                    $NULL    = '';
                }
                // if($field_type == 'attachment') {
                // 	$doupload = false;
                // 	if(!empty($data[$primary_key])) {
                // 		if(empty($_FILES[$fkval->field_name]) || empty($_FILES[$fkval->field_name]['name'])) {
                // 			$atchRes	= $this->getAttachmentDetail($fkval->field_name, $table_name, $finYrWise, $primary_key, $data[$primary_key], $uploadFolder);
                // 			if(!empty($fkval->required)){
                // 				if(!empty($atchRes['status'])) {
                // 					$data[$fkval->field_name]	= $atchRes['file_name'];
                // 				}
                // 			}
                // 		} else if(!empty($_FILES[$fkval->field_name]) && !empty($_FILES[$fkval->field_name]['name'])){
                // 			$doupload = true;
                // 		}
                // 	} if(!empty($_FILES[$fkval->field_name]) && !empty($_FILES[$fkval->field_name]['name'])){
                // 		$doupload = true;
                // 	}
                // 	if($doupload) {

                // 		$config	=	array();
                // 		$config['allowed_types']	= '*';
                // 		$config['max_size'] 		= $this->filesize_allowed * 1024;
                // 		$config['upload_path']		= $uploadFolder;
                // 		if(!is_dir($uploadFolder)) {
                // 			if(!mkdir($uploadFolder, 0777, true)) {
                // 				$error	= array('statusCode' => 400, 'error' => 'Permission denied. Unable to Upload Documents.');
                // 				log_message('error', 'statusCode:{statusCode}, error:{error}', $error);
                // 				return array('statusCode'=>400, 'data'=>array());
                // 			}
                // 		}
                // 		if(!empty($_FILES[$fkval->field_name]['name'])){
                // 			$config['file_name'] = $_FILES[$fkval->field_name]['name'];
                // 			$this->CI->upload->initialize($config);
                // 			if($this->CI->upload->do_upload($fkval->field_name)){
                // 				$data[$fkval->field_name] = $this->CI->upload->data('file_name');
                // 			} else {
                // 				$error	= array('statusCode' => 400, 'error' => "Somthing goes wrong while uploading file of ".$fkval->field_description);
                // 				log_message('error', 'statusCode:{statusCode}, error:{error}', $error);
                // 				return array('statusCode'=>400, 'data'=>array());
                // 			} 
                // 		}
                // 	}
                // 	$pdata[$fkval->field_name]	= !empty($data[$fkval->field_name])?$data[$fkval->field_name]:$NULL;
                // } else 
                if ($field_type == 'multiselect') {
                    $pdata[$fkval->field_name]    = !empty($data[$fkval->field_name]) ? implode(',', $data[$fkval->field_name]) : $NULL;
                } else if ($field_type == 'checkbox') {
                    $pdata[$fkval->field_name]    = isset($data[$fkval->field_name]) ? (is_array($data[$fkval->field_name]) ? implode(',', $data[$fkval->field_name]) : (!empty($data[$fkval->field_name]) ? $data[$fkval->field_name] : $NULL)) : $NULL;
                } else if ($data_type == 'text') {
                    $pdata[$fkval->field_name]    = !empty($data[$fkval->field_name]) ? addslashes(trim($data[$fkval->field_name])) : $NULL;
                } else if ($data_type == 'date') {
                    $pdata[$fkval->field_name]    = !empty($data[$fkval->field_name]) ? date('Y-m-d', strtotime($data[$fkval->field_name])) : $NULL;
                } else if ($data_type == 'datetime') {
                    $pdata[$fkval->field_name]    = !empty($data[$fkval->field_name]) ? date('Y-m-d H:i:s', strtotime($data[$fkval->field_name])) : $NULL;
                } else {
                    $pdata[$fkval->field_name]    = isset($data[$fkval->field_name]) ? is_array($data[$fkval->field_name]) ? implode(',', $data[$fkval->field_name]) : (!empty($data[$fkval->field_name]) ? $data[$fkval->field_name] : $NULL) : $NULL;
                }
            }
        }

        return  array('statusCode' => 200, 'data' => $pdata);
    }

    public function getDynamicFilterFieldConditions($queueId, $postData)
    {
        $retArray    = array();
        if (!empty($queueId)) {
            $form_id        = $queueId;
            $searchFields    = $this->CI->Formcontent_Model->getFormFieldsByFormId($form_id, array('filter' => 1));
            $subWhere    = array();
            $valarr        = array();
            $fieldNameIndx    = array();
            foreach ($searchFields->data as $sfky => $sfval) {
                if (in_array($sfval->field_type, array('checkbox', 'multiselect'))) {
                    foreach ($postData as $pkey => $pval) {

                        if (str_replace('[]', '', $pval['name']) == $sfval->field_name) {
                            if (empty($pval['value'])) continue;
                            $valarr[$sfval->field_name][]     = '"' . $pval['value'] . '"';
                            $fieldNameIndx[]    = $pkey;
                        }
                    }
                } else if (in_array($sfval->data_type, array('date', 'datetime'))) {
                    foreach ($postData as $pkey => $pval) {
                        if ($pval['name'] == 'from_' . $sfval->field_name) {
                            $fieldNameIndx[]    = $pkey;
                            if (empty($pval['value'])) continue;
                            if ($sfval->data_type == 'date') {
                                $valarr[$sfval->field_name . ' >']    = '"' . date('Y-m-d', strtotime($pval['value'])) . '"';
                            } else if ($sfval->data_type == 'datetime') {
                                $valarr[$sfval->field_name . ' >']    = '"' . date('Y-m-d H:i', strtotime($pval['value'])) . '"';
                            }
                        } else if ($pval['name'] == 'to_' . $sfval->field_name) {
                            $fieldNameIndx[]    = $pkey;
                            if (empty($pval['value'])) continue;
                            if ($sfval->data_type == 'date') {
                                $valarr[$sfval->field_name . ' <']    = '"' . date('Y-m-d', strtotime($pval['value'])) . '"';
                            } else if ($sfval->data_type == 'datetime') {
                                $valarr[$sfval->field_name . ' <']    = '"' . date('Y-m-d H:i', strtotime($pval['value'])) . '"';
                            }
                        }
                    }
                } else {
                    if ($sfval->data_type !== 'phone' && $sfval->data_type !== 'text' && $sfval->data_type !== 'date' && $sfval->data_type !== 'datetime' && $sfval->data_type !== 'email' &&  $sfval->data_type !== 'string') {
                        foreach ($postData as $pkey => $pval) {
                            if ($pval['name'] == 'from_' . $sfval->field_name) {
                                $fieldNameIndx[]    = $pkey;
                                if (empty($pval['value'])) continue;
                                $valarr[$sfval->field_name . ' >']    =  $pval['value'] ;
                            } else if ($pval['name'] == 'to_' . $sfval->field_name) {
                                $fieldNameIndx[]    = $pkey;
                                if (empty($pval['value'])) continue;
                                $valarr[$sfval->field_name . ' <']    =  $pval['value'] ;
                            }
                        }
                    } else {
                        foreach ($postData as $pkey => $pval) {
                            if ($pval['name'] == $sfval->field_name) {
                                $fieldNameIndx[]    = $pkey;
                                if (empty($pval['value'])) continue;
                                $valarr[$sfval->field_name]     = '"' . $pval['value'] . '"';
                            }
                        }
                    }
                }
            }
            if (!empty($valarr)) {
                foreach ($valarr as $fldky => $fldsrch) {
                    if (is_array($fldsrch)) {
                        $findset = [];
                        foreach ($fldsrch as $fldsrchvl)
                            $findset[] = "FIND_IN_SET(" . $fldsrchvl . ", {$fldky}) ";
                        $subWhere[] = implode(' OR ', $findset);
                    } else {
                        $subWhere[]    = " $fldky=" . $fldsrch;
                    }
                }
            }
            $retArray['subwherestr']    = implode(' AND ', $subWhere);
            $retArray['srchkeyindex']    = $fieldNameIndx;
        }
        return $retArray;
    }

    public function getDynamicFieldSelectList($queueId, $alias = '')
    {
        $colAias    = '';
        $tblAlias    = '';
        if (!empty($alias)) {
            $tblAlias    = $alias . '.';
            $colAias    = $alias . '_';
        }
        // $system_form_id	= $this->getSystemFormDetail($system_form_name);
        // $formList	= $this->CI->Dynamicform_Model->getAllFormList(array('t1.system_form_mapping_id'=>$system_form_id));
        $selectArr    = array();
        $aliasArr    = array();
        $joinArr    = array();
        if (!empty($queueId)) {
            $form_id    = $queueId;
            $listFields    = $this->CI->Formcontent_Model->getFormFieldsByFormId($form_id, array('list_column' => 1));

            foreach ($listFields->data as $lfky => $lfval) {
                if (strtolower($lfval->data_type) == 'date') {
                    $selectArr[]    = "IF((" . $tblAlias . $lfval->field_name . " IS NOT NULL AND TRIM(" . $tblAlias . $lfval->field_name . ") <> '' AND TRIM(" . $tblAlias . $lfval->field_name . ") <> '0000-00-00'), DATE_FORMAT(" . $tblAlias . $lfval->field_name . ", '%d-%m-%Y'), '') as " . $colAias . $lfval->field_name;
                    $aliasArr[]        = $tblAlias . $lfval->field_name;
                } else if (strtolower($lfval->data_type) == 'datetime') {
                    $selectArr[]    = "IF((" . $tblAlias . $lfval->field_name . " IS NOT NULL AND TRIM(" . $tblAlias . $lfval->field_name . ") <> '' AND TRIM(" . $tblAlias . $lfval->field_name . ") <> '0000-00-00 00:00:00'), DATE_FORMAT(" . $tblAlias . $lfval->field_name . ", '%d-%m-%Y %H:%i'), '') as " . $colAias . $lfval->field_name;
                    $aliasArr[]        = $tblAlias . $lfval->field_name;
                } else if (!empty($lfval->values_from_db) && !empty($lfval->db_val_column) && !empty($lfval->db_txt_column) && ($lfval->db_val_column != $lfval->db_txt_column)) {
                    $sqlStmt    = str_replace(';', '', $lfval->field_values);
                    $sqlStmt    = str_ireplace('FROM ', 'FROM ' . $this->CI->Dynamicform_Model->compDb . '.', $sqlStmt);
                    $sqlStmt    = str_ireplace('JOIN ', 'JOIN ' . $this->CI->Dynamicform_Model->compDb . '.', $sqlStmt);
                    $joinArr[]        = array(
                        'table'        => "({$sqlStmt}) j{$lfky}",
                        'condition'    => "FIND_IN_SET(j{$lfky}." . $lfval->db_val_column . ", " . $tblAlias . $lfval->field_name . ")",
                        'type'        => "LEFT"
                    );
                    $selectArr[]    = "GROUP_CONCAT(DISTINCT j{$lfky}." . $lfval->db_txt_column . ") " . $colAias . $lfval->field_name;
                    if (!in_array(strtolower($lfval->field_type), array('multiselect', 'checkbox'))) {
                        $aliasArr[]        = $tblAlias . $lfval->field_name;
                    }
                } else {
                    $selectArr[]    = $tblAlias . $lfval->field_name . ' AS ' . $colAias . $lfval->field_name;
                    $aliasArr[]        = $colAias . $lfval->field_name;
                }
            }
        }
        $selectStr = !empty($selectArr) ? ',' . implode(',', $selectArr) : '';
        $aliasStr  = !empty($aliasArr) ? ',' . implode(',', $aliasArr) : '';
        $this->CI->selectlist    = $selectStr;
        $this->CI->aliaslist    = $aliasStr;
        $this->CI->joinlist        = $joinArr;
        return $this->CI;
    }

    // public function getDynamicFieldExportList($system_form_name, $alias='') {
    // 	$colAias	= '';
    // 	$tblAlias	= '';
    // 	if(!empty($alias)) {
    // 		$tblAlias	= $alias.'.';
    // 		$colAias	= $alias.'_';
    // 	}
    // 	$system_form_id	= $this->getSystemFormDetail($system_form_name);
    // 	$formList	= $this->CI->Dynamicform_Model->getAllFormList(array('t1.system_form_mapping_id'=>$system_form_id));
    // 	$selectArr	= array();
    // 	$aliasArr	= array();
    // 	$joinArr	= array();
    // 	if(!empty($formList['data'])) {
    // 		$form_id	= $formList['data'][0]->id;
    // 		$listFields	= $this->CI->Dynamicform_Model->getFormFieldsByFormId($form_id, array('excel_export'=>1));

    // 		foreach($listFields->data as $lfky=>$lfval){
    // 			if(strtolower($lfval->data_type) == 'date') {
    // 				$selectArr[]	= "IF((".$tblAlias.$lfval->field_name." IS NOT NULL AND TRIM(".$tblAlias.$lfval->field_name.") <> '' AND TRIM(".$tblAlias.$lfval->field_name.") <> '0000-00-00'), DATE_FORMAT(".$tblAlias.$lfval->field_name.", '%d-%m-%Y'), '') as ".$colAias.$lfval->field_name;
    // 				$aliasArr[]		= $tblAlias.$lfval->field_name;
    // 			} else if(strtolower($lfval->data_type) == 'datetime') {
    // 				$selectArr[]	= "IF((".$tblAlias.$lfval->field_name." IS NOT NULL AND TRIM(".$tblAlias.$lfval->field_name.") <> '' AND TRIM(".$tblAlias.$lfval->field_name.") <> '0000-00-00 00:00:00'), DATE_FORMAT(".$tblAlias.$lfval->field_name.", '%d-%m-%Y %H:%i'), '') as ".$colAias.$lfval->field_name;
    // 				$aliasArr[]		= $tblAlias.$lfval->field_name;
    // 			} else if(!empty($lfval->values_from_db) && !empty($lfval->db_val_column) && !empty($lfval->db_txt_column) && ($lfval->db_val_column != $lfval->db_txt_column)) {
    // 				$sqlStmt	= str_replace(';', '', $lfval->field_values);
    // 				$sqlStmt	= str_ireplace('FROM ', 'FROM '.$this->CI->Dynamicform_Model->compDb.'.', $sqlStmt);
    // 				$joinArr[]		= array(
    // 					'table'		=> "({$sqlStmt}) j{$lfky}",
    // 					'condition'	=> "FIND_IN_SET(j{$lfky}.".$lfval->db_val_column.", ".$tblAlias.$lfval->field_name.")",
    // 					'type'		=> "LEFT"
    // 					);
    // 				$selectArr[]	= "GROUP_CONCAT(DISTINCT j{$lfky}.".$lfval->db_txt_column.") ".$colAias.$lfval->field_name;
    // 				if(!in_array(strtolower($lfval->field_type), array('multiselect', 'checkbox'))) {
    // 					$aliasArr[]		= $tblAlias.$lfval->field_name;
    // 				}
    // 			} else {
    // 				$selectArr[]	= $tblAlias.$lfval->field_name.' AS '.$colAias.$lfval->field_name;
    // 				$aliasArr[]		= $colAias.$lfval->field_name;
    // 			}
    // 		}
    // 	}
    // 	$selectStr = !empty($selectArr)?','.implode(',', $selectArr):'';
    // 	$aliasStr  = !empty($aliasArr)?','.implode(',', $aliasArr):'';
    // 	$this->CI->selectlist	= $selectStr;
    // 	$this->CI->aliaslist	= $aliasStr;
    // 	$this->CI->joinlist		= $joinArr;
    // 	return $this->CI;
    // }

    public function getDynamicFieldListColumnsHeader($queueId)
    {
        $headerArr    = array();
        if (!empty($queueId)) {
            $form_id    = $queueId;
            $listFields    = $this->CI->Formcontent_Model->getFormFieldsByFormId($form_id, array('list_column' => 1));

            foreach ($listFields->data as $lfky => $lfval) {
                $headerArr[]    = $lfval->field_description;
            }
        }
        return $headerArr;
    }

    public function getDynamicFieldListColumns($queueId, $alias = '')
    {
        $colAias    = '';
        if (!empty($alias)) {
            $colAias    = $alias . '_';
        }
        $colsArr    = array();
        if (!empty($queueId)) {
            $form_id    = $queueId;
            $listFields    = $this->CI->Formcontent_Model->getFormFieldsByFormId($form_id, array('list_column' => 1));

            foreach ($listFields->data as $lfky => $lfval) {
                $colsArr[]    = $colAias . $lfval->field_name;
            }
        }
        return $colsArr;
    }

    // public function getDynamicFieldAllDataColumns($system_form_name, $alias='') {
    // 	$colAias	= '';
    // 	if(!empty($alias)) {
    // 		$colAias	= $alias.'_';
    // 	}
    // 	$system_form_id	= $this->getSystemFormDetail($system_form_name);
    // 	$colsArr	= array();
    // 	$formList	= $this->CI->Dynamicform_Model->getAllFormList(array('t1.system_form_mapping_id'=>$system_form_id));
    // 	if(!empty($formList['data'])) {
    // 		$form_id	= $formList['data'][0]->id;
    // 		$listFields	= $this->CI->Dynamicform_Model->getFormFieldsByFormId($form_id);

    // 		foreach($listFields->data as $lfky=>$lfval){
    // 			$colsArr[$colAias.$lfval->field_name]	= $lfval->field_description;
    // 		}
    // 	}
    // 	return $colsArr;
    // }

    // public function getDynamicFilterFieldConditionsForExcel($system_form_name, $postData) {
    // 	$system_form_id	= $this->getSystemFormDetail($system_form_name);
    // 	$formList	= $this->CI->Dynamicform_Model->getAllFormList(array('t1.system_form_mapping_id'=>$system_form_id));
    // 	$retArray	= array();
    // 	if(!empty($formList['data'])) {
    // 		$form_id		= $formList['data'][0]->id;
    // 		$searchFields	= $this->CI->Dynamicform_Model->getFormFieldsByFormId($form_id, array('filter'=>1));
    // 		$subWhere	= array();
    // 		$valarr		= array();
    // 		$fieldNameIndx	= array();
    // 		foreach($searchFields->data as $sfky=>$sfval){
    // 			if(in_array($sfval->data_type, array('date', 'datetime'))) {
    // 				foreach($postData as $pkey=>$pval) {
    // 					if($pkey == 'from_'.$sfval->field_name) {
    // 						if(empty($pval)) continue;
    // 						if($sfval->data_type == 'date') {
    // 							$valarr[$sfval->field_name.' >']	= '"'.date('Y-m-d', strtotime($pval)).'"';
    // 						} else if($sfval->data_type == 'datetime') {
    // 							$valarr[$sfval->field_name.' >']	= '"'.date('Y-m-d H:i', strtotime($pval)).'"';
    // 						}
    // 					} else if($pkey == 'to_'.$sfval->field_name) {
    // 						if(empty($pval)) continue;
    // 						if($sfval->data_type == 'date') {
    // 							$valarr[$sfval->field_name.' <']	= '"'.date('Y-m-d', strtotime($pval)).'"';
    // 						} else if($sfval->data_type == 'datetime') {
    // 							$valarr[$sfval->field_name.' <']	= '"'.date('Y-m-d H:i', strtotime($pval)).'"';
    // 						}
    // 					}
    // 				}
    // 			} else if(in_array($sfval->field_type, array('checkbox', 'multiselect'))) {
    // 				foreach($postData as $pkey=>$pval) {
    // 					if($pkey == $sfval->field_name) {
    // 						if(empty($pval)) continue;
    // 						$valarr[$sfval->field_name]	 = $pval;
    // 					}
    // 				}
    // 			} else {
    // 				foreach($postData as $pkey=>$pval) {
    // 					if($pkey == $sfval->field_name) {
    // 						if(empty($pval)) continue;
    // 						$valarr[$sfval->field_name]	 = '"'.$pval.'"';
    // 					}
    // 				}
    // 			}
    // 		}
    // 		if(!empty($valarr)) {
    // 			foreach($valarr as $fldky=>$fldsrch) {
    // 				if(is_array($fldsrch)) {
    // 					$findset 	= [];
    // 					foreach($fldsrch as $fldsrchvl)
    // 						$findset[] = "FIND_IN_SET('".$fldsrchvl."', {$fldky}) ";
    // 					$subWhere[] = implode(' OR ', $findset);
    // 				} else {
    // 					$subWhere[]	= " $fldky=".$fldsrch;
    // 				}
    // 			}
    // 		}
    // 		$retArray	= implode(' AND ', $subWhere);
    // 	}
    // 	return $retArray;
    // }

    // public function getDynamicFieldExportSelectList($system_form_name, $alias='') {
    // 	$colAias	= '';
    // 	$tblAlias	= '';
    // 	if(!empty($alias)) {
    // 		$tblAlias	= $alias.'.';
    // 		$colAias	= $alias.'_';
    // 	}
    // 	$system_form_id	= $this->getSystemFormDetail($system_form_name);
    // 	$formList	= $this->CI->Dynamicform_Model->getAllFormList(array('t1.system_form_mapping_id'=>$system_form_id));
    // 	$selectArr	= array();
    // 	$aliasArr	= array();
    // 	$joinArr	= array();
    // 	if(!empty($formList['data'])) {
    // 		$form_id	= $formList['data'][0]->id;
    // 		$listFields	= $this->CI->Dynamicform_Model->getFormFieldsByFormId($form_id, array('excel_export'=>1));

    // 		foreach($listFields->data as $lfky=>$lfval){
    // 			if(strtolower($lfval->data_type) == 'date') {
    // 				$selectArr[]	= "IF((".$tblAlias.$lfval->field_name." IS NOT NULL AND TRIM(".$tblAlias.$lfval->field_name.") <> '' AND TRIM(".$tblAlias.$lfval->field_name.") <> '0000-00-00'), DATE_FORMAT(".$tblAlias.$lfval->field_name.", '%d-%m-%Y'), '') as ".$colAias.$lfval->field_name;
    // 				$aliasArr[]		= $colAias.$lfval->field_name;
    // 			} else if(strtolower($lfval->data_type) == 'datetime') {
    // 				$selectArr[]	= "IF((".$tblAlias.$lfval->field_name." IS NOT NULL AND TRIM(".$tblAlias.$lfval->field_name.") <> '' AND TRIM(".$tblAlias.$lfval->field_name.") <> '0000-00-00 00:00:00'), DATE_FORMAT(".$tblAlias.$lfval->field_name.", '%d-%m-%Y %H:%i'), '') as ".$colAias.$lfval->field_name;
    // 				$aliasArr[]		= $colAias.$lfval->field_name;
    // 			} else if(!empty($lfval->values_from_db) && !empty($lfval->db_val_column) && !empty($lfval->db_txt_column) && ($lfval->db_val_column != $lfval->db_txt_column)) {
    // 				$sqlStmt	= str_replace(';', '', $lfval->field_values);
    // 				$sqlStmt	= str_ireplace('FROM ', 'FROM '.$this->CI->Dynamicform_Model->compDb.'.', $sqlStmt);
    // 				$joinArr[]		= array(
    // 					'table'		=> "({$sqlStmt}) j{$lfky}",
    // 					'condition'	=> "FIND_IN_SET(j{$lfky}.".$lfval->db_val_column.", ".$tblAlias.$lfval->field_name.")",
    // 					'type'		=> "LEFT"
    // 					);
    // 				$selectArr[]	= "GROUP_CONCAT(DISTINCT j{$lfky}.".$lfval->db_txt_column.") ".$colAias.$lfval->field_name;
    // 			} else {
    // 				$selectArr[]	= $lfval->field_name ;
    // 				$aliasArr[]		= $lfval->field_name;
    // 			}
    // 		}
    // 	}

    // 	$selectStr = !empty($selectArr)?','.implode(',', $selectArr):'';
    // 	$aliasStr  = !empty($aliasArr)?','.implode(',', $aliasArr):'';
    // 	$this->CI->selectlist	= $selectStr;
    // 	$this->CI->aliaslist	= $aliasStr;
    // 	$this->CI->joinlist		= $joinArr;

    // 	return $this->CI;
    // }

    // public function getDynamicFieldExcelColumnsHeader($system_form_name, $alias='') {
    // 	$colAias	= '';
    // 	if(!empty($alias)) {
    // 		$colAias	= $alias.'_';
    // 	}
    // 	$system_form_id	= $this->getSystemFormDetail($system_form_name);
    // 	$formList	= $this->CI->Dynamicform_Model->getAllFormList(array('t1.system_form_mapping_id'=>$system_form_id));
    // 	$headerList	= array();
    // 	if(!empty($formList['data'])) {
    // 		$form_id	= $formList['data'][0]->id;
    // 		$listFields	= $this->CI->Dynamicform_Model->getFormFieldsByFormId($form_id, array('excel_export'=>1));

    // 		foreach($listFields->data as $lfky=>$lfval){
    // 			$headerList[$colAias.$lfval->field_name] = $lfval->field_description;
    // 		}
    // 	}
    // 	return $headerList;
    // }

    // public function getDynamicFieldPrintList($system_form_name, $alias='') {
    // 	$colAias	= '';
    // 	$tblAlias	= '';
    // 	if(!empty($alias)) {
    // 		$tblAlias	= $alias.'.';
    // 		$colAias	= $alias.'_';
    // 	}
    // 	$system_form_id	= $this->getSystemFormDetail($system_form_name);
    // 	$formList	= $this->CI->Dynamicform_Model->getAllFormList(array('t1.system_form_mapping_id'=>$system_form_id));
    // 	$selectArr	= array();
    // 	$aliasArr	= array();
    // 	$joinArr	= array();
    // 	if(!empty($formList['data'])) {
    // 		$form_id	= $formList['data'][0]->id;
    // 		$listFields	= $this->CI->Dynamicform_Model->getFormFieldsByFormId($form_id, array('print_pdf'=>1));

    // 		foreach($listFields->data as $lfky=>$lfval){
    // 			if(strtolower($lfval->data_type) == 'date') {
    // 				$selectArr[]	= "IF((".$tblAlias.$lfval->field_name." IS NOT NULL AND TRIM(".$tblAlias.$lfval->field_name.") <> '' AND TRIM(".$tblAlias.$lfval->field_name.") <> '0000-00-00'), DATE_FORMAT(".$tblAlias.$lfval->field_name.", '%d-%m-%Y'), '') as ".$colAias.$lfval->field_name;
    // 				$aliasArr[]		= $colAias.$lfval->field_name;
    // 			} else if(strtolower($lfval->data_type) == 'datetime') {
    // 				$selectArr[]	= "IF((".$tblAlias.$lfval->field_name." IS NOT NULL AND TRIM(".$tblAlias.$lfval->field_name.") <> '' AND TRIM(".$tblAlias.$lfval->field_name.") <> '0000-00-00 00:00:00'), DATE_FORMAT(".$tblAlias.$lfval->field_name.", '%d-%m-%Y %H:%i'), '') as ".$colAias.$lfval->field_name;
    // 				$aliasArr[]		= $colAias.$lfval->field_name;
    // 			} else if(!empty($lfval->values_from_db) && !empty($lfval->db_val_column) && !empty($lfval->db_txt_column) && ($lfval->db_val_column != $lfval->db_txt_column)) {
    // 				$sqlStmt	= str_replace(';', '', $lfval->field_values);
    // 				$sqlStmt	= str_ireplace('FROM ', 'FROM '.$this->CI->Dynamicform_Model->compDb.'.', $sqlStmt);
    // 				$joinArr[]		= array(
    // 					'table'		=> "({$sqlStmt}) j{$lfky}",
    // 					'condition'	=> "FIND_IN_SET(j{$lfky}.".$lfval->db_val_column.", ".$tblAlias.$lfval->field_name.")",
    // 					'type'		=> "LEFT"
    // 					);
    // 				$selectArr[]	= "GROUP_CONCAT(DISTINCT j{$lfky}.".$lfval->db_txt_column.") ".$colAias.$lfval->field_name;
    // 			} else {
    // 				$selectArr[]	= $tblAlias.$lfval->field_name.' AS '.$colAias.$lfval->field_name;
    // 				$aliasArr[]		= $colAias.$lfval->field_name;
    // 			}
    // 		}
    // 	}
    // 	$selectStr = !empty($selectArr)?','.implode(',', $selectArr):'';
    // 	$aliasStr  = !empty($aliasArr)?','.implode(',', $aliasArr):'';
    // 	$this->CI->selectlist	= $selectStr;
    // 	$this->CI->aliaslist	= $aliasStr;
    // 	$this->CI->joinlist		= $joinArr;
    // 	return $this->CI;
    // }

    // public function getDynamicFieldPrintColumnsHeader($system_form_name, $alias='') {
    // 	$colAias	= '';
    // 	if(!empty($alias)) {
    // 		$colAias	= $alias.'_';
    // 	}
    // 	$system_form_id	= $this->getSystemFormDetail($system_form_name);
    // 	$formList	= $this->CI->Dynamicform_Model->getAllFormList(array('t1.system_form_mapping_id'=>$system_form_id));
    // 	$headerList	= array();
    // 	if(!empty($formList['data'])) {
    // 		$form_id	= $formList['data'][0]->id;
    // 		$listFields	= $this->CI->Dynamicform_Model->getFormFieldsByFormId($form_id, array('print_pdf'=>1));

    // 		foreach($listFields->data as $lfky=>$lfval){
    // 			$headerList[$colAias.$lfval->field_name] = $lfval->field_description;
    // 		}
    // 	}
    // 	return $headerList;
    // }


    // public function setDynamicFieldDataArrayForApi($system_form_name, $data) {

    // 	$system_form_id	= $this->getSystemFormDetail($system_form_name);
    // 	$primary_key	= $this->getPrimaryKey($system_form_name);
    // 	$formList		= $this->CI->Dynamicform_Model->getAllFormList(array('t1.system_form_mapping_id'=>$system_form_id));
    // 	$pdata			= array();
    // 	if(!empty($formList['data'])) {
    // 		$form_id		= $formList['data'][0]->id;
    // 		$table_name		= $formList['data'][0]->table_name;
    // 		$finYrWise		= $formList['data'][0]->financial_year_wise;
    // 		$formFields		= $this->CI->Dynamicform_Model->getFormFieldsByFormId($form_id);

    // 		foreach($formFields->data	AS $fky=>$fkval) {
    // 			$data_type	= strtolower($fkval->data_type);
    // 			$field_type	= strtolower($fkval->field_type);
    // 			$NULL		= NULL;
    // 			if(!empty($fkval->required)){
    // 				$NULL	= '';
    // 			}
    // 			if($field_type == 'attachment') {
    // 				continue;
    // 			}
    // 			if(in_array(strtolower($fkval->field_type), array('dropdown', 'multiselect', 'checkbox', 'radio'))) {
    // 				if(empty($fkval->values_from_db) && !empty($fkval->field_values) && !empty($fkval->db_val_column) && !empty($fkval->db_txt_column) && ($fkval->db_val_column != $fkval->db_txt_column)) {
    // 					$selectQry	= str_replace(';','',$fkval->field_values);
    // 					$selectQry	= str_ireplace('FROM ', 'FROM '.$this->CI->Dynamicform_Model->compDb.'.', $selectQry);
    // 					$dataValArr	= !empty($data[$fkval->field_name])?explode(',', $data[$fkval->field_name]):'';
    // 					if(!empty($dataValArr)) {
    // 						$dataValStr	= implode("','", $dataValArr);
    // 						$select	= $this->db->query("SELECT {$fkval->db_val_column}, {$fkval->db_txt_column} FROM (".$selectQry.") AS derivedtbl WHERE {$fkval->db_txt_column} IN('".$dataValStr."')");
    // 						$dbRes	= $select->result();
    // 						$insValArr	= array();
    // 						if(!empty($dbRes)) {
    // 							foreach($dbRes as $rky=>$rkval) {
    // 								$insVal[]	= $rkval->{$fkval->db_val_column};
    // 							}
    // 						}
    // 						if(!empty($insValArr)) {
    // 							$pdata[$fkval->field_name]	= is_array($insValArr)?implode(',', $insValArr):(!empty($insValArr)?$insValArr:$NULL);
    // 						}
    // 					}
    // 				} else if($field_type == 'multiselect' || $field_type == 'checkbox') {
    // 					$dbvalTxt	= !empty($data[$fkval->field_name])?explode(',', $data[$fkval->field_name]):'';
    // 					$pdata[$fkval->field_name]	= isset($dbvalTxt)?(is_array($dbvalTxt)?implode(',', $dbvalTxt):(!empty($dbvalTxt)?$dbvalTxt:$NULL)):$NULL;	
    // 				} else {
    // 					$pdata[$fkval->field_name]	= isset($data[$fkval->field_name])?is_array($data[$fkval->field_name])?implode(',', $data[$fkval->field_name]):(!empty($data[$fkval->field_name])?$data[$fkval->field_name]:$NULL):$NULL;
    // 				}
    // 			} else if($data_type == 'text') {
    // 				$pdata[$fkval->field_name]	= !empty($data[$fkval->field_name])?addslashes(trim($data[$fkval->field_name])):$NULL;
    // 			} else if($data_type == 'date') {
    // 				$pdata[$fkval->field_name]	= !empty($data[$fkval->field_name])?date('Y-m-d', strtotime($data[$fkval->field_name])):$NULL;
    // 			} else if($data_type == 'datetime') {
    // 				$pdata[$fkval->field_name]	= !empty($data[$fkval->field_name])?date('Y-m-d H:i:s', strtotime($data[$fkval->field_name])):$NULL;
    // 			} else {
    // 				$pdata[$fkval->field_name]	= isset($data[$fkval->field_name])?is_array($data[$fkval->field_name])?implode(',', $data[$fkval->field_name]):(!empty($data[$fkval->field_name])?$data[$fkval->field_name]:$NULL):$NULL;
    // 			}
    // 		}
    // 	}

    // 	return  array('statusCode'=>200, 'data'=>$pdata);
    // }

    // public function getDynamicFieldLabels($system_form_name) {
    // 	$system_form_id	= $this->getSystemFormDetail($system_form_name);
    // 	$colsArr	= array();
    // 	$formList	= $this->CI->Dynamicform_Model->getAllFormList(array('t1.system_form_mapping_id'=>$system_form_id));
    // 	if(!empty($formList['data'])) {
    // 		$form_id	= $formList['data'][0]->id;
    // 		$listFields	= $this->CI->Dynamicform_Model->getFormFieldsByFormId($form_id);

    // 		foreach($listFields->data as $lfky=>$lfval){
    // 			$colsArr[$lfval->field_name]	= $lfval->field_description;
    // 		}
    // 	}
    // 	return $colsArr;
    // }


    // public function getDynamicFieldValidationArrayForApi($system_form_name, $primary_key_val) {

    // 	$system_form_id	= $this->getSystemFormDetail($system_form_name);
    // 	$primary_key	= $this->getPrimaryKey($system_form_name);
    // 	$formList		= $this->CI->Dynamicform_Model->getAllFormList(array('t1.system_form_mapping_id'=>$system_form_id));
    // 	$rules			= array();

    // 	if(!empty($formList['data'])) {
    // 		$form_id		= $formList['data'][0]->id;
    // 		$table_name		= $formList['data'][0]->table_name;
    // 		$finYrWise		= $formList['data'][0]->financial_year_wise;
    // 		$formFields		= $this->CI->Dynamicform_Model->getFormFieldsByFormId($form_id);
    // 		foreach($formFields->data	AS $fky=>$fkval) {
    // 			$fldRule	= array();
    // 			if(in_array(strtolower($fkval->field_type), array('attachment'))) {
    // 				continue;
    // 			}
    // 			if(in_array(strtolower($fkval->field_type), array('multiselect', 'checkbox'))) {
    // 				$fldRule['field']	= $fkval->field_name.'[]';
    // 			} else {
    // 				$fldRule['field']	= $fkval->field_name;
    // 			}
    // 			$fldRule['label']	= $fkval->field_description;
    // 			$frules		= array();
    // 			$dataType	= strtolower($fkval->data_type);
    // 			if(in_array($dataType, array('integer', 'phone'))){
    // 				$frules[]	= 'integer';
    // 				$frules[]	= 'regex_match[/^[0-9]+$/]';
    // 			} else if($dataType == 'email') {
    // 				$frules[]	= 'valid_email';
    // 			} else if($dataType == 'linkurl') {
    // 				$frules[]	= 'valid_url';
    // 			} else if($dataType == 'decimal') {
    // 				$frules[]	= 'decimal';
    // 			} else if($dataType == 'date') {
    // 				$frules[]	= 'regex_match[/^(\d{2})-(\d{2})-(\d{4})+$/]';

    // 			} else if($dataType == 'datetime') {
    // 				$frules[]	= 'regex_match[/^(\d{2})-(\d{2})-(\d{4}) (\d{2}):(\d{2})+$/]';
    // 			} else if($dataType == 'srting') {
    // 				if(in_array(strtolower($fkval->field_type), array('dropdown', 'multiselect', 'checkbox', 'radio'))) {
    // 					if(empty($fkval->values_from_db) && !empty($fkval->field_values)) {
    // 						$fldvals	= str_replace(' ','', $fkval->field_values);
    // 						$frules[]	= 'in_list['.$fldvals.']';
    // 					}
    // 				}
    // 			}
    // 			if(!empty($fkval->field_length) && !empty($fkval->decimal_length)) {
    // 				$total_field_length	= $fkval->field_length + $fkval->decimal_length + 1;
    // 				$frules[]	= 'max_length['.$total_field_length.']';
    // 			} else if(!empty($fkval->field_length)) {
    // 				$frules[]	= 'max_length['.$fkval->field_length.']';
    // 			}
    // 			if(!empty($fkval->unique_field)) {
    // 				$fldname	= $fkval->field_name;
    // 				$frules[]	=   array('unique_callable', function($value) use($table_name, $finYrWise, $fldname, $primary_key, $primary_key_val, $dataType){
    // 					if($dataType == 'date') {
    // 						$value	= !empty($value)?date('Y-m-d', strtotime($value)):'';
    // 					} else if($dataType == 'datetime') {
    // 						$value	= !empty($value)?date('Y-m-d H:i:s', strtotime($value)):'';
    // 					}
    // 					if($this->checkDataDedupe($table_name, $finYrWise, $fldname, $value, $primary_key, $primary_key_val)) {
    // 						return false;
    // 					} else {
    // 						return true;
    // 					}
    // 				}) ;
    // 				$fldRule['errors'] = array("unique_callable" => 'Duplicate value found for '.$fkval->field_description);
    // 			}
    // 			if(!empty($frules)) {
    // 				if(!empty($fkval->unique_field)) {
    // 					$fldRule['rules']	= $frules;
    // 				} else {
    // 					$fldRule['rules']	= implode('|', $frules);
    // 				}
    // 				$rules[] = $fldRule;
    // 			}
    // 		}
    // 	}

    // 	return  $rules;
    // }

    // public function getDynamicFieldValidationArrayForMobileApi($system_form_name, $primary_key_val) {

    // 	$system_form_id	= $this->getSystemFormDetail($system_form_name);
    // 	$primary_key	= $this->getPrimaryKey($system_form_name);
    // 	$formList		= $this->CI->Dynamicform_Model->getAllFormList(array('t1.system_form_mapping_id'=>$system_form_id));
    // 	$rules			= array();

    // 	if(!empty($formList['data'])) {
    // 		$form_id		= $formList['data'][0]->id;
    // 		$table_name		= $formList['data'][0]->table_name;
    // 		$finYrWise		= $formList['data'][0]->financial_year_wise;
    // 		$formFields		= $this->CI->Dynamicform_Model->getFormFieldsByFormId($form_id);
    // 		$uploadFolder	= $this->CI->Dynamictable_Model->uploadPath.'comp'.$this->CI->fx->clientCompId.'/dynamicform/'.$form_id.'/';
    // 		foreach($formFields->data	AS $fky=>$fkval) {
    // 			$fldRule	= array();

    // 			if(!empty($fkval->required) && $dataType == 'attachment') {
    // 				if(!empty($primary_key_val)) {
    // 					$atchRes	= $this->getAttachmentDetail($fkval->field_name, $table_name, $finYrWise, $primary_key, $primary_key_val, $uploadFolder);
    // 					if(empty($atchRes['status']) && empty($_POST[$fkval->field_name])) {
    // 						$frules[]	= 'required';
    // 					}
    // 				} else if(empty($_POST[$fkval->field_name])){
    // 					$frules[]	= 'required';
    // 				}
    // 			} else if(!empty($fkval->required)){
    // 				$frules[]	= 'required';
    // 			}

    // 			if(in_array(strtolower($fkval->field_type), array('multiselect', 'checkbox'))) {
    // 				$fldRule['field']	= $fkval->field_name.'[]';
    // 			} else {
    // 				$fldRule['field']	= $fkval->field_name;
    // 			}
    // 			$fldRule['label']	= $fkval->field_description;
    // 			$frules		= array();
    // 			$dataType	= strtolower($fkval->data_type);
    // 			if(in_array($dataType, array('integer', 'phone'))){
    // 				$frules[]	= 'integer';
    // 				$frules[]	= 'regex_match[/^[0-9]+$/]';
    // 			} else if($dataType == 'email') {
    // 				$frules[]	= 'valid_email';
    // 			} else if($dataType == 'linkurl') {
    // 				$frules[]	= 'valid_url';
    // 			} else if($dataType == 'decimal') {
    // 				$frules[]	= 'decimal';
    // 			} else if($dataType == 'date') {
    // 				$frules[]	= 'regex_match[/^(\d{2})-(\d{2})-(\d{4})+$/]';

    // 			} else if($dataType == 'datetime') {
    // 				$frules[]	= 'regex_match[/^(\d{2})-(\d{2})-(\d{4}) (\d{2}):(\d{2})+$/]';
    // 			} else if($dataType == 'srting') {
    // 				if(in_array(strtolower($fkval->field_type), array('dropdown', 'multiselect', 'checkbox', 'radio'))) {
    // 					if(empty($fkval->values_from_db) && !empty($fkval->field_values)) {
    // 						$fldvals	= str_replace(' ','', $fkval->field_values);
    // 						$frules[]	= 'in_list['.$fldvals.']';
    // 					}
    // 				}
    // 			}
    // 			if(!empty($fkval->field_length) && !empty($fkval->decimal_length)) {
    // 				$total_field_length	= $fkval->field_length + $fkval->decimal_length + 1;
    // 				$frules[]	= 'max_length['.$total_field_length.']';
    // 			} else if(!empty($fkval->field_length)) {
    // 				$frules[]	= 'max_length['.$fkval->field_length.']';
    // 			}
    // 			if(!empty($fkval->unique_field)) {
    // 				$fldname	= $fkval->field_name;
    // 				$frules[]	=   array('unique_callable', function($value) use($table_name, $finYrWise, $fldname, $primary_key, $primary_key_val, $dataType){
    // 					if($dataType == 'date') {
    // 						$value	= !empty($value)?date('Y-m-d', strtotime($value)):'';
    // 					} else if($dataType == 'datetime') {
    // 						$value	= !empty($value)?date('Y-m-d H:i:s', strtotime($value)):'';
    // 					}
    // 					if($this->CI->Dynamictable_Model->checkDataDedupe($table_name, $finYrWise, $fldname, $value, $primary_key, $primary_key_val)) {
    // 						return false;
    // 					} else {
    // 						return true;
    // 					}
    // 				}) ;
    // 				$fldRule['errors'] = array("unique_callable" => 'Duplicate value found for '.$fkval->field_description);
    // 			}
    // 			if(!empty($frules)) {
    // 				if(!empty($fkval->unique_field)) {
    // 					$fldRule['rules']	= $frules;
    // 				} else {
    // 					$fldRule['rules']	= implode('|', $frules);
    // 				}
    // 				$rules[] = $fldRule;
    // 			}
    // 		}
    // 	}

    // 	return  $rules;
    // }


    // public function setDynamicFieldDataArrayForMobileApi($system_form_name, $data) {

    // 	$system_form_id	= $this->getSystemFormDetail($system_form_name);
    // 	$primary_key	= $this->getPrimaryKey($system_form_name);
    // 	$formList	= $this->CI->Dynamicform_Model->getAllFormList(array('t1.system_form_mapping_id'=>$system_form_id));
    // 	$pdata			= array();
    // 	if(!empty($formList['data'])) {
    // 		$form_id		= $formList['data'][0]->id;
    // 		$table_name		= $formList['data'][0]->table_name;
    // 		$finYrWise		= $formList['data'][0]->financial_year_wise;
    // 		$formFields		= $this->CI->Dynamicform_Model->getFormFieldsByFormId($form_id);

    // 		foreach($formFields->data	AS $fky=>$fkval) {
    // 			$data_type	= strtolower($fkval->data_type);
    // 			$field_type	= strtolower($fkval->field_type);
    // 			$NULL		= NULL;
    // 			if(!empty($fkval->required)){
    // 				$NULL	= '';
    // 			}
    // 			if($field_type == 'multiselect') {
    // 				$pdata[$fkval->field_name]	= isset($data[$fkval->field_name])?(is_array($data[$fkval->field_name])?implode(',', $data[$fkval->field_name]):(!empty($data[$fkval->field_name])?$data[$fkval->field_name]:$NULL)):$NULL;
    // 			} else if($field_type == 'checkbox') {
    // 				$pdata[$fkval->field_name]	= isset($data[$fkval->field_name])?(is_array($data[$fkval->field_name])?implode(',', $data[$fkval->field_name]):(!empty($data[$fkval->field_name])?$data[$fkval->field_name]:$NULL)):$NULL;
    // 			} else if($data_type == 'text') {
    // 				$pdata[$fkval->field_name]	= !empty($data[$fkval->field_name])?addslashes(trim($data[$fkval->field_name])):$NULL;
    // 			} else if($data_type == 'date') {
    // 				$pdata[$fkval->field_name]	= !empty($data[$fkval->field_name])?date('Y-m-d', strtotime($data[$fkval->field_name])):$NULL;
    // 			} else if($data_type == 'datetime') {
    // 				$pdata[$fkval->field_name]	= !empty($data[$fkval->field_name])?date('Y-m-d H:i:s', strtotime($data[$fkval->field_name])):$NULL;
    // 			} else {
    // 				$pdata[$fkval->field_name]	= isset($data[$fkval->field_name])?is_array($data[$fkval->field_name])?(!empty($data[$fkval->field_name])?implode(',', $data[$fkval->field_name]):$NULL):(!empty($data[$fkval->field_name])?$data[$fkval->field_name]:$NULL):$NULL;
    // 			}
    // 		}
    // 	}

    // 	return  array('statusCode'=>200, 'data'=>$pdata);
    // }

    // public function uploadDynamicFormDocument($system_form_name, $field_name) {
    // 	$system_form_id	= $this->getSystemFormDetail($system_form_name);
    // 	$formList	= $this->CI->Dynamicform_Model->getAllFormList(array('t1.system_form_mapping_id'=>$system_form_id));
    // 	$retdata	= array();
    // 	if(!empty($formList['data'])) {
    // 		$form_id		= $formList['data'][0]->id;
    // 		$uploadFolder	= $this->CI->Dynamictable_Model->uploadPath.'comp'.$this->CI->fx->clientCompId.'/dynamicform/'.$form_id.'/';

    // 		$config	=	array();
    // 		$config['allowed_types']	= '*';
    // 		$config['max_size'] 		= $this->filesize_allowed * 1024;
    // 		$config['upload_path']		= $uploadFolder;
    // 		if(!is_dir($uploadFolder)) {
    // 			if(!mkdir($uploadFolder, 0777, true)) {
    // 				$error	= array('statusCode' => 400, 'error' => 'Permission denied. Unable to Upload Documents.');
    // 				log_message('error', 'statusCode:{statusCode}, error:{error}', $error);
    // 				return $error;
    // 			}
    // 		}
    // 		if(!empty($_FILES[$field_name]['name'])){
    // 			$config['file_name'] = $_FILES[$field_name]['name'];
    // 			$this->CI->upload->initialize($config);
    // 			if($this->CI->upload->do_upload($field_name)){
    // 				$retdata[$field_name] = $this->CI->upload->data('file_name');
    // 			} else {
    // 				$error	= array('statusCode' => 400, 'error' => "Somthing goes wrong while uploading file of ".$field_name);
    // 				log_message('error', 'statusCode:{statusCode}, error:{error}', $error);
    // 				return $error;
    // 			} 
    // 		} else {
    // 			$error	= array('statusCode' => 400, 'error' => 'File not found');
    // 			log_message('error', 'statusCode:{statusCode}, error:{error}', $error);
    // 			return $error;
    // 		}
    // 	} else {
    // 		$error	= array('statusCode' => 400, 'error' => 'Dynamic Form not found');
    // 		log_message('error', 'statusCode:{statusCode}, error:{error}', $error);
    // 		return $error;
    // 	}
    // 	return array('statusCode'=>200, 'data'=>$retdata); 
    // }

    // public function deleteDynamicFormFieldFile($system_form_name, $field_name, $primary_key_val) {
    // 	$primaryKey		= $this->getPrimaryKey($system_form_name);
    // 	$system_form_id	= $this->getSystemFormDetail($system_form_name);
    // 	$formList		= $this->CI->Dynamicform_Model->getAllFormList(array('t1.system_form_mapping_id'=>$system_form_id));

    // 	if(!empty($formList['data'])) {
    // 		$form_id		= $formList['data'][0]->id;
    // 		$uploadFolder	= $this->CI->Dynamictable_Model->uploadPath.'comp'.$this->CI->fx->clientCompId.'/dynamicform/'.$form_id.'/';
    // 		$table_name		= $formList['data'][0]->table_name;
    // 		$finYrWise		= $formList['data'][0]->financial_year_wise;
    // 		$dataRes		= $this->CI->Dynamictable_Model->getSystemFormDataByPrimaryKey($table_name, $finYrWise, $primaryKey, $primary_key_val);

    // 		if(!empty($dataRes->data)) {
    // 			$document_file_name	= $dataRes->data->$field_name; 
    // 			$updArr	= array();
    // 			$updArr[$field_name]= '';
    // 			$updRes	= $this->CI->Dynamictable_Model->updateSystemFormData($updArr, $table_name, $finYrWise, $primaryKey, $primary_key_val);
    // 			if(empty($updRes)) {
    // 				$document_file_name = '';
    // 				return array('statusCode'=>400, 'error'=>'Updation Failed');
    // 			} else if(!empty($document_file_name)) {
    // 				$file_path = "./". $uploadFolder .$document_file_name;
    // 				if(file_exists($file_path)) {
    // 					unlink($file_path);
    // 				}
    // 				return array('statusCode'=>200);
    // 			} else {
    // 				return array('statusCode'=>400, 'error'=>'Updation Failed');
    // 			}
    // 		} else {
    // 			return array('statusCode'=>400, 'error'=>'Data Not Found');
    // 		}
    // 	} else {
    // 		return array('statusCode'=>400, 'error'=>'Dynamic Form not found');
    // 	}
    // }

    // public function getDynamicSearchFieldValidationArrayForApi($system_form_name) {

    // 	$system_form_id	= $this->getSystemFormDetail($system_form_name);
    // 	$formList		= $this->CI->Dynamicform_Model->getAllFormList(array('t1.system_form_mapping_id'=>$system_form_id));
    // 	$rules			= array();

    // 	if(!empty($formList['data'])) {
    // 		$form_id		= $formList['data'][0]->id;
    // 		$table_name		= $formList['data'][0]->table_name;
    // 		$finYrWise		= $formList['data'][0]->financial_year_wise;
    // 		$formFields		= $this->CI->Dynamicform_Model->getFormFieldsByFormId($form_id);
    // 		foreach($formFields->data	AS $fky=>$fkval) {
    // 			$fldRule	= array();
    // 			if(in_array(strtolower($fkval->field_type), array('attachment'))) {
    // 				continue;
    // 			}
    // 			if(in_array(strtolower($fkval->field_type), array('multiselect', 'checkbox'))) {
    // 				$fldRule['field']	= $fkval->field_name.'[]';
    // 			} else {
    // 				$fldRule['field']	= $fkval->field_name;
    // 			}
    // 			$fldRule['label']	= $fkval->field_description;
    // 			$frules		= array();
    // 			$dataType	= strtolower($fkval->data_type);
    // 			if(in_array($dataType, array('integer', 'phone'))){
    // 				$frules[]	= 'integer';
    // 				$frules[]	= 'regex_match[/^[0-9]+$/]';
    // 			} else if($dataType == 'email') {
    // 				$frules[]	= 'valid_email';
    // 			} else if($dataType == 'linkurl') {
    // 				$frules[]	= 'valid_url';
    // 			} else if($dataType == 'decimal') {
    // 				$frules[]	= 'decimal';
    // 			} else if($dataType == 'date') {
    // 				$frules[]	= 'regex_match[/^(\d{2})-(\d{2})-(\d{4})+$/]';
    // 			} else if($dataType == 'datetime') {
    // 				$frules[]	= 'regex_match[/^(\d{2})-(\d{2})-(\d{4}) (\d{2}):(\d{2})+$/]';
    // 			}
    // 			if(!empty($fkval->field_length) && !empty($fkval->decimal_length)) {
    // 				$total_field_length	= $fkval->field_length + $fkval->decimal_length + 1;
    // 				$frules[]	= 'max_length['.$total_field_length.']';
    // 			} else if(!empty($fkval->field_length)) {
    // 				$frules[]	= 'max_length['.$fkval->field_length.']';
    // 			}
    // 			if(!empty($frules)) {
    // 				$fldRule['rules']	= implode('|', $frules);
    // 				$rules[] = $fldRule;
    // 			}
    // 		}
    // 	}

    // 	return  $rules;
    // }


    // public function getDynamicSearchFieldConditionsForApi($system_form_name, $postData, $alias='') {

    // 	$colAias	= '';
    // 	if(!empty($alias)) {
    // 		$colAias	= $alias.'.';
    // 	}
    // 	$system_form_id	= $this->getSystemFormDetail($system_form_name);
    // 	$formList	= $this->CI->Dynamicform_Model->getAllFormList(array('t1.system_form_mapping_id'=>$system_form_id));
    // 	$retArray	= array();
    // 	if(!empty($formList['data'])) {
    // 		$form_id		= $formList['data'][0]->id;
    // 		$searchFields	= $this->CI->Dynamicform_Model->getFormFieldsByFormId($form_id);
    // 		$subWhere	= array();
    // 		$valarr		= array();
    // 		$fieldNameIndx	= array();
    // 		foreach($postData as $pkey=>$pval) {
    // 			foreach($searchFields->data as $sfky=>$sfval){
    // 				if(($sfval->field_name == $pkey) || ($pkey == $sfval->field_name.'_from') || ($pkey == $sfval->field_name.'_to')) {

    // 					$colName = $colAias.$sfval->field_name;

    // 					if(in_array($sfval->field_type, array('checkbox', 'multiselect'))) {
    // 						$pvalArr	= explode(',', $pval);
    // 						if(count($pvalArr) == 1) {
    // 							if(!empty($pval) && !empty($sfval->values_from_db) && !empty($sfval->db_val_column) && !empty($sfval->db_txt_column) && ($sfval->db_val_column != $sfval->db_txt_column)) {
    // 								$selectQry	= str_replace(';', '', $sfval->field_values);
    // 								$select		= $this->CI->Dynamicform_Model->other_db->query("SELECT {$sfval->db_val_column}, {$sfval->db_txt_column} FROM (".$selectQry.") AS derivedtbl WHERE {$sfval->db_txt_column} = '{$pval}' LIMIT 0,1");
    // 								$comboRes	= $select->result_array();
    // 								if(!empty($comboRes)) {
    // 									$valarr[]	 = $colName.'="'.$comboRes[0][$sfval->db_val_column].'"';
    // 								}
    // 							} else if(!empty($pval)){
    // 								$valarr[]	 = $colName.'="'.$pval.'"';
    // 							}
    // 						} else if(count($pvalArr) > 1) {
    // 							if(!empty($pval) && !empty($sfval->values_from_db) && !empty($sfval->db_val_column) && !empty($sfval->db_txt_column) && ($sfval->db_val_column != $sfval->db_txt_column)) {
    // 								$selectQry	= str_replace(';', '', $sfval->field_values);
    // 								$pvalArrStr	= '"'.implode('","', $pvalArr).'"';
    // 								$select		= $this->CI->Dynamicform_Model->other_db->query("SELECT {$sfval->db_val_column}, {$sfval->db_txt_column} FROM (".$selectQry.") AS derivedtbl WHERE {$sfval->db_txt_column} IN($pvalArrStr)");
    // 								$comboRes	= $select->result_array();
    // 								if(!empty($comboRes)) {
    // 									$findset = array();
    // 									foreach($comboRes as $cbk=>$cbv){
    // 										$findset[] = "FIND_IN_SET(".$cbv->{$sfval->db_val_column}.", $colName) ";
    // 									}
    // 									$valarr[] = '('. implode(' OR ', $findset) .')';
    // 								}
    // 							} else if(!empty($pval)){
    // 								$findset = array();
    // 								foreach($pvalArr as $fldsrchvl)
    // 									$findset[] = "FIND_IN_SET(".$fldsrchvl.", $colName) ";
    // 								$valarr[] = '('. implode(' OR ', $findset) .')';
    // 							}
    // 						}
    // 					} else if(in_array($sfval->field_type, array('radio', 'dropdown'))) {
    // 						if(!empty($pval) && !empty($sfval->values_from_db) && !empty($sfval->db_val_column) && !empty($sfval->db_txt_column) && ($sfval->db_val_column != $sfval->db_txt_column)) {
    // 							$selectQry	= str_replace(';', '', $sfval->field_values);
    // 							$select		= $this->CI->Dynamicform_Model->other_db->query("SELECT {$sfval->db_val_column}, {$sfval->db_txt_column} FROM (".$selectQry.") AS derivedtbl WHERE {$sfval->db_txt_column} = '{$pval}' LIMIT 0,1");
    // 							$comboRes	= $select->result_array();
    // 							if(!empty($comboRes)) {
    // 								$valarr[]	 = $colName.'="'.$comboRes[0][$sfval->db_val_column].'"';
    // 							}
    // 						} else if(!empty($pval)){
    // 							$valarr[]	 = $colName.'="'.$pval.'"';
    // 						}
    // 					} else if(in_array($sfval->data_type, array('date', 'datetime'))) {
    // 						if($pkey == $sfval->field_name.'_from') {
    // 							if(empty($pval)) continue;
    // 							if($sfval->data_type == 'date') {
    // 								$valarr[]	= $colName.' >"'.date('Y-m-d', strtotime($pval)).'"';
    // 							} else if($sfval->data_type == 'datetime') {
    // 								$valarr[]	= $colName.' >"'.date('Y-m-d H:i', strtotime($pval)).'"';
    // 							}
    // 						} else if($pval['name'] == $sfval->field_name.'_to') {
    // 							if(empty($pval)) continue;
    // 							if($sfval->data_type == 'date') {
    // 								$valarr[]	= $colName.' <"'.date('Y-m-d', strtotime($pval)).'"';
    // 							} else if($sfval->data_type == 'datetime') {
    // 								$valarr[]	= $colName.' <"'.date('Y-m-d H:i', strtotime($pval)).'"';
    // 							}
    // 						}
    // 					} else {
    // 						if(empty($pval)) continue;
    // 						$valarr[]	 = $colName.'="'.$pval.'"';
    // 					}
    // 				}
    // 			}
    // 		}
    // 		$retArray	= implode(' AND ', $valarr);
    // 	}

    // 	return $retArray;
    // }

    // public function getDynamicFieldAllList($system_form_name, $alias='') {
    // 	$colAias	= '';
    // 	$tblAlias	= '';
    // 	if(!empty($alias)) {
    // 		$tblAlias	= $alias.'.';
    // 		$colAias	= $alias.'_';
    // 	}
    // 	$system_form_id	= $this->getSystemFormDetail($system_form_name);
    // 	$formList	= $this->CI->Dynamicform_Model->getAllFormList(array('t1.system_form_mapping_id'=>$system_form_id));
    // 	$selectArr	= array();
    // 	$aliasArr	= array();
    // 	$joinArr	= array();
    // 	if(!empty($formList['data'])) {
    // 		$form_id	= $formList['data'][0]->id;
    // 		$listFields	= $this->CI->Dynamicform_Model->getFormFieldsByFormId($form_id);

    // 		foreach($listFields->data as $lfky=>$lfval){
    // 			if(strtolower($lfval->data_type) == 'date') {
    // 				$selectArr[]	= "IF((".$tblAlias.$lfval->field_name." IS NOT NULL AND TRIM(".$tblAlias.$lfval->field_name.") <> '' AND TRIM(".$tblAlias.$lfval->field_name.") <> '0000-00-00'), DATE_FORMAT(".$tblAlias.$lfval->field_name.", '%d-%m-%Y'), '') as ".$colAias.$lfval->field_name;
    // 				$aliasArr[]		= $tblAlias.$lfval->field_name;
    // 			} else if(strtolower($lfval->data_type) == 'datetime') {
    // 				$selectArr[]	= "IF((".$tblAlias.$lfval->field_name." IS NOT NULL AND TRIM(".$tblAlias.$lfval->field_name.") <> '' AND TRIM(".$tblAlias.$lfval->field_name.") <> '0000-00-00 00:00:00'), DATE_FORMAT(".$tblAlias.$lfval->field_name.", '%d-%m-%Y %H:%i'), '') as ".$colAias.$lfval->field_name;
    // 				$aliasArr[]		= $tblAlias.$lfval->field_name;
    // 			} else if(!empty($lfval->values_from_db) && !empty($lfval->db_val_column) && !empty($lfval->db_txt_column) && ($lfval->db_val_column != $lfval->db_txt_column)) {
    // 				$sqlStmt	= str_replace(';', '', $lfval->field_values);
    // 				$sqlStmt	= str_ireplace('FROM ', 'FROM '.$this->CI->Dynamicform_Model->compDb.'.', $sqlStmt);
    // 				$sqlStmt	= str_ireplace('JOIN ', 'JOIN '.$this->CI->Dynamicform_Model->compDb.'.', $sqlStmt);
    // 				$joinArr[]		= array(
    // 					'table'		=> "({$sqlStmt}) j{$lfky}",
    // 					'condition'	=> "FIND_IN_SET(j{$lfky}.".$lfval->db_val_column.", ".$tblAlias.$lfval->field_name.")",
    // 					'type'		=> "LEFT"
    // 					);
    // 				$selectArr[]	= "GROUP_CONCAT(DISTINCT j{$lfky}.".$lfval->db_txt_column.") ".$colAias.$lfval->field_name;
    // 				if(!in_array(strtolower($lfval->field_type), array('multiselect', 'checkbox'))) {
    // 					$aliasArr[]		= $tblAlias.$lfval->field_name;
    // 				}
    // 			} else {
    // 				$selectArr[]	= $tblAlias.$lfval->field_name.' AS '.$colAias.$lfval->field_name;
    // 				$aliasArr[]		= $colAias.$lfval->field_name;
    // 			}
    // 		}
    // 	}
    // 	$selectStr = !empty($selectArr)?','.implode(',', $selectArr):'';
    // 	$aliasStr  = !empty($aliasArr)?','.implode(',', $aliasArr):'';
    // 	$this->CI->selectlist	= $selectStr;
    // 	$this->CI->aliaslist	= $aliasStr;
    // 	$this->CI->joinlist		= $joinArr;
    // 	return $this->CI;
    // }
}
