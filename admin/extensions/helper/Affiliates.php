<?php
namespace admin\extensions\helper;
use MongoDate;

class Affiliates extends \lithium\template\Helper {

	protected $_revenueHeading = array(
		'Month',
		'Total-Revenue'
	);

	protected $_registrationHeading = array(
		'Month',
		'Total-Registrations',
		'Total-Bounce'
	);

	protected $_effectiveHeading = array(
		'Code/Subaffiliates',
		'Never',
		'Login 1x',
		'Login 2x',
		'Login 3x',
		'Login 4x or More'
	);
	protected $_bounceHeading = array(
		'Email',
		'Bounce Type',
		'Created Date',
		'Report Date',
		'Delivery Date',
		'Delivery Message'
	);

	public function build($results = null, $options = array('type' => null)){
		switch ($options['type']) {
			case 'Revenue':
				$heading = $this->_revenueHeading;
				break;
			case 'Registrations':
				$heading = $this->_registrationHeading;
				break;
			case 'Effective':
				$heading = $this->_effectiveHeading;
				break;
			case 'Bounces':
				$heading = $this->_bounceHeading;
				if ((bool)$options['criteria']['show_subaffiliate'] == true ){
        	$heading = array_merge(
        			array_slice($this->_bounceHeading,0,1),
        			array('Invited By'),
        			array_slice($this->_bounceHeading,1)
        	);
				}
				break;
			default:
				break;
		}
        $html = '';
        $html .= '<table id="report" class="datatable" border="1">';
        $html .=  '<thead><tr><th>' . implode('</th><th>', $heading);
        $html .= '</tr></thead><tbody>';
		foreach ($results as $key => $value) {

		    if ($options['type'] == 'Effective') {
		        $html .= "<tr>";
		        $html .= "<td>" . $key ."</td>";
		        $html .= "<td>" . $value[NULL]['count'] ." (" . $value[NULL]['purchase_count'] . ")</td>";
		        $html .= "<td>" . $value[1]['count'] ." (" . $value[1]['purchase_count']. ")</td>";
		        $html .= "<td>" . $value[2]['count'] ." (" . $value[2]['purchase_count']. ")</td>";
		        $html .= "<td>" . $value[3]['count'] ." (" . $value[3]['purchase_count']. ")</td>";
		        $html .= "<td>" . $value["4 or more"]['count']  ." (" . $value[4]['purchase_count']. ")</td>";
		        $html .= "</tr>";
		    } else if ($options['type'] == 'Registrations') {
		         if (($key !== "total") && ($key !== "bounced")) {
                     $html .= "<tr>";
                     $html .= "<td colspan='3' style='background-color:#B8B8B8;font-weight:bold'>" . $key ."</td>";
                     $html .= "</tr>";

                     foreach($value as $code => $stats) {
                        $html .= "<tr>";
                        $html .= "<td>" . $code ."</td>";
                        $html .= "<td>" . $stats['total'] ."</td>";
                        $html .= "<td>" . $stats['bounced'] ."</td>";
                        $html .= "</tr>";
                     }
		         }
		    }else if ($options['type'] == 'Revenue') {
		        if (($key !== "total")) {
                     $html .= "<tr>";
                     $html .= "<td colspan='2' style='background-color:#B8B8B8;font-weight:bold'>" . $key ."</td>";
                     $html .= "</tr>";

                     foreach($value as $code => $total) {
                        $html .= "<tr>";
                        $html .= "<td>" . $code ."</td>";
                        $html .= "<td> $" . $total ."</td>";
                        $html .= "</tr>";
                     }
		         }
		    }else if ($options['type'] == 'Bounces') {
	    	
		        if (($key !== "total")) {
                        $html .= "<tr>";
                        $html .= "<td>" . $key ."</td>";
											if ((bool)$options['criteria']['show_subaffiliate'] == true ){
                        $html .= "<td> " . $value['invited_by'] ."</td>";	
                      }
                        $html .= "<td> " . $value['bounce_type'] ."</td>";
                        $html .= "<td> ".  $value['created_date']."</td>";
                        $html .= "<td> ".  $value['report_date']."</td>";
                        $html .= "<td> ".  $value['delivery_date']."</td>";
                        $html .= "<td> " . $value['delivery_message']."</td>";
                        $html .= "</tr>";
		         }
		    }

		}
		if($options['type'] == 'Registrations' || $options['type'] == 'Revenue') {
		    $html .= "<tr>";
		    $html .= "<td style='background-color:#D0D0D0;font-weight:bold'>Grand Total:</td>";
            $html .= "<td style='background-color:#D0D0D0;font-weight:bold'>" . $results['total'] ."</td>";
            if (array_key_exists('bounced', $results) ) {
                $html .= "<td style='background-color:#D0D0D0;font-weight:bold'>" . $results['bounced'] ."</td>";
            }
            $html .= "</tr>";
		}
		$html .= '</tbody> </table>';
		return $html;
	}
	public function sortArrayByArray($array,$orderArray) {
		$ordered = array();
		foreach($orderArray as $key => $value) {
			if(array_key_exists($value, $array)) {
				$ordered[$value] = $array[$value];
				unset($array[$value]);
			}
		}
	    return $ordered + $array;
	}
}

?>