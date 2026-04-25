<script setup>
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({ medicines: Array, suppliers: Array });

const form = useForm({
    medicine_id: props.medicines[0]?.id ?? '',
    supplier_id: props.suppliers[0]?.id ?? '',
    batch_number: '',
    expiry_date: '',
    cost_price: 0,
    selling_price: 0,
    current_stock_qty: 0,
});

function submit() {
    form.post('/batches');
}
</script>

<template>
    <AppLayout title="New batch">
        <form class="max-w-xl space-y-4 bg-white p-6 rounded-xl border border-slate-200" @submit.prevent="submit">
            <div>
                <label class="text-sm font-medium">Medicine</label>
                <select v-model="form.medicine_id" class="mt-1 w-full border rounded-lg px-3 py-2 text-sm" required>
                    <option v-for="m in medicines" :key="m.id" :value="m.id">{{ m.name }}</option>
                </select>
            </div>
            <div>
                <label class="text-sm font-medium">Supplier</label>
                <select v-model="form.supplier_id" class="mt-1 w-full border rounded-lg px-3 py-2 text-sm">
                    <option value="">—</option>
                    <option v-for="s in suppliers" :key="s.id" :value="s.id">{{ s.name }}</option>
                </select>
            </div>
            <div>
                <label class="text-sm font-medium">Batch number</label>
                <input v-model="form.batch_number" class="mt-1 w-full border rounded-lg px-3 py-2 text-sm" required />
            </div>
            <div>
                <label class="text-sm font-medium">Expiry date</label>
                <input v-model="form.expiry_date" type="date" class="mt-1 w-full border rounded-lg px-3 py-2 text-sm" required />
            </div>
            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="text-sm font-medium">Cost</label>
                    <input v-model.number="form.cost_price" type="number" step="0.0001" min="0" class="mt-1 w-full border rounded-lg px-3 py-2 text-sm" />
                </div>
                <div>
                    <label class="text-sm font-medium">Selling</label>
                    <input v-model.number="form.selling_price" type="number" step="0.0001" min="0" class="mt-1 w-full border rounded-lg px-3 py-2 text-sm" />
                </div>
                <div>
                    <label class="text-sm font-medium">Stock (base)</label>
                    <input v-model.number="form.current_stock_qty" type="number" step="0.0001" min="0" class="mt-1 w-full border rounded-lg px-3 py-2 text-sm" />
                </div>
            </div>
            <button type="submit" class="px-4 py-2 rounded-lg bg-emerald-600 text-white text-sm font-medium" :disabled="form.processing">Save</button>
        </form>
    </AppLayout>
</template>
