<script setup>
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

defineProps({
    returns: Object,
    suppliers: Array,
    medicines: Array,
    batches: Array,
});

const form = useForm({
    supplier_id: '',
    medicine_id: '',
    batch_id: '',
    quantity_base_units: 1,
    reason: '',
});

function submit() {
    form.post('/returns', { preserveScroll: true, onSuccess: () => form.reset() });
}
</script>

<template>
    <AppLayout title="Returns manager">
        <form class="grid md:grid-cols-2 gap-4 mb-8 bg-white p-4 rounded-xl border border-slate-200" @submit.prevent="submit">
            <select v-model="form.supplier_id" class="border rounded-lg px-3 py-2 text-sm" required>
                <option value="" disabled>Supplier</option>
                <option v-for="s in suppliers" :key="s.id" :value="s.id">{{ s.name }}</option>
            </select>
            <select v-model="form.medicine_id" class="border rounded-lg px-3 py-2 text-sm" required>
                <option value="" disabled>Medicine</option>
                <option v-for="m in medicines" :key="m.id" :value="m.id">{{ m.name }}</option>
            </select>
            <select v-model="form.batch_id" class="border rounded-lg px-3 py-2 text-sm">
                <option value="">Batch (optional)</option>
                <option v-for="b in batches" :key="b.id" :value="b.id">{{ b.batch_number }} — {{ b.medicine?.name }}</option>
            </select>
            <input v-model.number="form.quantity_base_units" type="number" min="0.0001" step="0.0001" class="border rounded-lg px-3 py-2 text-sm" required />
            <input v-model="form.reason" placeholder="Reason" class="border rounded-lg px-3 py-2 text-sm md:col-span-2" />
            <button type="submit" class="md:col-span-2 rounded-lg bg-emerald-600 text-white text-sm font-medium py-2" :disabled="form.processing">
                Record return
            </button>
        </form>
        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-left text-slate-600">
                    <tr>
                        <th class="px-4 py-3">Supplier</th>
                        <th class="px-4 py-3">Medicine</th>
                        <th class="px-4 py-3">Qty</th>
                        <th class="px-4 py-3">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="r in returns.data" :key="r.id" class="border-t border-slate-100">
                        <td class="px-4 py-3">{{ r.supplier?.name }}</td>
                        <td class="px-4 py-3">{{ r.medicine?.name }}</td>
                        <td class="px-4 py-3">{{ r.quantity_base_units }}</td>
                        <td class="px-4 py-3">{{ r.status }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AppLayout>
</template>
