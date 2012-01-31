<?php

namespace app\controllers;

use app\controllers\BaseController;
use MongoDate;
use app\models\Keyade;

/**
* API interface for protected feeds, provided for affiliate partners.
*
* TODO:
** Require token for security
** Require SSL for eavesdropping
** Define standard RESTful interface for access for a variety of affiliate partners
** Determine output format mechanism, some require XML to validate against proprietary DTDs
*/
class FeedsController extends \lithium\action\Controller {

	/**
	 * Test method, nothing more
	 *
	 * @return string Example query results or test output
	 */
	public function home($partner = null, $action = null, $start_date = null, $end_date = null){
	   // Don't render a template or view
	   // $this->render(array('layout' => false));
		// Make sure you ALWAYS pull the token from the request object and not the URL
		// Sanitize that input from the URL

		$start_date = new MongoDate(
			strtotime(
				substr($start_date, 0, 4) . '-' . substr($start_date, 4, 2) . '-' . substr($start_date, 6, 2) . " 00:00:00"
			)
		);
		$end_date = new MongoDate(
			strtotime(
				substr($end_date, 0, 4) . '-' . substr($end_date, 4, 2) . '-' . substr($end_date, 6, 2) . " 23:59:59"
			)
		);
		$data = array(
			'partner' => $partner,
			'action' => $action,
			'start_date' => $start_date,
			'end_date' => $end_date,
			'token' => $this->request->query['token']
		);

		switch($partner){
			case 'keyade':
				if($this->request->query['token'] != '7cf7e9d58a213b2ebb401517d342475e'){
					die("Nope, definitely something important missing.\n");
				}
				// TODO: authenticate with token in $_GET
				switch( $action ){
					case 'signups':
						$output = Keyade::signups( $data );
						break;
					case 'signupsByReferral':
						$output = Keyade::signupsByReferral( $data );
						break;
					case 'sales':
						$output = Keyade::sales( $data );

						break;
					case 'referringSales':
						$output = Keyade::referringSales( $data );
						break;
					default:
						die;
				}
				break;
		}
		return $this->render(array('xml' => $output));
	}

	/**
	 * Debugging
	 */
	public function debug( $thingie ){
		var_dump( $thingie );
		die;
	}

}
