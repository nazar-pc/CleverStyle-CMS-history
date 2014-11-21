<?php
class Mail extends PHPMailer {
	function __construct ($data = false) {
		global $Config;
		if (is_object($Config)) {
			if ($Config->core['smtp']) {
				$this->Mailer		= 'smtp';
				$this->Host			= $Config->core['smtp_host'];
				$this->Port			= $Config->core['smtp_port'];
				$this->SMTPSecure	= $Config->core['smtp_secure'];
				if ($Config->smtp_auth) {
					$this->SMTPAuth	= true;
					$this->Username	= $Config->core['smtp_user'];
					$this->Password	= $Config->core['smtp_password'];
				}
			}
		}
		$this->FromName	= $Config->core['mail_from_name'];
		$this->CharSet	= CHARSET;
		foreach ($data as $i => $v) {
			$this->$i	= $v;
		}
	}
}
?>