<div>
	<div class="cell">
		<div class="container">
			<div class="maincontent clearfix">
				<div class="content">
					<div class="odds inplay">
						<li>
							<div class="col-lg-3">
								<div class="left-bar" style="margin-top: 10px !important;">
									<div class="other-bars">
										<a href="javascript:;" class="title box-title-action" data-box="box-100002">روز ها</a>
										<div class="menu-container box-100002">
											<a href="{site_url}bets/upComing" class="link active">{jdate format='j F (l) ' date='+1 day' second_date=gmdate('Y-m-d')}<div class="clear"></div></a>
											<a href="{site_url}bets/upComing/1" class="link active">{jdate format='j F (l) ' date='+2 day' second_date=gmdate('Y-m-d')}<div class="clear"></div></a>
											<a href="{site_url}bets/upComing/2" class="link active">{jdate format='j F (l) ' date='+3 day' second_date=gmdate('Y-m-d')}<div class="clear"></div></a>
											<a href="{site_url}bets/upComing/3" class="link active">{jdate format='j F (l) ' date='+4 day' second_date=gmdate('Y-m-d')}<div class="clear"></div></a>
											<a href="{site_url}bets/upComing/4" class="link active">{jdate format='j F (l) ' date='+5 day' second_date=gmdate('Y-m-d')}<div class="clear"></div></a>
											<div class="clear"></div>
										</div>
									</div>
								</div>
								<div style="margin-top: 10px !important;" class="sidebar-desktop-casino-games">
									<a href="{site_url}casino/crash"><img style="margin-bottom: 10px !important; width: 100%; display: inline;" src="{site_url}casino/templates/images/logoes/crash.gif"></a>
									<a href="{site_url}casino/baccarat"><img style="margin-bottom: 10px !important; width: 100%; display: inline;" src="{site_url}casino/templates/images/logoes/baccarat.gif"></a>
									<a href="{site_url}casino/blackjack"><img style="margin-bottom: 10px !important; width: 100%; display: inline;" src="{site_url}casino/templates/images/logoes/blackjack.gif"></a>
									<a href="{site_url}casino/royal_roulette"><img style="margin-bottom: 10px !important; width: 100%; display: inline;" src="{site_url}casino/templates/images/logoes/royal_roulette.gif"></a>
									<a href="{site_url}casino/seven_clubs"><img style="margin-bottom: 10px !important; width: 100%; display: inline;" src="{site_url}casino/templates/images/logoes/seven_clubs.gif"></a>
									<a href="{site_url}casino/two_verdicts"><img style="margin-bottom: 10px !important; width: 100%; display: inline;" src="{site_url}casino/templates/images/logoes/two_verdicts.gif"></a>
								</div>
							</div>
							<div class="col-lg-6">
								<div class="events-holder" style="margin-top: 10px !important;">
									<div class="search-div"><span class="fa fa-search"></span><input type="text" id="search-box" placeholder="جستجو"></div>
									<div class="mt10 fs0">&nbsp;</div>
									{foreach $CompetitionMatches as $key => $val}
										{foreach $val as $match} 
											{$date1 = DateTime::createFromFormat('Y-m-d H:i:s', {gmt time={$match->starting_date|con:' ':$match->starting_time} format='Y-m-d H:i:s'})}
											{$date2 = DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s'))}
											{if ($day eq 0 )  AND $match->status == 'NS' AND {searchArray key='type' val='1x2' array=$match->odds->data[0]->types->data} != null} 
											   {assign var=gotBreak value=1}
												{break}
											{/if}
											{if ($day != 0 ) AND !empty($match->odds->data) AND $match->status == 'NS' AND {searchArray key='type' val='1x2' array=$match->odds->data[0]->types->data} != null}
												{assign var=gotBreak value=1}
												{break}
											{/if}
											{if $match->status != 'NS'}
												{assign var=gotBreak value=0}
											{/if}
											{if Miladr\Jalali\jDateTime::date('d', strtotime({gmtfa time=$match->starting_date format='Y/m/d'})) == Miladr\Jalali\jDateTime::date('d', time()) AND Miladr\Jalali\jDateTime::date('Y/m/d H:i:s', strtotime({gmtfa time=$match->starting_time format='H:i:s'})) > Miladr\Jalali\jDateTime::date('Y/m/d H:i:s', time())}
												{assign var=gotBreak value=1}
											{/if}
										{/foreach}
										{if $gotBreak == 0}
											{continue}
										{/if}
										<div class="event-row-parent-search sport-categories sport-categories-1">
											<div class="event-type">
												<div class="title">
													<div class="text"><span class="yellow">{$key|fa}</span></div>
													<div class="odd-title">میزبان</div>
													<div class="odd-title">مساوی</div>
													<div class="odd-title">میهمان</div>
													<div class="clear"></div>
												</div>
												<div class="odd-container">
													{foreach $val as $match}
														{$date1 = DateTime::createFromFormat('Y-m-d H:i:s', {gmt time={$match->starting_date|con:' ':$match->starting_time} format='Y-m-d H:i:s'})}
														{$date2 = DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s'))}
														{if Miladr\Jalali\jDateTime::date('d', strtotime({gmtfa time=$match->starting_date format='Y/m/d'})) == Miladr\Jalali\jDateTime::date('d', time()) AND Miladr\Jalali\jDateTime::date('Y/m/d H:i:s', strtotime({gmtfa time=$match->starting_time format='H:i:s'})) < Miladr\Jalali\jDateTime::date('Y/m/d H:i:s', time())}{continue}{/if}
														{$myArray = $match->odds->data[0]->types->data[{searchArray key='type' val='1x2' array=$match->odds->data[0]->types->data}]}
													<div class="event-row event-row-search event-{$match->id}">
														<a href="{site_url}bets/preEvents/{$match->id}" class="event-title">
															<div class="event-time">{gmtfa time=$match->starting_time format='H:i'|persian_number}</div>
															<div class="mt5">
																<div class="left home-team" style="font-size: 12px !important; margin-right: 20px !important;"><span class="host">{$match->homeTeam->name|fa}</span></div>
																<div class="clear"></div>
															</div>
															<div class="mt5">
																<div class="left away-team" style="font-size: 12px !important; margin-right: 20px !important;"><span class="guest">{$match->awayTeam->name|fa}</span></div>
																<div class="clear"></div>
															</div>
															<div class="clear"></div>
														</a>
														<span class="event-odds">
															<div class="market-box-10">
																<a href="javascript:;" data-eventid="{$match->id}" data-runnerid="{$match->id|con:'-': $match->odds->data[0]->bookmaker_id:'-1x2-': {$myArray->odds->data[{searchArray key='label' val='1' array=$myArray->odds->data}]->label}}" data-pick="{$match->homeTeam->name}" data-pick="{$match->homeTeam->name}" data-points="" class="inplaybtn odd-rate odd-main-button odd-link{if $myArray->odds->data[{searchArray key='label' val='1' array=$myArray->odds->data}]->value == ''} passive-ma{/if}"><span>{if empty($myArray->odds->data[{searchArray key='label' val='1' array=$myArray->odds->data}]->value)}<text style="color: white !important;">...</text>{else}{$myArray->odds->data[{searchArray key='label' val='1' array=$myArray->odds->data}]->value}{/if}</span></a>
																<a href="javascript:;" data-eventid="{$match->id}"  data-runnerid="{$match->id|con:'-': $match->odds->data[0]->bookmaker_id:'-1x2-': {$myArray->odds->data[{searchArray key='label' val='X' array=$myArray->odds->data}]->label}}" data-pick="مساوی" data-points="" class="inplaybtn odd-rate odd-main-button odd-link{if $myArray->odds->data[{searchArray key='label' val='X' array=$myArray->odds->data}]->value == ''} passive-ma{/if}"><span>{if empty($myArray->odds->data[{searchArray key='label' val='X' array=$myArray->odds->data}]->value)}<text style="color: white !important;">...</text>{else}{$myArray->odds->data[{searchArray key='label' val='X' array=$myArray->odds->data}]->value}{/if}</span></a>
																<a href="javascript:;" data-eventid="{$match->id}"  data-runnerid="{$match->id|con:'-': $match->odds->data[0]->bookmaker_id:'-1x2-': {$myArray->odds->data[{searchArray key='label' val='2' array=$myArray->odds->data}]->label}}" data-pick="{$match->awayTeam->name}" data-points="" class="inplaybtn odd-rate odd-main-button odd-link{if $myArray->odds->data[{searchArray key='label' val='2' array=$myArray->odds->data}]->value == ''} passive-ma{/if}"><span>{if empty($myArray->odds->data[{searchArray key='label' val='2' array=$myArray->odds->data}]->value)}<text style="color: white !important;">...</text>{else}{$myArray->odds->data[{searchArray key='label' val='2' array=$myArray->odds->data}]->value}{/if}</span></a>
															</div>
														</span>
														<a href="{site_url}bets/preEvents/{$match->id}" class="odd-all has-tip" title="شروط بیشتر"><span class="fa fa-bar-chart"></span></a>
														<div class="clear"></div>
													</div>
													{/foreach}
												</div>
											</div>
										</div>
									{/foreach}
								</div>
								<div class="alert alert-info no-matches-found-for-search" style="text-align: center !important; display: none; margin-top: 10px !important;">هیچ نتیجه ای مطابق با جستوجوی شما یافت نشد !</div>
							</div>
							<div class="col-lg-3">
								<div id="mobileTW" class="desktop" style="margin-top: 10px !important;">
									<table class="livescore betslip">
										<tbody>
											<tr><th style="color: #ffd33b  !important;">پیش بینی های من</th></tr>
											<tr>
												<td>
													<div class="nobet">برای پیش بینی، ضریب بازی مورد نظر خود را انتخاب کنید</div>
													<div class="selectedodds"><div class="betlist"></div><div style="margin-top: 20px !important; display: none;" class="alert alert-danger" id="error_for_mix_form">امکان ثبت شرط میکس بیشتر از 8 تایی وجود ندارد</div></div>
												</td>
											</tr>
										</tbody>
									</table>
									<div class="bettotal" style="display: none; width: 100%;">
										<table class="livescore multiple"></table>
										<ul class="bettotal">
											<li>مبلغ شرط: <span class="totalstake">0</span> تومان</li>
											<li>برد احتمالی: <span class="totalwin">0</span> تومان</li>
										</ul>
										<table class="livescore" style="width:100% !important;">
											<tbody>
												<tr>
													<td>
														<button style="height: 40px !important;" class="deleteall form-button red-button" href="javascript:void(0)">حذف همه</button>
														<button style="height: 40px !important;" class="placebet form-button disabled">ثبت شرط</button>
													</td>
												</tr>
											</tbody>
										</table>
									</div>
								</div>
								<div class="alertbox alertbox2 hidden"></div>
								<div style="margin-top: 10px !important;" class="sidebar-mobile-casino-games">
									<a href="{site_url}casino/crash"><img style="margin-bottom: 10px !important; width: 100%; display: inline;" src="{site_url}casino/templates/images/logoes/crash.gif"></a>
									<a href="{site_url}casino/baccarat"><img style="margin-bottom: 10px !important; width: 100%; display: inline;" src="{site_url}casino/templates/images/logoes/baccarat.gif"></a>
									<a href="{site_url}casino/blackjack"><img style="margin-bottom: 10px !important; width: 100%; display: inline;" src="{site_url}casino/templates/images/logoes/blackjack.gif"></a>
									<a href="{site_url}casino/royal_roulette"><img style="margin-bottom: 10px !important; width: 100%; display: inline;" src="{site_url}casino/templates/images/logoes/royal_roulette.gif"></a>
									<a href="{site_url}casino/seven_clubs"><img style="margin-bottom: 10px !important; width: 100%; display: inline;" src="{site_url}casino/templates/images/logoes/seven_clubs.gif"></a>
									<a href="{site_url}casino/two_verdicts"><img style="margin-bottom: 10px !important; width: 100%; display: inline;" src="{site_url}casino/templates/images/logoes/two_verdicts.gif"></a>
								</div>
							</div>
						</li>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>