function initRangeSlider(slider, options){
	var $slider = $(slider),
		minValue = options.minValue,
		maxValue = options.maxValue,
		step = ( options.step === undefined ) ? 1 : options.step,
		$inputLower = $slider.parent().find('.range-input-lower'),
		$inputUpper = $slider.parent().find('.range-input-upper'),
		startLower = ( options.startLower === undefined ) ? minValue : options.startLower,
		startUpper = ( options.startUpper === undefined ) ? maxValue : options.startUpper;
	function filterPips(value){
		if ( value === minValue || value === maxValue ) return 1;
		return 2;
	}
	$slider.noUiSlider({
		start: [startLower, startUpper],
		connect: true,
		behaviour: 'snap',
		step: step,
		range: {
			'min': minValue,
			'max': maxValue
		},
		format: wNumb(options.format)
	}).noUiSlider_pips({
		mode: 'values',
		values: options.pips.values,
		density: 50,
		filter: filterPips
	});
	$slider.Link('lower').to('-inline-<div class="handle-inner"></div>', function ( value ) {
		// The tooltip HTML is 'this', so additional
		// markup can be inserted here.
		$(this).html(
			'<div class="arrow"></div><div class="stripes"></div> \
			<span class="text">'+value+'</span>'
		);
	});
	$slider.Link('lower').to($inputLower);
	$slider.Link('upper').to('-inline-<div class="handle-inner"></div>', function ( value ) {
		// The tooltip HTML is 'this', so additional
		// markup can be inserted here.
		$(this).html(
			'<div class="arrow"></div><div class="stripes"></div> \
			<span class="text">'+value+'</span>'
		);
	});
	$slider.Link('upper').to($inputUpper);
}