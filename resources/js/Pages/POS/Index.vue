<script setup>
import { computed, ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import { toast } from 'vue3-toastify';
import axios from 'axios';
import AppLayout from '@/Layouts/AppLayout.vue';
import { useCartStore } from '@/stores/cart';
import { Search } from 'lucide-vue-next';

const props = defineProps({
    customers: Array,
    taxRate: Number,
});

const cart = useCartStore();
const q = ref('');
const results = ref([]);
const selectedCustomer = ref('');
const discount = ref(0);
const paymentMode = ref('cash');
const prescriptionInput = ref(null);
const soldUnit = ref('tablet');
const qty = ref(1);
const unitPrice = ref(0);
const activeMedicine = ref(null);

let timer;
watch(q, (v) => {
    clearTimeout(timer);
    timer = setTimeout(async () => {
        if (!v || v.length < 2) {
            results.value = [];
            return;
        }
        const { data } = await axios.get('/pos/search', { params: { q: v } });
        results.value = data.items || [];
    }, 250);
});

function pickMedicine(m) {
    activeMedicine.value = m;
    unitPrice.value = m.stock > 0 ? 1 : 0;
    soldUnit.value = 'tablet';
}

function addToCart() {
    if (!activeMedicine.value) {
        toast.error('Select a medicine first.');
        return;
    }
    if (activeMedicine.value.stock <= 0) {
        toast.error('Out of stock — check generic alternatives below.');
        return;
    }
    cart.addLine(activeMedicine.value, soldUnit.value, qty.value, unitPrice.value);
    toast.success('Added to cart');
}

const taxAmount = computed(() => {
    const taxable = Math.max(0, cart.subtotal - Number(discount.value || 0));
    return taxable * props.taxRate;
});

const grandTotal = computed(() => {
    const taxable = Math.max(0, cart.subtotal - Number(discount.value || 0));
    return taxable + taxable * props.taxRate;
});

function checkout() {
    if (!cart.lines.length) {
        toast.error('Cart is empty.');
        return;
    }
    const fd = new FormData();
    fd.append('discount', discount.value || 0);
    fd.append('payment_mode', paymentMode.value);
    if (selectedCustomer.value) {
        fd.append('customer_id', selectedCustomer.value);
    }
    if (prescriptionInput.value?.files?.[0]) {
        fd.append('prescription', prescriptionInput.value.files[0]);
    }
    cart.lines.forEach((line, i) => {
        fd.append(`lines[${i}][medicine_id]`, line.medicine_id);
        fd.append(`lines[${i}][sold_unit]`, line.sold_unit);
        fd.append(`lines[${i}][qty]`, line.qty);
        fd.append(`lines[${i}][unit_price]`, line.unit_price);
    });
    router.post('/sales', fd, {
        forceFormData: true,
        onSuccess: () => {
            cart.clear();
            if (prescriptionInput.value) {
                prescriptionInput.value.value = '';
            }
            toast.success('Sale completed');
        },
        onError: () => toast.error('Could not complete sale'),
    });
}
</script>

<template>
    <AppLayout title="Point of sale">
        <div class="grid xl:grid-cols-2 gap-8">
            <div class="space-y-4">
                <div class="relative">
                    <Search class="w-4 h-4 absolute left-3 top-3 text-slate-400" />
                    <input
                        v-model="q"
                        type="search"
                        placeholder="Search brand, generic, or barcode…"
                        class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-300 text-sm"
                    />
                </div>
                <ul class="bg-white rounded-xl border border-slate-200 divide-y max-h-64 overflow-auto">
                    <li
                        v-for="m in results"
                        :key="m.id"
                        class="p-3 hover:bg-slate-50 cursor-pointer text-sm"
                        @click="pickMedicine(m)"
                    >
                        <div class="font-medium text-secondary">{{ m.name }}</div>
                        <div class="text-xs text-slate-500">{{ m.generic_name }} · stock {{ m.stock.toFixed(0) }}</div>
                        <ul v-if="m.stock <= 0 && m.alternatives?.length" class="mt-2 text-xs text-emerald-700 space-y-1">
                            <li v-for="a in m.alternatives" :key="a.id">Alt: {{ a.name }} ({{ a.stock }})</li>
                        </ul>
                    </li>
                </ul>

                <div v-if="activeMedicine" class="bg-white rounded-xl border border-slate-200 p-4 space-y-3">
                    <div class="font-semibold">{{ activeMedicine.name }}</div>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="text-sm">
                            Unit
                            <select v-model="soldUnit" class="mt-1 w-full border rounded-lg px-2 py-1.5 text-sm">
                                <option value="tablet">Tablet</option>
                                <option value="strip">Strip</option>
                                <option value="box">Box</option>
                            </select>
                        </label>
                        <label class="text-sm">
                            Qty
                            <input v-model.number="qty" type="number" min="0.01" step="0.01" class="mt-1 w-full border rounded-lg px-2 py-1.5 text-sm" />
                        </label>
                        <label class="text-sm col-span-2">
                            Unit price
                            <input v-model.number="unitPrice" type="number" min="0" step="0.01" class="mt-1 w-full border rounded-lg px-2 py-1.5 text-sm" />
                        </label>
                    </div>
                    <button type="button" class="w-full py-2 rounded-lg bg-emerald-600 text-white text-sm font-medium" @click="addToCart">
                        Add to cart
                    </button>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm space-y-4">
                <h2 class="font-semibold text-secondary">Cart</h2>
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-slate-500 border-b">
                            <th class="pb-2">Item</th>
                            <th class="pb-2">Qty</th>
                            <th class="pb-2">Price</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(line, idx) in cart.lines" :key="idx" class="border-b border-slate-100">
                            <td class="py-2">{{ line.name }}</td>
                            <td>{{ line.qty }} {{ line.sold_unit }}</td>
                            <td>${{ (line.qty * line.unit_price).toFixed(2) }}</td>
                            <td>
                                <button type="button" class="text-red-600 text-xs" @click="cart.removeLine(idx)">Remove</button>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span>Subtotal</span>
                        <span>${{ cart.subtotal.toFixed(2) }}</span>
                    </div>
                    <label class="flex justify-between items-center gap-2">
                        <span>Discount</span>
                        <input v-model.number="discount" type="number" min="0" step="0.01" class="border rounded px-2 py-1 w-28" />
                    </label>
                    <div class="flex justify-between">
                        <span>Tax ({{ (taxRate * 100).toFixed(0) }}%)</span>
                        <span>${{ taxAmount.toFixed(2) }}</span>
                    </div>
                    <div class="flex justify-between font-semibold text-lg text-emerald-700">
                        <span>Total</span>
                        <span>${{ grandTotal.toFixed(2) }}</span>
                    </div>
                </div>

                <label class="block text-sm">
                    Customer (optional / credit)
                    <select v-model="selectedCustomer" class="mt-1 w-full border rounded-lg px-2 py-2 text-sm">
                        <option value="">Walk-in</option>
                        <option v-for="c in customers" :key="c.id" :value="c.id">
                            {{ c.name }} {{ c.is_chronic ? '(chronic)' : '' }}
                        </option>
                    </select>
                </label>

                <label class="block text-sm">
                    Payment
                    <select v-model="paymentMode" class="mt-1 w-full border rounded-lg px-2 py-2 text-sm">
                        <option value="cash">Cash</option>
                        <option value="credit">On credit</option>
                    </select>
                </label>

                <label class="block text-sm">
                    Prescription image
                    <input ref="prescriptionInput" type="file" accept="image/*,application/pdf" class="mt-1 w-full text-sm" />
                </label>

                <button
                    type="button"
                    class="w-full py-3 rounded-xl bg-secondary text-white font-medium"
                    @click="checkout"
                >
                    Complete sale
                </button>
            </div>
        </div>
    </AppLayout>
</template>
