var save = false;
var cache_interval;
	$(function() {
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
		).disableSelection();
		$('#debug').dialog({
				autoOpen:	false,
				height:		'400',
				hide:		'puff',
				show:		'scale',
				width:		'700'
		});
		$('.dialog').each(
			function (index, element) {
				if ($(element).attr('data-dialog')) {
					$(element).dialog($.secureEvalJSON($(element).attr('data-dialog')));
				} else {
					$(element).dialog();
				}
			}
		);
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
	});
	function menuadmin (item, direct_link) {
		var url = direct_link ? item : current_base_url+'/'+item;
		if (!save) {
			document.location.href = url;
		} else {
			if (confirm(save_before)) {
				$('#admin_form').attr('action', url);
				$('#save_settings').click();
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
		cache_interval	= setInterval(function () {progress(element)}, 100);
		$(element).html('').progressbar(
			{value: 1}
		).load(
			action, function () {
				clearInterval(cache_interval);
				setTimeout(
					function () {
						$(element).progressbar('destroy');
					},
					100
				);
			}
		);
	}
	function progress (element) {
		$(element).progressbar('value', $(element).progressbar('value')+1);
		if ($(element).progressbar('value') == 100) {
			$(element).progressbar('value', 1);
		}
	};
	function db_test (url, added) {
		$('#test_db').html('<div id="test_progress" style="width: 100%"></div>');
		$($('#test_progress')).progressbar({value: 1});
		$('#test_db').dialog('open');
		var test_interval	= setInterval(function () {progress('#test_progress')}, 100);
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
			var db = json_encode({
				type:		document.getElementsByName('db[type]').item(0).value,
				name:		document.getElementsByName('db[name]').item(0).value,
				user:		document.getElementsByName('db[user]').item(0).value,
				password:	document.getElementsByName('db[password]').item(0).value,
				host:		document.getElementsByName('db[host]').item(0).value,
				codepage:	document.getElementsByName('db[codepage]').item(0).value
			});
			$.ajax({
				url:		url,
				type:		'POST',
				data:		'db=' + db,
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
		test_interval	= setInterval(function () {progress('#test_progress')}, 100);
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
			var storage = json_encode({
				url:		document.getElementsByName('storage[url]').item(0).value,
				host:		document.getElementsByName('storage[host]').item(0).value,
				connection:	document.getElementsByName('storage[connection]').item(0).value,
				user:		document.getElementsByName('storage[user]').item(0).value,
				password:	document.getElementsByName('storage[password]').item(0).value
			});
			$.ajax({
				url:		url,
				type:		'POST',
				data:		'storage=' + storage,
				success:	function(result) {
					clearInterval(test_interval);
					$('#test_storage').html(result);
				}
			});
		}
	}
	//Для удобства и простоты - обертки для функций JavaScript с названиями аналогичных функций в PHP
	function json_encode (obj) {
		return $.toJSON(obj);
	}
	function json_decode (str) {
		return $.secureEvalJSON(str);
	}
	//Поддерживает алгоритмы sha224, sha256, sha384, sha512
	function hash (algo, data) {
		return new jsSHA(data).getHash(algo);
	}
	function setcookie (name, value, expires, path, domain, secure) {
		return $.cookie(name, value, {expires: expires, path: path ? path : '/', domain: domain, secure: secure});
	}
	function getcookie (name) {
		return $.cookie(name);
	}
