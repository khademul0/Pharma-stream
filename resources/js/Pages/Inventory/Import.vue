<script setup>
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const form = useForm({
    file: null,
});

function submit() {
    form.post('/inventory/import', { forceFormData: true });
}
</script>

<template>
    <AppLayout title="Bulk import">
        <div class="max-w-xl bg-white rounded-xl border border-slate-200 p-6 shadow-sm space-y-4">
            <p class="text-sm text-slate-600">
                CSV / Excel columns: name, generic_name, barcode, unit_type, conversion_rate, units_per_strip, strips_per_box,
                low_stock_threshold, supplier_name, supplier_phone, batch_number, expiry_date, cost_price, selling_price, stock_qty
            </p>
            <form @submit.prevent="submit">
                <input type="file" accept=".csv,.txt,.xlsx,.xls" class="block w-full text-sm" @input="form.file = $event.target.files[0]" />
                <button type="submit" class="mt-4 px-4 py-2 rounded-lg bg-emerald-600 text-white text-sm font-medium" :disabled="form.processing || !form.file">
                    Upload
                </button>
            </form>
        </div>
    </AppLayout>
</template>
