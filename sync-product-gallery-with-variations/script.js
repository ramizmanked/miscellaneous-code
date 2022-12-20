jQuery(window).load(function () {
	if (jQuery('body').hasClass('single-product')) {
		const gallery_wrapper = 'ol.flex-control-nav',
			no_of_thumbnails = jQuery(gallery_wrapper).children().length;

		if (jQuery(gallery_wrapper).length && no_of_thumbnails > 0) {
			for (let i = 1; i <= no_of_thumbnails; i++) {
				jQuery('ol.flex-control-nav li:nth-child(' + i + ')')
					.attr(
						'data-variant-id',
						jQuery(
							'.woocommerce-product-gallery__wrapper > div:nth-child(' +
								i +
								')'
						).attr('data-variant-id')
					)
					.attr(
						'data-variant-in-stock',
						jQuery(
							'.woocommerce-product-gallery__wrapper > div:nth-child(' +
								i +
								')'
						).attr('data-is-variant-in-stock')
					)
					.children('img')
					.attr(
						'title',
						jQuery(
							'.woocommerce-product-gallery__wrapper > div:nth-child(' +
								i +
								')'
						).attr('data-title')
					);
			}
		}

		jQuery(
			'.woocommerce div.product div.images .flex-control-thumbs li'
		).click(function (e) {
			if (e.isTrigger) {
				return true;
			}
			const variant_id = jQuery(this).attr('data-variant-id');
			if (variant_id !== undefined) {
				jQuery('.woocommerce div.product .variations select').each(
					function () {
						jQuery('option', this).each(function () {
							const options =
								jQuery(this).attr('data-variant-id');
							if (options !== undefined && options !== '') {
								if (options.indexOf(',') !== -1) {
									const option_array = options.split(',');
									if (option_array.includes(variant_id)) {
										const variant = jQuery(this)
											.prop('selected', 'selected')
											.val();
										jQuery(this)
											.parent('select')
											.val(variant)
											.trigger('change');
									}
								} else if (
									jQuery(this).attr('data-variant-id') ===
									variant_id
								) {
									const variant = jQuery(this)
										.prop('selected', 'selected')
										.val();
									jQuery(this)
										.parent('select')
										.val(variant)
										.trigger('change');
								}
							}
						});
					}
				);
			}
		});
	}
});
