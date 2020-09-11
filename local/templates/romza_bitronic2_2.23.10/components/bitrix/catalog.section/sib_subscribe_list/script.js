jQuery(function($){
	$('.personal-account-main .table-list__item .btn-delete').on('click', function(e){
		e.preventDefault();

		var $item = $(this).closest('.table-list__item');
		/*var spinner = RZB2.ajax.spinner($td);
		spinner.Start();*/

		$.ajax({
			url: SITE_DIR + 'ajax/sib/personal_products.php',
			type: "POST",
			data: {id: $(this).data('id')},
			dataType: "json",
			success: function(data) {
				if (typeof data == "object") {
					RZB2.ajax.showMessage(data.message, data.status);
					if (data.status == 'success') {
                        $item.fadeOut();
					}
				} else {
					RZB2.ajax.showMessage('Bad responce from server', 'fail');
				}
			},
			error: function(jqXHR, textStatus, errorThrown) {
				RZB2.ajax.showMessage('AJAX error: ' + textStatus, 'fail');
			},
		});
	});
});