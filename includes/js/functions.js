var save = false;
var stop_cache = true;
var cache_interval;
$(document).ready(
	function() {
		$(":radio").each(
			function (index, element) {
				if (!$(element).hasClass('noui')) {
					$(element).parent().buttonset();
				}
			}
		);
		$(":checkbox").each(
			function (index, element) {
				if (!$(element).hasClass('noui')) {
					if ($(element).parent('label')) {
						$(element).parent().buttonset();
					} else {
						$(element).button();
					}
				}
			}
		);
		$(":button").each(
			function (index, element) {
				if (!$(element).hasClass('noui')) {
					$(element).button();
				}
			}
		);		
		$('#debug').dialog({
				autoOpen: false,
				height: '400',
				hide: 'puff',
				show: 'scale',
				width: '700'
		});
		$('#admin_form *').change(
			function(){
				save = true;
			}
		);
		$('#admin_form:reset').change(
			function(){
				save = false;
			}
		);
		$('#admin_form > #apply_settings').mousedown(
			function () {
				$('#admin_form > #edit_settings').val('apply');
			}
		);
		$('#admin_form > #save_settings').mousedown(
			function () {
				$('#admin_form > #edit_settings').val('save');
			}
		);
		$('#admin_form > #cancel_settings').mousedown(
			function () {
				$('#admin_form > #edit_settings').val('cancel');
			}
		);
		$('textarea').each(
			function (index, element) {
				if (!$(element).hasClass('EDITOR') && !$(element).hasClass('EDITORH') && !$(element).hasClass('SEDITOR') && !$(element).hasClass('noresize')) {
					$(element).autoResize();
				}
			}
		);
	}
);
function menuadmin (item, direct_link) {
	if (direct_link) {
		url = direct_link;
	} else {
		url = base_url+'/'+item;
	}
	if (!save) {
		document.location.href = url;
	} else {
		if (confirm(save_before)) {
			if (!direct_link) {
				$('#admin_form').attr({action: url});
			}
			$('#admin_form > #save_settings').click();
		} else {
			if (confirm(continue_transfer)) {
				document.location.href = url;
			}
		}
	}
}
function debug_window () {
	$('#debug').dialog('open');
}
function admin_cache (element, action) {
	if (stop_cache) {
		stop_cache = false;
		cache_interval = setInterval(function () {cache_increase(element)}, 100);
		$(element).html('').progressbar(
			{value: 1}
		).load(
			action, function () {
				clearInterval(cache_interval);
				setTimeout(
					function () {
						$(element).progressbar('destroy');
						stop_cache = true;
					},
					100
				);
			}
		);
	}
}
function cache_increase (element) {
	if (!stop_cache) {
		$(element).progressbar('value', $(element).progressbar('value')+1);
		if ($(element).progressbar('value') == 100) {
			$(element).progressbar('value', 1);
		}
	}
};