<script setup>
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

defineProps({ customers: Object });

const form = useForm({
    name: '',
    phone: '',
    is_chronic: false,
    credit_limit: null,
});

function add() {
    form.post('/customers', { preserveScroll: true, onSuccess: () => form.reset() });
}
</script>

<template>
    <AppLayout title="Customers">
        <form class="grid md:grid-cols-5 gap-3 mb-8 bg-white p-4 rounded-xl border border-slate-200" @submit.prevent="add">
            <input v-model="form.name" placeholder="Name" class="border rounded-lg px-3 py-2 text-sm" required />
            <input v-model="form.phone" placeholder="Phone" class="border rounded-lg px-3 py-2 text-sm" />
            <label class="flex items-center gap-2 text-sm">
                <input v-model="form.is_chronic" type="checkbox" />
                Chronic
            </label>
            <input v-model.number="form.credit_limit" type="number" min="0" step="0.01" placeholder="Credit limit" class="border rounded-lg px-3 py-2 text-sm" />
            <button type="submit" class="rounded-lg bg-emerald-600 text-white text-sm font-medium" :disabled="form.processing">Add</button>
        </form>
        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-left text-slate-600">
                    <tr>
                        <th class="px-4 py-3">Name</th>
                        <th class="px-4 py-3">Phone</th>
                        <th class="px-4 py-3">Chronic</th>
                        <th class="px-4 py-3">Balance</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="c in customers.data" :key="c.id" class="border-t border-slate-100">
                        <td class="px-4 py-3 font-medium">{{ c.name }}</td>
                        <td class="px-4 py-3">{{ c.phone }}</td>
                        <td class="px-4 py-3">{{ c.is_chronic ? 'Yes' : 'No' }}</td>
                        <td class="px-4 py-3">{{ Number(c.balance_due).toFixed(2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AppLayout>
</template>
