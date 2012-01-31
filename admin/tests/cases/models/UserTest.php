<?php

namespace admin\tests\cases\models;

use admin\models\User;

class UserTest extends \lithium\test\Unit {

	public function testlookupUserInvitedBy() {
	    $id = "4ea1a190c24efcab060003c4";
	    $expected_invitedby = "momtv";

	    $invited_by = User::lookupUserInvitedBy($id);

	    $this->assert(is_string($invited_by), "The value returned is not a string");
	     $this->assertEqual($expected_invitedby, $invited_by, "Failed: expecting $expected_invitedby but got $invited_by");
	}

}

?>