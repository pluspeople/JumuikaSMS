<?php
/*	Copyright (c) 2011, PLUSPEOPLE Kenya Limited. 
		All rights reserved.

		Redistribution and use in source and binary forms, with or without
		modification, are permitted provided that the following conditions
		are met:
		1. Redistributions of source code must retain the above copyright
		   notice, this list of conditions and the following disclaimer.
		2. Redistributions in binary form must reproduce the above copyright
		   notice, this list of conditions and the following disclaimer in the
		   documentation and/or other materials provided with the distribution.
		3. Neither the name of PLUSPEOPLE nor the names of its contributors 
		   may be used to endorse or promote products derived from this software 
		   without specific prior written permission.
		
		THIS SOFTWARE IS PROVIDED BY THE REGENTS AND CONTRIBUTORS ``AS IS'' AND
		ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
		IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
		ARE DISCLAIMED.  IN NO EVENT SHALL THE REGENTS OR CONTRIBUTORS BE LIABLE
		FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL
		DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS
		OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION)
		HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
		LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY
		OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF
		SUCH DAMAGE.

		Created by:
		2011 - Michael Pedersen of Pluspeople Kenya LTD.
 */

class JumuikaSMS {
	const VERSION = 1.0.0;

	///////////////////////////////////////////////////////////
	// ATTRIBUTES
	protected $userName = "";
	protected $password = "";
	protected $route = "df";
	protected $baseUrl = "http://bulksms.jumuika.co.ke/";
	protected $message = "";
	protected $lastError = 0;
	protected $success = 1701;
	protected $error = array( 999 => "No response - connection or server down",
													 1702 => "Invalid URL Error",
													 1703 => "Invalid value in username or password field",
													 1704 => "Invalid value in 'type' field",
													 1705 => "Invalid Message",
													 1706 => "Invalid Destination",
													 1707 => "Invalid Source (Sender)",
													 1708 => "Invalid value for 'DLR' field",
													 1709 => "User validation failed",
													 1710 => "Internal Error",
													 1025 => "Insufficient Credit");

	///////////////////////////////////////////////////////////
	// CONSTRUCTOR
							
	public function __construct($username, $pw) {
		$this->userName = $username;
		$this->password = $pw;
	}

	///////////////////////////////////////////////////////////
	// PUBLIC METHODS

	/*
		This method returns a textual description of the last Error code
	 */
	public function getFormatedLastError() {
		return $this->error[$this->getLastError()];
	}

	/*
		Incase the sendSMS method fails (and returns false) 
		then using this method you will recieve the last error-code.

		NOTE: Appart from the code supplied by Jumuika, there is a pseudo code '999' 
		      indicating that the server could not be reached
	 */
	public function getLastError() {
		return $this->lastError;
	}

	/*
		This method is used to return the current balance of the your Jumuika account.
		The balance will be returned as an integer, and in case of a communications error 0 will be returned.
	 */
	public function getBalance() {
		$url = $this->baseUrl . "creditreport.php?username=" . $this->userName . "&password=" . $this->password;
		if ($balance = @file_get_contents($url)) {
			return $balance;
		} else {
			// error handling
			return 0;
		}
	}

	/*
		This method sets the message you are about to send.
		The reason that this is a seperate command is that,
		the same message might be send to multiple users (i.e. SMS-newsletter).

		NOTE: You must call this method before calling sendSMS.
	 */
	public function setMessage($input) {
		$this->message = $input;
	} 

	/*
		This is the actual send method - you use this to send the message set though 'setMessage()'
		On success this method will return true - if any failure occurs then false will be returned
		And then subsequently you can use getLastError() to obtain the particular error.
		
		NOTE: $to must be a fully qualified international mobile number starting with country-code i.e. 254
	 */
	public function sendSMS($to, $from) {
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

	/*
		This method can be used to validate/sanitize a user inputted mobile number.
		Given a user inputted mobile phone-number this method will return a fully qualified
		Kenyan phone-number - that then can be used with the sendSMS() method. 
	 */
	public function kenyanNumber($input) {
		$phone = '254' . substr(preg_replace('/[^0-9]*/', '', $input), -9);
		return $phone;
	}

}

?>