	<div class="help-question-div">
		<img src="{assets_url}/images/help_documention/times.png" class="close-help-div" style="height: 40px !important; width: 40px !important;" />
		<div class="tip-help">سوال یا مشکلی دارید ؟ لطفا قبل از استفاده از امکانات سایت بخش راهنما را کامل مطالعه کنید</div>
		<a href="{site_url}users/help">
			<img src="{assets_url}/images/help_documention/question_help.png" class="main-img-help" />
		</a>
	</div>

	<div class="footer-links desktop">
		<div class="inline container">
			<div style="text-align: right !important; margin-right: 20px !important;">
				<a href="https://www.fb.com/#" target="_blank"><img src="{assets_url}/images/icons/facebook.svg" style="margin: auto 3px !important; width: 32px !important; height: 32px !important;"></a>
				<a href="https://www.t.me/vipcasino90" target="_blank"><img src="{assets_url}/images/icons/telegram.svg" style="margin: auto 3px !important; width: 32px !important; height: 32px !important;"></a>
				<a href="https://www.instagram.com/vipcasino90" target="_blank"><img src="{assets_url}/images/icons/instagram.svg" style="margin: auto 3px !important; width: 32px !important; height: 32px !important;"></a>
			</div>
			<div class="clear"></div>
		</div>
	</div>

	<div class="mobile mobile-bar-holder"></div>
	<div class="mobile mobile-footer-bar">
		<a href="{site_url}" class="sport {if strpos($smarty.server.REQUEST_URI, "index") !== false OR {$smarty.server.REQUEST_URI} == "" OR {$smarty.server.REQUEST_URI} == "/"}active{/if}">صفحه اصلی</a>
		<a href="{site_url}dashboard" class="account {if strpos($smarty.server.REQUEST_URI, "casino") == false AND (strpos($smarty.server.REQUEST_URI, "myrecords") !== false OR strpos($smarty.server.REQUEST_URI, "dashboard") !== false OR strpos($smarty.server.REQUEST_URI, "payment") !== false OR strpos($smarty.server.REQUEST_URI, "users") !== false)}active{/if}">حساب کاربری</a>
		<a href="{site_url}bets/inplayBet" class="live {if strpos($smarty.server.REQUEST_URI, "inplayBet") !== false OR strpos($smarty.server.REQUEST_URI, "InplayOdds") !== false}active{/if}">پیش بینی زنده</a>
		<a href="{site_url}bets/upComing" class="scores {if strpos($smarty.server.REQUEST_URI, "upComing") !== false OR strpos($smarty.server.REQUEST_URI, "preEvents") !== false}active{/if}">پیش بینی پیش از بازی</a>
		<a href="{site_url}users/casino" class="casino {if strpos($smarty.server.REQUEST_URI, "casino") !== false}active{/if}">کازینو</a>
	</div>

	<!-- ========== اسپلش شاه بت (شیک و حرفه‌ای) ========== -->
	<div class="splash-view" id="shahbetSplash" style="display: none;">
		<div class="splash-container">
			<div class="splash-header">
				<div class="left splash-title">👑 شاه بت</div>
				<div class="right">
					<span class="fa fa-times pointer splash-close-button" onclick="closeShahbetSplash()"></span>
				</div>
				<div class="clear"></div>
			</div>
			<div class="splash-content">
				<div style="text-align: center; padding: 10px 5px;">
					
					<div style="font-size: 22px; font-weight: bold; color: #ffd33b; margin-bottom: 18px;">
						به خانواده شاه بت خوش آمدید
					</div>

					<div style="background: linear-gradient(135deg, #1a1a1a, #2d2d2d); border-radius: 12px; padding: 18px 15px; margin-bottom: 16px; border: 1px solid #ffd33b;">
						<div style="font-size: 17px; color: #ffd33b; font-weight: bold; margin-bottom: 8px;">
							🎁 بونوس ۱۰۰٪ اولین واریز
						</div>
						<div style="font-size: 13.5px; color: #eee;">
							هر مبلغی واریز کنید، همان مقدار هدیه بگیرید
						</div>
					</div>

					<div style="display: flex; justify-content: space-between; gap: 10px; margin-bottom: 16px;">
						<div style="flex: 1; background: #1f1f1f; border-radius: 10px; padding: 12px 8px; border: 1px solid #333;">
							<div style="font-size: 15px; color: #33cccc; font-weight: bold;">💎 ۱۰٪</div>
							<div style="font-size: 12px; color: #ccc; margin-top: 4px;">کش‌بک هفتگی</div>
						</div>
						<div style="flex: 1; background: #1f1f1f; border-radius: 10px; padding: 12px 8px; border: 1px solid #333;">
							<div style="font-size: 15px; color: #ff6b6b; font-weight: bold;">🔥 ۲۰٪</div>
							<div style="font-size: 12px; color: #ccc; margin-top: 4px;">بونوس درگاه‌ها</div>
						</div>
					</div>

					<div style="font-size: 13.5px; color: #ddd; line-height: 1.7; margin-bottom: 18px;">
						⚡ پرداخت آنی &nbsp;•&nbsp; پشتیبانی ۲۴ ساعته<br>
						ضرایب بالا و بازی‌های متنوع
					</div>

					<a href="{site_url}payment/credit" onclick="closeShahbetSplash()" style="display: inline-block; background: linear-gradient(90deg, #ffd33b, #f0c020); color: #111; font-weight: bold; padding: 11px 28px; border-radius: 30px; text-decoration: none; font-size: 14px; box-shadow: 0 4px 15px rgba(255, 211, 59, 0.3);">
						همین حالا شروع کنید
					</a>

				</div>
			</div>
		</div>
	</div>
	<!-- ========== پایان اسپلش ========== -->

	<script type="text/javascript">
		jQuery(document).ready(function(){

			// راهنما
			jQuery('.help-question-div').hover(function(){
				jQuery('.help-question-div .tip-help').css('display', 'inline-block');
				jQuery('.help-question-div .close-help-div').css('display', 'block');
			}, function(){
				jQuery('.help-question-div .tip-help').css('display', 'none');
				jQuery('.help-question-div .close-help-div').css('display', 'none');
			});

			jQuery('.close-help-div').click(function(){
				jQuery(this).unbind('click');
				jQuery(this).parent().fadeOut(500, function(){
					jQuery(this).remove();
				});
			});

			jQuery('.header-sub.desktop .litem').hover(function(){
				jQuery(this).find('.menu-mini-icons').fadeIn(500);
			}, function(){
				jQuery(this).find('.menu-mini-icons').hide();
			});

			// ========== کنترل اسپلش (فقط هر ۱ ساعت یکبار) ==========
			function openShahbetSplash() {
				jQuery('#shahbetSplash').fadeIn(350);
				jQuery('body').css('overflow', 'hidden');
			}

			function closeShahbetSplash() {
				jQuery('#shahbetSplash').fadeOut(300);
				jQuery('body').css('overflow', '');

				// ذخیره وضعیت + انقضا ۱ ساعته
				localStorage.setItem('shahbet_splash_seen', '1');
				localStorage.setItem('shahbet_splash_expire', Date.now() + (1 * 60 * 60 * 1000));
			}

			// بررسی وضعیت
			var seen = localStorage.getItem('shahbet_splash_seen');
			var expire = localStorage.getItem('shahbet_splash_expire');

			if (!seen || !expire || Date.now() > parseInt(expire)) {
				setTimeout(function(){
					openShahbetSplash();
				}, 900);
			}

			// بستن با کلیک روی پس‌زمینه
			jQuery(document).on('click', '#shahbetSplash', function(e) {
				if (e.target === this) {
					closeShahbetSplash();
				}
			});

			// بستن با ضربدر
			jQuery('.splash-close-button').on('click', function(){
				closeShahbetSplash();
			});

		});
	</script>
</body>