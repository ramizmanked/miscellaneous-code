$.fn.isInViewport = function (predefinedScroll = 0) {
	const elementTop = $(this).offset().top + predefinedScroll;
	const elementBottom = elementTop + $(this).outerHeight();
	const viewportTop = $(window).scrollTop();
	const viewportBottom = viewportTop + $(window).height();
	return elementBottom > viewportTop && elementTop < viewportBottom;
};

// Usage
if (elem.length && elem.isInViewport()) {
	// Element is in viewport.
}
