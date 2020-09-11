var edost_office_create_c = function(name) {
	var self = this;
	var protocol = (document.location.protocol == 'https:' ? 'https://' : 'http://')
	var geo, format, param_profile, onkeydown_backup = 'free', overflow_backup = false, onclose = '', map_loading = false, api21 = false, browser_width = 0, browser_height = 0, small_ico = false
	var update_number = 0
	var param_start, onclose_set_office_start
	var loading = '<div class="edost_map_loading"><img src="' + protocol + 'edostimg.ru/img/site/loading.gif" border="0" width="64" height="64"></div>'
	var tariff_info = '<div class="edost_button2_info2">чтобы выбрать пункт выдачи, нажмите на подходящий тариф</div>'
	var free_ico = 'Бесплатно!'

	this.map = false
	this.data = false
	this.timer = false
	this.timer_resize = false;
	this.timer_inside = false
	this.data_string = ''
	this.data_parsed = false
	this.loading_inside = ''
	this.inside = false
	this.isMobile = BX.browser.IsMobile()

	this.clone = function(o) {
		var v = {};
		for (var p in o) {
			if (o[p] instanceof Array) {
				v[p] = [];
				for (var i = 0; i < o[p].length; i++) v[p][i] = o[p][i];
			}
			else v[p] = o[p];
		}
		return v;
	}


	this.window = function(param, onclose_set_office) {
		if (param == 'shop' || param == 'office' || param == 'terminal' || param == 'all' || param.substr(0, 7) == 'profile' || param == 'inside') {
			param_start = param;
			onclose_set_office_start = onclose_set_office;
		}
		
		if (param.substr(0, 7) == 'profile') {
			param_profile = param.substr(8);
			param = 'profile';
		}

		if (param == 'inside') {
			self.inside = true;
			param = 'all';
		}
		else if (param != 'show') {
			self.inside = false;
		}

		if (onclose_set_office == true) onclose = param;
		
		var office_format = format;
		if (param == 'shop' || param == 'office' || param == 'terminal' || param == 'all' || param == 'profile') {
			format = param;
			if (param_profile) format += '_' + param_profile;
			param = 'show'
		}

		if (param == 'esc') {
			if (!self.map || !self.map.balloon.isOpen()) param = 'close';
			else {
				self.balloon('close');
				return;
			}
		}

		if (!self.inside)
			if (param != 'show') {
			    document.onkeydown = onkeydown_backup;
				document.body.style.overflow = overflow_backup;
			    onkeydown_backup = 'free';
			}
			else if (onkeydown_backup == 'free') {
			    onkeydown_backup = document.onkeydown;
			    overflow_backup = document.body.style.overflow;
				document.onkeydown = new Function('event', 'if (event.keyCode == 27) ' + name + '.window("esc");');
			}

		// интеграция окна
		if (self.inside) {
			var E = document.getElementById('edost_office_inside');
			if (!E) return;
			var E2 = document.getElementById('edost_office_inside_head');
			if (!E2) E.innerHTML = '<div id="edost_office_inside_head" class="edost_office_inside_head"></div><div id="edost_office_inside_map"></div>';
		}
		else {
			var E = document.getElementById('edost_office_window');
			if (!E) {
				var E = document.querySelector('#bx-soa-delivery .rbs-edost-map');

				/* var E2 = document.createElement('DIV');
				E2.className = 'edost_office_window_fon';
				E2.id = 'edost_office_window_fon';
				E2.style.display = 'none';
				E2.onclick = new Function('', name + '.window("close")');
				E.appendChild(E2); */

				var E2 = document.createElement('DIV');
				E2.className = 'edost_office_window';
				E2.id = 'edost_office_window';
				E2.style.display = 'none';
				E2.innerHTML = '<div id="edost_office_window_head" class="edost_office_window_head"></div><div id="edost_office_window_map"></div>';
				E.appendChild(E2);
			}
		}

		// собственный balloon (для маленького экрана)
		var E = document.getElementById('edost_office_balloon');
		if (!E) {
			var E = document.body;

			var E2 = document.createElement('DIV');
			E2.className = 'edost_office_balloon';
			E2.id = 'edost_office_balloon';
			E2.style.display = 'none';
//			var c = 'onclick="' + name + '.balloon(\'close\');"';
//			E2.innerHTML = '<div class="edost_office_window_close" onclick="' + name + '.balloon(\'close\');"></div><div id="edost_office_balloon_data"></div>';
			E2.innerHTML = '<div id="edost_office_balloon_data"></div>'
				+ '<div style="margin: 30px auto 0 auto;">'
				+ '<div class="edost_button_info" style="display: inline; margin: 0; padding: 10px 20px;" onclick="' + name + '.balloon(\'close\');">закрыть</div>'
				+ '</div>';
			E.appendChild(E2);
		}

		// подробная информация (для маленького экрана)
		var E = document.getElementById('edost_office_info');
		if (!E) {
			var E = document.body;

			var E2 = document.createElement('DIV');
			E2.className = 'edost_office_info';
			E2.id = 'edost_office_info';
			E2.style.display = 'none';
//			var c = 'onclick="edost_office.info(\'close\');"';
//			E2.innerHTML = (edost_resize.device == '' ? '<div class="edost_office_window_close" ' + c + '></div>' : '') + '<div id="edost_office_info_data"></div>'
			E2.innerHTML = '<div id="edost_office_info_data"></div>'
				+ '<div style="margin: 5px auto 0 auto;">'
				+ '<div class="edost_button_info" style="display: inline; margin: 0 30px 0 0; background: #888; font-size: 12px;" onclick="edost_office.info(\'blank\');">страница для печати</div>'
				+ '<div class="edost_button_info" style="display: inline; margin: 0; padding: 10px 20px;" onclick="edost_office.info(\'close\');">закрыть</div>'
				+ '</div>';

			E.appendChild(E2);
		}

		var display = (param != 'show' ? 'none' : 'block');

		var office_data = document.getElementById('edost_office_data');
		if (!office_data) return;

		var E = document.getElementById(self.inside ? 'edost_office_inside' : 'edost_office_window');
		if (!E) return;
		E.style.display = display;

		if (!self.inside) {
			var E = document.getElementById('edost_office_window_fon');
			if (E) E.style.display = display;
		}

		if (param == 'close' && onclose != '') {
			var s = onclose;
			onclose = '';
			edost_SetOffice_c(s);
		}
		if (param != 'show') return;

		self.fit('ico');

		// подготовка данных при первом запуске
		if (self.map && (office_data.value != 'parsed' || !self.data_parsed || office_format != format)) {
			if (office_data.value != 'parsed') {
				self.data_string = office_data.value;
				office_data.value = 'parsed';
				edost_office.data_parsed = edost_office2.data_parsed = false;
			}
			else if (!self.data_parsed) self.data_string = (name == 'edost_office' ? edost_office2.data_string : edost_office.data_string);

			var v = (window.JSON && window.JSON.parse ? JSON.parse(self.data_string) : eval('(' + self.data_string + ')'));
			//console.log(v);
			self.data = [];
			self.data_parsed = true;
			var tariff = [];
			var point = v.point;
			var ico_path = v.ico_path + (v.ico_path.substr(-1) != '/' ? '/' : '');
			var cod_tariff = false;

			// распаковка и поиск активных тарифов (format: 'shop' - самовывоз из магазина,  'office' - пункты выдачи,  'terminal' - терминалы ТК)
			for (var i = 0; i < v.tariff.length; i++) {
//				alert(v.tariff[i]);

				var ar = v.tariff[i].split('|');
				//console.log(format);
				if (ar[13] == undefined) continue;
				var p = {
					"profile": ar[0], "company": ar[1], "name": ar[2], "tariff_id": ar[3], "price": ar[4], "price_formatted": ar[5], "pricecash": ar[6],
					"codplus": ar[7], "codplus_formatted": ar[8], "day": ar[9], "insurance": ar[10], "to_office": ar[11], "company_id": ar[12], "format": ar[13],
					"cod_tariff": (ar[14] != undefined ? ar[14] : '')
				};
				if (p.format == format || format == 'all' || format == 'profile_' + p.profile) tariff.push(p);
			}
//			edost_ShowData(tariff, '', 20);

			// распаковка офисов
			for (var i = 0; i < point.length; i++) {
				var p = [];
				for (var i2 = 0; i2 < point[i].data.length; i2++) {
					var ar = point[i].data[i2].split('|');
					if (ar[7] == undefined) continue;
					var v = {
						"id": ar[0], "name": ar[1], "address": ar[2], "schedule": ar[3].replace(/,/g, '<br>'), "gps": ar[4].split(','), "type": ar[5], "metro": ar[6], "codmax": ar[7],
						"detailed": (ar[8] != undefined ? ar[8] : false)
					};
					p.push(v);
				}
				point[i].data = p;
			}
//			edost_ShowData(point, '', 20);

			// разделение тарифов по группам (по службам доставки и эксклюзивным ценам)
			var office = [];
			for (var i = 0; i < tariff.length; i++) {
				var v = tariff[i];

				var u = -1;
				for (var i2 = 0; i2 < office.length; i2++) if (v.company_id == office[i2].company_id && v.to_office == office[i2].to_office) {
					u = i2;
					break;
				}

				if (u == -1) {
					var r = {"company": v.company, "company_id": v.company_id, "ico": v.tariff_id, "to_office": v.to_office, "format": v.format, "point": [], "button": "", "button2": "", "button2_info": "", "button_cod": "", "button_cod2": "", "cod": true, "header2": "", "header2_min": ""};
					r.header = '<span class="edost_name">' + v.company + '</span>';
					u = office.length;
					office[u] = r;
				}

				if (v.codplus == '') office[u].cod = false;
				else if (office[u].codplus_max == undefined || v.codplus*1 > office[u].codplus_max[0]*1) office[u].codplus_max = [v.codplus, v.codplus_formatted];

				if (office[u].price_min == undefined || v.price*1 < office[u].price_min[0]*1) office[u].price_min = [v.price, v.price_formatted];
				if (office[u].price_max == undefined || v.price*1 > office[u].price_max[0]*1) office[u].price_max = [v.price, v.price_formatted];
				if (v.pricecash !== '' && (office[u].pricecash_max == undefined || v.pricecash*1 > office[u].pricecash_max*1)) office[u].pricecash_max = v.pricecash;

				if (v.cod_tariff != '') cod_tariff = true;

				var price = (v.price_formatted == 0 ? '<span class="edost_price_free">' + free_ico + '</span>' : '<span class="edost_price">' + v.price_formatted + '</span>');

				var s = [];
				if (v.day != '') s.push('<span class="edost_day">' + v.day + '</span>');
				if (v.name != '') s.push('<span class="edost_tariff">' + v.name + '</span>');
				if (v.insurance == 1 && v.cod_tariff != 'Y') s.push('<span class="edost_insurance">со страховкой</span>');
				var s2 = '<span>' + price + '</span>' + (s.length > 0 ? '<br>' + s.join(', ') + '' : '');
				s = price + (s.length > 0 ? ' (' + s.join(', ') + ')' : '');

				if (v.cod_tariff != 'Y') {
					office[u].header2 += '<br>' + s;
					if (office[u].price_min[0] == v.price) office[u].header2_min = '<br>' + price + '<br>' + '<span class="edost_day">' + v.day + '</span>';
				}

				if (v.cod_tariff != '') {
					var c = '<br><div class="edost_payment_map"><span class="edost_payment_' + (v.cod_tariff == 'N' ? 'normal' : 'cod') + '">' + (v.cod_tariff == 'N' ? 'с предоплатой заказа' : 'с оплатой при получении') + '</span></div>';
					s += c;
					s2 += c;
				}

				var button, c;
				if(v.format == 'shop'){
					button = '';
				} else {
					c = 'edost_SetOffice_c(\'' + v.profile + '\', \'%office%\', \'' + v.cod_tariff + '\', \'' + v.format + '\')';
					button = '<div class="edost_button" onclick="' + c + '">'
						+ '<table align="center" cellpadding="0" cellspacing="0" border="0"><tr><td class="edost_button_left">' + s + '</td><td class="edost_button_right">выбрать</td></tr></table>'
						+ '</div>';
				}
				

				button2 = '<div class="edost_button2" onclick="' + name + '.balloon(\'close\'); ' + c + '">' + s2 + '</div>';

				office[u].button += button;
				office[u].button2 += button2;

				if (v.cod_tariff != 'Y') {
					office[u].button_cod += button;
					office[u].button_cod2 += button2;
				}

				office[u].button2_info = tariff_info;
			}
//			edost_ShowData(office, '', 20);

			// добавление копии группы тарифов для офисов без наложенного платежа
			var ar = [];
			for (var i = 0; i < office.length; i++) {
				var v = self.clone(office[i]);
				ar.push(v);

				if (office[i].cod) {
					var a = true;
					for (var i2 = 0; i2 < office.length; i2++) if (i != i2 && office[i].company_id == office[i2].company_id && office[i].button_cod == office[i2].button_cod && !office[i2].cod) { a = false; break; }
					if (a) {
						var v = self.clone(office[i]);
						v.cod = false;
						v.button = office[i].button_cod;
						v.button2 = office[i].button_cod2;
						ar.push(v);
					}
				}
			}
			office = ar;

			// прикрепление офисов к группам тарифов (сначала офисы с эксклюзивной ценой, потом - все остальные)
			for (var n = 0; n <= 1; n++)
				for (var i = 0; i < office.length; i++) if ((n == 0 && office[i].to_office != '') || (n == 1 && office[i].to_office == ''))
					for (var u = 0; u < point.length; u++) if (point[u].company_id == office[i].company_id)
						for (var u2 = 0; u2 < point[u].data.length; u2++) if (point[u].data[u2] != 'none') {
							if (n == 0 && point[u].data[u2].type != office[i].to_office) continue;

							var v = point[u].data[u2];
							v.cod = office[i].cod;
							if (v.cod && v.codmax !== '' && office[i].pricecash_max*1 > v.codmax*1) v.cod = false;

							var a = true;
							if (office[i].cod && !v.cod)
								for (var i2 = 0; i2 < office.length; i2++) if (i != i2 && office[i].company_id == office[i2].company_id && office[i].button_cod == office[i2].button_cod && !office[i2].cod) {
									office[i2].point.push(v);
									a = false;
									break;
								}

							if (a) office[i].point.push(v);
							point[u].data[u2] = 'none';
						}
//			edost_ShowData(office, '', 20);

			// подпись о возможности наложки в заголовке с группами тарифов
			if (!self.inside) {
				var n = 0;
				for (var i = 0; i < office.length; i++) if (office[i].cod && office[i].point.length > 0) n++;
				if (n != 0 && n != office.length)
					for (var i = 0; i < office.length; i++) if (office[i].cod) {
						office[i].header2 += '<div class="edost_payment">+ возможна оплата при получении</div>';
						office[i].header2_min += '<div class="edost_payment">+ возможна<br> оплата при<br> получении</div>';
					}
			}

			// заголовок с группами тарифов
			var n = office.length;
			var s = '';
			var price_min = -1;
			var count = 0;
			for (var i = 0; i < n; i++) if (office[i].point.length > 0) count++;
			if (self.inside && count >= 6) small_ico = true;
			for (var i = 0; i < n; i++) if (office[i].point.length > 0) {
				office[i].button += '<div class="edost_button_bottom"></div>';
				if (price_min == -1 || office[i].price_min[0]*1 < price_min*1) price_min = office[i].price_min[0];

				var v = office[i];

				var head = '';
				if (n == 1) {
					if (v.point.length == 1) {
						if (v.company_id == 26) head = 'Постамат / пункт выдачи';
						else if (v.company_id == 72) head = 'Почтомат';
						else if (v.format == 'terminal') head = 'Терминал ТК';
						else if (v.format == 'shop') head = 'Магазин';
						else head = 'Пункт выдачи';
					}
					else {
						if (v.company_id == 26) head = 'Постаматы и пункты выдачи';
						else if (v.company_id == 72) head = 'Почтоматы';
						else if (v.format == 'terminal') head = 'Терминалы ТК';
						else if (v.format == 'shop') head = 'Адреса магазинов';
						else head = 'Пункты выдачи';
					}
				}
				if (v.company_id.substr(0, 1) == 's' && (v.company.substr(0, 9) == 'Самовывоз' || v.format == 'shop')) {
					if (n != 1 && n <= 5)
						if (v.point.length == 1) head = (v.format == 'shop' ? 'Магазин' : 'Пункт выдачи');
						else head = (v.format == 'shop' ? 'Магазины' : 'Пункты выдачи');
					office[i].header = '';
				}
				if (head != '') head = '<span class="edost_name">' + head + ' </span>';

				s += '<td id="' + name + '_price_td_' + i + '" onclick="' + name + '.set_map(' + i + ');">'
				s += '<img class="edost_ico edost_ico_' + (small_ico ? 'small' : 'normal') + '" src="' + ico_path + office[i].ico + '.gif" border="0">' + head;
				if (n <= 5) s += office[i].header;
				if (!small_ico)
					if (n > 5) s += office[i].header2_min;
					else if (n > 1) s += office[i].header2;
				s += '</td>';

				if (n > 1) s += '<td width="8" class="edost_office_head_delimiter"></td><td width="8"></td>';
			}
			else {
				office.splice(i, 1); i--; n--; // удаление группы без пунктов выдачи
			}

			var E = document.getElementById('edost_office_' + (self.inside ? 'inside' : 'window') + '_head');
			if (self.inside) E.innerHTML = '';
			else E.innerHTML = '<table class="edost_office_head" cellpadding="0" cellspacing="0" border="0"><tr>'
				+ s + '<td width="120" class="edost_office_head_all"><div id="' + name + '_price_td_all" onclick="' + name + '.set_map(\'all\');">показать все</div></td>'
				+ '</tr></table>';

			// поиск одинаковых адресов у разных служб доставки (repeat_individual = true - у каждого офиса свой заголовок с временем работы)
			var n = -1;
			for (var i = 0; i < office.length-1; i++) for (var i2 = 0; i2 < office[i].point.length; i2++) if (office[i].point[i2].repeat == undefined) {
				var repeat_individual = false;
				for (var u = i; u < office.length; u++) {
					var start = (u == i ? i2+1 : 0);
					for (var u2 = start; u2 < office[u].point.length; u2++) if (office[u].point[u2].repeat == undefined && office[i].point[i2].address == office[u].point[u2].address) {
						if (office[i].point[i2].repeat == undefined) {
							n++;
							office[i].point[i2].repeat = n;
						}
						office[u].point[u2].repeat = n;

						if (office[i].point[i2].schedule != office[u].point[u2].schedule ||
							(office[i].point[i2].type == 5 || office[i].point[i2].type == 6) && office[i].point[i2].type != office[u].point[u2].type) repeat_individual = true;
					}
				}
				if (repeat_individual) {
					office[i].point[i2].repeat_individual = true;
					for (var u = i; u < office.length; u++) for (var u2 = 0; u2 < office[u].point.length; u2++)
						if (office[u].point[u2].repeat == n) office[u].point[u2].repeat_individual = true;
				}
			}

			// выделение жирным дешевой доставки
			var n = 0;
			for (var i = 0; i < office.length; i++)
				if (office[i].price_min[0]*1 > price_min*1 + 50*1) office[i].bold = false;
				else {
					office[i].bold = true;
					n++;
				}
			if (n == office.length) for (var i = 0; i < office.length; i++) office[i].bold = false;

			self.data = office;

			// удаление с карты старых меток
			if (self.map)
				if (api21) self.map.geoObjects.removeAll();
				else self.map.geoObjects.each(function(v) { self.map.geoObjects.remove(v); });

			// размещение меток на карте
			geo = new ymaps.Clusterer({preset: api21 ? 'islands#invertedDarkBlueClusterIcons' : 'twirl#invertedBlueClusterIcons', groupByCoordinates: false, clusterDisableClickZoom: false, zoomMargin: 100}); // maxZoom: 10

			var repeat = [];
			for (var i = 0; i < office.length; i++) {
				var v = office[i];
				var point = [], point2 = [];
				for (var i2 = 0; i2 < v.point.length; i2++) {
					var head = 'Пункт выдачи';
					var hint = '';
					var ico_map = v.company_id;
					var ico = '<img class="edost_ico edost_ico_small rbs-fix-ico-edost" src="' + ico_path + 'small/' + v.ico + '.gif" border="0">';

					var detailed = '';
					//if (v.point[i2].detailed != 'N') detailed = '<span class="edost_link" onclick="' + name + '.info(\'' + v.point[i2].id + '\'' + (v.point[i2].detailed ? ', \'' + v.point[i2].detailed + '\'' : '') + ')">подробнее...</span>';

					var c = (v.point[i2].cod && !cod_tariff ? 'возможна оплата за заказ при получении' : '') + (v.point[i2].cod && !cod_tariff && v.codplus_max != undefined && v.codplus_max[1] != 0 ? '%codplus%' : '');
					var payment = (v.codplus_max ? c.replace(/%codplus%/g, ' (+ ' + v.codplus_max[1] + ')') : '');
					var payment2 = (v.codplus_max ? c.replace(/%codplus%/g, '<br>+ ' + v.codplus_max[1]) : '');

					if (v.company_id.substr(0, 1) == 's') {
						ico_map = 0;
						if (v.company.substr(0, 9) == 'Самовывоз' || v.format == 'shop') v.company = '';
					}

					if (v.format == 'shop') head = 'Магазин';
					if (v.format == 'terminal') head = 'Терминал ТК';
					if (v.company_id == 26 && (v.point[i2].type == 5 || v.point[i2].type == 6)) {
						head = 'Постамат';
						ico_map += '-2';
						hint = '&nbsp;<a href="' + protocol + 'pickpoint.ru/faq/?category=5" target="_blank"><img class="edost_ico edost_hint" src="' + protocol + 'edostimg.ru/img/hint/hint_grey.gif"></a>';
						if (payment != '') {
							payment += '<br>наличными или банковской картой Visa и MasterCard';
							payment2 += '<br>наличными или банковской картой Visa и MasterCard';
						}
			        }
					if (v.company_id == 72 && (v.point[i2].type == 5 || v.point[i2].type == 6)) {
						head = 'Почтомат';
						ico_map += '-2';
						hint = '&nbsp;<a href="' + protocol + 'inpost.ru/ru/customers/kak-poluchit-zakaz" target="_blank"><img class="edost_ico edost_hint" src="' + protocol + 'edostimg.ru/img/hint/hint_grey.gif"></a>';
			        }

					if (payment != '') {
						payment = '<div class="edost_payment edost_balloon_payment">' + payment + '</div>';
						payment2 = '<div class="edost_payment edost_balloon_payment">' + payment2 + '</div>';
					}

					var button = v.button.replace(/%office%/g, v.point[i2].id);
					var button2 = v.button2.replace(/%office%/g, v.point[i2].id);
					var button2_info = v.button2_info.replace(/%office%/g, v.point[i2].id);

					var ico_price = (v.price_min[1] == 0 ? free_ico : v.price_min[1]);
					if (v.bold) ico_price = '<b>' + ico_price + '</b>';

					var font_size = (v.company.length >= 11 ? ' style="font-size: 13px;"' : '');

					var icoHref = protocol + 'edostimg.ru/img/companymap/' + ico_map + '.png';
					
					var additionalClass = '';
					if(v.format == 'shop'){
						icoHref = '/local/templates/romza_bitronic2_2.23.10/components/bitrix/sale.order.ajax/sib_order_new/images/sibico.jpg';
						additionalClass = 'rbs-root-point';
						ico = '<img class="rbs-ico-shop rbs-fix-ico-edost" src="' + icoHref + '" border="0">';
						//BX.Sale.OrderAjaxComponent.cityDeliveryName;
					}
					
					var descriptionAll = '<div class="rbs-edost-fix">' + ico + 'г. ' + BX.Sale.OrderAjaxComponent.cityDeliveryName + '<br>' + v.point[i2].address + '</div>';

					var icon = {
						iconImageHref: icoHref,
						iconImageSize: [36, 36],
						iconImageOffset: [-12, -36]
					};
					if (api21) icon.iconLayout = 'default#image';

					var s = '<span class="edost_name"' + font_size + '>' + head + ' ' + v.company + '</span>' + hint + '<br>'
						+ (v.point[i2].name != '' ? v.point[i2].name + '<br>' : '')
						+ descriptionAll
						+ ' ' + detailed;
					var placemark = new ymaps.Placemark([v.point[i2].gps[1], v.point[i2].gps[0]], {
						balloonContent: '<div class="edost_balloon">'
							+ '<div class="edost_balloon_schedule"' + font_size + '>' + v.point[i2].schedule + '</div>'
                            + s + payment + button
							+ '</div>',
						iconContent: '<div class="edost_ico_price ' + additionalClass + ' rbs-point-'+v.point[i2].id+'">' + ico_price + '</div>'
					}, icon);
					placemark.properties.set('balloonContent2', s + '<div class="edost_balloon_schedule2">' + v.point[i2].schedule + '</div>' + payment2 + button2 + button2_info);
					//placemark.events.add('balloonopen', function (e) { self.balloon(e) });

					if (v.point[i2].repeat == undefined) point.push(placemark);
					else {
						point2.push(placemark);

						// отдельная группа меток для офисов с одинаковыми адресами всех служб доставки
						var u = v.point[i2].repeat;
						if (repeat[u] == undefined) {
							s = (v.point[i2].name != '' ? v.point[i2].name + '<br>' : '') + v.point[i2].address;
							var s2 = s;
							if (!v.point[i2].repeat_individual) {
								s += ' ' + detailed;
								s2 = '<span class="edost_name">' + head + '</span><br>' + s + '<div class="edost_balloon_schedule2">' + v.point[i2].schedule + '</div>';
								s = '<div class="edost_balloon_schedule">' + v.point[i2].schedule + '</div><span class="edost_name">' + head + '</span><br>' + s;
							}
							repeat[u] = {"info": s, "info2": s2, "button": "", "button2": "", "point": v.point[i2], "price_min": v.price_min, "bold": v.bold};;
						}

						if (v.bold) repeat[u].bold = v.bold;
						if (v.price_min[0]*1 < repeat[u].price_min[0]*1) repeat[u].price_min = v.price_min;

						var s = '<div class="edost_balloon_delimiter"></div>';
						var s2 = '<div class="edost_balloon_delimiter2"></div>';
						if (v.point[i2].repeat_individual) {
							s2 += ico + '<span class="edost_name"' + font_size + '>' + head + ' ' + v.company + '</span>' + hint + '<br>'
								+ (detailed != '' ? '<div class="edost_balloon_detailed">' + detailed + '</div>' : '')
								+ '<div class="edost_balloon_schedule2">' + v.point[i2].schedule + '</div>'
								+ payment2;
							s += '<div class="edost_balloon_schedule edost_balloon_schedule_individual"' + font_size + '>' + v.point[i2].schedule + '</div>'
								+ ico + '<span class="edost_name"' + font_size + '>' + head + ' ' + v.company + '</span>' + hint + '<br>'
								+ (detailed != '' ? '<div class="edost_balloon_detailed">' + detailed + '</div>' : '')
								+ payment;
						}
						else {
							var c = '<span class="edost_day">служба доставки </span>' + ico + '<span class="edost_name">' + v.company + '</span>' + hint + '<br>';
							s += '<div class="edost_balloon_individual">' + c + payment + '</div>';
							s2 += '<div>' + c + payment2 + '</div>';
						}
						repeat[u].button += s + button;
						repeat[u].button2 += s2 + button2;
					}
				}

				self.data[i].geo = new ymaps.Clusterer({preset: api21 ? 'islands#invertedDarkBlueClusterIcons' : 'twirl#invertedBlueClusterIcons', groupByCoordinates: false, clusterDisableClickZoom: false, zoomMargin: 100});
				self.data[i].geo.add(point);
				self.data[i].geo.add(point2);
				geo.add(point);
			}

			// размещение на карте меток для офисов с одинаковыми адресами всех служб доставки
			var point = [];
			for (var i = 0; i < repeat.length; i++) if (repeat[i] != undefined) {
				var v = repeat[i];

				var ico_price = (v.price_min[1] == 0 ? free_ico : v.price_min[1]);
				if (v.bold) ico_price = '<b>' + ico_price + '</b>';

				var icon = {
					iconImageHref: protocol + 'edostimg.ru/img/companymap/0.png',
					iconImageSize: [36, 36],
					iconImageOffset: [-12, -36]
				};
				if (api21) icon.iconLayout = 'default#image';

				var placemark = new ymaps.Placemark([v.point.gps[1], v.point.gps[0]], {
					balloonContent: '<div class="edost_balloon">' + v.info + v.button + '</div>',
					iconContent: '<div class="edost_ico_price">' + ico_price + '</div>'
				}, icon);
				placemark.properties.set('balloonContent2', v.info2 + v.button2 + tariff_info);
				placemark.events.add('balloonopen', function (e) { self.balloon(e) });

				point.push(placemark);
			}
			geo.add(point);

			self.set_map('all');
		}

        
		self.fit();
		if (self.timer_resize != undefined) window.clearInterval(self.timer_resize);
		self.timer_resize = window.setInterval(name + '.fit("resize")', 400);


		// карта
		if (self.map){
            //self.map.container.fitToViewport();
        } else {
			// подключение карты
			var E = document.getElementById('edost_office_' + (self.inside ? 'inside' : 'window') + '_map');
			if (E) {
				var s = loading;
				if (self.inside)
					if (window.edost_catalogdelivery && edost_catalogdelivery.loading != '') s = edost_catalogdelivery.loading;
					else if (self.loading_inside != '') s = self.loading_inside;

				E.innerHTML = s;
				self.add_map();
			}
		}

	}


	// установка размера окна
	this.fit = function(param) {
        
		var E = document.getElementById(self.inside ? 'edost_office_inside' : 'edost_office_window');
		if (!E || E.style.display == 'none') return;

		// размер окна браузера
		var minWidth = 900;
		if(window.innerWidth < 1490 && window.innerWidth > 768){
			minWidth = minWidth - (1490 - window.innerWidth);
		} else if(window.innerWidth <= 768){
			minWidth = window.innerWidth - 65;
		}

		if(window.edostActiveId > 0){
			var curPoint = $('.rbs-point-' + window.edostActiveId);
			if(curPoint.length){
				curPoint.closest('.ymaps-image-with-content').css({
					width: '50px',
					height: '50px',
					backgroundRepeat: 'no-repeat',
					backgroundSize: 'cover'
				});
				curPoint.addClass('rbs-active-point');
			}
		}

		if(!$('.rbs-edost-map').hasClass('rbs-root-city') && $('.rbs-root-point').length){
			$('.rbs-root-point').closest('.rbs-edost-map').addClass('rbs-root-city');
		}

		var browser_w = minWidth;
		var browser_h = 600;
		small_ico = (browser_w < 1100 || browser_h < 600 ? true : false);
		small_ico = false;

		if (param === 'ico') return;

		if (param == 'resize' && (browser_width != 0 && Math.abs(browser_width - browser_w) > 100 || browser_height != 0 && Math.abs(browser_height - browser_h) > 100)) {
			format = '';
			if (self.timer_resize != undefined) window.clearInterval(self.timer_resize);
			self.window(param_start, onclose_set_office_start);
			return;
		}

		if (param == 'resize' && Math.abs(browser_width - browser_w) < 20 && Math.abs(browser_height - browser_h) < 20) {
			update_number++;
			if (update_number > 2) return;
		}
		else update_number = 0;

		browser_width = browser_w;
		browser_height = browser_h;

		var E2 = document.getElementById('edost_office_' + (self.inside ? 'inside' : 'window') + '_head');
		var E3 = document.getElementById('edost_office_' + (self.inside ? 'inside' : 'window') + '_map');
		if (!E2 || !E3) return;

		/* if (!self.inside) document.body.style.overflow = (small_ico ? 'hidden' : overflow_backup); */

		var window_w = E.offsetWidth;
		var window_h = E.offsetHeight;
		var head_h = E2.offsetHeight;
		var max_w = browser_w - (small_ico ? 4 : 100);

		window_w = max_w;
		if (!small_ico && window_w > 1200) {
			window_w = (browser_h > 960 ? Math.round(browser_h*1.25) : 1200);
			if (window_w > max_w) window_w = max_w;
		}

		if (!self.inside) {
			window_h = browser_h - (small_ico ? 6 : 100);
			E.style.width = browser_w + 'px';
			E.style.height = window_h + 'px';
			/* E.style.left = (small_ico ? '-3' : Math.round((browser_w - window_w)*0.5)) + 'px';
			E.style.top = (small_ico ? '-1' : Math.round((browser_h - window_h)*0.5)) + 'px'; */
			E3.style.width = '100%';
		}

		if (window_h == 0) return;

		E3.style.height = window_h - head_h - 2 + 'px';

		if (self.map) {
			//self.map.container.fitToViewport();
        }

	}


	this.set_map = function(n) {
		
		if (self.data == undefined) return;
		if (self.data.length == 1) n = 'all';

		if (api21) self.map.geoObjects.removeAll();

		var point_count = 0;
		for (var i = 0; i < self.data.length; i++) {
			var show = (n == 'all' || i == n ? true : false);

			var E = document.getElementById(name + '_price_td_' + i);
			if (E) E.className = 'edost_active_' + (show ? 'on' : 'off');

			if (!show) self.map.geoObjects.remove(self.data[i].geo);
			else {
				point_count += self.data[i].point.length;
				if (n == 'all') self.map.geoObjects.remove(self.data[i].geo);
				else {
					self.map.geoObjects.add(self.data[i].geo);

					var p = self.data[i].geo.getBounds();
					if (point_count == 1) self.map.setCenter(p[0]);
					else self.map.setBounds(p, {checkZoomRange: false});
				}
			}
		}

		if (n != 'all') self.map.geoObjects.remove(geo);
		else {
			self.map.geoObjects.add(geo);
			var p = geo.getBounds();

			if (point_count == 1) self.map.setCenter(p[0]);
			else self.map.setBounds(p, {checkZoomRange: false});
		}
		var E = document.getElementById(name + '_price_td_all');
		if (E) E.style.display = (n == 'all' || self.data.length == 1 ? 'none' : 'block');

		var currentPoint = '';
		if(window.edostActiveId > 0){
			var deliveryThis = BX.Sale.OrderAjaxComponent.getSelectedDelivery();
			if(deliveryThis && deliveryThis.isEdostItem){
				if(deliveryThis.office_data[window.edostActiveId]){
					currentPoint = deliveryThis.office_data[window.edostActiveId].gps;
					currentPoint = currentPoint.split(',');
				}
			}
		}
		
		if(currentPoint){
			self.map.setCenter([currentPoint[1], currentPoint[0]]);
			point_count = 1;
		}

		if (point_count == 1) self.map.setZoom(16);
		else {
			var z = self.map.getZoom();
			if (z == 0) z = 16;
			self.map.setZoom(z - 1);
		}

	}


	this.create_map = function() {

		if (self.map) return;
		var E = document.getElementById('edost_office_' + (self.inside ? 'inside' : 'window') + '_map');
		if (!E) return;

		E.innerHTML = '';
		E.className = 'edost_map';

		api21 = (window.ymaps && window.ymaps.control && window.ymaps.control.FullscreenControl ? true : false);

		var v = {center: [0, 0], zoom: 12, type: 'yandex#map', behaviors: ['default', 'scrollZoom']};
		if (api21) v.controls = ['zoomControl'];

		self.map = new ymaps.Map('edost_office_' + (self.inside ? 'inside' : 'window') + '_map', v);
		if (name == 'edost_office') edost_map = self.map; // поддержка старых функций

		if (!api21) {
			self.map.controls
				.add('zoomControl', { left: 5, top: 5 })
				//.add('typeSelector')
				//.add('mapTools', { left: 35, top: 5 });
		}
		if(self.isMobile){
			self.map.behaviors.disable('scrollZoom');
			self.map.behaviors.disable('drag');
		}
		//self.map.behaviors.disable('drag');

		map_loading = false;

		if (self.inside) {
			var E = document.getElementById('edost_office_detailed');
			if (E) E.style.display = 'block';
		}

		self.window('show', '');

	}


	this.add_map = function() {

		if (map_loading) return;

		map_loading = true;

		if (!window.ymaps) {
			var E = document.body;
			var E2 = document.createElement('SCRIPT');
			E2.type = 'text/javascript';
			E2.charset = 'utf-8';
			E2.src = protocol + 'api-maps.yandex.ru/' + (edost_resize.os == 'android' ? '2.1.50' : '2.0-stable') + '/?load=package.standard,package.clusters&apikey=5683d596-fa7a-45d6-96bf-fdc4f95c1e67&lang=ru-RU';
			E.appendChild(E2);
		}

		if (window.ymaps) ymaps.ready(self.create_map);
		else {
			if (self.timer != undefined) window.clearInterval(self.timer);
			self.timer = window.setInterval('if (window.ymaps) { window.clearInterval(' + name + '.timer); ymaps.ready(' + name + '.create_map); }', 500);
		}

	}


	// собственный balloon (для маленького экрана)
	this.balloon = function(param) {

		if (param === 'close') {
			if (edost_office_c.map && edost_office_c.map.balloon.isOpen()) edost_office_c.map.balloon.close();
			if (edost_office2_c.map && edost_office2_c.map.balloon.isOpen()) edost_office2_c.map.balloon.close();
		}

		var E = document.getElementById('edost_office_balloon');
		if (!E) return;

		if (param === 'close') {
			E.style.display = 'none';
			return;
		}

		if (browser_width > 500 && browser_height > 400 && edost_resize.device != 'phone') return;

		E.style.display = 'block';

		var s = param.get('target')['properties'].get('balloonContent2');
		var E = document.getElementById('edost_office_balloon_data');
		E.innerHTML = s;

	}


	// подробная информация (для маленького экрана)
	this.info = function(param, link) {

		var E = document.getElementById('edost_office_info');
		if (!E) return;

		if (param === 'close') {
			E.style.display = 'none';
			return;
		}

		if (link == undefined || link == '') link = protocol + 'edost.ru/office.php?c=' + param;
		else link = link.replace(/%id%/g, param);

		var blank = (param === 'blank' ? true : false);
		if (blank) {
			var E2 = document.getElementById('edost_office_info_iframe');
			if (!E2) return;
			link = E2.src.replace(/map=N/g, 'print=Y');
			self.info('close');
		}

		if (edost_resize.device == '' && browser_width > 500 && browser_height > 400 || blank) window.open(link, '_blank');
		else {
			E.style.display = 'block';

			var E = document.getElementById('edost_office_info_data');
			E.innerHTML = '<iframe id="edost_office_info_iframe" name="edost_office_info_iframe" src="' + link + (link.indexOf('?') === -1 ? '?' : '&') + 'window=N&map=N" frameborder="0" width="100%" height="' + (edost_resize.browser_height - 80) + '"></iframe>';
		}

	}

}

var edost_office_c = new edost_office_create_c('edost_office_c');
var edostActiveId = BX.getCookie('edostActiveId')?BX.getCookie('edostActiveId'):0;

function edost_SetOffice_c(profile, id, cod, mode) {
    /* console.log([profile, id, cod, mode]); */
    var E = document.getElementById("edost_office");
    if (E) E.value = id;
    
    if(id){
        window.edostActiveId = id;
        BX.setCookie('edostActiveId', id, {expires: 86400});
    }

    if (edost_office_c.map) {
        edost_office_c.map.balloon.close();
    }
}
