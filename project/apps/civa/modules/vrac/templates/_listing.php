<table cellspacing="0" cellpadding="0" class="table_listing">
	<?php include_partial('vrac/listingHeader') ?>
	<tbody>
		<?php 
			$counter = 0;
			foreach ($vracs as $item) {
				include_partial('vrac/listingItem', array('item' => $item->value, 'alt' => ($counter%2)));
				$counter++;
			}
		?>
	</tbody>
</table>