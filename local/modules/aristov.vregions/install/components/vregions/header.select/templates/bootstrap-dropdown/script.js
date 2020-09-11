var positionForAjax = {}
var ajax_url = "/bitrix/components/vregions/header.select/ajax.php";

// ������ ������������ �������
function ChangeVRegion(sender){
	var href = sender.getAttribute("href")
	var cookie = sender.getAttribute("data-cookie")
	
	var vRegionsArr = {
		sessid : BX.bitrix_sessid(),
		site_id : BX.message('SITE_ID'),
		action : "set-cookie",
		cookie : cookie
	}
	
	BX.ajax({
		url: ajax_url,
		data: vRegionsArr,
		method: "POST",
		dataType: "json",
		onsuccess: function(answer){
			// console.log(vRegionsArr)
			console.log(answer)
			if (answer.redirect){
				location.href = href + location.pathname;
			}
		},
		onfailure: function(){
			
		}
	});

	return false
}

window.onload = function(){
	var vRegionsArr = {
		sessid : BX.bitrix_sessid(),
		site_id : BX.message('SITE_ID'),
		action : "check-auto-geo-ness"
	}
	
	BX.ajax({
		url: ajax_url,
		data: vRegionsArr,
		method: "POST",
		dataType: "json",
		onsuccess: function(answer){
			// console.log(vRegionsArr);
			console.log(answer);
			if (answer.success){ // ���� ���� ���������� ��������������
				// �������� ���������� �������������� ������������
				if (answer.method == "google"){
					if (navigator.geolocation){
						navigator.geolocation.getCurrentPosition(
							geolocationSuccess,
							geolocationFailure
						);
					}
					else{ // ���� �� ���������� (��������, �� ���� ����������)
						goToDefaultLocation();
					}
				}
				if (answer.method == "sxgeo"){
					goCoordsByPHP();
				}
			}
		},
		onfailure: function(){
			
		}
	});
}

// �������� ���������� ����� php
function goCoordsByPHP(position){
	var vRegionsArr = {
		sessid : BX.bitrix_sessid(),
		site_id : BX.message('SITE_ID'),
		action : "get-php-coords"
	}
	
	BX.ajax({
		url: ajax_url,
		data: vRegionsArr,
		method: "POST",
		dataType: "json",
		onsuccess: function(answer){
			// console.log(vRegionsArr);
			// console.log(answer);
			
			if (answer.lat && answer.lon){
				console.log(answer);
				// ��������� �� ��������
				redirectToSubDomain(answer.lat, answer.lon);
			}
		},
		onfailure: function(){
			
		}
	});
}

// ���� �� ���������� ����������
function geolocationSuccess(position){
	// ����������� �������������� � ������ LatLng
	var location = new google.maps.LatLng(position.coords.latitude,position.coords.longitude);
	positionForAjax = position
	// console.log(positionForAjax)
	
	// ��������� �� ��������
	redirectToSubDomain(positionForAjax.coords.latitude, positionForAjax.coords.longitude);
}

// ��������� ������
function geolocationFailure(positionError){
	console.log("Your browser does not support geolocation");
	goToDefaultLocation();
}

// ���� ������ �� ����������
function goToDefaultLocation(){
	console.log("fail")
}

// ������� �� ��������
function redirectToSubDomain(lat, lon){
	vRegionsArr = {};
	vRegionsArr.sessid = BX.bitrix_sessid();
	vRegionsArr.site_id = BX.message("SITE_ID");
	vRegionsArr.action = "get-closest-region";
	// vRegionsArr.longitude = gvRegionsArr.coords.longitude;
	vRegionsArr.longitude = lon;
	// vRegionsArr.latitude = gvRegionsArr.coords.latitude;
	vRegionsArr.latitude = lat;
	// console.log(vRegionsArr);
	// debugger
	
	BX.ajax({
		url: ajax_url,
		data: vRegionsArr,
		method: "POST",
		dataType: "json",
		onsuccess: function(answer){
			// console.log(vRegionsArr);
			console.log(answer);
			if (answer.redirect){
				location.href = answer.url_without_path+location.pathname
			}
		},
		onfailure: function(){
			
		}
	});
}