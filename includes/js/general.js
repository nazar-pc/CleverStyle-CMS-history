$(function() {
	$(":radio").each(function (index, element) {
		if (!$(element).hasClass('noui')) {
			$(element).parent().buttonset();
		}
	});
	$(":checkbox").each(function (index, element) {
		if (!$(element).hasClass('noui')) {
			if ($(element).parent('label')) {
				$(element).parent().buttonset();
			} else {
				$(element).button();
			}
		}
	});
	$(":button").each(function (index, element) {
		if (!$(element).hasClass('noui')) {
			$(element).button();
		}
	});
	$('.ui-button').disableSelection();
	$('#debug').dialog({
			autoOpen:	false,
			height:		'400',
			hide:		'puff',
			show:		'scale',
			width:		'700'
	});
	$('.dialog').each(function (index, element) {
		if ($(element).attr('data-dialog')) {
			$(element).dialog($.secureEvalJSON($(element).attr('data-dialog')));
		} else {
			$(element).dialog();
		}
	});
	$('#admin_form *').change(function(){
		save = true;
	});
	$('#admin_form:reset').change(function(){
		save = false;
	});
	$('textarea').each(function (index, element) {
		if (!$(element).is('.EDITOR, .EDITORH, .SEDITOR, .noresize')) {
			$(element).autoResize();
		}
	});
	$('#login_slide').click(function () {
		$('#anonym_header_form').slideUp();
		$('#login_header_form').slideDown();
		$('#user_login').focus();
	});
	$('#registration_slide').click(function () {
		$('#anonym_header_form').slideUp();
		$('#register_header_form').slideDown();
		$('#register').focus();
	});
	$('#login_list').change(function() {
		$('#user_login').val(this.value);
		$('#user_login').focus();
		if (this.value) {
			$('#user_password, #show_password').hide();
		} else {
			$('#user_password, #show_password').show();
		}
	});
	$('#user_login, #user_password').keyup(function (event) {
		if (event.which == 13) {
			$('#login_process').click();
		}
	});
	$('#register').keyup(function (event) {
		if (event.which == 13) {
			$('#register_process').click();
		}
	});
	$('#register_list').change(function() {
		$('#register').val(this.value);
		$('#register').focus();
	});
	$('#login_process').click(function() {
		login($('#user_login').val(), $('#user_password').val());
	});
	$('#show_password').click(function() {
		if ($('#user_password').prop('type') == 'password') {
			$('#user_password').prop('type', 'text');
			$(this).addClass('ui-icon-unlocked').removeClass('ui-icon-locked');
		} else {
			$('#user_password').prop('type', 'password');
			$(this).addClass('ui-icon-locked').removeClass('ui-icon-unlocked');
		}
	});
	$('#register_process').click(function() {
		$('<div title="'+rules_agree+'">'+rules_text+'</div>')
			.appendTo('body')
			.dialog({
				autoOpen	: true,
				modal		: true,
				buttons		: [
					{
						text	: yes,
						click	: function () {
							$(this).dialog('close');
							registration($('#register').val());
						}
					},
					{
						text	: no,
						click	: function () {
							$(this).dialog('close');
						}
					}
				]
			});
	});
	$('.restore_password').click(function() {
		//TODO Restore password processing
	});
	$('.header_back').click(function() {
		$('#anonym_header_form').slideDown();
		$('#register_header_form').slideUp();
		$('#login_header_form').slideUp();
	});
	$('.reload_button').click(function () {
		location.reload();
	});
	$('.blocks_items_title+a, .blocks_items_title+a+a').click(function () {
		menuadmin(this.href, true); return false;
	});
	$('#change_theme, #change_color_scheme, #change_language').click(function () {
		$('#apply_settings').click();
	});
	$('#change_active_themes').change(function () {
		$(this).find('option[value=\''+$('#change_theme').val()+'\']').prop('selected', true);
	});
	$('#change_active_languages').change(function () {
		$(this).find('option[value=\''+$('#change_language').val()+'\']').prop('selected', true);
	});
	$('#system_readme_open').click(function () {
		$('#system_readme').dialog('open');
	});
	$('#system_license_open').click(function () {
		$('#system_license').dialog('open');
	});
	$('#debug_objects_toggle').click(function () {
		$('#debug_objects').toggle(500);
		if($(this).hasClass('open')){
			add = '<span class="ui-icon ui-icon-triangle-1-e" style="display: inline-block;"></span>';
			$(this).removeClass('open');
		}else{
			add = '<span class="ui-icon ui-icon-triangle-1-se" style="display: inline-block;"></span>';
			$(this).addClass('open');
		}
		$(this).html(add+objects);
	});
	$('#debug_user_toggle').click(function () {
		$('#debug_user').toggle(500);
		if($(this).hasClass('open')){
			add = '<span class="ui-icon ui-icon-triangle-1-e" style="display: inline-block;"></span>';
			$(this).removeClass('open');
		}else{
			add = '<span class="ui-icon ui-icon-triangle-1-se" style="display: inline-block;"></span>';
			$(this).addClass('open');
		}
		$(this).html(add+user_data);
	});
	$('#debug_queries_toggle').click(function () {
		$('#debug_queries').toggle(500);
		if($(this).hasClass('open')){
			add = '<span class="ui-icon ui-icon-triangle-1-e" style="display: inline-block;"></span>';
			$(this).removeClass('open');
		}else{
			add = '<span class="ui-icon ui-icon-triangle-1-se" style="display: inline-block;"></span>';
			$(this).addClass('open');
		}
		$(this).html(add+queries);
	});
	$('#debug_cookies_toggle').click(function () {
		$('#debug_cookies').toggle(500);
		if($(this).hasClass('open')){
			add = '<span class="ui-icon ui-icon-triangle-1-e" style="display: inline-block;"></span>';
			$(this).removeClass('open');
		}else{
			add = '<span class="ui-icon ui-icon-triangle-1-se" style="display: inline-block;"></span>';
			$(this).addClass('open');
		}
		$(this).html(add+cookies);
	});
});