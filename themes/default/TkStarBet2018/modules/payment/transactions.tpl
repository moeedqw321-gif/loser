<div class="container">
	<div class="page-content light">
		<div class="ph15"></div>
		<div class="inline container">
			{include file="partials/dashboard_menu.tpl"}
			<div class="left static-content">
				{if $_GET["pay"] == "true"}
				<div style="margin-top:20px !important;width:100% !important;background-color:alert-success !important;color:white !important;padding:20px !important;">پرداخت با موفقیت انجام شد . موجودی تراکنش به حساب شما افزوده شد</div>
				{elseif $_GET["pay"] == "false"}
				<div style="margin-top:20px !important;width:100% !important;background-color:#ff6600 !important;color:white !important;padding:20px !important;">خطایی در هنگام انجام تراکنش به وجود آمده است . لطفا مجددا تلاش فرمایید . در صورت کسر وجه از حساب شما تا 72 ساعت آینده مبلغ به حسابتان باز میگردد</div>
				{/if}
				<div class="page-area container inline form-container">
					<div class="page-title">سابقه تراکنش ها</div>
					<div class="p7 inline center" style="width: 100% !important;">
						<table class="table nopointer">
							<thead>
								<tr>
									<th>شماره پیگیری تراکنش</th>
									<th>زمان تراکنش</th>
									<th>نوع تراکنش</th>
									<th>مبلغ به تومان</th>
									<th>وضعیت</th>
								</tr>
							</thead>
							<tbody>
							{foreach $transactions as $transaction}
								{$transaction = (object)$transaction}
								<tr>
									<td>{$transaction->trans_id}</td>
									<td>{jdate format='j F Y H:i' date=date('Y/m/d H:i:s', $transaction->created_at)}</td>
									<td>{$transaction->description}</td>
									<td style="direction: ltr !important; color: {if $transaction->price <= -1}orange{else}green{/if} !important;">{$transaction->price|number_format}</td>
									<td>{if $transaction->status == '1' OR $transaction->status == 1}<span class="label label-success" style="width: 200px !important; display: inline-block !important; color: white !important;">پرداخت شده</span>{else}<span class="label label-danger" style="width: 200px !important; display: inline-block !important; color: white !important;"><b>معلق</span>{/if}</td>
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