<script setup>
import { router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    date: String,
    count: Number,
    subtotal: Number,
    tax: Number,
    discount: Number,
    total: Number,
});

function changeDate(e) {
    router.get('/reports/z', { date: e.target.value }, { preserveState: true });
}
</script>

<template>
    <AppLayout title="Z-Report">
        <div class="max-w-lg bg-white rounded-xl border border-slate-200 p-6 shadow-sm space-y-4">
            <label class="text-sm font-medium">Date</label>
            <input type="date" :value="date" class="border rounded-lg px-3 py-2 text-sm" @change="changeDate" />
            <div class="space-y-2 text-sm border-t border-slate-100 pt-4">
                <div class="flex justify-between"><span>Sales count</span><span>{{ count }}</span></div>
                <div class="flex justify-between"><span>Subtotal</span><span>${{ subtotal.toFixed(2) }}</span></div>
                <div class="flex justify-between"><span>Tax</span><span>${{ tax.toFixed(2) }}</span></div>
                <div class="flex justify-between"><span>Discount</span><span>${{ discount.toFixed(2) }}</span></div>
                <div class="flex justify-between font-semibold text-lg text-emerald-700 border-t border-slate-100 pt-2">
                    <span>Total</span><span>${{ total.toFixed(2) }}</span>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
