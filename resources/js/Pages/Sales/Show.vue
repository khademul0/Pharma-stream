<script setup>
import { computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import { usePage } from '@inertiajs/vue3';
import { Printer, Share2, ArrowLeft, Plus } from 'lucide-vue-next';
import { toast } from 'vue3-toastify';

const props = defineProps({
    sale: Object,
});

const page = usePage();
// Access tenant info from page middleware
const tenant = computed(() => page.props.tenant);

const formatDate = (dateStr) => {
    return new Date(dateStr).toLocaleString();
};

const handlePrint = () => {
    window.print();
};

const handleShare = async () => {
    try {
        const shareData = {
            title: `Invoice ${props.sale.sale_number} from ${tenant.value?.name || 'Pharmacy'}`,
            text: `Here is your invoice for a total of $${Number(props.sale.total).toFixed(2)}.`,
            url: window.location.href,
        };

        if (navigator.share) {
            await navigator.share(shareData);
            toast.success('Invoice shared successfully');
        } else {
            // Fallback: Copy URL to clipboard
            await navigator.clipboard.writeText(window.location.href);
            toast.success('Link copied to clipboard (Sharing not supported on this browser)');
        }
    } catch (err) {
        if (err.name !== 'NotAllowedError') {
            console.error('Error sharing:', err);
            toast.error('Failed to share invoice');
        }
    }
};
</script>

<template>
    <Head title="Invoice View" />
    
    <div class="min-h-screen bg-slate-100 flex flex-col py-8 print:py-0 print:bg-white">
        <!-- Action Bar (Hidden when printing) -->
        <div class="max-w-3xl w-full mx-auto mb-6 px-4 print:hidden">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div class="flex items-center gap-3">
                    <Link href="/sales" class="p-2 border rounded bg-white hover:bg-slate-50 text-slate-600 transition-colors">
                        <ArrowLeft class="w-5 h-5" />
                    </Link>
                    <h1 class="text-xl font-bold text-slate-800">Invoice: {{ sale.sale_number }}</h1>
                </div>
                
                <div class="flex items-center gap-2">
                    <Link href="/pos" class="flex items-center gap-1.5 px-3 py-2 bg-slate-800 text-white text-sm font-medium rounded hover:bg-slate-700 transition">
                        <Plus class="w-4 h-4" /> New Sale
                    </Link>
                    <button @click="handlePrint" class="flex items-center gap-1.5 px-3 py-2 bg-emerald-600 text-white text-sm font-medium rounded hover:bg-emerald-700 transition">
                        <Printer class="w-4 h-4" /> Print
                    </button>
                    <button @click="handleShare" class="flex items-center gap-1.5 px-3 py-2 border border-slate-300 bg-white text-slate-700 text-sm font-medium rounded hover:bg-slate-50 transition">
                        <Share2 class="w-4 h-4" /> Share
                    </button>
                </div>
            </div>
        </div>

        <!-- Invoice Paper -->
        <div class="max-w-3xl w-full mx-auto bg-white rounded-xl border border-slate-200 shadow-sm print:shadow-none print:border-none p-8 flex-1">
            
            <!-- Header -->
            <div class="flex justify-between items-start border-b border-slate-200 pb-6 mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-secondary">{{ tenant?.name || 'PharmaStream Clinic' }}</h2>
                    <p class="text-sm text-slate-500 mt-1">Reg: {{ tenant?.slug || 'default' }}</p>
                </div>
                <div class="text-right">
                    <div class="text-3xl font-bold text-slate-200 uppercase tracking-widest print:text-black">INVOICE</div>
                    <div class="text-sm text-slate-500 mt-2 font-mono">{{ sale.sale_number }}</div>
                    <div class="text-sm text-slate-500">{{ formatDate(sale.sold_at) }}</div>
                </div>
            </div>

            <!-- Customer & Payment Info -->
            <div class="grid grid-cols-2 gap-8 mb-8 pb-8 border-b border-slate-200">
                <div>
                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Billed To</h3>
                    <div v-if="sale.customer">
                        <p class="font-medium text-slate-800">{{ sale.customer.name }}</p>
                        <p class="text-sm text-slate-600" v-if="sale.customer.phone">{{ sale.customer.phone }}</p>
                        <p class="text-sm text-slate-600" v-if="sale.customer.email">{{ sale.customer.email }}</p>
                        <span v-if="sale.customer.is_chronic" class="mt-1 inline-block px-2 py-0.5 bg-amber-100 text-amber-800 text-xs font-semibold rounded">Chronic Patient</span>
                    </div>
                    <div v-else>
                        <p class="font-medium text-slate-800 italic">Walk-in Customer</p>
                    </div>
                </div>
                <div>
                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Payment Details</h3>
                    <p class="text-sm"><span class="text-slate-500">Method:</span> <span class="capitalize font-medium">{{ sale.payment_status === 'credit' ? 'Credit Balance' : 'Cash/Card' }}</span></p>
                    <p class="text-sm"><span class="text-slate-500">Status:</span> 
                        <span class="capitalize font-medium ml-1" :class="sale.payment_status === 'paid' ? 'text-emerald-600' : 'text-red-600'">
                            {{ sale.payment_status }}
                        </span>
                    </p>
                    <p class="text-sm mt-2"><span class="text-slate-500">Served By:</span> {{ sale.user ? sale.user.name : 'Unknown' }}</p>
                </div>
            </div>

            <!-- Items Table -->
            <table class="w-full mb-8 text-sm">
                <thead>
                    <tr class="border-b border-slate-200 text-slate-600">
                        <th class="py-2 text-left font-medium">Item Description</th>
                        <th class="py-2 text-center font-medium">Qty</th>
                        <th class="py-2 text-right font-medium">Unit Price</th>
                        <th class="py-2 text-right font-medium">Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <tr v-for="item in sale.items" :key="item.id">
                        <td class="py-3">
                            <p class="font-medium text-slate-800">{{ item.medicine.name }}</p>
                            <p class="text-xs text-slate-500" v-if="item.medicine.generic_name">{{ item.medicine.generic_name }}</p>
                        </td>
                        <td class="py-3 text-center text-slate-600">
                            {{ Number(item.quantity_sold) }} <span class="text-xs">{{ item.sold_unit }}</span>
                        </td>
                        <td class="py-3 text-right text-slate-600">${{ Number(item.unit_price).toFixed(2) }}</td>
                        <td class="py-3 text-right font-medium text-slate-800">${{ Number(item.line_subtotal).toFixed(2) }}</td>
                    </tr>
                </tbody>
            </table>

            <!-- Totals -->
            <div class="flex justify-end pt-4">
                <div class="w-1/2">
                    <div class="flex justify-between py-1.5 text-sm">
                        <span class="text-slate-600">Subtotal</span>
                        <span class="font-medium">${{ Number(sale.subtotal).toFixed(2) }}</span>
                    </div>
                    <div class="flex justify-between py-1.5 text-sm" v-if="Number(sale.discount) > 0">
                        <span class="text-slate-600">Discount</span>
                        <span class="font-medium text-red-600">-${{ Number(sale.discount).toFixed(2) }}</span>
                    </div>
                    <div class="flex justify-between py-1.5 text-sm">
                        <span class="text-slate-600">Tax</span>
                        <span class="font-medium">${{ Number(sale.tax).toFixed(2) }}</span>
                    </div>
                    <div class="flex justify-between py-3 border-t border-b border-slate-200 mt-2 mb-2 font-bold text-lg">
                        <span class="text-slate-800">Total</span>
                        <span class="text-emerald-700">${{ Number(sale.total).toFixed(2) }}</span>
                    </div>
                    <div v-if="sale.payment_status === 'credit'" class="flex justify-between py-1 mb-2 text-sm text-red-600 font-bold">
                        <span>Balance Due</span>
                        <span>${{ Number(sale.balance_due).toFixed(2) }}</span>
                    </div>
                </div>
            </div>

            <!-- Footer Message -->
            <div class="mt-16 pt-8 border-t border-slate-200 text-center">
                <p class="text-sm font-medium text-slate-800">Thank you for your business!</p>
                <p class="text-xs text-slate-500 mt-1">For any queries regarding this invoice, please contact us.</p>
            </div>
            
        </div>
    </div>
</template>

<style>
@media print {
    @page { margin: 0.5cm; }
    body { background-color: white !important; }
    /* Hide layout sidebars if any get dragged in by Inertia */
    aside, nav, header { display: none !important; }
}
</style>
