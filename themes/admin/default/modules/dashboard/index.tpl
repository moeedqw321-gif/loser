<div id="page_content_inner">
    <h3 class="heading_a title-top uk-margin-bottom">{$title}</h3>
    <div class="md-card uk-margin-medium-bottom">
        <div class="md-card-content">
            <p>به داشبورد مدیریتی {setting name="site_name"} خوش آمدید</p>
            <p>برای استفاده از امکانات پنل ، از منوی کناری استفاده کنید.</p>
        </div>
    </div>

    <h4 class="heading_c">آخرین رکوردها</h4>
    {literal}
        <div class="uk-grid uk-grid-medium" data-uk-grid-margin data-uk-grid-match="{target:'.md-card'}">
        {/literal}
        <div class="uk-width-medium-1-3">
            <div class="md-card">
                <div class="md-card-content">
                    <h4>
                        <i class="uk-icon-users uk-icon-small .uk-icon-justify uk-text-success"></i>
                        ({$notif.users|persian_number}) مجموع کاربران
                    </h4>
                    <a href="{site_url()|con:ADMIN_PATH}/users">مشاهده لیست کاربران</a>
                </div>
            </div>
        </div>
        <div class="uk-width-medium-1-3">
            <div class="md-card">
                <div class="md-card-content">
                    <h4>
                        <i class="uk-icon-money uk-icon-small .uk-icon-justify uk-text-success"></i>
                        {$notif.variziEmroz|price_format} مجموع واریزی های امروز‌
                    </h4>
                    <a href="{site_url()|con:ADMIN_PATH}/payment/transactions/credit">مشاهده لیست تراکنش‌ها</a>
                </div>
            </div>
        </div>
        <div class="uk-width-medium-1-3">
            <div class="md-card">
                <div class="md-card-content">
                    <h4>
                        <i class="uk-icon-money uk-icon-small .uk-icon-justify uk-text-success"></i>
                        {$notif.sumCashUsers|price_format} مجموع موجودی کاربران
                    </h4>
                </div>
            </div>
        </div>
        <div class="uk-width-medium-1-3">
            <div class="md-card">
                <div class="md-card-content">
                    <h4>
                        <i class="uk-icon-money uk-icon-small .uk-icon-justify uk-text-success"></i>
                        {$notif.bardashti|price_format} مجموع برداشت‌های کاربرها
                    </h4>
                    <a href="{site_url()|con:ADMIN_PATH}/payment/transactions/withdraw">مشاهده لیست تراکنش‌ها</a>
                </div>
            </div>
        </div>
    </div>
</div>
