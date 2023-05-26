<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once (APPPATH . 'third_party/razorpay/Razorpay.php');
 

use Razorpay\Api\Api;
class Razorpay {
	function __construct($constData) {
		$this -> ci = &get_instance();
		$this ->RAZORPAY_API_KEY = $constData['RAZORPAY_API_KEY'];
		$this ->RAZORPAY_API_SECRET = $constData['RAZORPAY_API_SECRET'];
		$this -> client = new Api($constData['RAZORPAY_API_KEY'], $constData['RAZORPAY_API_SECRET']);
	}

	function setOrder($orderId, $amount, $currency = 'INR') {
		return $this -> client -> order -> create(array('receipt' => $orderId, 'amount' => round($amount * 100), 'currency' => $currency, 'payment_capture' => '0'));
	}

	function setOrderAndGetId($orderId, $amount, $currency = 'INR') {
		$order = $this -> setOrder($orderId, $amount);
		return $order['id'];
	}

	function verfifyOrderPayment($data) {
		$attrbutes = array("razorpay_signature" => $data['razorpay_signature'], 'razorpay_payment_id' => $data['razorpay_payment_id'], "razorpay_order_id" => $data['razorpay_order_id']);
		return $this -> client -> utility -> verifyPaymentSignature($attrbutes);
	}

	function fetchPayment($payment_id, $amount, $currency = 'INR') {
		$amount = round($amount * 100);
		$payment = $this -> client -> payment->fetch($payment_id);
		return $payment->capture(array('amount' => $amount, 'currency' => "$currency"));
	}

	function capturePayment($payment_id,$amount) {
		$amount = round($amount*100);
		return $this -> client ->payment->fetch($payment_id)->capture(array('amount'=>$amount));
	}

	function fetchPaysmenStatusByOrderId($order_id){
	 
		$username_password = base64_encode("$this->RAZORPAY_API_KEY:$this->RAZORPAY_API_SECRET");
		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_URL => "https://api.razorpay.com/v1/orders/$order_id/payments",
			CURLOPT_RETURNTRANSFER => true,		 
			CURLOPT_TIMEOUT => 40,
			CURLOPT_ENCODING => '',
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'GET',
			CURLOPT_HTTPHEADER => array(
				"Authorization: Basic $username_password"
			),
		));

		$response = curl_exec($curl);
		$http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		$curl_error = curl_error($curl);
		curl_close($curl);
		/******************** CALL CURL API ----------------- ENDS ****************************/
		 
		if ($curl_error)
			return array('success' => FALSE, 'message' => $curl_error);

		$dataResp =json_decode($response,true);
		if(!is_array($dataResp['items']) || count($dataResp['items'])<1){
			return array('success' => false, 'message' => "Payment processing not completed by user	
", 'data' => $response);
		}

		return array('success' => true, 'message' => "Data found",'data'=> $response);
	}
}
