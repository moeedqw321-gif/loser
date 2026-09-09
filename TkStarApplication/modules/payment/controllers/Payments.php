<?php
class Payments extends Public_Controller {

    public $validation_rules = array(
        'credit' => array(
            ['field' => 'amount' , 'rules' => 'numeric|trim|required|htmlspecialchars' , 'label' => 'Ù…Ø¨Ù„Øº' ] ,
        )
    );
    public $api_token = 'Sk_1c21fafaedacb';
    public function __construct () {
        parent::__construct();
        $this->load->eloquent('settings/Setting');
		$this->load->sentinel();
    }

    public function credit () {
        $this->checkAuth(true);
        $this->load->library('zahedipal');
        $amount = $this->input->post('amount');
        $type = $this->input->post('type');
        if ( $this->formValidate(FALSE) ) {
            if($type == 'pm'){
                $this->load->eloquent('Transaction');
                $cash = $this->__getUserCash();
                Transaction::create([
                    'trans_id' => '1',
                    'price' => $amount ,
                    'invoice_type' => 1 ,
                    'cash' => $cash + $amount ,
                    'user_id' => $this->user->id ,
                    'description' => 'افزایش موجودی حساب' ,
                ]);
                echo '<form name=\'myForm\' action=\'https://perfectmoney.is/api/step1.asp\' method=\'POST\'>
    <input type=\'hidden\' name=\'PAYEE_ACCOUNT\' value=\'U15949464\'>
    <input type=\'hidden\' name=\'PAYEE_NAME\' value=\'KafeBet Payment\'>
    <input type=\'hidden\' name=\'PAYMENT_ID\' value=\'' . time() . '\'>
    <input type=\'hidden\' name=\'PAYMENT_AMOUNT\' value=\'' . round($amount / 4200, 2) . '\'>
    <input type=\'hidden\' name=\'PAYMENT_UNITS\' value=\'USD\'>
    <input type=\'hidden\' name=\'STATUS_URL\' value=\'' . site_url('payment/credit') . '\'>
    <input type=\'hidden\' name=\'PAYMENT_URL\' value=\'' . site_url('payment/credit') . '\'>
    <input type=\'hidden\' name=\'PAYMENT_URL_METHOD\' value=\'POST\'>
    <input type=\'hidden\' name=\'NOPAYMENT_URL\' value=\'' . site_url('payment/credit') . '\'>
    <input type=\'hidden\' name=\'NOPAYMENT_URL_METHOD\' value=\'POST\'>
    <input type=\'hidden\' name=\'SUGGESTED_MEMO\' value=\'\'>
    <input type=\'hidden\' name=\'BAGGAGE_FIELDS\' value=\'IDENT\'><br>
</form><script type=\'text/javascript\'>window.onload = function(){document.forms[\'myForm\'].submit();}</script>';die();
            }else{
                $data = json_encode(array(
                    'pin' => $this->api_token,
                    'price' => $amount,
                    'order_id' => 1,
                    'callback' => 'http://persepolisbookshop.com/verfiy.php',
                    'ip'=> $_SERVER['REMOTE_ADDR'],
                    'callback_type' => 2
                ));
                $res = $this->zahedipal->zpCurl($data);
                $res = json_decode($res, false);
                $transID = $res->form_details->fields->Token;
                $_SESSION['au'] = $res->au;
                $_SESSION['amount'] = $amount;
                $_SESSION['transID'] = $transID;
                $this->load->eloquent('Transaction');
                $cash = $this->__getUserCash();
                Transaction::create([
                    'trans_id' => $res->au,
                    'savano' => $transID,
                    'price' => $amount ,
                    'invoice_type' => 1 ,
                    'cash' => $cash + $amount ,
                    'user_id' => $this->user->id ,
                    'description' => 'Ø´Ø§Ø±Ú˜ Ø­Ø³Ø§Ø¨ Ú©Ø§Ø±Ø¨Ø±ÛŒ' ,
                ]);
                header("Location: https://pec.shaparak.ir/NewIPG/?Token=".$transID);exit();
            }
        } else {
            $this->smart->view('credit');
        }
    }

    public function verify_payment () {
        $this->checkAuth(true);
        $this->load->library('zahedipal');
        $this->load->eloquent('transaction');
        $au = Transaction::where('trans_id' , $_SESSION['transID'])->where('status' , 0)->first();
        $data = array(
            'pin' => $this->api_token,
            'amount' => $_SESSION['amount'],
            'transid' => $au->trans_id
        );
        $data = json_encode(array (
			'pin' => $this->api_token,
			'price' => $_SESSION['amount'],
			'order_id' => 1,
			'au' =>  $_SESSION['au'],
			'bank_return' => $_POST + $_GET,
		));
        $res = $this->zahedipal->zpCurl($data , true);
		$res = json_decode($res, false);
        if(!is_object($res)){
            $au->update(array( 'status' => 2 ));
            $this->message->set_message('Ù¾Ø±Ø¯Ø§Ø®Øª Ø§Ù†Ø¬Ø§Ù… Ù†Ø´Ø¯Ù‡ Ø§Ø³Øª .' , 'fail' , 'Ù¾Ø±Ø¯Ø§Ø®Øª Ø§Ù†Ø¬Ø§Ù… Ù†Ø´Ø¯' , 'dashboard')->redirect();
        } else if (!empty($res->result) AND ($res->result == 1 OR $res->result == '1')) {
            $au->update(array( 'status' => 1 ));
            $cash = $this->__getUserCash();
            $this->sentinel->getUserRepository()->where('id' , $this->user->id)->update(array( 'cash' => $cash + $au->price ));
            $this->load->library('email');
            $site_name = Setting::findByCode('site_name')->value;
            $this->email->from('noreply@tkstar.ir' , $site_name);
            $this->email->to($this->user->email);
            $this->email->subject($site_name . ' - Ø´Ø§Ø±Ú˜ Ø­Ø³Ø§Ø¨ Ú©Ø§Ø±Ø¨Ø±ÛŒ');
            $this->email->message("Ø­Ø³Ø§Ø¨ Ú©Ø§Ø±Ø¨Ø±ÛŒ Ø´Ù…Ø§ Ø¨Ø§ Ù…ÙˆÙÙ‚ÛŒØª Ø¨Ù‡ Ù…Ø¨Ù„Øº $amount ØªÙˆÙ…Ø§Ù† Ø´Ø§Ø±Ú˜ Ú¯Ø±Ø¯ÛŒØ¯.  .\n\n Ø´Ù…Ø§Ø±Ù‡ Ù¾ÛŒÚ¯ÛŒØ±ÛŒ ØªØ±Ø§Ú©Ù†Ø´: $au->trans_id \n Ø¨Ø§ ØªØ´Ú©Ø± - ØªÛŒÙ…" . $site_name);
            $this->email->send();
            $this->message->set_message('Ù¾Ø±Ø¯Ø§Ø®Øª Ø¨Ø§ Ù…ÙˆÙÙ‚ÛŒØª Ø§Ù†Ø¬Ø§Ù… Ø´Ø¯Ù‡ Ø§Ø³Øª . Ú©Ø¯ Ø±Ù‡Ú¯ÛŒØ±ÛŒ ØªØ±Ø§Ú©Ù†Ø´ : ' . $au->trans_id , 'success' , 'Ù¾Ø±Ø¯Ø§Ø®Øª Ø§Ù†Ø¬Ø§Ù… Ø´Ø¯' , 'dashboard')->redirect();
        }
    }
    public function transactions ( $page = 0 ) {
        $this->checkAuth(true);
        $this->load->eloquent('transaction');
        $transactions = Transaction::where('user_id' , $this->user->id)->orderBy('id' , 'desc')->get();
        $config["base_url"] = site_url() . "payment/transactions";
        $config["total_rows"] = $transactions->count();
        $config["uri_segment"] = 3;
        $this->pagination->initialize($config);
        $this->smart->assign([
            'transactions' => $transactions ,
            'title' => 'ØªØ±Ø§Ú©Ù†Ø´ Ù‡Ø§ÛŒ Ù…Ø§Ù„ÛŒ Ù…Ù†' ,
            'cash' => $this->__getUserCash() ,
            'transaction_states' => 1,
			'_GET' => $_GET
        ]);
        $this->smart->view('transactions');
    }

}
