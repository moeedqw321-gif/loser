<div id="page_content_inner">
    <h3 class="heading_a title-top">{$title}</h3>
    <div class="md-card uk-margin-medium-bottom">
        <div class="md-card-content">
            <table class="uk-table dataTable uk-table-striped" id="dt_default" role="grid" aria-describedby="dt_default_info">
                <thead>
                    <tr>
                        <th class="sorting">شناسه داخلی تراکنش</th>
			<th class="sorting">کد پیگیری</th>
			<th class="sorting">شماره کارت</th>
			<th class="sorting">زمان واریز</th>
                        <th class="sorting">کاربر</th>
                        <th class="sorting">مبلغ پرداختی</th>
                        <th class="sorting">تاریخ ثبت</th>
                        <th class="sorting">عملیات</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th class="sorting">شناسه داخلی تراکنش</th>
			<th class="sorting">کد پیگیری</th>
			<th class="sorting">شماره کارت</th>
		        <th class="sorting">زمان واریز</th>
                        <th class="sorting">کاربر</th>
                        <th class="sorting">مبلغ پرداختی</th>
                        <th class="sorting">تاریخ ثبت</th>
                        <th class="sorting">عملیات</th>
                    </tr>
                </tfoot>
                <tbody class="uk-table uk-table-striped">
                    {foreach from=$transactions item=val}  
                        <tr>
                            <td>
                                {$val.id}
                            </td>
							<td>
                                {$val.trans_id}
                            </td>
							<td>
                                {$val.c_card_number}
                            </td>
							<td>
                                {$val.c_time_pay}
                            </td>
                            <td>
                                {$val.user.first_name} {$val.user.last_name} <span style="direction:ltr;"> ({$val.user.email})</span>
                            </td>
                            <td>
                                {$val.price|price_format}
							</td>
                            <td>
                                <label style="color:#8fdf82">
                                    {jdate format='j F Y H:i:s' date=$val.created_at}
                                </label>
                            </td>
							<td>
                                <a class="uk-icon-small uk-icon-hover uk-icon-check uk-text-success cardAccept" href="{site_url({ADMIN_PATH|con:'/payment/transactions/cardAccept/':$val.id})}" data-uk-tooltip title="پذیرفتن"></a>

                                <a class="uk-icon-small uk-icon-hover uk-icon-times uk-text-danger cardCancel" href="{site_url({ADMIN_PATH|con:'/payment/transactions/cardCancel/':$val.id})}" data-uk-tooltip title="تقلبی"></a>                            </td>
                        </tr>
                    {/foreach}
                </tbody>
            </table>
        </div>
    </div>
</div>
