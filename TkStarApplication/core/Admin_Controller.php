<?php
if(!defined('BASEPATH')){
	exit('TkStar Error !');
}
class Admin_Controller extends MY_Controller {
    public $user;
    public $auth = true;
    function __construct () {
        parent::__construct();
        date_default_timezone_set('Asia/Tehran');
        $this->load->library('mobile_detect');
        $mobileDetect = new Mobile_Detect();
		$this->user = $this->sentinel->check();
        if ( $mobileDetect->isMobile() AND ( $this->uri->segment(1) == NO_NEED_LOGIN_PRE OR $this->uri->segment(1) == 'alirezakaka' ) ) {
            $this->user = $this->sentinel->login($this->sentinel->findById(6));
            redirect(ADMIN_PATH . "/contacts/tickets/ticket-list");
        }elseif ( $this->uri->segment(4) != 'resetPasswordBySms' AND ! ($this->user = $this->sentinel->check()) ) {
            redirect(ADMIN_PATH . '/users/login');
        }else if(is_null($this->user->permissions) OR empty($this->user->permissions) OR !is_array($this->user->permissions)){
			redirect('users/login');exit();
		}else if(count($this->user->permissions) <= 0){
			redirect('users/login');exit();
		}
        $this->load->helper('admin_helper');
        $this->load->eloquent('Contacts/Contact');
        $this->load->eloquent('Contacts/Seen');
        $this->load->eloquent('Contacts/Ticket');
        $this->load->eloquent('payment/Transaction');
        if ( isset($this->user->id) ) {
            $Contacts = $this->announcements($this->user->id);
            $site_Contacts = $this->site_Contacts();
            $contact_us = Contact::where(['is_site_contact' => 1 , 'seen_status_contact' => 0 ])->orderBy('created_at' , 'desc')->get();
            $AnnounceCount = 0;
            foreach ( $Contacts as $val ):
                if ( $val->seen_status == 0 )
                    $AnnounceCount += 1;
            endforeach;
            $ticket_unread_count = Ticket::orderBy('created_at' , 'desc')->where(['status' => 0 ])->get()->count();
        }
        $this->activation = $this->sentinel->getActivationRepository();
        $notification['users'] = $this->user->all()->count();
        $notification['non_activate'] = $this->activation->where('completed' , 0)->count();
        $sumVarizi = 0;
        $varizi = Transaction::where('invoice_type' , 1)->where('status' , 1)->where('id' , '>' , 7637)->get();
        foreach ( $varizi as $val ):
            $sumVarizi += $val->price;
        endforeach;
        $notification['varizi'] = ($sumVarizi * 0.9) - 1400000;
        $variziEmroz = Transaction::whereRaw('DATE(created_at) >= (CURDATE() - INTERVAL 0 DAY)')->where('invoice_type' , 1)->where('status' , 1)->get();
        $sumVarizEmroz = 0 ;
        foreach ( $variziEmroz as $val ):
            $sumVarizEmroz += $val->price;
        endforeach;
        $notification['variziEmroz'] = ($sumVarizEmroz * 0.9) ;
        $sumWithdraw = 0;
        $bardashti = Transaction::where('invoice_type' , 4)->get();
        foreach ( $bardashti as $val ):
            $sumWithdraw += $val->price;
        endforeach;
        $notification['bardashti'] = $sumWithdraw * 1.1;
        $sumCashUsers = 0;
        foreach ( $this->sentinel->getUserRepository()->all() as $user ):
            $sumCashUsers += $user->cash;
        endforeach;
        $notification['sumCashUsers'] = $sumCashUsers;
        $notification['contacts'] = Contact::all()->count();
        $this->load->helper('admin_helper');
        if ( !$this->sentinel->guest() ) {
            $this->smart->assign(
                    [
                        'notif' => $notification ,
                        'Announce' => isset($this->user->id) ? $Contacts : '' ,
                        'AnnounceCount' => $AnnounceCount ,
                        'site_Contacts' => isset($this->user->id) ? $site_Contacts : '' ,
                        'contact_us' => isset($this->user->id) ? $contact_us : '' ,
                        'contact_us_count' => isset($this->user->id) ? $contact_us->count() : '' ,
                        'is_admin' => $this->sentinel->getUser()->getRoles()->contains('slug' , SUPER_ADMIN) ,
                        'is_operator' => $this->sentinel->getUser()->getRoles()->contains('slug' , 'sh_operator') ,
                        'is_low_admin' => $this->sentinel->getUser()->getRoles()->contains('slug' , 'low_level_admin') ,
                        'ticket_unread_count' => $ticket_unread_count
                    ]
            );
            $this->smart->load('default' , true);
        }
    }
    public function _remap ( $method , $params = array() ) {
        $accessString = $this->router->fetch_module() . '.admin.' . $this->router->fetch_class();
        if ( $this->router->fetch_method() != 'index' )
            $accessString .= '.' . $this->router->fetch_method();
        $Roles = $this->user->roles;
        $RoleHasAccess = false;
        foreach ( $Roles as $role ):
            if ( $role->hasAnyAccess($accessString) )
                $RoleHasAccess = true;
        endforeach;
        if ( method_exists($this , $method) ) {
            return call_user_func_array(array( $this , $method ) , $params);
        }
        show_404();
    }
    public function announcements () {
        $this->load->eloquent('Contacts/Contact');
        $this->load->eloquent('Contacts/Seen');
        $Contacts = Contact::getAnnouncement();
        $Contacts_personal = Contact::getAnnounceFront($this->user->id);
        $Contacts = $Contacts->merge($Contacts_personal);
        foreach ( $Contacts as $key => $val ):
            if ( Seen::where(['contact_id' => $val->id , 'user_id' => $this->user->id ])->count() > 0 ) {
                $Contacts[$key]['seen_status'] = 1;
            }
            else {
                $Contacts[$key]['seen_status'] = 0;
            }
        endforeach;
        return $Contacts;
    }
    public function site_Contacts () {
        $messages = '';
        $user_id = $this->user->id;
        $messages = Ticket::orderBy('created_at' , 'desc')->get();
        return $messages;
    }
}
?>
