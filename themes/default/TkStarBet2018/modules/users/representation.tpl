<script type="text/javascript">
	$(document).ready(function(){
		$('#htmlcode, #referer_link').on('keyup keydown change click', function(){
			$(this).focus();
			$(this).select();
		});
	});
</script>
<div class="container">
	<div class="page-content light">
		<div class="ph15"></div>
		<div class="inline container">
			{include file="partials/dashboard_menu.tpl"}
			<div class="left static-content">
				<div class="page-area container inline form-container">
					<div class="page-title">طرح نمایندگی</div>
					<div class="p7 inline center" style="padding: 15px !important;color: #ffffff !important;">
                        <p>
                            کسانی که علاقمند هستند به عنوان نماینده سایت فعالیت کنند و کاربران جدیدی را به سایت جذب کنند میتوانند از طرح نمایندگی سایت استفاده کنید.
                            برای استفاده از این طرح شما میتوانید با استفاده از لینک ثبت نام و کد های HTML
                            که برای قرار دادن بنر های سایت در وبسایت های دیگر در نظر گرفته شده است استفاده کنند.
                            هر کاربری که با کلیک بروی این لینک ها در سایت ثبت نام کند زیر مجموعه شما خواهد بود و شما بابت فعایت او در سایت کمیسیون دریافت خواهید کرد.
                        </p>
                        <br>
                        <h4>نحوه محاسبه کمیسیون نماینده:</h4>
                        <p><span style="color: #000000 !important;" class="stepbubble">1</span> هر نماینده {$affiliate_count} درصد از سود سایت از هر زیر مجموعه را به عنوان کمیسیون دریافت میکند</p>
                        <p><span style="color: #000000 !important;" class="stepbubble">1</span> کمیسیون نماینده بصورت مادام العمر به نماینده پرداخت میشود</p>
                        <p><span style="color: #000000 !important;" class="stepbubble">3</span> سایت ما حق تغییر درصد کمیسیون را در آینده برای خود محفوظ نگه میدارد</p><br />
                        <p>در زیر میتوانید لینک ثبت نام منحصر بفرد خودتان را پیدا کنید. همچنین اگر میخواهید بنرهای سایت را در وبسایت یا وبلاگ خود قرار دهید میتوانید از کدهای HTML موجود استفاده کنید</p>
                        <p>دقت کنید که در صورت فیلتر شدن آدرس سایت با آدرس بدون فیلتر وارد همین بخش شوید و لینک ثبت نام یا کد HTML جدید را دریافت کنید</p>
                        <p style="text-align: center !important;"><a style="color: orange !important; font-size: 16px !important;" class="registrationlink sprite-link" onclick="$('#inviteoptions').slideToggle();" href="javascript:void(0)">دریافت لینک ثبت نام</a></p>
                        <div class="inviteoptions" id="inviteoptions" style="display: none;">
                            <div><input type="text" id="referer_link" readonly="readonly" value="{site_url}users/register/{$user->id}"></div>
                            <div class="htmlcode">
                                <p>همچنین می توانید کد HTML زیر را برای استفاده در وبلاگ یا سایت خود استفاده کنید</p>
                                <textarea id="htmlcode" readonly="readonly">&lt;a style="display: block !important; background: url({assets_url}/images/main_logo.png) !important;" href="{site_url}users/register/{$user->id}"&gt;پیش بینی زنده فوتبال در {$site_name} به همراه کازینو آنلاین&lt;/a&gt;</textarea>
                            </div>
                        </div>
                        <br>
                        {if $sub_count}
						<div class="report" style="color: #000000 !important;">
							<div class="col-md-6 col-sm-12 col-xs-12">
								<span class="report-title">تعداد کاربران زیر مجموعه شما</span>
								<span class="report-data">{$sub_count|persian_number} کاربر</span>
							</div>
							<div class="col-md-6 col-sm-12 col-xs-12">
								<span class="report-title">مجموع درآمد شما از زیرمجموعه‌ها</span>
								<span class="report-data">{$affilate_total|number_format} تومان</span>
							</div>
						</div>
                        {/if}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>