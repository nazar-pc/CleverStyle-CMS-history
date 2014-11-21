<?php
class Error {
	protected	$num = 0,
				$Page,
				$Config;
	function __construct () {
		set_error_handler(array(&$this, 'show'));
	}
	function show ($errno, $errstr='', $errfile=true, $errline='', $log=0) {
		global $L, $Page, $Config;
		if ($errfile && $errline) {
			++$this->num;
			switch ($errno) {
				case E_USER_ERROR:
				case E_ERROR:
					$Page->title($L->fatal.' #'.$errno.': '.$errstr.' '.$L->page_generation_aborted.'...');
					$Page->content(
						'<p><b>'.$L->fatal.' #'.$errno.':</b> '.$errstr.' '.$L->in_line.' '.$errline.' '.$L->of_file.' '
						.$errfile.', PHP '.PHP_VERSION.' ('.PHP_OS.")<br>\n"
						.$L->page_generation_aborted."...<br>\n"
						.$L->report_to_admin."<br>\n"
						.(is_object($Config) ?
							($Config->core['admin_mail'] ? $L->admin_mail.': <a href="mailto:'.$Config->core['admin_mail']."\">".$Config->core['admin_mail']."</a><br>\n" : '')
							.($Config->core['admin_phone'] ? $L->admin_phone.': '.$Config->core['admin_phone']."<br>\n" : '')
						: '')
					);
					global $Classes;
					$Classes->__destruct();
				break;
				
				case E_USER_WARNING:
				case E_WARNING:
					if (!$Page->Title) {
						$Page->title($L->error.' #'.$errno.': '.$errstr);
					}
					$Page->content(
						'<b>'.$L->error.' #'.$errno.':</b> '.$errstr.' '.$L->in_line.' '.$errline.' '.$L->of_file.' '
						.$errfile.', PHP '.PHP_VERSION.' ('.PHP_OS.")<br>"
						.$L->report_to_admin."<br>\n"
						.(is_object($Config) ?
							($Config->core['admin_mail'] ? $L->admin_mail.': <a href="mailto:'.$Config->core['admin_mail']."\">".$Config->core['admin_mail']."</a><br>\n" : '')
							.($Config->core['admin_phone'] ? $L->admin_phone.': '.$Config->core['admin_phone']."<br>\n" : '')
						: '')
						."<hr size=\"1\" noshade><br>\n"
					);
				break;
				
				case E_USER_NOTICE:
				case E_NOTICE:
					if (!$Page->Title) {
						$Page->title($L->warning.' #'.$errno.': '.$errstr);
					}
					$Page->content(
						'<b>'.$L->warning.' #'.$errno.':</b> '.$errstr.' '.$L->in_line.' '.$errline.' '.$L->of_file.' '
						.$errfile.', PHP '.PHP_VERSION.' ('.PHP_OS.")<br>"
					);
				break;
				
				default:
					if (!$Page->Title) {
						$Page->title($L->error.' #'.$errno.': '.$errstr);
					}
					$Page->content(
						'<b>'.$L->error.' #'.$errno.':</b> '.$errstr.' '.$L->in_line.' '.$errline.' '.$L->of_file.' '
						.$errfile.', PHP '.PHP_VERSION.' ('.PHP_OS.")<br>\n");
				break;
			}
		} else {
			if ($errstr != 'stop') {
				$Page->title($L->error.': '.$errno);
				$Page->content('<b>'.$L->error.':</b> '.$errno."<br>\n");
			} else {
				$Page->Title = array($L->error.': '.$errno);
				$Page->Content = '<h2 align="center"><b>'.$L->error.':</b> '.$errno."<br></h2>\n";
			}
			if ($errstr == 'stop') {
				global $Classes, $stop;
				$stop = 2;
				$Classes->__destruct();
				exit;
			}
		}
	}
	private function log ($text) {
		
	}
	private function mail ($text) {
		
	}
	function num () {
        return $this->num;
    }
}
?>