<script type="text/javascript">
	document.addEventListener("DOMContentLoaded", function () {
		new IMask(document.getElementById('sheba'), {
			mask: '(IR)00 - 0000 - 0000 - 0000 - 0000 - 0000 - 00'
		});
		new IMask(document.getElementById('card_number'), {
			mask: '0000 - 0000 - 0000 - 0000'
		});
	});
	String.prototype.reverse = function () {
		return this.split('').reverse().join('');
	}
	$(document).ready(function(){
		function reformatText(input) {        
			var x = input.val();
			x = x.replace(/,/g, '');
			x = x.reverse();
			x = x.replace(/.../g, function (e) {
				return e + ',';
			});
			x = x.reverse();
			x = x.replace(/^,/, '');
			input.val(x);
		}
		$('#amount').on('keyup keydown click change', function(){
			reformatText($(this));
		});
		$('#bank_name').change(function(){
			var $this_value = $(this).val().trim().toString();
			if($this_value == 'saman'){
				$('#saman_bank_description').slideDown();
				$('#account_number_div').slideDown();
			}else{
				$('#saman_bank_description').slideUp();
				$('#account_number_div').slideUp();
			}
		});	
		$('#payment_method').change(function(){
			var $this_value = $(this).val().trim().toString();
			if($this_value == 'card'){
				$('#type_input').attr('value','card');
				$('#shaba_description').slideUp();
				$('#other_description').slideUp();
				$('#accountNumber_input').slideUp();
				$('#shaba_input').slideUp();
				$('#card_description').slideDown();
				$('#name_input').slideDown();
				$('#price_input').slideDown();
				$('#bank_select').slideDown();
				$('#cardNumber_input').slideDown();
				$('#btn_all').slideDown();
			}else if($this_value == 'shaba'){
				$('#type_input').attr('value','shaba');
				$('#card_description').slideUp();
				$('#other_description').slideUp();
				$('#cardNumber_input').slideDown();
				$('#accountNumber_input').slideUp();
				$('#shaba_description').slideDown();
				$('#name_input').slideDown();
				$('#price_input').slideDown();
				$('#bank_select').slideDown();
				$('#shaba_input').slideDown();
				$('#btn_all').slideDown();
			}else if($this_value == 'perfectmoney'){
				$('#type_input').attr('value','perfectmoney');
				$('#card_description').slideUp();
				$('#shaba_description').slideUp();
				$('#name_input').slideUp();
				$('#cardNumber_input').slideUp();
				$('#bank_select').slideUp();
				$('#shaba_input').slideUp();
				$('#other_description').slideDown();
				$('#price_input').slideDown();
				$('#accountNumber_input').slideDown();
				$('#btn_all').slideDown();
			}else if($this_value == 'webmoney'){
				$('#type_input').attr('value','webmoney');
				$('#card_description').slideUp();
				$('#shaba_description').slideUp();
				$('#name_input').slideUp();
				$('#cardNumber_input').slideUp();
				$('#bank_select').slideUp();
				$('#shaba_input').slideUp();
				$('#other_description').slideDown();
				$('#price_input').slideDown();
				$('#accountNumber_input').slideDown();
				$('#btn_all').slideDown();
			}else if($this_value == 'bitcoin'){
			    $('#type_input').attr('value','bitcoin');
				$('#card_description').slideUp();
				$('#shaba_description').slideUp();
				$('#name_input').slideUp();
				$('#cardNumber_input').slideUp();
				$('#bank_select').slideUp();
				$('#shaba_input').slideUp();
				$('#other_description').slideDown();
				$('#price_input').slideDown();
				$('#accountNumber_input').slideDown();
				$('#btn_all').slideDown();
			}else{
				$('#type_input').attr('value','');
				$('#card_description').slideUp();
				$('#shaba_description').slideUp();
				$('#name_input').slideUp();
				$('#cardNumber_input').slideUp();
				$('#bank_select').slideUp();
				$('#shaba_input').slideUp();
				$('#other_description').slideUp();
				$('#price_input').slideUp();
				$('#accountNumber_input').slideUp();
				$('#btn_all').slideUp();
			}
		});
	});
</script>
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
					<div class="page-title">درخواست جایزه</div>
					
					<div class="withdraw-description" style="margin-bottom: 20px !important; line-height: 40px !important; font-size: 15px !important; text-align: right !important;">
						<span>سلام کاربر عزیز شما از اینجا قادر خواهید بود جایزه خود را به یکی از راه های زیر برداشت نمایید  :</span><br />
						<span>1. برداشت از طریق کارت به کارت</span><br />
						<span>2. برداشت از طریق شماره شبا</span><br />
						<span>3. برداشت از طریق پرفکت مانی</span><br />
						<span>4. برداشت از طریق وب مانی</span><br />
						<span>5. برداشت از طریق بیت کوین</span><br />
						<span>برای برداشت کافیست از منوی کشویی زیر یکی از راه  ها را انتخاب کرده و اطلاعات مورد نیاز ما را وارد نمایید.</span><br />
					</div>	
					

					<div class="withdraw-description alert alert-danger" id="saman_bank_description" style="margin-top: 15px !important; line-height: 40px !important; font-size: 15px !important; text-align: right !important;"><span>پرداخت های بانک سامان بین 2 الی 4 ساعت انجام میشود</span></div>
						<div class="mt15" style="margin-bottom: 30px;">
								<div class="left form-title mw160">روش برداشت</div>
								<div class="left form-element">
									<select id="payment_method" name="payment_method" style="height: 40px !important;" class="form-input user-form-data">
										<option value="">لطفا انتخاب کنید </option>
										<option value="card">برداشت با کارت به کارت</option>
										<option value="shaba">برداشت با شماره شبا</option>
										<option value="perfectmoney">برداشت با حساب پرفکت مانی</option>
										<option value="webmoney">برداشت با حساب وبمانی</option>
										<option value="bitcoin">برداشت با حساب بیت کوین</option>
									</select>
								</div><br><br>
								<div class="clear"></div>
							</div>
					
			
				<div class="p30 inline" align="right" style="margin-top: -30px !important; text-align: right !important; width: 100% !important;">
						<div id="card_description" class="withdraw-description" style=" display:none;margin-bottom: 20px !important; line-height: 40px !important; font-size: 15px !important; text-align: right !important;">
						<span><text style="font-weight: bold !important;"><font color="red">توجه* : مشخصات حساب بانکی باید مطابق با اطلاعات حساب کاربری شما باشد در غیر این صورت پرداخت انجام نخواهد شد</font></span><br />
						<span>با توجه به قوانین جدید بانکی زمان واریز بین ۲ تا ۴ ساعت کاری می باشد</span><br />
						<span>حداقل مبلغ قابل برداشت : {50000|number_format} تومان</span><br />
						<span>حداکثر مبلغ قابل برداشت : {150000|number_format} تومان</span><br />
						<span>عملیات پایا در روزهای جمعه و تعطیلات رسمی توسط بانک انجام نمی شود</span><br />
						</div>	
						<div id="shaba_description" class="withdraw-description" style="display:none;margin-bottom: 20px !important; line-height: 40px !important; font-size: 15px !important; text-align: right !important;">
						<span><text style="font-weight: bold !important;"><font color="red">توجه* : مشخصات حساب بانکی باید مطابق با اطلاعات حساب کاربری شما باشد در غیر این صورت پرداخت انجام نخواهد شد</font></span><br />
						<span>با توجه به قوانین جدید بانکی زمان واریز بین ۲۴ تا ۴۸ ساعت کاری می باشد</span><br />
						<span>حداقل مبلغ قابل برداشت : {50000|number_format} تومان</span><br />
						<span>حداکثر مبلغ قابل برداشت : {10000000|number_format} تومان</span><br />
						<span>عملیات پایا در روزهای جمعه و تعطیلات رسمی توسط بانک انجام نمی شود</span><br />
						</div>	
						<div id="other_description" class="withdraw-description" style="display:none;margin-bottom: 20px !important; line-height: 40px !important; font-size: 15px !important; text-align: right !important;">
						<span>با توجه به درخواست های زیاد واریز بین 1 تا 2 ساعت کاری می باشد</span><br />
						<span>حداقل مبلغ قابل برداشت : {50000|number_format} تومان</span><br />
						<span>حداکثر مبلغ قابل برداشت : {10000000|number_format} تومان</span><br />
						<span>میزان واریز مبلغ به صورت ارز به قیمت همان روز محاسبه میگردد</span><br />
						</div>	
						<div class="mt15">
							<div class="left form-title mw160 c-main">موجودی</div>
							<div class="left form-element fs16 mt8" style="text-align: right !important; direction: rtl !important;"><span class="bold">{$cash|number_format}</span> تومان</div>
							<div class="clear"></div>
						</div>
						<div class="mt15">
							<div class="left form-title mw160 c-main">موجودی قابل برداشت</div>
							<div class="left form-element fs16 mt8" style="text-align: right !important; direction: rtl !important;"><span class="bold">{$cash|number_format}</span> تومان</div>
							<div class="clear"></div>
						</div>
						<form action="{$action}" method="post">
							<input type="hidden" name="type" id="type_input" value="">
							<div class="mt15" id="name_input" style="display:none">
								<div class="left form-title mw160">نام و نام خانوادگی</div>
								<div class="left form-element"><input type="text" readonly="readonly" disabled="disabled" value="{$my_name}" style="height: 40px; text-align: center !important;" class="form-input"></div>
								<div class="clear"></div>
							</div>
							<div class="mt15" id="price_input" style="display:none">
								<div class="left form-title mw160">مبلغ</div>
								<div class="left form-element"><input type="text" style="height: 40px !important; direction: ltr !important;" id="amount" name="amount" class="form-input text-money-format"></div>
								<div class="clear"></div>
							</div>
							<div class="mt15" id="bank_select" style="display:none">
								<div class="left form-title mw160">نام بانک</div>
								<div class="left form-element">
									<select id="bank_name" name="bank_name" style="height: 40px !important;" class="form-input user-form-data">
										<option value="">لطفا نام بانک خود را انتخاب کنید</option>
										<option value="saman">بانک سامان</option>
										<option value="meli">بانک ملی</option>
										<option value="melat">بانک ملت</option>
										<option value="pasargad">پاسارگاد</option>
										<option value="tejarat">بانک تجارت</option>
										<option value="saderat">بانک صادرات</option>
										<option value="sepah">بانک سپه</option>
										<option value="parsian">بانک پارسیان</option>
										<option value="ayande">بانک آینده</option>
										<option value="sarmaye">بانک سرمایه</option>
										<option value="keshavarzi">بانک کشاورزی</option>
										<option value="ghavamin">بانک قوامین</option>
										<option value="eghtesad_novin">بانک اقتصاد نوین</option>
										<option value="maskan">بانک مسکن</option>
										<option value="dey">بانک دی</option>
										<option value="shahr">بانک شهر</option>
										<option value="refah">بانک رفاه</option>
										<option value="ansar">بانک انصار</option>
										<option value="sina">بانک سینا</option>
									</select>
								</div><br><br>
								<div class="clear"></div>
							</div>
							<div class="mt15" id="shaba_input" style="display:none">
								<div class="left form-title mw160">شماره شبا</div>
								<div class="left form-element">
									<input type="text" style="height: 40px !important; direction: ltr !important;" id="sheba" name="sheba" class="form-input user-form-data text-format" value="">
								</div>
								<div class="clear"></div>
							</div>
							<div class="mt15" id="accountNumber_input" style="display: none;" >
								<div class="left form-title mw160">شماره حساب</div>
								<div class="left form-element">
									<input type="text" style="height: 40px !important; direction: ltr !important;" id="account_number" name="account_number" class="form-input user-form-data text-format" value="">
								</div>
								<div class="clear"></div>
							</div>
							<div class="mt15" id="cardNumber_input" style="display: none;">
								<div class="left form-title mw160">شماره کارت</div>
								<div class="left form-element">
									<input type="text" style="height: 40px !important; direction: ltr !important;" id="card_number" name="card_number" class="form-input user-form-data text-format" value="">
								</div>
								<div class="clear"></div>
							</div>
							<div class="mt15" id="btn_all" style="display:none" >
								<div class="left form-element" style="margin-right: 59px;">
									<button href="javascript:;" class="form-button action-button">برداشت جایزه</button>
								</div>
								<div class="clear"></div>
							</div>
						</form>
						</div>
                        <section class="formbox row_100 clearfix" style="margin-top: 30px !important;">
                            <div class="support_messages" style="display: block;">
                                <table class="leaguetable support">
									<thead>
                                        <tr>
                                            <th style="width: 25%; text-align: center !important;">شناسه درخواست</th>
                                            <th style="width: 25%; text-align: center !important;">تاریخ درخواست</th>
                                            <th style="width: 25%; text-align: center !important;">مبلغ درخواستی</th>
                                            <th style="width: 25%; text-align: center !important;">وضعیت</th>
                                        </tr>
									</thead>
                                    <tbody>
                                        {foreach $withdrawList as $val}
										<tr>
											<td style="width: 25%; text-align: center !important;">{$val->id}</td>
											<td style="width: 25%; text-align: center !important;">{jdate format='l d F Y ساعت H:i:s' date=$val->created_at}</td>
											<td style="width: 25%; text-align: center !important;">{$val->amount|price_format}</td>
											<td style="width: 25%; text-align: center !important;">{if $val->status eq 0}پرداخت نشده/درحال بررسی{else if $val->status eq -1}درخواست رد شده{else if $val->status eq 1}پرداخت شده{else if $val->status eq 2}بررسی شده، در انتظار پرداخت{/if}</td>
										</tr>
                                        {/foreach}
                                    </tbody>
                                </table>
                            </div>
                        </section>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>