BX.namespace('BX.Sale.PersonalOrderComponent');

(function() {
	BX.Sale.PersonalOrderComponent.PersonalOrderList = {
		init : function(params)
		{
			var wrapper = $('.main-orders');

			params.paymentList = params.paymentList || {};
			params.url = params.url || "";

            wrapper.find('.order_status_sort').change(function() {
				var optionSelected = $("option:selected", this),
					status = optionSelected.data('status'),
					data = {ajax: 'Y', sort_status: status};

                if (status === undefined)
                    status = 0;

				BX.ajax(
					{
						method: 'POST',
						dataType: 'html',
						url: params.url,
						data: data,
						onsuccess: function (result) {
                            wrapper.find('.orders_list').html(result);
                        }
					}, this
				);
            });
		}
	};
})();
