<div class="container">
	<div class="page-content light">
		<div class="ph15"></div>
		
		<div class="inline container">
			<style>
				/* =========================================================
				   منوی دسکتاپ - ORIGINAL
				   ========================================================= */
				.tplm0lnk {
					display: block;
					padding: 11px 14px !important;
					text-align: right !important;
					margin-bottom: 5px;
					border-radius: 6px;
					color: #ddd !important;
					transition: all 0.2s ease;
					font-size: 13.5px;
				}
				.tplm0lnk:hover {
					background: rgba(255, 211, 59, 0.12) !important;
					color: #ffd33b !important;
				}
				.tplm0lnk.active {
					background: rgba(255, 211, 59, 0.18) !important;
					color: #ffd33b !important;
					font-weight: 500;
				}
				.tplm0ico {
					font-size: 14px;
					margin-left: 9px;
					background: rgba(0,0,0,0.4);
					padding: 6px 7px;
					border-radius: 5px;
					color: #ffd33b;
				}

				/* =========================================================
				   منوی موبایل افقی - ORIGINAL
				   ========================================================= */
				.tpum0cnt {
					width: 100%;
					overflow-x: auto;
					-ms-overflow-style: none;
					scrollbar-width: none;
				}
				.tpum0cnt::-webkit-scrollbar {
					display: none;
				}
				.tpum0lnk {
					width: 84px;
					padding: 8px 4px !important;
					border-radius: 8px;
					text-align: center;
				}
				.tpum0ico {
					font-size: 18px;
					width: 42px;
					height: 42px;
					background: rgba(0, 0, 0, 0.5);
					border-radius: 8px;
					line-height: 42px;
					color: #ffd33b;
					margin: 0 auto;
				}
				.tpum0txt {
					font-size: 10px !important;
					margin-top: 6px;
					color: #eee;
					white-space: nowrap;
					overflow: hidden;
					text-overflow: ellipsis;
				}

				/* =========================================================
				   چیدمان اصلی - دسکتاپ (قوی و ثابت)
				   ========================================================= */
				.inline.container {
					width: 100% !important;
					max-width: 100% !important;
					box-sizing: border-box !important;
					overflow: hidden;
				}

				/* منو سمت راست - فقط دسکتاپ */
				.inline.container > .static-menu {
					float: right !important;
					width: 220px !important;
					max-width: 220px !important;
					min-width: 220px !important;
					box-sizing: border-box !important;
					margin: 0 !important;
					padding: 0 !important;
					direction: rtl !important;
					text-align: right !important;
				}

				/* محتوای اصلی کنار منو */
				.inline.container > .static-content {
					float: right !important;
					width: calc(100% - 235px) !important;
					max-width: calc(100% - 235px) !important;
					min-width: 0 !important;
					box-sizing: border-box !important;
					margin: 0 !important;
					padding: 0 !important;
				}

				/* داخل منو */
				.static-menu .link-container,
				.static-menu .desktop {
					width: 100% !important;
					max-width: 100% !important;
					box-sizing: border-box !important;
					margin: 0 !important;
					padding: 0 !important;
				}
				.static-menu .desktop .tplm0lnk {
					width: 100% !important;
					box-sizing: border-box !important;
					text-align: right !important;
					margin: 0 0 5px 0 !important;
				}

				/* جلوگیری از بیرون‌زدگی */
				.static-menu,
				.static-menu *,
				.static-content,
				.static-content * {
					box-sizing: border-box;
				}

				/* عنوان صفحه */
				.static-content .page-area {
					width: 100% !important;
					max-width: 100% !important;
					box-sizing: border-box !important;
				}
				.static-content .page-title {
					text-align: right !important;
					direction: rtl !important;
				}

				/* باکس اطلاعات حساب */
				.static-content .p30.inline {
					width: 100% !important;
					max-width: 100% !important;
					box-sizing: border-box !important;
					text-align: right !important;
					direction: rtl !important;
					padding: 28px 22px !important;
				}

				/* اطلاعات کاربر */
				.account-page-container {
					text-align: right !important;
					float: none !important;
					width: 100% !important;
					line-height: 1.95;
					font-size: 14px;
					color: #e0e0e0;
				}
				.account-page-container .bold {
					font-weight: 600;
					color: #ffd33b;
				}
				.account-page-container .mt15 {
					margin-top: 12px;
				}
				.account-page-container .mt30 {
					margin-top: 24px;
				}
				.account-page-container a.tdn {
					text-decoration: none;
					color: #ff0000 !important;
					font-weight: 500;
				}
				.account-page-container a.tdn:hover {
					color: #ffe066 !important;
				}

				/* فاصله‌های تمیز */
				.moeed-user-info {
					text-align: right !important;
					direction: rtl !important;
					padding-top: 8px !important;
					padding-bottom: 8px !important;
				}
				.moeed-user-name {
					margin-bottom: 18px !important;
				}
				.moeed-info-row {
					margin-bottom: 14px !important;
					line-height: 2.1 !important;
				}
				.moeed-info-row:last-child {
					margin-bottom: 0 !important;
				}

				/* لینک شارژ حساب - قرمز */
				.account-page-container .moeed-account-link,
				.moeed-account-link {
					display: inline-block !important;
					color: #ff3b3b !important;
					font-weight: 600 !important;
					text-decoration: none !important;
					margin-right: 8px !important;
					white-space: nowrap !important;
					transition: all 0.2s ease !important;
				}
				.account-page-container .moeed-account-link .fa,
				.moeed-account-link .fa {
					color: #ff3b3b !important;
					margin-left: 4px !important;
				}
				.account-page-container .moeed-account-link:hover,
				.moeed-account-link:hover {
					color: #ff6666 !important;
				}
				.account-page-container .moeed-account-link:hover .fa,
				.moeed-account-link:hover .fa {
					color: #ff6666 !important;
				}

				/* =========================================================
				   MOBILE فقط وقتی عرض کم باشه
				   ========================================================= */
				@media (max-width: 768px) {
					.inline.container > .static-menu,
					.inline.container > .static-content {
						float: none !important;
						width: 100% !important;
						max-width: 100% !important;
						min-width: 0 !important;
						margin: 0 !important;
						padding: 0 !important;
					}

					.account-page-container {
						width: 100% !important;
						float: none !important;
						text-align: right !important;
					}

					.static-menu .tpum0cnt {
						width: 100% !important;
						max-width: 100% !important;
						overflow-x: auto !important;
						overflow-y: hidden !important;
						direction: rtl !important;
					}
					.static-menu .tpum0cnt > div {
						width: 940px !important;
						min-width: 940px !important;
						max-width: none !important;
					}

					.static-menu .mobile .link-container {
						width: 100% !important;
						max-width: 100% !important;
					}

					.moeed-user-info {
						width: 100% !important;
						padding: 4px 2px !important;
					}
					.moeed-info-row {
						margin-bottom: 14px !important;
						line-height: 2 !important;
					}
				}
			</style>

			<!-- =========================================================
			     منوی سمت راست
			     ========================================================= -->
			<div class="left static-menu">
				
				<!-- موبایل سلکت -->
				<div class="mobile">
					<div class="link-container" style="margin-bottom: 5px !important;">
						<select class="selector" onchange="window.location=this.value">
							<option value="{site_url}dashboard" selected>حساب کاربری</option>
							<option value="{site_url}bets/myrecords">پیش بینی های من</option>
							<option value="{site_url}payment/transactions">سابقه تراکنش ها</option>
							<option value="{site_url}payment/cardtocard">کارت به کارت ها</option>
							<option value="{site_url}payment/credit">شارژ حساب</option>
							<option value="{site_url}users/withdraw">درخواست جایزه</option>
							<option value="{site_url}users/representation">طرح نمایندگی</option>
							<option value="{site_url}users/profile">پروفایل من</option>
							<option value="{site_url}contacts/tickets/ticket-list">پشتیبانی</option>
							<option value="{site_url}users/logout">خروج از حساب کاربری</option>
						</select>
					</div>
					<div class="page-area" style="width: 100% !important; margin: 5px 0 -5px 0; box-sizing: border-box !important;">
	<div class="page-title">
		منوی حساب کاربری
	</div>
</div>

				<div class="link-container">
					
					<!-- دسکتاپ -->
					<div class="desktop">
						<a href="{site_url}dashboard" class="tplm0lnk active">
							<i class="tplm0ico fa fa-user"></i>
							حساب کاربری
						</a>
						<a href="{site_url}bets/myrecords" class="tplm0lnk">
							<i class="tplm0ico fa fa-list-ol"></i>
							پیش بینی های من
						</a>
						<a href="{site_url}payment/transactions" class="tplm0lnk">
							<i class="tplm0ico fa fa-history"></i>
							سابقه تراکنش ها
						</a>
						<a href="{site_url}payment/cardtocard" class="tplm0lnk">
							<i class="tplm0ico fa fa-credit-card"></i>
							کارت به کارت ها
						</a>
						<a href="{site_url}payment/credit" class="tplm0lnk">
							<i class="tplm0ico fa fa-plus-circle"></i>
							شارژ حساب
						</a>
						<a href="{site_url}users/withdraw" class="tplm0lnk">
							<i class="tplm0ico fa fa-bank"></i>
							درخواست جایزه
						</a>
						<a href="{site_url}users/representation" class="tplm0lnk">
							<i class="tplm0ico fa fa-users"></i>
							طرح نمایندگی
						</a>
						<a href="{site_url}users/profile" class="tplm0lnk">
							<i class="tplm0ico fa fa-cog"></i>
							پروفایل من
						</a>
						<a href="{site_url}contacts/tickets/ticket-list" class="tplm0lnk">
							<i class="tplm0ico fa fa-envelope-open-o"></i>
							پشتیبانی
						</a>
						<a href="{site_url}users/logout" class="tplm0lnk">
							<i class="tplm0ico fa fa-sign-out"></i>
							خروج از حساب کاربری
						</a>
					</div>

					<!-- موبایل افقی -->
					<div class="mobile">
						<div class="tpum0cnt">
							<div style="width: 940px;">
								<a href="{site_url}dashboard" class="left tpum0lnk active">
									<div class="fa fa-user tpum0ico"></div>
									<div class="tpum0txt">حساب کاربری</div>
								</a>
								<a href="{site_url}bets/myrecords" class="left tpum0lnk">
									<div class="fa fa-list-ol tpum0ico"></div>
									<div class="tpum0txt">پیش بینی‌ها</div>
								</a>
								<a href="{site_url}payment/transactions" class="left tpum0lnk">
									<div class="fa fa-history tpum0ico"></div>
									<div class="tpum0txt">تراکنش‌ها</div>
								</a>
								<a href="{site_url}payment/cardtocard" class="left tpum0lnk">
									<div class="fa fa-credit-card tpum0ico"></div>
									<div class="tpum0txt">کارت به کارت</div>
								</a>
								<a href="{site_url}payment/credit" class="left tpum0lnk">
									<div class="fa fa-plus-circle tpum0ico"></div>
									<div class="tpum0txt">شارژ حساب</div>
								</a>
								<a href="{site_url}users/withdraw" class="left tpum0lnk">
									<div class="fa fa-bank tpum0ico"></div>
									<div class="tpum0txt">درخواست جایزه</div>
								</a>
								<a href="{site_url}users/representation" class="left tpum0lnk">
									<div class="fa fa-users tpum0ico"></div>
									<div class="tpum0txt">نمایندگی</div>
								</a>
								<a href="{site_url}users/profile" class="left tpum0lnk">
									<div class="fa fa-cog tpum0ico"></div>
									<div class="tpum0txt">پروفایل</div>
								</a>
								<a href="{site_url}contacts/tickets/ticket-list" class="left tpum0lnk">
									<div class="fa fa-envelope-open-o tpum0ico"></div>
									<div class="tpum0txt">پشتیبانی</div>
								</a>
								<a href="{site_url}users/logout" class="left tpum0lnk">
									<div class="fa fa-sign-out tpum0ico"></div>
									<div class="tpum0txt">خروج</div>
								</a>
								<div class="clear"></div>
							</div>
						</div>
					</div>
					
				</div>
			</div>

			<!-- =========================================================
			     محتوای اصلی
			     ========================================================= -->
			<div class="left static-content">
				<div class="page-area container inline form-container">
					
					<div class="page-title">حساب کاربری</div>
					
					<div class="p30 inline">
						
						<div class="account-page-container">
							<div class="moeed-user-info">
								
								<div class="moeed-user-name" style="font-size: 18px;">
									<span class="name">{$user->first_name} {$user->last_name}</span><br>
								</div>
								
								{if isset($user->nickname) && $user->nickname != ''}
									<div class="moeed-info-row mt15">
										نام مستعار :
										<span class="name">{$user->first_name} {$user->last_name}</span><br>
									</div>
								{/if}
								
								<div class="moeed-info-row mt15">
									سطح :
									<span class="bold">
										{if isset($user->level)}
											{$user->level|persian_number}
										{else}
											۱
										{/if}
									</span>
								</div>
								
								<div class="moeed-info-row mt15">
									موجودی :
									<span class="bold">{$user->cash|price_format}</span>
									&nbsp;
									<a href="{site_url}payment/credit" class="tdn moeed-account-link">
										<span class="fa fa-plus"></span>
										شارژ حساب
									</a>
								</div>
								
								<div class="moeed-info-row mt15">
									موجودی قابل برداشت :
									<span class="bold">{$user->cash|price_format}</span>
								</div>
								
								<div class="mt30">
									<a href="{site_url}users/profile" class="tdn">
										<span class="fa fa-pencil"></span>
										بروزرسانی پروفایل
									</a>
								</div>
								
							</div>
						</div>
						
						<div class="clear"></div>
					</div>
					
				</div>
			</div>
			
			<div class="clear"></div>
		</div>
		
		<div class="ph15"></div>
	</div>
</div>