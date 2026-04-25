<script setup>
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

defineProps({ batches: Object });
</script>

<template>
    <AppLayout title="Batches">
        <div class="flex justify-between items-center mb-6">
            <p class="text-sm text-slate-600">FEFO uses earliest expiry first.</p>
            <Link href="/batches/create" class="px-4 py-2 rounded-lg bg-emerald-600 text-white text-sm font-medium">New batch</Link>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-left text-slate-600">
                    <tr>
                        <th class="px-4 py-3">Medicine</th>
                        <th class="px-4 py-3">Batch</th>
                        <th class="px-4 py-3">Expiry</th>
                        <th class="px-4 py-3">Stock</th>
                        <th class="px-4 py-3">Sell</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="b in batches.data" :key="b.id" class="border-t border-slate-100">
                        <td class="px-4 py-3">{{ b.medicine?.name }}</td>
                        <td class="px-4 py-3">{{ b.batch_number }}</td>
                        <td class="px-4 py-3">{{ b.expiry_date }}</td>
                        <td class="px-4 py-3">{{ Number(b.current_stock_qty).toFixed(2) }}</td>
                        <td class="px-4 py-3">{{ Number(b.selling_price).toFixed(2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AppLayout>
</template>
