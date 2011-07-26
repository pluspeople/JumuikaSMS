<?php

class JumuikaSMS {
	const VERSION = 1.0.0;
	protected $userName = "";
	protected $password = "";
	protected $route = "df";
	protected $baseUrl = "http://bulksms.jumuika.co.ke/";
	protected $message = "";
	protected $lastError = 0;
	protected $success = 1701;
	protected $error = array(1702 => "Invalid URL Error",
								 1703 => "Invalid value in username or password field",
								 1704 => "Invalid value in 'type' field",
								 1705 => "Invalid Message",
								 1706 => "Invalid Destination",
								 1707 => "Invalid Source (Sender)",
								 1708 => "Invalid value for 'DLR' field",
								 1709 => "User validation failed",
								 1710 => "Internal Error",
								 1025 => "Insufficient Credit");
							

	public function __construct() {
		$this->userName = "username";
		$this->password = "pw";
	}

	public function getLastError() {
		return $this->lastError;
	}

	public function getBalance() {
		// Credit check Format url: http://bulksms.jumuika.co.ke/creditreport.php?username=USERNAME&password=PASSWORD		
		$url = $this->baseUrl . "creditreport.php?username=" . $this->userName . "&password=" . $this->password;
		if ($balance = @file_get_contents($url)) {
			return $balance;
		} else {
			// error handling
			return 0;
		}
	}

	public function setMessage($input) {
		$this->message = $input;
	} 

	// Sending Format url: http://bulksms.jumuika.co.ke/index.php?username=USERNAME&password=PASSWORD&message=MESSAGE&from=FROM&to=TO&route=df
	public function sendSms($to, $from) {
		if ($to != "") {
			$url = $this->baseUrl . "index.php?username=" . $this->userName . "&password=" . $this->password . "&message=" . urlencode($this->message) . "&from=" . $from . "&to=" . $to . "&route=" . $this->route;

			$response = @file_get_contents($url);
			if ($response == $this->success) {
				return true;
			} else {
				if ($response != false) {
					$this->lastError = $response;
				} else {
					$this->lastError = 999; // no connection
				}
				return false;
			}
		}
	} 

	public function kenyanNumber($input) {
		$phone = '254' . substr(preg_replace('/[^0-9]*/', '', $input), -9);
		return $phone;
	}

}

?>