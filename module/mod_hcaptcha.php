<?php
/*
mod_hcaptcha.php
*/

class mod_hcaptcha extends ModuleHelper {
	private $KEY_PUBLIC   = 'SITE KEY';
	private $KEY_PRIVATE  = 'SECRET KEY';

	public function getModuleName(){
		return 'mod_hcaptcha : hCaptcha';
	}

	public function getModuleVersionInfo(){
		return 'v1.0';
	}

	public function autoHookHead(&$head, $isReply){
		$head.='<script src="https://js.hcaptcha.com/1/api.js" async defer></script>';
	}

	/* hCaptcha button */
	public function autoHookPostForm(&$txt){
		$txt .= '<tr><th class="postblock">Verify</th><td>'.'<div class="h-captcha" data-sitekey="'.$this->KEY_PUBLIC.'"></div>'.'</td></tr>';
	}
	
	function validateCaptcha($privatekey, $response) {
	    $responseData = json_decode(file_get_contents('https://api.hcaptcha.com/siteverify?secret='.$privatekey.'&response='.$response));
	    return $responseData->success;
    }

	/* Validate */
	public function autoHookRegistBegin(&$name, &$email, &$sub, &$com, $upfileInfo, $accessInfo){
		if (valid() >= LEV_MODERATOR ) return; //no captcha for admin mode
		
		$resp = $this->validateCaptcha('SECRET KEY', $_POST['h-captcha-response']);
		if($resp == null){ error('Verification failed. You are not acting like a human!'); } // fail
	}
}
