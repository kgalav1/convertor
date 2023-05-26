<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Include PHPMailer library files

//~ require_once FCPATH.'PHPMailer/src/Exception.php';
//~ require_once FCPATH.'PHPMailer/src/PHPMailer.php';
//~ require_once FCPATH.'PHPMailer/src/SMTP.php';
//~ require_once FCPATH.'PHPMailer/src/POP3.php';
//~ require_once FCPATH.'PHPMailer/src/OAuth.php';

require_once './PHPMailer/src/Exception.php';
require_once './PHPMailer/src/PHPMailer.php';
require_once './PHPMailer/src/SMTP.php';
require_once './PHPMailer/src/POP3.php';
require_once './PHPMailer/src/OAuth.php';
		
use PHPMailer\PHPMailer\PHPMailer;
//use PHPMailer\PHPMailer\Exception;

//require 'vendor/autoload.php';

class Phpmail_Lib {
	public $emailObj = '';
    public function __construct(){
        log_message('Debug', 'PHPMailer class is loaded.');
    }
	
    public function load()
    {
		$this->emailObj = new PHPMailer();
        return $this->emailObj;
    }

    public function send_email($sender_detail=array(), $receiver_detail=array(), $ccTo=array(), $bccTo=array(), $replyTo=array(), $subject='', $messageBody='', $altMessageBody='', $attachments=array(), $isHtml=true) {
		if(!empty($sender_detail) && !empty($receiver_detail)) {
			try {
				
				if(empty($this->emailObj)) {
					$this->emailObj = new PHPMailer();
				}
				$this->emailObj->SMTPDebug = 0;
				$this->emailObj->isSMTP();
				$this->emailObj->SMTPAuth  = true;
				//From
				$this->emailObj->Host		= $sender_detail['sender_host'];
				$this->emailObj->Port		= $sender_detail['sender_port'];
				$this->emailObj->Username	= trim($sender_detail['sender_username']);
				$this->emailObj->Password	= trim($sender_detail['sender_password']);
				$this->emailObj->SMTPOptions = array('ssl' => array('verify_peer' => ($sender_detail['sender_smtp_verified_ssl'] == 1) ? true : false, 'verify_peer_name' => ($sender_detail['sender_smtp_verified_ssl'] == 1) ? true : false, 'allow_self_signed' => true));
				if($sender_detail['sender_smtp_verified_ssl'] == 1) {
					$this->emailObj-> SMTPSecure = 'ssl';
				}
				
				if(!empty($sender_detail['sender_email_id']) && !empty($sender_detail['sender_name'])) {
					$this->emailObj->setFrom($sender_detail['sender_email_id'], $sender_detail['sender_name']);
				} if(empty($sender_detail['sender_email_id'])) {
					$this->emailObj->setFrom($sender_detail['sender_email_id'], $sender_detail['sender_name']);
				}
				//To
				foreach($receiver_detail as $rkey=>$rval) {
					if(!empty($rval['email_id']) && !empty($rval['name'])) {
						$this->emailObj->addAddress($rval['email_id'], $rval['name']);
					} else if(!empty($rval['email_id'])) {
						$this->emailObj->addAddress($rval['email_id']);
					}
				}
				//ccTo
				foreach($ccTo as $rkey=>$rval) {
					if(!empty($rval['email_id']) && !empty($rval['name'])) {
						$this->emailObj->addCC($rval['email_id'], $rval['name']);
					} else if(!empty($rval['email_id'])) {
						$this->emailObj->addCC($rval['email_id']);
					}
				}
				//bccTo
				foreach($bccTo as $rkey=>$rval) {
					if(!empty($rval['email_id']) && !empty($rval['name'])) {
						$this->emailObj->addBCC($rval['email_id'], $rval['name']);
					} else if(!empty($rval['email_id'])) {
						$this->emailObj->addBCC($rval['email_id']);
					}
				}
				//replyTo
				foreach($replyTo as $rkey=>$rval) {
					if(!empty($rval['email_id']) && !empty($rval['name'])) {
						$this->emailObj->addReplyTo($rval['email_id'], $rval['name']);
					} else if(!empty($rval['email_id'])) {
						$this->emailObj->addReplyTo($rval['email_id']);
					}
				}
				//attachments
				foreach($attachments as $rkey=>$file) {
					if(!empty($file['path']) && !empty($file['name'])) {
						$this->emailObj->addAttachment($file['path'], $file['name']);
					} else if(!empty($file['path'])) {
						$this->emailObj->addAttachment($file['path']);
					}
				}

				$this->emailObj->isHTML($isHtml);
				$this->emailObj->Subject = !empty($subject)?$subject:'';
				$this->emailObj->Body    = !empty($messageBody)?$messageBody:'';
				$this->emailObj->AltBody = !empty($altMessageBody)?$altMessageBody:'';
				
				if($this->emailObj->send()) {
					return true;
				} else {
					log_message('Debug', "Message could not be sent. Mailer Error: {$this->emailObj->ErrorInfo}");
					return false;
				}
			}  catch (Exception $e) {
				log_message('Debug', "Message could not be sent. Error: ". $e->getMessage());
				log_message('Debug', "Message could not be sent. Mailer Error: {$this->emailObj->ErrorInfo}");
				return false;
			}
		} else {
			return false;
		}
	}
}
