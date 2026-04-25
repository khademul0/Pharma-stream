<script setup>
import { router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    from: String,
    to: String,
    revenue: Number,
    cost: Number,
    profit: Number,
});

function apply(e, field) {
    const params = { from: props.from, to: props.to, [field]: e.target.value };
    router.get('/reports/profit-loss', params, { preserveState: true });
}
</script>

<template>
    <AppLayout title="Profit & loss">
        <div class="flex gap-4 mb-6">
            <label class="text-sm">From <input type="date" :value="from" class="ml-2 border rounded-lg px-2 py-1" @change="apply($event, 'from')" /></label>
            <label class="text-sm">To <input type="date" :value="to" class="ml-2 border rounded-lg px-2 py-1" @change="apply($event, 'to')" /></label>
        </div>
        <div class="max-w-lg bg-white rounded-xl border border-slate-200 p-6 shadow-sm space-y-2 text-sm">
            <div class="flex justify-between"><span>Revenue</span><span>${{ revenue.toFixed(2) }}</span></div>
            <div class="flex justify-between"><span>Cost (batch)</span><span>${{ cost.toFixed(2) }}</span></div>
            <div class="flex justify-between font-semibold text-lg text-emerald-700 border-t pt-2"><span>Profit</span><span>${{ profit.toFixed(2) }}</span></div>
        </div>
    </AppLayout>
</template>
