<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    sales: Object,
});

const formatDate = (dateStr) => {
    return new Date(dateStr).toLocaleString();
};
</script>

<template>
    <AppLayout title="Sales History">
        <div class="space-y-6">
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-semibold text-secondary">Sales History</h1>
                <Link
                    href="/pos"
                    class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors"
                >
                    New Sale
                </Link>
            </div>

            <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-slate-50 text-slate-600 font-medium border-b border-slate-200">
                            <tr>
                                <th class="px-6 py-4">Date</th>
                                <th class="px-6 py-4">Sale#</th>
                                <th class="px-6 py-4">Customer</th>
                                <th class="px-6 py-4">Subtotal</th>
                                <th class="px-6 py-4">Discount</th>
                                <th class="px-6 py-4">Tax</th>
                                <th class="px-6 py-4">Total</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr v-for="sale in sales.data" :key="sale.id" class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4 text-slate-600">{{ formatDate(sale.sold_at) }}</td>
                                <td class="px-6 py-4 font-mono text-xs text-slate-500">{{ sale.sale_number }}</td>
                                <td class="px-6 py-4 font-medium text-slate-900">{{ sale.customer ? sale.customer.name : 'Walk-in' }}</td>
                                <td class="px-6 py-4">${{ Number(sale.subtotal).toFixed(2) }}</td>
                                <td class="px-6 py-4">${{ Number(sale.discount).toFixed(2) }}</td>
                                <td class="px-6 py-4">${{ Number(sale.tax).toFixed(2) }}</td>
                                <td class="px-6 py-4 font-bold text-emerald-700">${{ Number(sale.total).toFixed(2) }}</td>
                                <td class="px-6 py-4">
                                    <span 
                                        class="px-2 py-0.5 rounded-full text-xs font-medium"
                                        :class="sale.payment_status === 'paid' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800'"
                                    >
                                        {{ sale.payment_status.toUpperCase() }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <Link
                                        :href="`/sales/${sale.id}`"
                                        class="text-blue-600 hover:text-blue-800 font-medium bg-blue-50 px-3 py-1 rounded transition-colors"
                                    >
                                        View Invoice
                                    </Link>
                                </td>
                            </tr>
                            <tr v-if="!sales.data.length">
                                <td colspan="9" class="px-6 py-8 text-center text-slate-500">
                                    No sales records found.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <div class="flex items-center justify-between" v-if="sales.links && sales.links.length > 3">
                <div class="text-sm text-slate-600">
                    Showing {{ sales.from || 0 }} to {{ sales.to || 0 }} of {{ sales.total }} results
                </div>
                <div class="flex gap-1">
                    <Link
                        v-for="(link, idx) in sales.links"
                        :key="idx"
                        :href="link.url || '#'"
                        v-html="link.label"
                        class="px-3 py-1 rounded border text-sm"
                        :class="[
                            link.active ? 'bg-emerald-600 text-white border-emerald-600' : 'bg-white text-slate-700 border-slate-300 hover:bg-slate-50',
                            !link.url && 'opacity-50 cursor-not-allowed'
                        ]"
                    />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
