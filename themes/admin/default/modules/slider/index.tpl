<div class="page-header">
    <div class="page-header-left">
        <nav class="page-breadcrumb">
            <a href="{site_url(ADMIN_PATH)}">داشبورد</a>
            <span class="page-breadcrumb-sep">/</span>
            <span class="page-breadcrumb-current">اسلایدرها</span>
        </nav>
        <h1 class="page-title">اسلایدرها</h1>
        <p class="page-description">مدیریت تصاویر اسلایدر سایت</p>
    </div>
    <div class="page-actions">
        <a class="btn btn-primary" href="{site_url(ADMIN_PATH)}/slider/slider-image/edit">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            افزودن اسلایدر
        </a>
    </div>
</div>

<div class="table-wrapper table-responsive-card">
    <table class="table" id="sliders-table">
        <thead>
            <tr>
                <th style="width:40px;">ترتیب</th>
                <th>تصویر</th>
                <th>عنوان</th>
                <th>زیرنویس</th>
                <th>وضعیت</th>
                <th>تاریخ ایجاد</th>
                <th>عملیات</th>
            </tr>
        </thead>
        <tbody id="sliders-list">
            {foreach from=$Sliders item=val}
            <tr data-id="{$val.id}" class="sortable-row">
                <td data-label="ترتیب" class="handle" style="cursor:move;color:var(--text-muted);font-size:16px;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="9" x2="21" y2="9"/><line x1="3" y1="15" x2="21" y2="15"/></svg>
                </td>
                <td data-label="تصویر">
                    {if $val.desktop_image}
                        <img src="{site_url($val.desktop_image)}" width="120" height="60" style="object-fit:cover;border-radius:var(--radius-md);">
                    {elseif $val.image}
                        <img src="{site_url()|con:'upload/slider/':$val.image}" width="120" height="60" style="object-fit:cover;border-radius:var(--radius-md);">
                    {else}
                        <div style="width:120px;height:60px;background:var(--bg-surface-hover);border-radius:var(--radius-md);display:flex;align-items:center;justify-content:center;color:var(--text-muted);font-size:11px;">بدون تصویر</div>
                    {/if}
                </td>
                <td data-label="عنوان" class="font-semibold">{$val.title|default:$val.subtitle|default:'-'}</td>
                <td data-label="زیرنویس" class="text-muted">{$val.subtitle|default:'-'}</td>
                <td data-label="وضعیت">
                    <label class="form-switch">
                        <input type="checkbox" {if $val.status eq 1}checked{/if} onchange="toggleSliderStatus({$val.id}, this)">
                        <span class="form-switch-slider"></span>
                    </label>
                </td>
                <td data-label="تاریخ" class="text-muted" style="font-size:11px;">{$val.created_at}</td>
                <td data-label="عملیات">
                    <div class="table-actions">
                        <a class="btn btn-sm btn-primary" href="{site_url(ADMIN_PATH)}/slider/slider-image/edit/{$val.id}" title="ویرایش">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                        </a>
                        <a class="btn btn-sm btn-danger" href="{site_url(ADMIN_PATH)}/slider/slider-image/delete/{$val.id}" title="حذف" onclick="event.preventDefault();Admin.confirmDelete('{site_url(ADMIN_PATH)}/slider/slider-image/delete/{$val.id}')">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                        </a>
                    </div>
                </td>
            </tr>
            {/foreach}
        </tbody>
    </table>
</div>

<script>
function toggleSliderStatus(id, el) {
    $.ajax({
        url: ADMIN_URL + '/slider/slider-image/toggle-status/' + id,
        type: 'POST',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                Admin.toast.success(response.message);
            } else {
                el.checked = !el.checked;
            }
        },
        error: function() {
            el.checked = !el.checked;
        }
    });
}
</script>
