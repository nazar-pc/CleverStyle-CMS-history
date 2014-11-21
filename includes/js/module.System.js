$(function () {
	if (in_admin && module == 'System' && routing[0] == 'components' && routing[1] == 'blocks' && routing[2] != 'settings') {
		$('#apply_settings, #save_settings').click(
			function () {
				$('#position').val(
					json_encode({
						top:		$('#top_blocks_items').sortable('toArray'),
						left:		$('#left_blocks_items').sortable('toArray'),
						invisible:	$('#invisible_blocks_items').sortable('toArray'),
						right:		$('#right_blocks_items').sortable('toArray'),
						bottom:		$('#bottom_blocks_items').sortable('toArray')
					})
				);
			}
		);
		$('#top_blocks_items, #left_blocks_items, #invisible_blocks_items, #right_blocks_items, #bottom_blocks_items').sortable({
			connectWith:	'.blocks_items',
			placeholder:	'ui-state-default',
			items:			'li:not(.ui-state-disabled)',
			cancel:			'.ui-state-disabled',
			update:			function (event, ui) {save = true;}
		}).disableSelection();
	}
});
function blocks_toggle (position) {
	if ($('#'+position+'_blocks_items').attr('data-mode') == 'open') {
		$('#'+position+'_blocks_items > li:not(.ui-state-disabled)').slideUp('fast');
		$('#'+position+'_blocks_items').attr('data-mode', 'close');
	} else {
		$('#'+position+'_blocks_items > li:not(.ui-state-disabled)').slideDown('fast');
		$('#'+position+'_blocks_items').attr('data-mode', 'open');
	}
}