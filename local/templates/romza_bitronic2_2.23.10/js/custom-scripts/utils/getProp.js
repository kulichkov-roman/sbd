function getPropFromRadio(radio, def){
	// look for checked radio
	var checked = radio.filter(':checked');
	if ( checked.length ){ // radio checked found, set value
		return checked.val();
	} 

	// no checked radio, look for defaults
	var defaults = radio.filter('[data-default]');
	if ( defaults.length ){ // defaults found, set value
		defaults.attr('checked', true);
		return defaults.val();
	}

	// no checked, no defaults... let's use value from first radio
	radio[0].attr('checked', true);
	return radio[0].val();
}
function getPropFromText(textinput, def){
	// check if we have some text at all
	var val = textinput.val();
	if ( val && val !== '' ){
		return val;
	}
	return def;
}
function getValuesFromInput(input){
	var values = [];
	if ( input.is('[type="radio"]') ){
		for ( var i = 0; i < input.length; i++ ){
			values.push(input.eq(i).val());
		}
		return values;
	} 
	if ( input.is('select') ){
		var options = input.children('option');
		for ( var i = 0; i < options.length; i++ ){
			values.push(options.eq(i).val());
		}
		return values;
	}

	values = ['true', 'false'];
	return values;
}
/* 
function for getting properies' values on page load and associate them with inputs
property : string like "b2_styling-type" which is used to find associated inputs and
to store value in local storage

settings : an object which can have the following contents:
	input : jQuery element containing associated input of any type.
	If no input passed, function tries to find it via [name="property"]
	element : jQuery element to which value is applied as class
	values : possible values of property, used as classes on associated element
	and as values of associated input. If no values are passed, array is built based
	on associated input values. If there is no input, than values are true and false.
	func: function to perform on property change, if just changing class
	is not enough (example: move menu to side or top)
	!IMPORTANT! If values are true and false (STRINGS!!!), then property itself
	is used for	working with associated DOM element
*/
function getProp(property, settings){
	//console.log('getProp called');
	var input = settings.input || $('[name="'+property+'"]');
	var el = settings.element;
	var values = (settings.values) ? settings.values.split(' ') : getValuesFromInput(input);
	var func = settings.func;
	var value;
	var predefined = false;
	var isBoolean = false;

	if ( values[0] == "true" || values[0] == "false" ){
		isBoolean = true;
	}
	// first, check for predefined values on DOM (may be set via PHP)
	if ( el ){
		if ( isBoolean ){
			if ( el.hasClass(property) ){
				value = 'true';
			}
		} else {
			for ( var i = 0; i < values.length; i++ ){
				if ( el.hasClass(values[i]) ){
					value = values[i];
					predefined = true;
					break;
				}
			}
		}
		
	}

	// if value wasn't predefined, check in local storage
	if ( !value ){
		value = localStorage.getItem(property);
	}

	// if still not found, check for input values
	if ( !value ){
		if ( input.is('[type="radio"]') ){
			value = getPropFromRadio(input);
		} else if ( input.is('select') ){
			value = input.val();
		} else if ( input.is('[type="checkbox"]') ){
			value = (input.attr('checked')) ? 'true' : 'false';
		} else if ( input.is('[type="text"]') ){
			value = getPropFromText(input, values[0]);
		} else { // input undefined, forget it and just set first available val
			value = values[0];
		}
	} else { // if value is already found, update input values
		if ( input.is('[type="radio"]') ){
			input.filter('[value="'+value+'"]').attr('checked', true);
		} else if ( input.is('select, [type="text"]') ){
			input.val(value);
		} else if ( input.is('[type="checkbox"]') ){
			input.attr('checked', value);
		}
	}

	// if value was NOT predefined, update DOM
	if ( !predefined ){
		if ( el ){
			if ( isBoolean ){
				if ( value == "true" ) el.addClass(property);
				else el.removeClass(property);
			} else{
				el.removeClass(values.join(' ')).addClass(value);
			}
		}
		if (func){
			func(value);
		}
	}
	
	if (input.length){
		input.on('change', function(){
			if ( input.is('[type="checkbox"]') ){
				value = (input.attr('checked')) ? 'true' : 'false';
			} else {
				value = $(this).val();
			}
			localStorage.setItem(property, value);
			if ( el ){
				if ( isBoolean ){
					if ( value == "true" ) el.addClass(property);
					else el.removeClass(property);
				} else{
					el.removeClass(values.join(' ')).addClass(value);
				}
			}
			if (func){
				func(value);
			}
		})
	}
	return value;
}