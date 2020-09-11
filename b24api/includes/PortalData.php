<?php
/**
 * Класс для работы с данными портала
 */

class PortalData
{
	// Произвольный список
	public function getList($method, $sub_array='', $filter=[], $select=[], $order=["ID"=>"asc"], $limit=0) {
		$list = [];
		$resp = executeMethod($method, [
			"order" => $order,
			"filter" => $filter,
		], false);
		$count = $resp['total'];
		if ($count) {
			$req_list = [];
			$req_limit = (!$limit || $count < $limit) ? $count : $limit;
			$req_count = ceil($req_limit / 50);
			$batch_count = ceil($req_count / 50);
			$list_i = 0;
			$next = 0;
			for ($k=0; $k < $batch_count; $k++) {
				for ($i=0; $i < 50 && $next < $req_limit; $i++) {
					$req_list[$i] = $method . '?' . http_build_query([
						"order"  => $order,
						"filter" => $filter,
						"select" => $select,
						"start"  => $next,
					]);
					$next += 50;
				}
				$resp = executeMethod('batch', [
					"halt"  => false,
					"cmd" => $req_list,
				]);
				foreach ($resp['result'] as $step_list) {
					if ($sub_array) {
						$step_list = $step_list[$sub_array];
					}
					if (is_array($step_list)) {
						foreach ($step_list as $item) {
							if ($list_i < $req_limit) {
								$list[] = $item;
								$list_i ++;
							}
						}
					}
				}
			}
		}
		return $list;
	}

	// Создание сделки
	public function createDeal($track_num, $inp_data) {
		$fields = [
			"TITLE" => "Заказ " . $track_num,
            "TYPE_ID" => "GOODS",
            "STAGE_ID" => "NEW",
			PORTAL_ID_FIELD => $track_num,
			"COMPANY_ID" => PORTAL_COMPANY,
            "CONTACT_ID" => PORTAL_CONTACT,
        ];
		$params = [];
		$data = json_decode($inp_data, true);
		$fields['UF_CRM_1454907569'] = $data['consignee']['consignee_postcode'] . ', ' . $data['consignee']['consignee_province'] . ', ' . $data['consignee']['consignee_city'] . ', ' . $data['consignee']['consignee_street'];
		$fields['UF_CRM_1558085292'] = $data['consignee']['consignee_postcode'];
		$fields['UF_CRM_1557832573'] = $data['consignee']['consignee_name'];
		$fields['UF_CRM_1557832627'] = $data['consignee']['consignee_telephone'] ? ('+7' . $data['consignee']['consignee_telephone']) : '';
		$fields['UF_CRM_1559126530'] = $data['consignee']['consignee_mobile'] ? ('+7' . $data['consignee']['consignee_mobile']) : '';
		$fields['UF_CRM_1559218961'] = $data['order_weight'];
		$res = executeMethod('crm.deal.add', ['fields' => $fields, 'params' => $params], false);
		return $res;
	}

	// Получение стадии сделки
	public function getDealStage($track_num) {
		$stage = false;
		$filter = [
			PORTAL_ID_FIELD => $track_num,
		];
		$res = executeMethod('crm.deal.list', ['filter' => $filter]);
		if ($res) {
			$deal = $res[0];
			$stage = [
				'track_number' => $track_num,
				'code' => $deal['STAGE_ID'],
				'name' => '',
			];
			// Название стадии
			$stages = [];
			$stages_res = executeMethod('crm.status.list', [
				'filter' => [
					'ENTITY_ID' => 'DEAL_STAGE',
				]
			]);
			foreach ($stages_res as $item) {
				$stages[$item['STATUS_ID']] = $item['NAME'];
			}
			$stage['name'] = $stages[$deal['STAGE_ID']];
		}
		return $stage;
	}

	// Получение данных сделки
	public function getDeal($id) {
		$result = false;
		$res = executeMethod('crm.deal.get', ['id' => $id]);
		if ($res) {
			$result = $res;
		}
		return $result;
	}

	// Получение данных сделки
	public function updateDeal($id, $fields) {
		$result = false;
		$res = executeMethod('crm.deal.update', ['id' => $id, 'fields' => $fields]);
		if ($res) {
			$result = $res;
		}
		return $result;
	}

	// Список сделок для обновления
	public function getDealListForUpdate($period_s=60) {
		$filter = [
			'>DATE_MODIFY' => date('c', time() - $period_s),
		];
		$result = $this->getList('crm.deal.list', '', $filter, [], [], 50);
		return $result;
	}
}
