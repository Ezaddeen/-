{{-- لوحة تحكم مدير المنصة تعرض المؤشرات والرسوم وطلبات المتاجر والطلبات الحديثة. --}}
<div class="platform-dashboard" dir="rtl">
    <div class="platform-dashboard-header">
        <div>
            <span>نظرة عامة على المنصة</span>
            <h1>لوحة تحكم مدير المنصة</h1>
        </div>
        <div class="period-picker" role="group" aria-label="فترة التقرير">
            @foreach ([7 => '7 أيام', 30 => '30 يومًا', 90 => '90 يومًا'] as $days => $label)
                <button type="button" wire:click="setPeriod('{{ $days }}')" @class(['active' => (int) $this->period === $days])>
                    {{ $label }}
                </button>
            @endforeach
        </div>
    </div>

    <section class="dashboard-metrics">
        <article><span>إجمالي المستخدمين</span><strong>{{ number_format($metrics['users']) }}</strong><small>{{ number_format($metrics['customers']) }} عميل</small></article>
        <article><span>المتاجر المعتمدة</span><strong>{{ number_format($metrics['sellers']) }}</strong><small>{{ number_format($metrics['pending_sellers']) }} طلبًا بانتظار المراجعة</small></article>
        <article><span>المنتجات المسجلة</span><strong>{{ number_format($metrics['products']) }}</strong><small>داخل كتالوج المنصة</small></article>
        <article><span>طلبات الفترة</span><strong>{{ number_format($metrics['period_orders']) }}</strong><small>{{ number_format($metrics['period_total']) }} ر.ي</small></article>
        <article><span>مدفوعات تنتظر المراجعة</span><strong>{{ number_format($metrics['pending_payments']) }}</strong><small>لا تغلق دفعة قبل مراجعتها</small></article>
    </section>

    <section class="dashboard-grid">
        <article class="dashboard-panel dashboard-orders-chart">
            <header><div><span>نشاط المنصة</span><h2>الطلبات خلال آخر {{ $period }} يومًا</h2></div></header>
            @php
                $maxValue = max([1, ...$orderSeries['values']]);
            @endphp
            <div class="bar-chart" aria-label="رسم الطلبات">
                @foreach ($orderSeries['values'] as $index => $value)
                    <div class="bar-column" title="{{ $orderSeries['labels'][$index] }}: {{ $value }} طلب">
                        <span style="height: {{ max(4, ($value / $maxValue) * 100) }}%"></span>
                        @if ($index % max(1, (int) ceil(count($orderSeries['values']) / 8)) === 0)
                            <small>{{ $orderSeries['labels'][$index] }}</small>
                        @endif
                    </div>
                @endforeach
            </div>
        </article>

        <article class="dashboard-panel">
            <header><div><span>حالة الطلبات</span><h2>التوزيع الحالي</h2></div></header>
            <div class="status-list">
                @forelse ($statusSummary as $status => $count)
                    <div><span>{{ $status }}</span><strong>{{ number_format($count) }}</strong></div>
                @empty
                    <p class="dashboard-empty">لا توجد طلبات مسجلة بعد.</p>
                @endforelse
            </div>
        </article>
    </section>

    <section class="dashboard-grid dashboard-grid-wide">
        <article class="dashboard-panel">
            <header><div><span>المراجعة</span><h2>طلبات إنشاء المتاجر</h2></div></header>
            @forelse ($pendingApplications as $application)
                <div class="dashboard-application">
                    <div>
                        <strong>{{ $application['shop_name'] }}</strong>
                        <span>{{ $application['applicant'] }} · {{ $application['craft'] }}</span>
                        <small>{{ $application['governorate'] }}، {{ $application['city'] }} · {{ $application['created_at'] }}</small>
                    </div>
                    <div class="application-actions">
                        <button type="button" wire:click="approveApplication({{ $application['id'] }})">اعتماد</button>
                        <details>
                            <summary>رفض</summary>
                            <div>
                                <input type="text" wire:model.defer="rejectionReason" maxlength="500" placeholder="سبب الرفض">
                                <button type="button" wire:click="rejectApplication({{ $application['id'] }}, $wire.rejectionReason)">حفظ الرفض</button>
                            </div>
                        </details>
                    </div>
                </div>
            @empty
                <p class="dashboard-empty">لا توجد طلبات متاجر بانتظار المراجعة.</p>
            @endforelse
        </article>

        <article class="dashboard-panel">
            <header><div><span>الطلبات الحديثة</span><h2>آخر الطلبات المسجلة</h2></div></header>
            <div class="recent-orders">
                @forelse ($recentOrders as $order)
                    <div>
                        <div><strong>{{ $order['reference'] }}</strong><small>{{ $order['customer'] }}</small></div>
                        <span>{{ $order['status'] }}</span>
                        <strong>{{ number_format($order['total']) }} ر.ي</strong>
                    </div>
                @empty
                    <p class="dashboard-empty">لا توجد طلبات مسجلة بعد.</p>
                @endforelse
            </div>
        </article>
    </section>
<style>
.platform-dashboard{font-family:inherit;color:#183c32;direction:rtl}.platform-dashboard-header{display:flex;justify-content:space-between;align-items:flex-end;gap:1rem;margin-bottom:1.25rem}.platform-dashboard-header span,.dashboard-panel header span{color:#738078;font-size:.82rem}.platform-dashboard-header h1{margin:.25rem 0 0;font-size:1.55rem}.period-picker{display:flex;gap:.35rem}.period-picker button{border:1px solid #d9e3dd;background:#fff;color:#52645b;border-radius:.6rem;padding:.55rem .75rem}.period-picker button.active{background:#183c32;color:#fff;border-color:#183c32}.dashboard-metrics{display:grid;grid-template-columns:repeat(5,minmax(0,1fr));gap:.8rem;margin-bottom:.8rem}.dashboard-metrics article{border:1px solid #e1e8e4;border-radius:.8rem;background:#fff;padding:1rem;display:grid;gap:.35rem}.dashboard-metrics article span{color:#69776f;font-size:.85rem}.dashboard-metrics article strong{font-size:1.6rem}.dashboard-metrics article small{color:#87948d}.dashboard-grid{display:grid;grid-template-columns:minmax(0,2fr) minmax(220px,.8fr);gap:.8rem;margin-bottom:.8rem}.dashboard-grid-wide{grid-template-columns:1fr 1fr}.dashboard-panel{border:1px solid #e1e8e4;border-radius:.8rem;background:#fff;padding:1rem}.dashboard-panel header{margin-bottom:1rem}.dashboard-panel header h2{margin:.15rem 0;font-size:1.05rem}.bar-chart{height:220px;display:flex;align-items:flex-end;gap:.25rem;padding-top:.5rem}.bar-column{flex:1;height:100%;display:flex;align-items:flex-end;position:relative}.bar-column span{display:block;width:100%;min-height:4px;border-radius:.35rem .35rem 0 0;background:#69a985}.bar-column small{position:absolute;bottom:-1.25rem;width:100%;text-align:center;font-size:.65rem;color:#7b8781}.status-list{display:grid;gap:.55rem}.status-list div{display:flex;justify-content:space-between;border-bottom:1px solid #eef2ef;padding:.55rem 0}.dashboard-application{display:flex;justify-content:space-between;gap:.75rem;border-bottom:1px solid #eef2ef;padding:.75rem 0}.dashboard-application>div:first-child{display:grid}.dashboard-application span,.dashboard-application small{color:#75827c}.application-actions{display:flex;align-items:flex-start;gap:.35rem}.application-actions button,.application-actions summary{border:1px solid #cfdad4;border-radius:.45rem;background:#fff;padding:.45rem .65rem;cursor:pointer}.application-actions button:first-child{background:#183c32;color:#fff}.application-actions details div{display:grid;gap:.35rem;margin-top:.35rem}.application-actions input{border:1px solid #cfdad4;border-radius:.45rem;padding:.45rem}.recent-orders>div{display:grid;grid-template-columns:minmax(130px,1fr) auto auto;align-items:center;gap:.75rem;border-bottom:1px solid #eef2ef;padding:.65rem 0}.recent-orders>div>div{display:grid}.recent-orders small{color:#75827c}.dashboard-empty{color:#75827c;text-align:center;padding:1.5rem}@media(max-width:1023px){.dashboard-metrics{grid-template-columns:repeat(2,minmax(0,1fr))}.dashboard-grid,.dashboard-grid-wide{grid-template-columns:1fr}.platform-dashboard-header{align-items:flex-start}.recent-orders>div{grid-template-columns:1fr}}@media(max-width:640px){.dashboard-metrics{grid-template-columns:1fr}.platform-dashboard-header{flex-direction:column}.period-picker{width:100%}.period-picker button{flex:1}.dashboard-application{flex-direction:column}}
</style>
</div>
