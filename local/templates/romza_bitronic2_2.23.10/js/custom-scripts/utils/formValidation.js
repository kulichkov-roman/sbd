function textFieldCheck(field){
	var val = field.val();
	field.siblings('.v_error').remove();
	// first check: if required and empty - not valid.
	if (field.attr('required') && !val){
		field.removeClass('valid').addClass('not-valid');
		field.parent().append('<div class="v_error">' + BX.message('BITRONIC2_VALIDATION_FILL_FIELD') + '</div>');
		return false;
	}

	// second check: 
	// if field type is email and val is not like email - not valid.
	if (field.is('[type="email"]') && !isEmail(val)) {
		field.removeClass('valid').addClass('not-valid');
		field.parent().append('<div class="v_error">' + BX.message('BITRONIC2_VALIDATION_NOT_EMAIL') + '</div>');
		return false;
	}

	// we got here?! Sooo... let's assume the field is valid then.
	field.removeClass('not-valid').addClass('valid');
	return true;
}
function formCheck(form){
	var isValid = true;
	form.find('.textinput').each(function(){
		if (!textFieldCheck($(this))) {
			isValid = false;
			return false;
		}
	})
	return isValid;
}