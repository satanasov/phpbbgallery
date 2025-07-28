$(document).ready(function() {
	/**
	 * Function to resize albums
	 */
	function resize() {
		$('.albums').each(function() {
			var $this = $(this),
				albums = $this.children(),
				maxWidth = Math.floor($this.width()),
				lastPosition = 0,
				lastRow = [],
				maxHeight = 0,
				nextPosition = 0;

			// Reset all albums
			albums.removeClass('first-item').css('min-height', '');

			albums.each(function(i) {
				var item = $(this),
					left = Math.floor(item.offset().left);

				if (i > 0 && left <= nextPosition) {
					// Align last row
					if (lastRow.length) {
						albums.filter(lastRow.join(',')).css('min-height', maxHeight + 'px');
					}

					// Clear row
					item.addClass('first-item');

					// Reset counters
					left = 0;
					lastFirst = i;
					maxHeight = 0;
					lastRow = [];
				}

				lastRow.push(':nth-child(' + (i + 1) + ')');
				lastPosition = left;
				nextPosition = left + item.width() - 1;
				maxHeight = Math.max(maxHeight, item.height());	
			});
		});
	}
	resize();
	$(window).resize(resize);
});