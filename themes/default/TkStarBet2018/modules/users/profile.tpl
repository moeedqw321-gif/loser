<div class="container">
	{if !empty($smarty.const.get_message)}
		{assign var = message_details value = (object)$smarty.const.get_message}
		{if !empty($message_details->message)}
			{if $message_details->type == 'fail'}
				{assign var=alert_type value='alert-danger'}
			{elseif $message_details->type == 'warning'}
				{assign var=alert_type value='alert-warning'}
			{elseif $message_details->type == 'success'}
				{assign var=alert_type value='alert-success'}
			{/if}
			<div class="row"><div style="text-align: right !important; width: 100% !important; margin-top: 10px !important; margin-bottom: -30px !important;"><div class="alert {$alert_type}" style="display: block !important;">{$message_details->message}</div></div></div>
		{/if}
	{/if}
	<div class="page-content light">
		<div class="ph15"></div>
		<div class="inline container">
			{include file="partials/dashboard_menu.tpl"}
			<div class="left static-content">
				<div class="page-area container inline form-container">
					<div class="page-title">تغییر کلمه عبور</div>
					<div class="p30 inline" align="right" style="margin-top: -30px !important; text-align: right !important; width: 100% !important;">
						<form action="{site_url}users/changeProfile" method="post">
							<div class="mt15">
								<div class="left form-title mw160">ایمیل</div>
								<div class="left form-element"><input type="text" value="{$my->email}" readonly="readonly" disabled="disabled" style="height: 40px; text-align: center !important; direction: ltr !important;" class="form-input"></div>
								<div class="clear"></div>
							</div>
							<div class="mt15">
								<div class="left form-title mw160">نام و نام خانوادگی</div>
								<div class="left form-element"><input type="text" value="{$my->first_name|con:' ':$my->last_name}" readonly="readonly" disabled="disabled" style="height: 40px; text-align: center !important; direction: rtl !important;" class="form-input"></div>
								<div class="clear"></div>
							</div>
							<div class="mt15">
								<div class="left form-title mw160">شماره موبایل</div>
								<div class="left form-element"><input type="text" value="{$my_mobile}" readonly="readonly" disabled="disabled" style="height: 40px; text-align: center !important; direction: ltr !important;" class="form-input"></div>
								<div class="clear"></div>
							</div>
							<div class="mt15">
								<div class="left form-title mw160">نام مستعار</div>
								<div class="left form-element"><input type="text" value="{$my->username}" name="username" style="height: 40px; text-align: right !important; direction: ltr !important;" class="form-input"></div>
								<div class="clear"></div>
							</div>
							<div class="mt15">
								<div class="left form-title mw160">آخرین ورود</div>
								<div class="left form-element"><input type="text" value="{Miladr\Jalali\jDateTime::date('l d F Y ساعت H:i:s', strtotime({$my->last_login}))}" readonly="readonly" disabled="disabled" style="height: 40px; text-align: center !important; direction: ltr !important;" class="form-input"></div>
								<div class="clear"></div>
							</div>
							<div class="mt15">
								<div class="left form-element" style="margin-right: 59px;"><button type="submit" href="javascript:;" class="form-button action-button">ویرایش</button></div>
								<div class="clear"></div>
							</div>
                        </form>
					</div>
                </div>
				<div class="page-area container inline form-container">
					<div class="page-title">تغییر کلمه عبور</div>
					<div class="p30 inline" align="right" style="margin-top: -30px !important; text-align: right !important; width: 100% !important;">
						<form action="{site_url}users/profile" method="post">
							<div class="mt15">
								<div class="left form-title mw160">کلمه عبور فعلی</div>
								<div class="left form-element"><input type="password" value="" name="current_password" autocomplete="off" style="height: 40px !important;" class="form-input"></div>
								<div class="clear"></div>
							</div>
							<div class="mt15">
								<div class="left form-title mw160">کلمه عبور جدید</div>
								<div class="left form-element"><input type="password" value="" name="new_password" autocomplete="off" style="height: 40px !important;" class="form-input"></div>
								<div class="clear"></div>
							</div>
							<div class="mt15">
								<div class="left form-title mw160">تکرار کلمه عبور جدید</div>
								<div class="left form-element"><input type="password" value="" name="re_new_password" autocomplete="off" style="height: 40px !important;" class="form-input"></div>
								<div class="clear"></div>
							</div>
							<div class="mt15">
								<div class="left form-element" style="margin-right: 59px;"><button type="submit" href="javascript:;" class="form-button action-button">تغییر کلمه عبور</button></div>
								<div class="clear"></div>
							</div>
                        </form>
					</div>
                </div>
            </div>
        </div>
    </div>
</div>