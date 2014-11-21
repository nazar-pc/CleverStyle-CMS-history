$(
	function() {
		$('.EDITOR').tinymce({
			// General options
			doctype : '<!doctype html>',
			theme : "advanced",
			skin : "o2k7",
			//skin_variant : "black",
			plugins : "pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,insertdatetime,preview,media,searchreplace,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,inlinepopups,autosave,advlist",
			// Theme options
			theme_advanced_buttons1 : "bold,italic,underline,strikethrough,sub,sup,|,justifyleft,justifycenter,justifyright,justifyfull,|,forecolor,backcolor,|,bullist,numlist,|,link,unlink,anchor,image,media,emotions,charmap,code",
			theme_advanced_buttons2 : "save,newdocument,|,copy,cut,paste,pastetext,pasteword,|,undo,redo,|,search,replace,|,tablecontrols",
			theme_advanced_buttons3 : "insertlayer,moveforward,movebackward,absolute,|,advhr,cleanup,removeformat,visualaid,|,ltr,rtl,|,outdent,indent,blockquote,cite,abbr,acronym,del,ins,insertdate,inserttime,attribs,|,preview,fullscreen",
			theme_advanced_buttons4 : "styleselect,styleprops,formatselect,fontselect,fontsizeselect,|,visualchars,nonbreaking,template,pagebreak,restoredraft,|,help",
			theme_advanced_toolbar_location : "top",
			theme_advanced_toolbar_align : "center",
			theme_advanced_statusbar_location : "bottom",
			theme_advanced_resizing : true,
			file_browser_callback : typeof(AjexFileManager) == 'object' ? 'AjexFileManager.open' : ''
		});
		$('.EDITORH').tinymce({
			// General options
			doctype : '<!doctype html>',
			theme : "advanced",
			skin : "o2k7",
			//skin_variant : "black",
			plugins : "pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,insertdatetime,preview,media,searchreplace,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,inlinepopups,autosave",
			// Theme options
			theme_advanced_buttons1 : "bold,italic,underline,strikethrough,sub,sup,|,justifyleft,justifycenter,justifyright,justifyfull,|,forecolor,backcolor,|,bullist,numlist,|,link,unlink,anchor,image,media,emotions,charmap,code",
			theme_advanced_buttons2 : "save,newdocument,|,copy,cut,paste,pastetext,pasteword,|,undo,redo,|,search,replace,|,tablecontrols",
			theme_advanced_buttons3 : "insertlayer,moveforward,movebackward,absolute,|,advhr,cleanup,removeformat,visualaid,|,ltr,rtl,|,outdent,indent,blockquote,cite,abbr,acronym,del,ins,insertdate,inserttime,attribs,|,preview,fullscreen",
			theme_advanced_buttons4 : "styleselect,styleprops,formatselect,fontselect,fontsizeselect,|,visualchars,nonbreaking,template,pagebreak,restoredraft,|,help",
			theme_advanced_toolbar_location : "external",
			theme_advanced_toolbar_align : "center",
			theme_advanced_statusbar_location : "bottom",
			theme_advanced_resizing : true,
			file_browser_callback : typeof(AjexFileManager) == 'object' ? 'AjexFileManager.open' : ''
		});
		if (typeof(AjexFileManager) == 'object') {
			AjexFileManager.init({
				returnTo: 'tinymce',
				height: 550
			});
		}
		$('.SEDITOR').tinymce({
			doctype : '<!doctype html>',
			theme : "simple",
			skin : "o2k7"
		});
		$('textarea').autoResize();
	}
);