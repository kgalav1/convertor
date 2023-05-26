<?php
defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('Asia/Kolkata');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'plugin/PHPMailer/src/Exception.php';
require 'plugin/PHPMailer/src/PHPMailer.php';
require 'plugin/PHPMailer/src/SMTP.php';

class Mailer {
	function __construct() {
		$this->CI = &get_instance();
	}
	public $setMail;

	
	function setSenderConfiguration() {
		$this->getCompanyDetail(); 
		if (empty($this->compDtls->smtp_email_id) || empty($this->compDtls->smtp_email_password) || empty($this->compDtls->smtp_email_server) || empty($this->compDtls->smtp_port)) {
			return json_encode(array('status' => false, 'message' => 'Mail configuration not exist, Please check company setting'));
			die ;
		} 
		$this->setMail = new PHPMailer();
		$this->setMail->SMTPDebug 	= 0;
		$this->setMail->isSMTP();
		$this->setMail->SMTPAuth 	= true;
		$this->setMail->Host 		= $this->compDtls->smtp_email_server;
		$this->setMail->Port 		= $this->compDtls->smtp_port ? $this->compDtls->smtp_port : 587;
		$this->setMail->Username 	= $this->compDtls->smtp_email_id;
		$this->setMail->Password 	= $this->compDtls->smtp_email_password;
		$this->setMail->SMTPSecure 	= PHPMailer::ENCRYPTION_STARTTLS;
		if ($this->compDtls->smtp_enable_ssl == 1) {
			$this->setMail->SMTPSecure = 'ssl';
		}
		$this->setMail->SMTPOptions = array('ssl' => array('verify_peer' => ($this->compDtls->smtp_verified_ssl == 1) ? true : false, 'verify_peer_name' => ($this->compDtls->smtp_verified_ssl == 1) ? true : false, 'allow_self_signed' => true ));
		$this->setMail->isHTML(true);
		$this->setMail->setFrom($this->compDtls->smtp_email_id, $this->compDtls->name);
	}
	
	public function sendEmail($email_data, $data, $isHtml=true) {
		if(!empty($email_data['receiver_detail'])) {
			$email_sent = false;
			$email_sent_msg = '';
			$sendTo = array();
			$sendToName = array();
			$emailSmsArrayLog = array();
			try {
				$this->setSenderConfiguration();
				if(!$this->setMail) {
					throw new Exception('Error in Email Configuration');
				} else {
					//To
					foreach($email_data['receiver_detail'] as $rkey=>$rval) {
						if(!empty($rval['email_id']) && !empty($rval['name'])) {
							$this->setMail->addAddress($rval['email_id'], $rval['name']);
							$sendTo[] = $rval['email_id'];
							$sendToName[] =  $rval['name'];
						} else if(!empty($rval['email_id'])) {
							$this->setMail->addAddress($rval['email_id']);
							$sendTo[] = $rval['email_id'];
						}
					}
					//ccTo
					if(!empty($email_data['cc_to'])) {
						foreach($email_data['cc_to'] as $rkey=>$rval) {
							if(!empty($rval['email_id']) && !empty($rval['name'])) {
								$this->setMail->addCC($rval['email_id'], $rval['name']);
								$sendTo[] = $rval['email_id'];
								$sendToName[] =  $rval['name'];
							} else if(!empty($rval['email_id'])) {
								$this->setMail->addCC($rval['email_id']);
								$sendTo[] = $rval['email_id'];
							}
						}
					}
					//bccTo
					if(!empty($email_data['bcc_to'])) {
						foreach($email_data['bcc_to'] as $rkey=>$rval) {
							if(!empty($rval['email_id']) && !empty($rval['name'])) {
								$this->setMail->addBCC($rval['email_id'], $rval['name']);
								$sendTo[] = $rval['email_id'];
								$sendToName[] =  $rval['name'];
							} else if(!empty($rval['email_id'])) {
								$this->setMail->addBCC($rval['email_id']);
								$sendTo[] = $rval['email_id'];
							}
						}
					}
					//replyTo
					if(!empty($email_data['reply_to'])) {
						foreach($email_data['reply_to'] as $rkey=>$rval) {
							if(!empty($rval['email_id']) && !empty($rval['name'])) {
								$this->setMail->addReplyTo($rval['email_id'], $rval['name']);
							} else if(!empty($rval['email_id'])) {
								$this->setMail->addReplyTo($rval['email_id']);
							}
						}
					}
					if(!empty($email_data['attachments'])) {
						//attachments
						foreach($email_data['attachments'] as $rkey=>$file) {
							if(!empty($file['path']) && !empty($file['name'])) {
								$this->setMail->addAttachment($file['path'], $file['name']);
							} else if(!empty($file['path'])) {
								$this->setMail->addAttachment($file['path']);
							} else if(!empty($file['html']) && !empty($file['name'])) {
								$this->setMail->addAttachment($file['html'], $file['name']);
							} else if(!empty($file['html'])) {
								$this->setMail->addAttachment($file['html']);
							}
						}
					}
					$this->setMail->isHTML($isHtml);
					$this->setMail->Subject = !empty($email_data['message_subject'])?$email_data['message_subject']:'';
					$this->setMail->Body    = !empty($email_data['message_body'])?$email_data['message_body']:'';
					$this->setMail->AltBody = !empty($email_data['alt_message_body'])?$email_data['alt_message_body']:'';
					
					if($this->setMail->send()) {
						$email_sent = true;
						$email_sent_msg = 'Email Sent';
					} else {
						$email_sent = false;
						$email_sent_msg = "Message could not be sent. Mailer Error: {$this->setMail->ErrorInfo}";
					}
				}
			}  catch (Exception $e) {
				$email_sent = false;
				$email_sent_msg = "Message could not be sent. Error: ". $e->getMessage();
			}

			$emailSmsArrayLog[] = array(
				'type'				=> TEMPLATE_TYPE_EMAIL,
				'reference_name' 	=> $data['reference_name'],
				'reference_id' 		=> $data['reference_id'],
				'send_to' 			=> $email_data['receiver_detail'][0]['email_id'],
				'send_to_name' 		=> $email_data['receiver_detail'][0]['name'],
				'subject' 			=> $email_data['message_subject'],
				'body' 				=> $email_data['message_body'],
				'sent_by_user' 		=> !empty($this->CI->fx->clientId)?$this->CI->fx->clientId:NULL,
				'response' 			=> strval($email_sent_msg)
			);
			
			if (count($emailSmsArrayLog) > 0) {
				$this->CI->load->model('Acc_Model');
				$this->CI->Acc_Model->createMultiEmailSmsLogs($emailSmsArrayLog);
			}
			return $email_sent;
		} else {
			return false;
		}
	}
	
	function sendSMS($messageBody, $mobile, $data, $is_sms = false, $is_whatsapp = false) { 
		try {
			$this->getCompanyDetail(); 
			if ($is_sms==false && $is_whatsapp==false) {
				return (array('success' => false, 'message' => "Please select atleast one medium"));
			}
			$responseMsg = "";
			$msg = str_replace(array("&nbsp;", " ", "'"), array(' ', ' ', '`'), $messageBody);
			$msg = str_replace(array('  '), array(' '), preg_replace('/[^A-Za-z0-9\<>?|_.:*#&$!(){;"%-+`}@=,\n\/ ]/', '', $msg));
			$mobileArray = explode(',', $mobile);

			$emailSmsArrayLog = array();
			$urlArray = array();

			if ($is_sms==TRUE && $this->compDtls->sms_api != '') {
				$urlArray[$this->compDtls->sms_api] = array('type' => TEMPLATE_TYPE_SMS);
			} else if ($is_sms==true) {
				return (array('success' => false, 'message' => "SMS Api not configured!!"));
			 
			}

			if ($is_whatsapp==TRUE && $this->compDtls->whatsapp_api != '') {
				$urlArray[$this->compDtls->whatsapp_api] = array('type' => TEMPLATE_TYPE_WHATSAPP);
			} else if ($is_whatsapp == TRUE) {
				return (array('success' => false, 'message' => "Whatsapp Api not configured!!"));
			}
			
			foreach ($urlArray as $comp_url => $urlData) {
				foreach ($mobileArray as $key => $mobile) {
					if (strlen($mobile) != 10 || !is_numeric($mobile)) {
						$responseMsg .= "Mobile Number ($mobile) should be of 10 digits only <br>";
						continue;
					}
					$ch = curl_init();
					$msg1 = curl_escape($ch, $msg);
					$url = str_replace(array('{0}', '{1}'), array($mobile, $msg1), $comp_url);
					
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
					curl_setopt($ch, CURLOPT_URL, $url);
					curl_setopt($ch, CURLOPT_TIMEOUT, 60);
					curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
					curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

					$curl_error = curl_error($ch);
					$response = curl_exec($ch);
				
					$http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
					curl_close($ch);
					
					if (!empty($curl_error)) {
						$responseMsg .= 'API Error : ' . $curl_error . "<br>";
						continue;
					}
					if ($http_status != 200) {
						$responseMsg .= 'API Error Not getting 200 Status code: ' . strval($response) . "<br>";
						continue;
					}
					$emailSmsArrayLog[] = array(
						'type'				=> $urlData['type'],
						'reference_name'	=> $data['reference_name'],
						'reference_id'		=> $data['reference_id'],
						'send_to'			=> $mobile,
						'send_to_name'		=> $data['sent_to_name'],
						'subject'			=> NULL,
						'body'				=> $messageBody,
						'sent_by_user'		=> !empty($this->CI->fx->clientId)?$this->CI->fx->clientId:NULL,
						'response'			=> strval($response)
					);
				}
			}

			if (count($emailSmsArrayLog) > 0)
				$this->CI->Acc_Model->createMultiEmailSmsLogs($emailSmsArrayLog);

			if ($responseMsg != '') {
				return array('success' => false, 'message' => $responseMsg);
				 
			}
			return (array('success' => true, 'message' => 'Message sent successfully'));
		} catch (Exception $e) {
			return (array('success' => false, 'message' => $e->getMessage()));
		}
	}
}
?>
