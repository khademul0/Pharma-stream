<script setup>
import { router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    from: String,
    to: String,
    tax: Number,
});

function apply(e, field) {
    router.get('/reports/tax', { from: props.from, to: props.to, [field]: e.target.value }, { preserveState: true });
}
</script>

<template>
    <AppLayout title="Tax liability">
        <div class="flex gap-4 mb-6">
            <label class="text-sm">From <input type="date" :value="from" class="ml-2 border rounded-lg px-2 py-1" @change="apply($event, 'from')" /></label>
            <label class="text-sm">To <input type="date" :value="to" class="ml-2 border rounded-lg px-2 py-1" @change="apply($event, 'to')" /></label>
        </div>
        <div class="max-w-lg bg-white rounded-xl border border-slate-200 p-6 shadow-sm">
            <div class="text-sm text-slate-600">Collected tax (GST/VAT component)</div>
            <div class="text-3xl font-semibold text-secondary mt-2">${{ tax.toFixed(2) }}</div>
        </div>
    </AppLayout>
</template>
