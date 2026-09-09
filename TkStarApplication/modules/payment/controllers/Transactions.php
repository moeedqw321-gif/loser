<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Transactions
 *
 *  *
 */
class Transactions extends Public_Controller {

    public function __construct () {
        parent::__construct();
        $this->load->eloquent('Transaction');
		$this->load->sentinel();
    }

    public function index () {
        $this->checkAuth(true);
        $transactions = Transaction::where('user_id' , $this->user->id)->orderBy('created_at' , 'desc')->get();
        $this->smart->assign([
            'title' => 'تراکنش های مالی من' ,
            'transactions' => $transactions,
			'_GET' => $_GET
        ]);
    }

}
