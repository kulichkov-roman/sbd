function ChangeVRegion(sender){
	var cookie = sender.getAttribute("data-cookie");
	var href   = sender.getAttribute("href");

	var av = new AristovVregions;
	av.setCookie(
		cookie,
		function(answer){
			if (answer.redirect){
				location.href = href + location.pathname + location.search + location.hash;
			}
			if (answer.reload){
				location.reload();
			}

			OpenVregionsPopUp("close");
		}
	);

	return false;
}

// !! keep function with this name
function vrAskRegion(region_name, cookie, url_without_path){
	var vregions_popup = document.getElementsByClassName("vregions-popup-que");
	if (vregions_popup[0]){
		var region_name_elem     = vregions_popup[0].querySelector("#suggested-region");
		var success_quess_button = vregions_popup[0].querySelector("#we_guessed");
		var sepia                = document.getElementsByClassName("vregions-sepia")[0];

		OpenVregionsPopUp("close");

		region_name_elem.innerHTML = region_name;
		success_quess_button.setAttribute("data-cookie", cookie);
		success_quess_button.setAttribute("href", url_without_path);

		sepia.style.display             = "block";
		vregions_popup[0].style.display = "block";
		vrAddClass(document.getElementsByTagName('body')[0], 'modal-open');
	}

	return false;
}

function OpenVregionsPopUp(action, popup_id, sepia_id){
	if (!popup_id){
		popup_id = "vregions-popup";
	}
	var vregions_popup  = document.getElementById(popup_id);
	var vregions_popups = document.getElementsByClassName("vr-popup");

	var sepia           = document.getElementById(sepia_id);
	var vregions_sepias = document.getElementsByClassName('vregions-sepia');

	for (var i = 0; i < vregions_popups.length; i++){
		vregions_popups[i].style.display = "none";
	}

	if (!action || action !== "close"){
		sepia.style.display          = "block";
		vregions_popup.style.display = "block";
		vrAddClass(document.getElementsByTagName('body')[0], 'modal-open');
	}

	if (action === "close"){
		for (var i = 0; i < vregions_popups.length; i++){
			vregions_popups[i].style.display = "none";
		}
		for (var i = 0; i < vregions_sepias.length; i++){
			vregions_sepias[i].style.display = "none";
		}
		vrRemoveClass(document.getElementsByTagName('body')[0], 'modal-open');
	}

	return false;
}

function vrAddClass(o, c){
	var re = new RegExp("(^|\\s)" + c + "(\\s|$)", "g")
	if (re.test(o.className)) return
	o.className = (o.className + " " + c).replace(/\s+/g, " ").replace(/(^ | $)/g, "")
}

function vrRemoveClass(o, c){
	var re      = new RegExp("(^|\\s)" + c + "(\\s|$)", "g")
	o.className = o.className.replace(re, "$1").replace(/\s+/g, " ").replace(/(^ | $)/g, "")
}

$(document).on("keyup", ".js-vregions-search-input", function(event){
	var mask = $(this).val();

	if (mask.length){
		var av = new AristovVregions;
		av.findRegionByNameMask(
			mask,
			function(answer){
				$('.vregions-suggestions-wrap').remove();
				vrRemoveClass(document.getElementsByClassName('js-vregions-search-input')[0], 'with-suggestions');

				if (answer.regions && answer.regions.length){
					vrAddClass(document.getElementsByClassName('js-vregions-search-input')[0], 'with-suggestions');

					$('.js-vregions-search-wrap').append('<div class="vregions-suggestions-wrap"></div>');

					answer.regions.forEach(function(el, i){
						if (el.NAME && el.HREF){
							$('.vregions-suggestions-wrap').append('<a href="' + el.HREF + '" class="vregions-suggestion" data-cookie="' + el.CODE + '" onclick="ChangeVRegion(this); return false;">' + el.NAME + '</a>');
						}else{
							if (el.CITY_NAME){
								$('.vregions-suggestions-wrap').append('<a class="vregions-suggestion" data-loc_id="' + el.ID + '" onclick="ChangeVBitrixLocation(this); return false;">' + el.CITY_NAME + '</a>');
							}
						}
					});
				}
			}
		);
	}
});

$(document).on("blur", ".js-vregions-search-input", function(event){
	var mask = $(".js-vregions-search-input").val();

	if (!mask){
		$('.vregions-suggestions-wrap').remove();
		vrRemoveClass(document.getElementsByClassName('js-vregions-search-input')[0], 'with-suggestions');
	}
});

function ChangeVBitrixLocation(sender){
	var id = sender.getAttribute("data-loc_id");

	var av = new AristovVregions;
	av.changeBitrixLocation(
		id,
		function(answer){
			if (answer.success){
				location.reload();
			}
		}
	);
}

$(document).on("click", ".vr-popup", function(e){
	if (e.target.className == 'vr-popup' || e.target.className == 'vr-popup vregions-popup-que'){
		OpenVregionsPopUp('close');
	}
});

// фильтрация по областям
$(document).on("change", ".js-vregions-oblast__select", function(e){
	var oblast = $(this).val();

	$('.js-vr-popup__region-link').show();

	if (oblast){
		$('.js-vr-popup__region-link').each(function(i, el){
			var regionOblast = $(el).attr('data-oblast');
			if (regionOblast != oblast){
				$(el).hide();
			}
		});
	}
});

// кнопка моего города нет в списке
$(document).on("click", ".js-another-region-btn", function(e){
	var av = new AristovVregions;
	av.checkLocation(
		'ip',
		function(answer2){
			if (answer2.lat && answer2.lon){
				av.redirectToClosestRegion(answer2.lat, answer2.lon, true);
			}
		}
	);

	return false;
});