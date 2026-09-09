<div class="container">
	<div class="page-content light">
		<div class="ph15"></div>
		<div class="inline container">
			{include file="partials/dashboard_menu.tpl"}
			<div class="left static-content">
				<div class="page-area container inline form-container">
					<div class="page-title">سابقه کارت به کارت ها</div>
					<div class="p7 inline center" style="width: 100% !important;">
						<table class="table nopointer">
							<thead>
								<tr>
									<th>کد پیگیری</th>
									<th>زمان ثبت</th>
									<th>زمان تراکنش</th>
									<th>شماره کارت(چهار رقم اخر)</th>
									<th>نوع تراکنش</th>
									<th>مبلغ به تومان</th>
									<th>وضعیت</th>
								</tr>
							</thead>
							<tbody>
							{foreach $cardtocard as $transaction}
								{$transaction = (object)$transaction}
								<tr>
									<td>{$transaction->trans_id}</td>
									<td>{jdate format='j F Y H:i' date=date('Y/m/d H:i:s', $transaction->created_at)}</td>
									<td>{$transaction->c_time_pay}</td>
									<td>{$transaction->c_card_number}</td>
									<td>{$transaction->description}</td>
									<td style="direction: ltr !important; color: {if $transaction->price <= -1}orange{else}green{/if} !important;">{$transaction->price|number_format}</td>
									<td>{if $transaction->status == '1' OR $transaction->status == 1}<span class="label label-success" style="padding: 5px; width: auto !important; display: inline-block !important; color: white !important;">پرداخت شده</span>{elseif $transaction->status == '2' OR $transaction->status == 2}<span class="label label-warning" style="padding: 5px; width: auto !important; display: inline-block !important; color: white !important;"><b>در حال پیگیری</span>{else}<span class="label label-danger" style="padding: 5px; width: auto !important;display: inline-block !important; color: white !important;"><b>تقلبی</span>{/if}</td>
								</tr>
							{/foreach}
							</tbody>
						</table>
					</div>
                </div>
            </div>
        </div>
    </div>
</div>