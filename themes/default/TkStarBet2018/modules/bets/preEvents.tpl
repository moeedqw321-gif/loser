    <div class="cell">
        <div class="container">
            <div class="maincontent clearfix">
                <div class="content">
                    <ul class="odds inplay">
						<li>
							<div class="col-lg-3">
								<div style="margin-top: 10px !important;" class="sidebar-desktop-casino-games">
									</div>
							</div>
							<div class="col-lg-6">
								<input type="hidden" class="host" value="{$matches->homeTeam->name|fa}">
								<input type="hidden" class="guest" value="{$matches->awayTeam->name|fa}">
								<div class="event-container" style="margin-top: 10px !important;">
									<div class="center border info football">
										<div class="title title-bg">{$leagua->league->name|fa}</div>
										<div class="team team-bg mt15 ts left right-align">
											<div class="mt5 medium home-team text-center">{$matches->homeTeam->name|fa}</div>
											<span class="home-cards"><!--cards--></span>
										</div>
										<div class="left score">
											<div class="score-box score-bg ts left mt15">
												<span class="home-score">{0|persian_number}</span>
												<div class="line-1"></div>
												<div class="line-2"></div>
											</div>
											<div class="score-box score-middle score-bg ts left mt15">
												:
												<div class="line-1"></div>
												<div class="line-2"></div>
											</div>					
											<div class="score-box score-bg ts left mt15">
												<span class="away-score">{0|persian_number}</span>
												<div class="line-1"></div>
												<div class="line-2"></div>
											</div>
											<div class="left time">
												<span class="period">بازی شروع نشده است</span>
												<span class="event-minute"></span>
											</div>
											<div class="clear"></div>
										</div>
										<div class="team team-bg mt15 ts left left-align">
											<div class="mt5 medium away-team text-center">{$matches->awayTeam->name|fa}</div>
											<span class="away-cards"><!--cards--></span>
										</div>
										<div class="clear"></div>
									</div>
									<div class="market-types" style="margin-top: -5px;">
										{foreach $odds as $key => $val}
											{if $val->type eq 'Home/Away' OR $val->type eq '3Way Handicap'}
												{continue}
											{/if}
											<div class="mt5 market-type market-type-32" data="32">
												<a href="javascript:;" class="title box-title-action inplayheader" data-box="market-box-32"><span class="market-name mn-1-32"><b>{$val->type|fa}</b></span></a>
												<div class="odd-container market-box-32 odddetails" data-eventid="{$matches->id}">
													{foreach $val->odds->data as $odd}
														{if $val->type == '1x2'}
															{if $odd->label == 1 OR $odd->label == '1' OR $odd->label == '1X' OR $odd->label == 'X1' OR $odd->label == '1x' OR $odd->label == 'x1'}
																{$oddLabel = $matches->homeTeam->name|fa}
															{else if $odd->label == 2 OR $odd->label == '2' OR $odd->label == '2X' OR $odd->label == 'X2' OR $odd->label == '2x' OR $odd->label == 'x2'}
																{$oddLabel = $matches->awayTeam->name|fa}
															{else}
																{$oddLabel = 'مساوی'}
															{/if}
														{else}
															{if $odd->label == 1 OR $odd->label == '1' OR $odd->label == '1X' OR $odd->label == 'X1' OR $odd->label == '1x' OR $odd->label == 'x1'}
																{$oddLabel = 'میزبان'}
															{else if $odd->label == 2 OR $odd->label == '2' OR $odd->label == '2X' OR $odd->label == 'X2' OR $odd->label == '2x' OR $odd->label == 'x2'}
																{$oddLabel = 'میهمان'}
															{else}
																{if $odd->label == {$matches->homeTeam->name|con:'/':'Draw'}}
																	{$oddLabel = 'میزبان/مساوی'}
																{else if $odd->label == {$matches->awayTeam->name|con:'/':'Draw'} }
																	{$oddLabel = 'میهمان/مساوی'}
																{else if $odd->label == {'Draw'|con:'/':$matches->awayTeam->name} }
																	{$oddLabel = 'مساوی/میهمان'}
																{else if $odd->label == {'Draw'|con:'/':$matches->homeTeam->name} }
																	{$oddLabel = 'مساوی/میزبان'}
																{else if $odd->label == {$matches->awayTeam->name|con:'/':$matches->homeTeam->name} }
																	{$oddLabel = 'میهمان/میزبان'}
																{else if $odd->label == {$matches->homeTeam->name|con:'/':$matches->awayTeam->name} }
																	{$oddLabel = 'میزبان/میهمان'}
																{else if $odd->label == {$matches->homeTeam->name|con:'/':$matches->homeTeam->name} }
																	{$oddLabel = 'میزبان/میزبان'}
																{else if $odd->label == {$matches->awayTeam->name|con:'/':$matches->awayTeam->name} }
																	{$oddLabel = 'میهمان/میهمان'}
																{else if $odd->label == {'Draw/Draw'} }
																	{$oddLabel = 'مساوی/مساوی'}
																{else if $odd->label == 'x' OR $odd->label == 'X' OR $odd->label == '12' OR $odd->label == ':12' OR $odd->label == '::12'}
																	{$oddLabel = 'مساوی'}
																{else}
																	{$oddLabel = $odd->label|fa}
																{/if}
															{/if}
														{/if}
														<a data-runnerid="{$matches->id|con:'-': $matches->odds->data[0]->bookmaker_id:'-': {$val->type} :'-':{$odd->label}}" data-pick="{$oddLabel}" data-points="" class="inplaybtn eventodd odd-link odd-sub-button odd-{if (count($val->odds->data) % 3) == 0}triple{else}double{/if}" href="javascript:;">
															<div class="odd-title"><span>{$oddLabel}</span></div>
															<div class="odd-rate odd-main-button odd-222341759"><span>{$odd->value}</span></div>
														</a>
													{/foreach}
													<div class="clear"></div>
												</div>
											</div>
										{/foreach}
									</div>
								</div>
							</div>
							<div class="col-lg-3">
								<div style="margin-top: 10px !important;">
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
									</div>
							</div>
						</li>
					</ul>
				</div>
			</div>
		</div>
	</div>