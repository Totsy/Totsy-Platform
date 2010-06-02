<?php
namespace app\controllers;

use app\models\Page;

class AdminController extends \lithium\action\Controller {

    public function index() {
	
		$pages = Page::all();
        return compact('pages');
	
    }

	public function add() {
		$success = false;
		
        if ($this->request->data) {

            $page = Page::create($this->request->data);
            $success = $page->save();
        }
        return compact('success');
	}
}
?>