<?php

namespace admin\models;

use MongoDate;
use MongoCode;

class Affiliate extends Base {

	protected $_meta = array('source' => 'affiliates');

	protected $_schema = array(
			'_id' => array('type' => 'id'),
			'invitation_codes'=>array('type'=>'array', 'null'=>false ),
			'affiliate'=> array('type'=>'boolean', 'null'=>false, 'default'=>true),
			'active'=>array('type'=>'boolean', 'null'=>false, 'default'=>true)
			);

	public static function pixelFormating($pixels, $codes, $category) {
		if ( empty($pixels) ){ return array(); }
		$formatted = array();
		foreach($pixels as $key=>$pixel){

		    if ($pixel['enable'] == '1' || $pixel['enable'] == 'on') {
		    	$temp['enable'] = true;
		    } else {
		    	$temp['enable'] = false;
		    }

		    if ($pixel['enable'] && array_key_exists('page', $pixel)) {
		    	if(!isset($category)) {
		    		if(in_array('/a/', $pixel['page'])){
		    			foreach ($codes as $value) {
		    				$pixel['page'][] = '/a/' . $value;
		    			}
		    		}
		    	} else {
		    		foreach ($codes as $value) {
		    			$pixel['page'][] = '/'.$category.'?a=' . $value;
		    		}
		    	}
		    }

		    $temp['page'] = array_values($pixel['page']);
		    $temp['pixel'] = $pixel['pixel'];
		    $formatted[] = $temp;
		}
		return $formatted;
	}

	public static function effectiveCoReg($name, $date, $affiliate) {
	    $key = array('logincounter' => 1,"invited_by" => 1);
	    $name = "";
         extract( static::generateConditions(compact('name','date','affiliate')),EXTR_OVERWRITE);
        $condition = $conditions;

        $initial = array("count" => 0, "purchase_count" => 0);
        $reduce = "function (rec, prev){
            if (rec.logincounter == prev.logincounter) {
                ++prev.count;
            }
            if (rec.purchase_count) {
                ++prev.purchase_count;
            }
        }";

        $userCol = User::collection();

        $results = $userCol->group($key,$initial,$reduce,compact('condition'));
        $results = $results['retval'];
        $coreg = array();
        $default = array('count' => 0, 'purchase_count' => 0);
        foreach ($results as $value) {
            if (!array_key_exists(1,$coreg[$value["invited_by"]])) {
                 $coreg[$value["invited_by"]][1] = $default;
            }
            if (!array_key_exists(2,$coreg[$value["invited_by"]])) {
                 $coreg[$value["invited_by"]][2] = $default;
            }
            if (!array_key_exists(3,$coreg[$value["invited_by"]])) {
                 $coreg[$value["invited_by"]][3] = $default;
            }
            if (!array_key_exists("4 or more",$coreg[$value["invited_by"]])) {
                 $coreg[$value["invited_by"]]["4 or more"] = $default;
            }
            if ($value['logincounter'] > 3) {
                $coreg[$value["invited_by"]]["4 or more"] = array(
                    'count' => $value['count'] + $coreg[$value["invited_by"]]["4 or more"]["count"],
                    'purchase_count' => $value['purchase_count']
                );
            } else {
                $coreg[$value["invited_by"]][$value['logincounter']] = array(
                    'count' => $value['count'],
                    'purchase_count' => $value['purchase_count']
                );
            }
        }
        return $coreg;
	}

	public static function registrationCount($name, $date, $affiliate) {
	    extract( static::generateConditions(compact('name','date','affiliate')),EXTR_OVERWRITE);
        $keys = new MongoCode("function(doc){
            return {
                'Date': doc.$dateField.getMonth(),
                'subaff':doc.invited_by
            }}");
        $inital = array('total' => 0, 'bounced'=>0);
        $reduce = new MongoCode('function(doc, prev){
            prev.total += 1;
            if (typeof(doc.email_engagement)!="undefined"){ prev.bounced++; }
        }');
        $collection = User::collection();
        $results = $collection->group($keys, $inital, $reduce, $conditions);
        $results = $results['retval'];

        $registrations = array();
        $registrations['total'] = $registrations['bounced'] = 0;
        foreach ($results as $result) {
            $date = date('F', mktime(0,0,0,($result['Date'] + 1)));
            $registrations[$date][$result['subaff']]['total'] = $result['total'];
            $registrations[$date][$result['subaff']]['bounced'] = $result['bounced'];
            $registrations['bounced'] += $result['bounced'];
            $registrations['total'] += $result['total'];
        }

        $registrations['bounced'] = number_format($registrations['bounced']);
        $registrations['total'] = number_format($registrations['total']);

        return $registrations;
	}

	public static function generateConditions(array $data = array()){a;
		extract($data);
		$conditions = array();
		$dateField = 'date_created';
		switch ($name) {
			case 'trendytogs':
				$conditions = array(
					'trendytogs_signup' => array('$exists' => true)
				);
				$dateField = 'date_created';
			break;
			case 'keyade':
				$conditions = array(
					'$or' => array(
							array(
								'keyade_referral_user_id' => array('$ne' => NULL )
							),
							array(
								'keyade_user_id' => array('$ne' => NULL )
							)
					)
				);
				$dateField = 'created_date';
				if (!empty($date)) {
					$conditions = $conditions + $date;
				}
			break;
			default:
				$conditions = array(
					'invited_by' => $affiliate,
				);
				$dateField = 'created_date';
				if (!empty($date)) {
					$conditions = $conditions + $date;
				}
			break;
		}
		return compact('conditions','dateField');
	}

	public static function revenueCount($name, $affiliate,$min, $max) {
        switch ($name) {
                case 'keyade':
                $conditions = array(
                    'purchase_count' => array('$gte' => 1),
                    '$or' => array(
                            array(
                                'keyade_referral_user_id' => array('$ne' => NULL )
                            ),
                            array(
                                'keyade_user_id' => array('$ne' => NULL )
                            )
                    )
                );
                break;
                default:
                    $conditions = array(
                            'invited_by' => $affiliate,
                            'purchase_count' => array('$gte' => 1)
                    );
                break;
            }
            $fields = array('_id' => true);
            $users = User::find('all', array(
                'conditions' => $conditions,
                'fields' => $fields
            ));
            $result_ids = $users->data();
            $ids = array();
            foreach($result_ids as $id) {
                $ids[] = $id['_id'];
            }
            $orders = Order::find('all', array(
                        'conditions' => array(
                            'user_id' => array('$in' => $ids),
                            'date_created' => array(
                                '$gte' => $min,
                                '$lte' => $max
                    )),
                    'fields' => array('date_created' => 1, 'total' => 1,'user_id' => 1)
                ));
            $orders = $orders->data();
            $reportId = substr(md5(uniqid(rand(),1)), 1, 15);
            $collection = Report::collection();
            foreach ($orders as $order) {
                $order['date_created'] = new MongoDate($order['date_created']['sec']);
                $order['subaff'] = User::lookupUserInvitedBy($order['user_id']);
                $collection->save(array('data' => $order, 'report_id' => $reportId));
            }

            $keys = new MongoCode("function(doc){
            return {
                'Date': doc.data.date_created.getMonth(),
                'subaff' : doc.data.subaff

            }}");
            $inital = array('total' => 0);
            $reduce = new MongoCode('function(doc, prev){
                prev.total += doc.data.total
                }'
            );
            $conditions = array('report_id' => $reportId);
            $results = $collection->group($keys, $inital, $reduce, $conditions);
            $results = $results['retval'];
            $revenue = array();
            $revenue['total'] = 0;
            foreach ($results as $result) {
                $date = date('F', mktime(0,0,0,($result['Date'] + 1)));
                $revenue[$date][$result['subaff']] = $result['total'];
                $revenue['total'] += $result['total'];
            }
            $revenue['total'] = round($revenue['total'], 2);
            $revenue['total'] = number_format($revenue['total'], 2);
            $revenue['total'] = "$".$revenue['total'];
            $collection->remove($conditions);

            return $revenue;
	}

	public static function bounceReport($name,$date,$affiliate) {
        extract( static::generateConditions(compact('name','date','affiliate')),EXTR_OVERWRITE);
        $conditions = $conditions + array('email_engagement'=>array('$exists'=>true));
        $cursor = User::collection()->
                            find($conditions)->
                            fields(array(
															'email'=>true,
															'email_engagement'=> true,
															'created_date' => true,
															'invited_by' => true
														));
        $total = $cursor->count();
        $bounces = array();

        foreach($cursor as $value) {
            $bounces[$value['email']]['bounce_type'] = $value['email_engagement']['type'];
            $bounces[$value['email']]['created_date'] = date('m/d/Y',$value['created_date']->sec);
            $bounces[$value['email']]['report_date'] = date('m/d/Y',$value['email_engagement']['date']->sec);
            $bounces[$value['email']]['delivery_date'] = date('m/d/Y',$value['email_engagement']['delivery']['status_time']->sec);
            $bounces[$value['email']]['delivery_message'] = $value['email_engagement']['delivery']['message'];
            $bounces[$value['email']]['invited_by'] = $value['invited_by'];
        }
        $bounces['total'] = count($bounces);
        return $bounces;
    }

	public static function landingPages() {
		$landing = array();
	}

	public static function retrieveBackgrounds() {
		//$data = File::find('all',array('conditions' => array('tag' => 'background'), 'fields' => array('_id' => 1)));
		$data = File::find('all',array('conditions' => array('_id' => 'background'), 'fields' => array('_id' => 1)));
		return $data->data();
	}
}

?>