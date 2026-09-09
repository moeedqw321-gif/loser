<script src="{assets_url}/bundles/timepicker.min.js"></script>
<link href="{assets_url}/Content/timepicker.min.css" rel="stylesheet"/>

<div class="container">
	<div class="page-content light">
		<div class="ph15"></div>
		<div class="inline container">
			{include file="partials/dashboard_menu.tpl"}
			<div class="left static-content">
				<div class="page-area container inline form-container" align="center">
					<div class="page-title">شارژ حساب کاربری</div>
                    <div class="row">
					<div class="col-lg-12">
							<div class="topup-form"  style="display:{if $card_save == ""} none {else} {/if};">
								<section class="sitebox">
									<h4>{$card_save}</h4>
									<h5> شما همچنین میتوانید از منو بخش کارت به کارت ها وضعیت واریزی خود را پی گیری نمایید.</h5>
									
								</section>
							</div>
						</div>
						<div class="col-lg-12" id="pay" style="display:{if $card_save == ""} {else} none {/if};">
							<div class="topup-options">
								<div class="row">
									<div class="col-lg-6">
										<a onclick="document.getElementById('pay_title1').innerHTML = 'شارژ حساب با کارت به کارت';document.getElementById('type1').value = 'card_card';document.getElementById('pay').style.display = 'none';document.getElementById('main_form_card').style.display = '';" href="javascript:;" class="item-2-box">
											<div class="image" style="background-image: url({assets_url}/images/1524077801-2203-5418.jpg"></div>
											<div class="title"><div>شارژ حساب با كارت به كارت</div></div>
										</a>
									</div>
									<div class="col-lg-6">
										<a onclick="document.getElementById('pay_title').innerHTML = 'پرداخت از طریق پرفکت مانی';document.getElementById('type').value = 'pm';document.getElementById('pay').style.display = 'none';document.getElementById('main_form').style.display = '';" href="javascript:;" class="item-2-box">
											<div class="image" style="background-image: url({assets_url}/images/1524077764-8155-9766.jpg"></div>
											<div class="title"><div>پرداخت از طريق حساب پرفكت ماني</div></div>
										</a>
									</div>
								</div>
							</div>
						</div>
						<div class="col-lg-12">
							<div class="topup-form" id="main_form" style="display:none;">
								<section class="sitebox">
									<div class="normal-form">
										<h2 id="pay_title"></h2>
										<form action="{site_url}payment/credit" method="post">
											<div class="siteform">
												<input type="hidden" value="" id="type" name="type" />
												<div class="amountinput">
													<label class="label" for="Amount">مبلغ به تومان</label>
													<input autocomplete="off" class="input ltrinput centre" data-val="true" data-val-number="The field مبلغ به تومان must be a number." data-val-range="حداقل مبلغ افزایش موجودی ۱۰۰۰ تومان است." data-val-range-max="2147483647" data-val-range-min="1000" data-val-regex="مبلغ به تومان باید با فرمت درست وارد شود. " data-val-regex-pattern="^\d+$" data-val-required="وارد کردن مبلغ به تومان الزامی است." id="Amount" name="amount" type="text" value="">
													<span class="field-validation-valid error_message" data-valmsg-for="Amount" data-valmsg-replace="true"></span>
													<span class="error_message max_error"></span>
												</div>
												<input style="padding-right: 30px;padding-left: 30px;" class="btn btn-success floatright" type="submit" value="پرداخت">
												<a style="margin-left:5px"onclick="document.getElementById('pay').style.display = '';document.getElementById('main_form').style.display = 'none';" class="btn btn-danger floatright" href="javascript:;"  > بازگشت</a>
											</div>
										</form>                     
									</div>
								</section>
							</div>
						</div>
						<div class="col-lg-12">
							<div class="topup-form" id="main_form_card" style="display:none;">
								<section class="sitebox">
									<div class="normal-form">
										<h2 id="pay_title1"></h2>
										<div class="alert alert-info" style='display:block;'>
										ابتدا مبلغ مورد نظر خود را به شماره کارت ذیل انتقال داده و بعد از انتقال  (چهار رقم آخر) شماره کارت خود را همراه کد پیگیری طبق فیش واریزی وارد کنید.
										</div>
										<form action="{site_url}payment/credit" method="post">
											<div class="siteform">
												<input type="hidden" value="" id="type1" name="type" />
												<div class="amountinput">
													<label class="label" style="font-weight: bold;color: #3c3c3c;font-size: 15px;" for="Amount">شماره کارت جهت واریز</label>
													<input class="input ltrinput centre" style="color: #000;font-size: 15px;" type="text" value="{$card_number}" disabled>
													<label class="label" style="font-weight: bold;color: #000;font-size: 15px;" for="Amount">نام صاحب حساب</label>
													<input class="input ltrinput centre" style="color: #323131;font-size: 15px;" type="text" value="{$card_name}" disabled>
													<label class="label" style="color: #4ea24e;" for="Amount">مبلغ به تومان</label>
													<input autocomplete="off" class="input ltrinput centre" data-val="true" data-val-number="The field مبلغ به تومان must be a number." data-val-range="حداقل مبلغ افزایش موجودی ۱۰۰۰ تومان است." data-val-range-max="2147483647" data-val-range-min="1000" data-val-regex="مبلغ به تومان باید با فرمت درست وارد شود. " data-val-regex-pattern="^\d+$" data-val-required="وارد کردن مبلغ به تومان الزامی است." id="Amount" name="amount" type="text" value="" required>
													<span class="field-validation-valid error_message" data-valmsg-for="Amount" data-valmsg-replace="true"></span>
													<label class="label" style="color: #4ea24e;" for="Amount">چهار رقم اخر شماره کارت</label>
													<input autocomplete="off" class="input ltrinput centre" name="c_card_number" maxlength="4" type="text" value="" required>
													<label class="label" style="color: #4ea24e;" for="Amount">زمان پرداخت</label>
													<input autocomplete="off" class="input ltrinput centre" name="c_time_pay" type="text" id="time" placeholder="21:10" maxlength="10" value="" required>
													<label class="label" style="color: #4ea24e;" for="Amount">کد پیگیری</label>
													<input autocomplete="off" class="input ltrinput centre" name="c_tracking_code" type="text" value="" required>

													<span class="error_message max_error"></span>
												</div>
												<input style="padding-right: 30px;padding-left: 30px;" class="btn btn-success floatright" type="submit" value="پرداخت">
												<a style="margin-left:5px"onclick="document.getElementById('pay').style.display = '';document.getElementById('main_form_card').style.display = 'none';" class="btn btn-danger floatright" href="javascript:;"  > بازگشت</a>
											</div>
										</form>                     
									</div>
								</section>
							</div>
						</div>
					</div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
var timepicker = new TimePicker('time', {
  lang: 'fa',
  theme: 'dark'
});
timepicker.on('change', function(evt) {
  
  var value = (evt.hour || '00') + ':' + (evt.minute || '00');
  evt.element.value = value;

});
</script>