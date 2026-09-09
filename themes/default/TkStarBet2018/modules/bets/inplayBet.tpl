{add_js file='bundles/updates.js' part='footer'}

<div>
    <div class="cell">
        <div class="container">
               
            <div class="maincontent clearfix">
                <div class="content">
                    <ul class="odds inplay">
                        <li>
                            <i class="fa-loader fa fa-spinner fa-pulse" style="display: none;"></i>
                            <table class="table inplaytable nohover" style="margin-top:3px;">
                                <tbody>
									<tr>
									
									
									
									<div class="sports-info-header-container">
<div class="sports-info-header sports-info-header--sport-4" id="sport-header">
<h1 class="sports-info-header__header">
<div><span class="match_list_date">پیش بینی های زنده</span></div>
</h1>
<div class="pc-container pm-tabs pc-component ui-tabs days">
<a class="active" style="color: rgb(238, 238, 238);" href="{site_url}bets/inplayBet"><span>پیش بینی زنده</span></a>
<a style="padding: 5px 15px; color: rgb(238, 238, 238); margin-left: 4px;" href="{site_url}bets/upComing"><span>امروز</span></a><a style="padding: 5px 15px; color: rgb(238, 238, 238); margin-left: 4px;" href="{site_url}bets/upComing/1"><span>                                                    {jdate format='j F (l) ' date='+1 day' second_date=gmdate('Y-m-d')}
</span></a><a style="padding: 5px 15px; color: rgb(238, 238, 238); margin-left: 4px;" href="{site_url}bets/upComing/2"><span>                                                    {jdate format='j F (l) ' date='+2 day' second_date=gmdate('Y-m-d')}
</span></a> <a class="no-hover" style="background:#273772;color: rgb(238, 238, 238) padding: 5px 10px !important; border-radius: 4px; color: rgb(238, 238, 238);" href="{site_url}bets/myrecords"><span>سابقه پیش بینی</span></a>
</div>
</div>		
									</tr>
                                    {if !empty($sortedByLeague) }
                                        <tr class="branchheader inplayheader">
                                            <th>
                                                <span class="match">
                                                    <strong>فوتبال</strong>
                                                    (<span class="totalevents"></span>)
                                                </span>
                                            </th>
                                            <th colspan="4">

                                            </th>
                                        </tr>
                                        {foreach $sortedByLeague as $league => $matches}
                                            {if $league == 'Soccer'}
                                                {continue}
                                            {/if}
                                            <tr class="inplayheader" data-league="{$league}">
                                                <th><span class="match"><b>{$league|fa}</b></span></th>
                                                <th style="width:44px"><b>میزبان</b></th>
                                                <th style="width:44px"><b>مساوی</b></th>
                                                <th style="width:44px"><b>میهمان</b></th>
                                                <th style="width:25px"></th>
                                            </tr>
                                            {assign var=i value=0}
                                            {foreach $matches as $match}
                                                {if  $match->teams->odds != "" AND isset($match->teams->odds->odd->id)}
                                                    {assign var=FulltimeId value=$match->teams->odds->odd->id}
                                                {else if  $match->teams->odds != "" AND isset($match->teams->odds->odd[0]->id)}
                                                    {assign var=FulltimeId value=$match->teams->odds->odd[0]->id}
                                                {else}
                                                    {assign var=FulltimeId value=0}
                                                {/if}
                                                {if ($match->period != "Finished" AND $match->period != "Not Started") AND $match->teams->odds != "" }
                                                    <tr data-eventid="{$match->id}" data-marketid="" class="odddetails"  data-league="{$league}">
                                                        <td>
                                                            <span class="fa fa-clock-o timer"></span>
                                                            <b class="host ellipsis">{$match->teams->home->name|fa}</b>
                                                            <span class="inplayscore">

                                                                {assign var=scores value=explode('-',$match->result)}
                                                                <span class="hScore">{if isset($scores[0])} {$scores[0]|persian_number}{else}۰{/if}</span> - <span class="aScore">{if isset($scores[1])}{$scores[1]|persian_number}{else}۰{/if}</span>


                                                            </span>
                                                            <b class="guest ellipsis">{$match->teams->away->name|fa}</b>

                                                            {assign var=time value=$match->time}
                                                            <span class="inplaytime">{$time|persian_number}:۰۰</span>
                                                            {if $match->period == 'Paused' > 0 AND $match->time ==45}
                                                                <input class="eninplaytime" type="hidden" value="HT">
                                                            {else if $match->time > 0}
                                                                <input class="eninplaytime" type="hidden" value="{$time}:{$match->id % 100}">
                                                            {/if}
                                                        </td>
                                                        <td style='padding:0' colspan='3'>
                                                            <div class='eventodds'>
                                                                <span class="eventsuspended {if isset($match->teams->odds->odd[0]) AND $match->teams->odds != null }
                                                                {if $match->teams->odds->odd[0]->type[0]->suspend == 0}hidden{/if}{/if}">غیر فعال</span>
                                                            <ul class='mlodds'>

                                                                <li data-prev_odd="{$match->teams->odds->odd[0]->type[0]->odd}"  data-runnerid="{$match->id}-{404}-1x2-1" data-pick="{$match->teams->home->name}" data-points='' class="inplaybtn eventodd {if $match->teams->odds->odd[0]->type[0]->odd <= 1}disabled{/if}"><i></i>
                                                                    <span>
                                                                        {if $match->teams->odds->odd[0]->type[0]->odd > 1}
                                                                            {$match->teams->odds->odd[0]->type[0]->odd}
                                                                        {/if}
                                                                    </span>
                                                                </li>

                                                                <li data-prev_odd="{$match->teams->odds->odd[0]->type[1]->odd}"  data-runnerid="{$match->id}-{404}-1x2-X" data-pick="مساوی" data-points='' class="inplaybtn eventodd {if $match->teams->odds->odd[0]->type[1]->odd <= 1}disabled{/if}"><i></i>
                                                                    <span>
                                                                        {if $match->teams->odds->odd[0]->type[1]->odd > 1}
                                                                            {$match->teams->odds->odd[0]->type[1]->odd}
                                                                        {/if}
                                                                    </span>
                                                                </li>
                                                                <li data-prev_odd="{$match->teams->odds->odd[0]->type[2]->odd}" data-runnerid="{$match->id}-{404}-1x2-2" data-pick="{$match->teams->away->name}" data-points='' class="inplaybtn eventodd {if $match->teams->odds->odd[0]->type[2]->odd <= 1}disabled{/if}">
                                                                    {if $match->teams->odds->odd[0]->type[2]->odd > 1}
                                                                        <span>
                                                                            {$match->teams->odds->odd[0]->type[2]->odd}
                                                                        </span>
                                                                    {/if}
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <a class="has-tip fa fa-plus-circle more" title="شرط های بیشتر" href="{site_url}bets/InplayOdds/{$match->id}"></a>
                                                    </td>
                                                </tr>
                                                {/if}
                                                    {assign var=i value=$i+1}
                                                    {/foreach}
                                                        {/foreach}
                                                            {else}
                                                                <tr>
                                                                    <td>
                                                                        هیچ مسابقه ای برگزار نخواهد شد.
                                                                    </td>
                                                                </tr>
                                                                {/if}
                                                                </tbody>
                                                            </table>
                                                            <input id="lastupdate" type="hidden" value="{$lastUpdated}">
                                                        </li>
                                                        <li class="inplay-controller">
                                                            <div class="margin-left-3px">
                                                                <div>
                                                                    <table class="livescore betslip" style="margin-top:3px;">
                                                                        <tbody><tr><th>پیش بینی های من</th></tr>
                                                                            <tr>
                                                                                <td>
                                                                                    <div class="nobet">
                                                                                        هنوز هیچ شرطی بسته نشده است. برای پیش بینی بروی ضریب مورد نظر خود کلیک کنید.
                                                                                    </div>
                                                                                    <div class="selectedodds">
                                                                                        <div class="betlist">
                                                                                        </div>
                                                                                    </div>
                                                                                </td>
                                                                            </tr>
                                                                        </tbody></table>
			<div class="betlist"></div>
		</div>
	</div>
	<div class="bettotal">
		<table class="livescore multiple"></table>
		<ul class="bettotal">
			<li><span style="font-weight:400;">مجموع مبالغ</span> <span class="totalstake" style="font-weight:400;">0</span></li>
			<li><span>برد احتمالی <span style="font-size:90% !important;padding:0 3px;">(تومان)</span></span> <span class="totalwin">0</span></li>
			<p>
		</ul>
		<div style="padding:30px 0 10px 0;text-align:center;">
			<button class="btn btn-more placebet">ثبت شرط</button>
		</div>
	</div>
                                                                <div class="alertbox alertbox2 hidden"></div>
                                                            </div>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>