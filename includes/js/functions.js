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
				autoOpen:	false,
				height:		'400',
				hide:		'puff',
				show:		'scale',
				width:		'700'
		});
		$('#test_db, #test_storage').dialog({
				autoOpen:	false,
				height:		'75',
				hide:		'puff',
				modal:		true,
				resizable:	false,
				show:		'scale',
				width:		'250'
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
		stop_cache		= false;
		cache_interval	= setInterval(function () {progress(element)}, 100);
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
function progress (element) {
	if (!stop_cache) {
		$(element).progressbar('value', $(element).progressbar('value')+1);
		if ($(element).progressbar('value') == 100) {
			$(element).progressbar('value', 1);
		}
	}
};
function db_test (url, added) {
	$('#test_db').html('<div id="test_progress" style="width: 100%"></div>');
	$($('#test_progress')).progressbar({value: 1});
	$('#test_db').dialog('open');
	var test_interval = setInterval(function () {progress(element)}, 100);
	if (added == true) {
		$.ajax({
			url:		url,
			type:		'POST',
			success:	function(result) {
				clearInterval(test_interval);
				$('#test_db').html(result);
			}
		});
	} else {
		var db = {
			type:		document.getElementsByName('db[type]').item(0).value,
			name:		document.getElementsByName('db[name]').item(0).value,
			user:		document.getElementsByName('db[user]').item(0).value,
			password:	document.getElementsByName('db[password]').item(0).value,
			host:		document.getElementsByName('db[host]').item(0).value,
			codepage:	document.getElementsByName('db[codepage]').item(0).value
		};
		$.ajax({
			url:		url,
			type:		'POST',
			data:		'db=' + $.toJSON(db),
			success:	function(result) {
				clearInterval(test_interval);
				$('#test_db').html(result);
			}
		});
	}
}
function storage_test (url, added) {
	$('#test_storage').html('<div id="test_progress" style="width: 100%"></div>');
	$($('#test_progress')).progressbar({value: 1});
	$('#test_storage').dialog('open');
	test_interval = setInterval(function () {progress(element)}, 100);
	if (added == true) {
		$.ajax({
			url:		url,
			type:		'POST',
			success:	function(result) {
				clearInterval(test_interval);
				$('#test_storage').html(result);
			}
		});
	} else {
		var db = {
			type:		document.getElementsByName('storage[connection]').item(0).value,
			user:		document.getElementsByName('storage[user]').item(0).value,
			password:	document.getElementsByName('storage[password]').item(0).value,
			host:		document.getElementsByName('storage[host]').item(0).value
		};
		$.ajax({
			url:		url,
			type:		'POST',
			data:		'db=' + $.toJSON(db),
			success:	function(result) {
				clearInterval(test_interval);
				$('#test_storage').html(result);
			}
		});
	}
}