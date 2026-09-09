	<center class="header-main-center">
		<div class="header-top header desktop">
			<center>
				<div class="mr20">
				{if !$is_logged_in}
					<div class="right mt5">
						<a href="{site_url}users/register" class="link signup">ثبت نام</a>
						<a href="{site_url}users/login" class="link login">ورود به حساب کاربری</a>
					</div>
				{else}
					<div class="right mt5">
						<div class="name">{$user->first_name} {$user->last_name}</div>
						<a href="{site_url}payment/credit" class="balance"><span class="user-balance-place">{$user->cash|price_format}</span></a>
						<a href="{site_url}payment/credit" class="account login">افزایش موجودی</a>
						<a href="{site_url}dashboard" class="account login">حساب کاربری</a>
						<a href="{site_url}users/logout" class="account signup">خروج از حساب کاربری</a>
					</div>
					<div class="clear"></div>
				{/if}
				</div>
			</center>
		</div>
		<div class="header header-wrapper mr20 mb10 desktop top_bar_list">
			<div class="left"><a href="{site_url}"><img src="{assets_url}/images/main_logo.png" style="margin-top: 50px !important;"></a></div>
			<div class="left top-bar-wrapper">
				<div class="top-bar desktop">
					<div class="container inline">
						<a href="{site_url}" class="{if strpos($smarty.server.REQUEST_URI, 'index') !== false OR {$smarty.server.REQUEST_URI} == '' OR {$smarty.server.REQUEST_URI} == '/'}active{/if}">صفحه اصلی</a>
						<a href="{site_url}bets/inplayBet" class="live-tab {if strpos($smarty.server.REQUEST_URI, 'inplayBet') !== false OR strpos($smarty.server.REQUEST_URI, 'InplayOdds') !== false}active{/if}">پیش بینی زنده</a>
						<a href="{site_url}bets/upComing" class="{if strpos($smarty.server.REQUEST_URI, 'upComing') !== false OR strpos($smarty.server.REQUEST_URI, 'preEvents') !== false}active{/if}">پیش بینی پیش از بازی</a>
						<a href="{site_url}users/casino" class="{if strpos($smarty.server.REQUEST_URI, 'casino') !== false}active{/if}">کازینو آنلاین</a>
						<a href="{site_url}contacts/tickets/ticket-list" class="{if strpos($smarty.server.REQUEST_URI, 'contacts/tickets/ticket-list') !== false OR strpos($smarty.server.REQUEST_URI, 'contacts/tickets/new-ticket') !== false}active{/if}">پشتیبانی</a>
						<a href="{site_url}users/help" class="{if strpos($smarty.server.REQUEST_URI, 'users/help') !== false}active{/if}">راهنما</a>
					</div>
				</div>
			</div>
			<div class="clear"></div>
		</div>
		<div class="header header-sub desktop">
			<div class="mr20">
				<a href="{site_url}casino/crash" class="litem"><div class="menu-mini-icons"><img src="{site_url}casino/templates/images/logoes/mini/crash.png" /></div><div class="menu-mini-icons-title">انفجار</div></a>
				<a href="{site_url}casino/baccarat" class="litem"><div class="menu-mini-icons"><img src="{site_url}casino/templates/images/logoes/mini/baccarat.png" /></div><div class="menu-mini-icons-title">باکارات</div></a>
				<a href="{site_url}casino/blackjack" class="litem"><div class="menu-mini-icons"><img src="{site_url}casino/templates/images/logoes/mini/blackjack.png" /></div><div class="menu-mini-icons-title">بلک جک (21)</div></a>
				<a href="{site_url}casino/royal_roulette" class="litem"><div class="menu-mini-icons"><img src="{site_url}casino/templates/images/logoes/mini/royal_roulette.png" /></div><div class="menu-mini-icons-title">رویال رولت</div></a>
				<a href="{site_url}casino/seven_clubs" class="litem"><div class="menu-mini-icons"><img src="{site_url}casino/templates/images/logoes/mini/seven_clubs.png" /></div><div class="menu-mini-icons-title">چهار برگ (پاسور)</div></a>
				<a href="{site_url}casino/two_verdicts" class="litem"><div class="menu-mini-icons"><img src="{site_url}casino/templates/images/logoes/mini/two_verdicts.png" /></div><div class="menu-mini-icons-title">حکم دو نفره</div></a>
				<a href="{site_url}casino/plinko" class="litem"><div class="menu-mini-icons"><img src="{site_url}casino/templates/images/logoes/mini/plinko.png" /></div><div class="menu-mini-icons-title">توپ و سبد</div></a>
				<a href="{site_url}casino/craps" class="litem"><div class="menu-mini-icons"><img src="{site_url}casino/templates/images/logoes/mini/craps.png" /></div><div class="menu-mini-icons-title">زمین و تاس (کرپس)</div></a>
				<a href="{site_url}casino/fortune_wheel" class="litem"><div class="menu-mini-icons"><img src="{site_url}casino/templates/images/logoes/mini/fortune_wheel.png" /></div><div class="menu-mini-icons-title">گردونه شانس</div></a>
				<a href="{site_url}casino/high_low" class="litem"><div class="menu-mini-icons"><img src="{site_url}casino/templates/images/logoes/mini/high_low.png" /></div><div class="menu-mini-icons-title">کمتر بیشتر</div></a>
			</div>
		</div>
		<div class="header container mobile mobile-bar top_bar_list">
			<div class="icon"><a href="javascript:;" class="mobile-menu-action fa fa-bars"></a></div>
<div class="logo" style="position: absolute; left: 50%; transform: translateX(-50%); margin: 0;">
    <a href="/default">
        <img src="{assets_url}/images/main_logo.png" height="40">
    </a>
</div>			<div class="icon" style="float: left !important;"><a href="javascript:;" class="mobile-bet-action fa fa-calculator"><strong id="betTW" class="fa-stack-1x text-primary" style="font-size: 11px;background-color: red;padding: 2.3px;width: auto;margin-top: 6px;border-radius: 10px;color: white;">0</strong></a></div>
		</div>
		<div class="mobile mobile-bar-holder"></div>
	</center>
	<div class="mobile-menu mobile" style="text-align: center !important;">
		{if !$is_logged_in}
			<div class="buttons" style="margin-top: 10px !important;background-color: transparent !important;">
				<a href="{site_url}users/login" class="button1">ورود به حساب کاربری</a>
				<a href="{site_url}users/register" class="button2">ثبت نام</a>
			</div>
		{else}
			<div class="info" style="font-size: 20px !important; width: 100% !important;">
				<span class="name">{$user->first_name} {$user->last_name}</span><br>
				<span class="user-balance-place">{$user->cash|price_format}</span>
			</div>
			<div class="buttons" style="margin-top: 10px !important;background-color: transparent !important;">
				<a href="{site_url}payment/credit" class="button1">افزایش موجودی</a>
				<a href="{site_url}users/logout" class="button2">خروج از حساب کاربری</a>
			</div>
		{/if}
		<div class="items">
			<a href="{site_url}" class="active">صفحه اصلی</a>
			<a href="{site_url}bets/inplayBet" class="active">پیش بینی زنده</a>
			<a href="{site_url}bets/upComing" class="active">پیش بینی پیش از بازی</a>
			<a href="{site_url}casino" class="active">کازینو آنلاین</a>
			<a href="{site_url}contacts/tickets/ticket-list">پشتیبانی</a>
			<a href="{site_url}help">راهنما</a>
			<a href="{site_url}casino/crash" class="active">انفجار</a>
			<a href="{site_url}casino/baccarat">باکارات</a>
			<a href="{site_url}casino/blackjack">بلک جک (21)</a>
			<a href="{site_url}casino/royal_roulette">رویال رولت</a>
			<a href="{site_url}casino/seven_clubs">پاسور</a>
			<a href="{site_url}casino/two_verdicts">حکم</a>
			<a href="{site_url}casino/plinko">توپ و سبد</a>
			<a href="{site_url}casino/crasp">زمین و تاس (کرپس)</a>
			<a href="{site_url}casino/fortune_wheel">گردونه شانس</a>
			<a href="{site_url}casino/high_low">کمتر بیشتر</a>
		</div>
	</div>

	</div>