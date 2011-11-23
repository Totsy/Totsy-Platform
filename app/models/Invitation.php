<?php

namespace app\models;

use lithium\storage\Session;
use lithium\util\Validator;
use app\extensions\Mailer;
use MongoDate;
use MongoId;

class Invitation extends \lithium\data\Model {

	protected $_dates = array(
		'now' => 0
	);

	public static function collection() {
		return static::_connection()->connection->invitations;
	}

	public static function dates($name) {
	     return new MongoDate(time() + static::_object()->_dates[$name]);
	}

	public static function add($invitation, $id, $code, $email) {
		$invitation->user_id = $id;
		$invitation->date_sent = static::dates('now');
		$invitation->email = $email;
		$invitation->status = 'Sent';
		$invitation->invitation_code = $code;
		return static::_object()->save($invitation);
	}

	public static function reject($winner, $email) {
		return static::collection()->update(array(
			'email' => $email,
			'$ne' => $winner),
			array('$set' => array(
				'status' => 'Ignored',
				'date_updated' => static::dates('now')
		)));

	}

	public static function linkUpInvites($invite_code, $email) {
	    if ($invite_code) {
            $inviter = User::find('first', array(
                'conditions' => array(
                    'invitation_codes' => array($invite_code)
            )));
            if ($inviter) {
                $invited = Invitation::find('first', array(
                    'conditions' => array(
                        'user_id' => (string) $inviter->_id,
                        'email' => $email
                )));
                
                if ($inviter->invited_by === 'keyade') {
                    $data['keyade_referral_user_id'] = $inviter->keyade_user_id;
                }
                if ($invited) {
                	Mailer::send('Invited_Register', $inviter->email);
                
                    $invited->status = 'Accepted';
                    $invited->date_updated = Invitation::dates('now');
                    $invited->save();
                    if ($invite_code != 'keyade') {
                        Invitation::reject($inviter->_id, $email);
                    }
                } else {
                /**
                * This block was included because users can pass on their
                * invite url by mouth @_@
                **/
                    $invitation = Invitation::create();
                    $invitation->user_id = (string) $inviter->_id;
                    $invitation->email = $email;
                    $invitation->date_accepted = Invitation::dates('now');
                    $invitation->status = 'Accepted';
                    $invitation->save();
                }
            }
        }
	}

	public static function retrieveInviteCode($searchBy = null){
		if (is_null($searchBy)) return null;
		$invite_code = User::lookup($searchBy);
		$invite_code = $invite_code['invitation_codes'][0];
		return $invite_code;
	}
}
