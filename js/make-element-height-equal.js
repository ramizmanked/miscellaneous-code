/**
 * Set equal height (heighest among elements) for all matching elements.
 *
 * @package
 */

let heightToSet = 0;
$('.wrapper-div .inner-div').each(function () {
	$('.element-to-adjust', this).each(function () {
		if ($(this).height() > heightToSet) {
			heightToSet = $(this).height();
		}
	});
});
$('.wrapper-div .inner-div .content-wrapper').height(heightToSet);
