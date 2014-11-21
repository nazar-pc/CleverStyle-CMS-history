<?php
class Error {
	protected	$num = 0;
	function __construct () {
		set_error_handler(array($this, 'process'));
	}
	function process ($errno, $errstr='', $errfile=true, $errline='') {
		global $L, $Page, $Config;
		if (is_array($errno)) {
			$args = $errno;
			unset($errno);
			$errno = isset($args[0]) ? $args[0] : '';
			$errstr = isset($args[1]) ? $args[1] : $errstr;
			$errfile = isset($errfile[2]) ? $errfile[2] : $errfile;
			$errline = isset($args[3]) ? $args[3] : $errline;
			unset($args);
		}
		if ($errfile && $errline) {
			switch ($errno) {
				case E_USER_ERROR:
				case E_ERROR:
					++$this->num;
					$Page->title($L->fatal.' #'.$errno.': '.$errstr.' '.$L->page_generation_aborted.'...');
					$Page->content(
						'<p><span style="text-transform: uppercase; font-weight: bold;">'.$L->fatal.' #'.$errno.':</span> '.$errstr.' '.$L->on_line.' '.$errline.' '.$L->of_file.' '
						.$errfile.', PHP '.PHP_VERSION.' ('.PHP_OS.")<br>\n"
						.$L->page_generation_aborted."...<br>\n"
						.$L->report_to_admin."<br>\n"
						.(is_object($Config) ?
							($Config->core['admin_mail'] ? $L->admin_mail.': <a href="mailto:'.$Config->core['admin_mail']."\">".$Config->core['admin_mail']."</a><br>\n" : '')
							.($Config->core['admin_phone'] ? $L->admin_phone.': '.$Config->core['admin_phone']."<br>\n" : '')
						: '').'<br>'
					);
					__finish();
				break;
				
				case E_USER_WARNING:
				case E_WARNING:
					if (!$Page->Title) {
						$Page->title($L->error.' #'.$errno.': '.$errstr);
					}
					$Page->content(
						'<span style="text-transform: uppercase; font-weight: bold;">'.$L->error.' #'.$errno.':</span> '.$errstr.' '.$L->on_line.' '.$errline.' '.$L->of_file.' '
						.$errfile.', PHP '.PHP_VERSION.' ('.PHP_OS.")<br>"
						.$L->report_to_admin."<br>\n"
						.(is_object($Config) ?
							($Config->core['admin_mail'] ? $L->admin_mail.': <a href="mailto:'.$Config->core['admin_mail']."\">".$Config->core['admin_mail']."</a><br>\n" : '')
							.($Config->core['admin_phone'] ? $L->admin_phone.': '.$Config->core['admin_phone']."<br>\n" : '')
						: '').'<br>'
					);
				break;
				
				case E_USER_NOTICE:
				case E_NOTICE:
					if (!$Page->Title) {
						$Page->title($L->warning.' #'.$errno.': '.$errstr);
					}
					$Page->content(
						'<span style="text-transform: uppercase; font-weight: bold;">'.$L->warning.' #'.$errno.':</span> '.$errstr.' '.$L->on_line.' '.$errline.' '.$L->of_file.' '
						.$errfile.', PHP '.PHP_VERSION.' ('.PHP_OS.")<br><br>\n"
					);
				break;
				
				default:
					if (!$Page->Title) {
						$Page->title($L->error.' #'.$errno.': '.$errstr);
					}
					$Page->content(
						'<span style="text-transform: uppercase; font-weight: bold;">'.$L->error.':</span> '.$errstr.' '.$L->on_line.' '.$errline.' '.$L->of_file.' '
						.$errfile.', PHP '.PHP_VERSION.' ('.PHP_OS.")<br><br>\n");
				break;
			}
		} else {
			if ($errstr == 'stop') {
				$Page->Title = array($L->error.': '.$errno);
				$Page->Content = '<h2 align="center"><span style="text-transform: uppercase; font-weight: bold;">'.$L->error.':</span> '.$errno."<br></h2>\n";
				global $stop;
				$stop = 2;
				__finish();
			} else {
				$Page->title($L->error.': '.$errno);
				$Page->content('<span style="text-transform: uppercase; font-weight: bold;">'.$L->error.':</span> '.$errno."<br>\n");
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
	function __call ($func, $args) {
		$this->process($args);
	}
}
?>