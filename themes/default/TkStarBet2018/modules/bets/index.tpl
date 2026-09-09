<style type="text/css">
    .blink-green {
        background-color: green;
        -webkit-animation: 1s linear infinite condemned_blink_effect_green;
        animation: 1s linear infinite condemned_blink_effect_green;
    }
    .blink-red {
        background-color: red;
        -webkit-animation: 1s linear infinite condemned_blink_effect_red;
        animation: 1s linear infinite condemned_blink_effect_red;
    }
    @-webkit-keyframes condemned_blink_effect_red {
        0% {
            background-color: red;
        }
        50% {
            background-color: red;
        }
        100% {
            background-color: #383838;
        }
    }
    @keyframes condemned_blink_effect_red {
        0% {
            background-color: red;
        }
        50% {
            background-color: red;
        }
        100% {
            background-color: #383838;
        }
    }
    @-webkit-keyframes condemned_blink_effect_green {
        0% {
            background-color: green;
        }
        50% {
            background-color: green;
        }
        100% {
            background-color: #383838;
        }
    }
    @keyframes condemned_blink_effect_green {
        0% {
            background-color: green;
        }
        50% {
            background-color: green;
        }
        100% {
            background-color: #383838;
        }
    }
</style>
<script type="text/javascript">
	$(document).ready(function(){
		$.ajax({
			type: 'GET',
			url: '{site_url}bets/reloadInplayOdds',
			success:function($r){
				jQuery('.inplay-games').html($r);
				intervalBackGrounds();
				prepareButtonEvents();
				if ($.cookie("betslip")){
					var obj = JSON.parse($.cookie("betslip"));
					obj.forEach(function(entry) {
					$(".market-box-10 > [data-runnerid="+entry['runnerid']+"]").addClass("selected");
				});
			}
			}
		});

		setInterval(function(){
			$.ajax({
				type: 'GET',
				url: '{site_url}bets/reloadInplayOdds',
				success: function($r){
					jQuery('.inplay-games').html($r);
					intervalBackGrounds();
					prepareButtonEvents();
					if ($.cookie("betslip")){
						var obj = JSON.parse($.cookie("betslip"));
						obj.forEach(function(entry) {
						$(".market-box-10 > [data-runnerid="+entry['runnerid']+"]").addClass("selected");
					});
				}
				}
			});

		}, 30000);
		setTimeout(function(){
			window.location = window.location;
		}, 180000);
	});
    function intervalBackGrounds(){
        self.intervalBlinkBC = 0;
        var intervalID = setInterval(function(){
			$('.blink-green').removeClass('blink-green');
			clearInterval(intervalID);
        }, 2000);
        self.intervalBlinkBC2 = 0;
        var intervalID2 = setInterval(function(){
			$('.blink-red').removeClass('blink-red');
			clearInterval(intervalID2);
        }, 2000);
    }
</script>
<div>
	<div class="cell">
		<div class="container">
			<div class="maincontent clearfix">
				<div class="content">
					<ul class="odds inplay">
						<li>
							<div class="col-lg-3">
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
								<script type="text/javascript">
									jQuery(document).ready(function(){
										var image_container_width = jQuery(".image-container").width();
										jQuery('.image-container').height(image_container_width / 2);
										jQuery(window).resize(function(){
											jQuery('.image-container').height(image_container_width / 2);
										});
										var splash_total = jQuery('.main-splash .image-container .image').length;
										if(splash_total >= 1){
											setInterval(function(){
												var total = jQuery('.main-splash .image-container .image').length;
												var new_total = parseInt(total - 1);
												var main_splash_data = jQuery('.main-splash').attr('data');
												var active = parseInt(main_splash_data);
												if(active >= new_total){
													jQuery('.main-splash').attr({ data: '0' });
													jQuery('.main-splash .image-container .image').animate({ marginRight: '0%' }, 500);
													return false;
												}else{
													active++;
													var active_node = jQuery('.main-splash .image-container .image')[active];
                                                                                                        var dactive_node = jQuery('.main-splash .image-container .image')[0];
													jQuery(active_node).clearQueue().finish();
													jQuery(active_node).animate({ marginRight: '-100%' }, 500);
													jQuery('.main-splash').attr({ 'data': active});
												}
											}, 5000);
										}
									});
								</script>
								<div class="main-splash" data="0">
									<div class="image-container" style="height: 400px !important; margin-top: 8px !important;">
										<div class="image" style="background-image: url({assets_url}/images/slider/slide1.jpg); margin-left: 0%;"></div>
										<div class="image" style="background-image: url({assets_url}/images/slider/slide2.jpg); margin-left: 0%;"></div>
										<!--<div class="image" style="background-image: url({assets_url}/images/slider/sport.jpg); margin-left: 0%;"></div> -->
										<!-- <a target="_blank" href="https://t.me/vipcasino90"><div class="image" style="background-image: url({assets_url}/images/slider/telegram.jpg); margin-left: 0%;"></div></a> -->
										<!-- <a target="_blank" href="https://instagram.com/vipcasino90"><div class="image" style="background-image: url({assets_url}/images/slider/instagram.jpg); margin-left: 0%;"></div></a> -->
									</div>
								</div>				
								<div class="events-holder" style="margin-top: 10px !important;">
									<div class="search-div"><span class="fa fa-search"></span><input type="text" id="search-box" placeholder="&#1580;&#1587;&#1578;&#1580;&#1608;"></div>
									<div class="mt10 fs0">&nbsp;</div>
									<div class="inplay-games">
										<div align="center" style="padding: 20px !important;">
											<i class="fa fa-spinner fa-pulse fa-5x"></i>
										</div>
									</div>
								</div>
								<div class="alert alert-info no-matches-found-for-search" style="text-align: center !important; display: none; margin-top: 10px !important;">&#1607;&#1740;&#1670; &#1606;&#1578;&#1740;&#1580;&#1607; &#1575;&#1740; &#1605;&#1591;&#1575;&#1576;&#1602; &#1576;&#1575; &#1580;&#1587;&#1578;&#1608;&#1580;&#1608;&#1740; &#1588;&#1605;&#1575; &#1740;&#1575;&#1601;&#1578; &#1606;&#1588;&#1583; !</div>
							</div>
							<div class="col-lg-3 ">
								<div id="mobileTW" class="desktop" style="margin-top: 10px ">
									<table class="livescore betslip">
										<tbody>
											<tr><th style="color: #ffd33b  !important;">&#1662;&#1740;&#1588; &#1576;&#1740;&#1606;&#1740; &#1607;&#1575;&#1740; &#1605;&#1606;</th></tr>
											<tr>
												<td>
													<div class="nobet">&#1576;&#1585;&#1575;&#1740; &#1662;&#1740;&#1588; &#1576;&#1740;&#1606;&#1740;&#1548; &#1590;&#1585;&#1740;&#1576; &#1576;&#1575;&#1586;&#1740; &#1605;&#1608;&#1585;&#1583; &#1606;&#1592;&#1585; &#1582;&#1608;&#1583; &#1585;&#1575; &#1575;&#1606;&#1578;&#1582;&#1575;&#1576; &#1705;&#1606;&#1740;&#1583;</div>
													<div class="selectedodds"><div class="betlist"></div></div>
												</td>
											</tr>
										</tbody>
									</table>
									<div class="bettotal" style="display: none; width: 100%;">
										<table class="livescore multiple"></table>
										<ul class="bettotal">
											<li>&#1605;&#1576;&#1604;&#1594; &#1588;&#1585;&#1591;: <span class="totalstake">0</span> &#1578;&#1608;&#1605;&#1575;&#1606;</li>
											<li>&#1576;&#1585;&#1583; &#1575;&#1581;&#1578;&#1605;&#1575;&#1604;&#1740;: <span class="totalwin">0</span> &#1578;&#1608;&#1605;&#1575;&#1606;</li>
										</ul>
										<table class="livescore" style="width:100% !important;">
											<tbody>
												<tr>
													<td>
														<button style="height: 40px !important;" class="deleteall form-button red-button" href="javascript:void(0)">&#1581;&#1584;&#1601; &#1607;&#1605;&#1607;</button>
														<button style="height: 40px !important;" class="placebet form-button disabled">&#1579;&#1576;&#1578; &#1588;&#1585;&#1591;</button>
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
					</ul>
				</div>
			</div>
		</div>
	</div>
</div>
</div>
<script>


</script>