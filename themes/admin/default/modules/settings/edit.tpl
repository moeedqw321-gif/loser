<div id="page_content_inner">
    <h3 class="heading_a title-top uk-margin-small-bottom">تنظیمات کلی سیستم</h3>
    <div class="md-card uk-margin-medium-bottom">
        <div class="md-card-content">
            {form_open(null, 'id="settings_form"')}
            <div data-uk-grid-margin="" class="uk-grid">
                <div class="uk-width-medium-1-1">
                    <div class="md-card" data-uk-grid-margin>
                        <div class="md-card-content">
                            {literal}
                                <ul class="uk-tab" data-uk-tab="{connect:'#settings_tabs', animation:'slide-right'}">
                                    <li class="uk-active"><a href="#">عمومی</a></li>
                                    <li><a href="#">مالی</a></li>
                                </ul>
                            {/literal}
                            <ul id="settings_tabs" class="uk-switcher uk-margin">
                                <li>
                                    <div class="uk-width-medium-1-2 uk-float-right uk-margin-top">
                                        <div class="uk-width-medium-5-6 uk-margin-large-bottom">
                                            <div class="uk-form-row">
                                                <label class="uk-form-label" for="site_name">عنوان سایت</label>
                                                <input type="text" maxlength="60" id="site_name" class="md-input" name="site_name" value="{$site_name}">
                                            </div>
                                        </div>
                                        <div class="uk-width-medium-5-6 uk-margin-large-bottom">
                                            <div class="uk-form-row">
                                                <label class="uk-form-label" for="homepage">صفحه اصلی</label>
                                                <select name="homepage" id="homepage" class="md-input">
                                                    <option value="">انتخاب کنید</option>
                                                    {foreach from=$Pages item=page}
                                                        <option value="{$page->id}" {if isset($homepage) and $homepage->value eq $page->id}selected{/if}>{$page->name}</option>
                                                    {/foreach}
                                                </select>
                                            </div>
                                        </div>
                                        <div class="uk-width-medium-5-6 uk-margin-large-bottom">
                                            <div class="uk-form-row">
                                                <label class="uk-form-label" for="custom_error_page">صفحه خطای 404</label>
                                                <select name="custom_error_page" id="custom_error_page" class="md-input">
                                                    <option value="">انتخاب کنید</option>
                                                    {foreach from=$Pages item=page}
                                                        <option value="{$page->id}" {if isset($custom_error_page) and $custom_error_page->value eq $page->id}selected{/if}>{$page->name}</option>
                                                    {/foreach}
                                                </select>
                                            </div>
                                        </div>
                                        <div class="uk-width-medium-5-6 uk-margin-large-bottom">
                                            <div class="uk-form-row">
                                                <label class="uk-form-label" for="footer">متن فوتر</label>
                                                <input type="text" id="footer" class="md-input" name="footer" value="{$footer}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="uk-width-medium-1-2 uk-float-left uk-margin-top">
                                        <div class="uk-width-medium-5-6 uk-margin-small-bottom">
                                            <div class="uk-form-row">
                                                <label class="uk-form-label">وضعیت سایت</label>
                                                <span class="icheck-inline">
                                                    <input type="radio" id="site_status0" name="site_status" value="0" data-md-icheck {if $site_status eq 0}checked{/if}>
                                                    <label for="site_status0" class="inline-label">در حال بروزرسانی</label>
                                                </span>
                                                <span class="icheck-inline">
                                                    <input type="radio" id="site_status1" name="site_status" value="1" data-md-icheck {if $site_status eq 1}checked{/if}>
                                                    <label for="site_status1" class="inline-label">آنلاین</label>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="uk-width-medium-1-2 uk-float-right uk-margin-top">
                                        <div class="uk-width-medium-5-6 uk-margin-large-bottom">
                                            <div class="uk-form-row">
                                                <label class="uk-form-label" for="card_number">شماره کارت</label>
                                                <input type="text" maxlength="19" id="card_number" class="md-input" name="card_number" value="{$card_number}">
                                            </div>
                                        </div>
                                        <div class="uk-width-medium-5-6 uk-margin-large-bottom">
                                            <div class="uk-form-row">
                                                <label class="uk-form-label" for="card_name">نام دارنده کارت</label>
                                                <input type="text" maxlength="60" id="card_name" class="md-input" name="card_name" value="{$card_name}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="uk-width-medium-1-2 uk-float-left uk-margin-top">
                                        <div class="uk-width-medium-5-6 uk-margin-small-bottom">
                                            <div class="uk-form-row">
                                                <label class="uk-form-label" for="affiliate_count">درصد affiliate</label>
                                                <input type="number" maxlength="60" id="affiliate_count" class="md-input" name="affiliate_count" value="{$affiliate_count}" min="0" max="100">
                                            </div>
                                        </div>
                                        <div class="uk-width-medium-5-6 uk-margin-small-bottom">
                                            <div class="uk-form-row">
                                                <label class="uk-form-label" for="min_amount_withdraw">حداقل مبلغ برداشت (تومان)</label>
                                                <input type="number" id="min_amount_withdraw" class="md-input" name="min_amount_withdraw" value="{$min_amount_withdraw}">
                                            </div>
                                        </div>
                                        <div class="uk-width-medium-5-6 uk-margin-small-bottom">
                                            <div class="uk-form-row">
                                                <label class="uk-form-label" for="max_amount_withdraw">حداکثر مبلغ برداشت (تومان)</label>
                                                <input type="number" id="max_amount_withdraw" class="md-input" name="max_amount_withdraw" value="{$max_amount_withdraw}">
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="uk-width-medium-1-1">
                    <button type="submit" name="save_settings" value="1" class="md-btn md-btn-flat md-btn-success btn-list"><span>ذخیره</span></button>
                </div>
            </div>
            {form_close()}
        </div>
    </div>

    <div class="md-card uk-margin-medium-bottom">
        <div class="md-card-content">
            <h3 class="heading_c">اطلاعات سرور</h3>
            <div class="uk-grid uk-grid-small">
                <div class="uk-width-medium-1-3">
                    <span class="uk-text-bold">امضای سرور:</span> {$server_signature}
                </div>
                <div class="uk-width-medium-1-3">
                    <span class="uk-text-bold">نمایش خطا:</span> {$display_errors}
                </div>
                <div class="uk-width-medium-1-3">
                    <span class="uk-text-bold">post_max_size:</span> {$post_max_size}
                </div>
            </div>
        </div>
    </div>
</div>
