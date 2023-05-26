<?php
defined('BASEPATH') or exit('No direct script access allowed');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token, token');

class Formcontent extends CI_Controller
{
    function __construct()
	{
		parent::__construct();
        $this->user_id = '';
		$this->user_email = '';
		$this->user_mobile = '';
		$this->user_type = '';
		$this->full_control = '';
        $this->convertHeaderToPost($this->input->request_headers());
		$this->load->model('Formcontent_Model','formcontent');
        $this->load->library('fx');
    }

    function api()
	{
		$post = json_decode(file_get_contents("php://input"));
		if (!empty(empty($post->method))) {
			call_user_func_array(array($this->api_model, $post->method), array($post));
		}
	}

    private function encode($string)
	{
		return $this->fx->encrypt_decrypt('encrypt', $string);
	}

	private function decode($string)
	{
		return $this->fx->encrypt_decrypt('decrypt', $string);
	}

    private function convertHeaderToPost($headerData)
	{
		foreach ($headerData as $key => $value) {
			if (strtolower($key) == 'token') {
				$_POST[strtolower($key)] = $value;
				break;
			}
		}
	}

	function setData($token)
	{
		$array = explode("-", $this->decode($token));
		if (empty($array[0])) {
			return;
		}

		$this->user_id = $this->db->escape_str($array[0]);
		$this->user_email = $this->db->escape_str($array[1]);
		$this->user_mobile = $this->db->escape_str($array[2]);
		$this->user_type = $this->db->escape_str($array[3]);
		$this->full_control = $this->db->escape_str($array[4]);
		return true;
	}

	public function response($success, $message, $status_code = 200, $data = array())
	{
		$response = array(
			'success' => $success,
			'message' => $message,
			'result' => (count($data) < 1) ? new stdClass() : $data,
			'status_code' => $status_code,
		);
		echo json_encode($response);
		exit;
	}

    public function getAllFormList()
    {        
        $token = $this->input->post('token');
		if ($token == "") {
			$this->response(FALSE, 'Invalid Token.', 200);
			return;
		}
		if ($this->setData($token) == false)
			return;

        $postvars = json_decode(file_get_contents("php://input"), true);
        $post = $postvars;
        if(!empty($post)){
            $form_id		= $post;
			$formFields		= $this->formcontent->getFormFieldsByFormId($form_id);
			foreach($formFields->data as $key => $val){
				$val->db_res = '';
				if($val->values_from_db == 1){
					$dbQuery = stripslashes($val->field_values);
					$val->db_res = $this->db->query($dbQuery)->result();
				}
			}
			$envArr = array();
			$envArr			= array(
				'form_id'		=> $form_id,
				'formFields'	=> $formFields->data,
			);
            if (count($envArr)>0) {
            	$this->response(TRUE, '', 200, $envArr);
            } else {
            	$this->response(FALSE, 'Data Not Found', 400);
            }
        }else{
            $this->response(FALSE, 'Queue Id is required', 400);
        }
    }
}