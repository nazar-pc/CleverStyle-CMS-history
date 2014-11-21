var save = false;
function menuadmin (item) {
	url = base_url+'/'+item;
	if (!save) {
		document.location.href = url;
	} else {
		if (confirm(save_before)) {
			$('.admin_form').attr({action: url});
			$('.admin_form > #save_settings').click();
		} else {
			if (confirm(continue_transfer)) {
				document.location.href = url;
			}
		}
	}
}
