$(
	function() {
		$(":radio, :checkbox").each(
			function (index, element) {
				if (!$(element).hasClass('noui')) {
					$(element).parent().buttonset();
				}
			}
		);
	}
);