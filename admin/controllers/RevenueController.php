<?php

namespace admin\controllers;

use admin\models\Dashboard;
use MongoDate;

class RevenueController extends \lithium\action\Controller {

	// Show the revenue data by day for the selected time period
	public function daily() {
		
		if ($this->request->data) {
			$start_date = $this->request->data['start_date'];
			$end_date = $this->request->data['end_date'];
		} elseif ($this->request->query) {
			$start_date = $this->request->query['start_date'];
			$end_date = $this->request->query['end_date'];
		} else {
			$current_month = date("m");
			$current_day = date("d");
			$current_year = date("y");
			
			$start_date = $current_month . '/01/' . $current_year;
			$end_date = $current_month . '/' . $current_day . '/' . $current_year;
			
		}
		
		$net_revenue = $this->findNetDetailData($start_date, $end_date);
		$gross_revenue = $this->findGrossDetailData($start_date, $end_date);
		
		return compact(
			'net_revenue',
			'gross_revenue',
			'start_date',
			'end_date'
		);
		
	}
	
	// Show revenue data for the last 6 months, summed by month
	public function monthly() {
		$month = strtotime("6 months ago");
		$month_string = date("Y-m",strtotime(date("m",$month).'/01/'.date("y",$month)));
		
		$net_revenue = array();
		$gross_revenue = array();
		
		for($i=0;$i<6;$i++) {
			$net_revenue[] = $this->findNetMonthData($month_string);
			$gross_revenue[] = $this->findGrossMonthData($month_string);
			
			$month = strtotime("next month",$month);
			$month_string = date("Y-m",strtotime(date("m",$month).'/01/'.date("y",$month)));
		}
		
		return compact(
			'net_revenue',
			'gross_revenue'
		);
	}

	public function promocodes() {
		$month = strtotime("6 months ago");
		$month_string = date("Y-m",strtotime(date("m",$month).'/01/'.date("y",$month)));
		
		$promocodes = array();
		
		for($i=0;$i<6;$i++) {
			$promocodes[$month_string] = $this->findPromocodeData($month_string);
			
			$month = strtotime("next month",$month);
			$month_string = date("Y-m",strtotime(date("m",$month).'/01/'.date("y",$month)));
		}
		
		return compact(
			'promocodes'
		);
	}
	
	private function findNetDetailData($start_date, $end_date) {
		$conditions = array(
			'type' => 'revenue',
			'date' => array(
				'$gte' => new MongoDate(strtotime($start_date)),
				'$lte' => new MongoDate(strtotime($end_date))
		));
		
		$net_revenue = Dashboard::find('all', compact('conditions'));
		$net_revenue = $net_revenue->data();
		
		$net_total = array();
		$net_total['date_string'] = 'Total';
		$net_total['total'] = 0;
		$net_total['product'] = 0;
		$net_total['handling_total'] = 0;
		$net_total['fs_service'] = 0;
		$net_total['fs_promo'] = 0;
		$net_total['tax'] = 0;
		$net_total['promo_discount'] = 0;
		$net_total['discount'] = 0;
		$net_total['credit_used'] = 0;
		
		foreach($net_revenue as $net_day) {
			$net_total['total'] += $net_day['total'];
			$net_total['product'] += $net_day['product'];
			$net_total['handling_total'] += $net_day['handling_total'];
			$net_total['fs_service'] += $net_day['fs_service'];
			$net_total['fs_promo'] += $net_day['fs_promo'];
			$net_total['tax'] += $net_day['tax'];
			$net_total['promo_discount'] += $net_day['promo_discount'];
			$net_total['discount'] += $net_day['discount'];
			$net_total['credit_used'] += $net_day['credit_used'];
		}
		
		$net_revenue[] = $net_total;

		return $net_revenue;
	}

	private function findGrossDetailData($start_date, $end_date) {
		$conditions = array(
			'type' => 'gross',
			'date' => array(
				'$gte' => new MongoDate(strtotime($start_date)),
				'$lte' => new MongoDate(strtotime($end_date))
		));
		
		$gross_revenue = Dashboard::find('all', compact('conditions'));
		$gross_revenue = $gross_revenue->data();
		
		$gross_total = array();
		$gross_total['date_string'] = 'Total';
		$gross_total['total'] = 0;
		$gross_total['subTotal'] = 0;
		$gross_total['handling_total'] = 0;
		$gross_total['tax'] = 0;
		
		foreach($gross_revenue as $gross_day) {
			$gross_total['total'] += $gross_day['total'];
			$gross_total['subTotal'] += $gross_day['subTotal'];
			$gross_total['handling_total'] += $gross_day['handling_total'];
			$gross_total['tax'] += $gross_day['tax'];
		}

		$gross_revenue[] = $gross_total;
		
		return $gross_revenue;
	}
	
	// $month = 'YYYY-MM'
	private function findNetMonthData($month) {
		$conditions = array(
			'type' => 'revenue',
			'date_string' => array(
				'like' => "/^$month/"
		));
		
		$net_revenue = Dashboard::find('all', compact('conditions'));
		$net_revenue = $net_revenue->data();
		
		$net_total = array();
		$net_total['month'] = $month;
		$net_total['total'] = 0;
		$net_total['product'] = 0;
		$net_total['handling_total'] = 0;
		$net_total['fs_service'] = 0;
		$net_total['fs_promo'] = 0;
		$net_total['tax'] = 0;
		$net_total['promo_discount'] = 0;
		$net_total['discount'] = 0;
		$net_total['credit_used'] = 0;
		
		foreach($net_revenue as $net_day) {
			$net_total['total'] += $net_day['total'];
			$net_total['product'] += $net_day['product'];
			$net_total['handling_total'] += $net_day['handling_total'];
			$net_total['fs_service'] += $net_day['fs_service'];
			$net_total['fs_promo'] += $net_day['fs_promo'];
			$net_total['tax'] += $net_day['tax'];
			$net_total['promo_discount'] += $net_day['promo_discount'];
			$net_total['discount'] += $net_day['discount'];
			$net_total['credit_used'] += $net_day['credit_used'];
		}

		return $net_total;
	}

	// $month = 'YYYY-MM'
	private function findGrossMonthData($month) {
		$conditions = array(
			'type' => 'gross',
			'date_string' => array(
				'like' => "/^$month/"
		));
		
		$gross_revenue = Dashboard::find('all', compact('conditions'));
		$gross_revenue = $gross_revenue->data();
		
		$gross_total = array();
		$gross_total['month'] = $month;
		$gross_total['total'] = 0;
		$gross_total['subTotal'] = 0;
		$gross_total['handling_total'] = 0;
		$gross_total['tax'] = 0;
		
		foreach($gross_revenue as $gross_day) {
			$gross_total['total'] += $gross_day['total'];
			$gross_total['subTotal'] += $gross_day['subTotal'];
			$gross_total['handling_total'] += $gross_day['handling_total'];
			$gross_total['tax'] += $gross_day['tax'];
		}
		
		return $gross_total;
	}

	// $month = 'YYYY-MM'
	private function findPromocodeData($month) {
		$conditions = array(
			'type' => 'promocodes',
			'date_string' => array(
				'like' => "/^$month/"
		));
		
		$promocodes = Dashboard::find('all', compact('conditions'));
		$promocodes = $promocodes->data();
		
		$promocodes_total = array();
		$promocodes_total['Total'] = array();
		$promocodes_total['Total']['code'] = 'Total';
		
		foreach($promocodes as $promocode_day) {
			foreach ($promocode_day['codes'] as $promocode) {
				$code = $promocode['code'];
				
				if (!isset($promocodes_total[$code])) {
					$promocodes_total[$code] = array();
					$promocodes_total[$code]['code'] = $code;
					$promocodes_total[$code]['value'] = $promocode['code_value'];
					$promocodes_total[$code]['type'] = $promocode['code_type'];
				}
				
				// Total for this code
				$promocodes_total[$code]['amount_saved'] += $promocode['amount_saved'];
				$promocodes_total[$code]['net'] += $promocode['net_total'];
				$promocodes_total[$code]['gross'] += $promocode['gross_total'];
				$promocodes_total[$code]['number_used'] += $promocode['number_used'];
				
				// Total for the month
				$promocodes_total['Total']['amount_saved'] += $promocode['amount_saved'];
				$promocodes_total['Total']['net'] += $promocode['net_total'];
				$promocodes_total['Total']['gross'] += $promocode['gross_total'];
				$promocodes_total['Total']['number_used'] += $promocode['number_used'];
			}
		}
		
		asort($promocodes_total);
		
		return $promocodes_total;
	}

}

?>