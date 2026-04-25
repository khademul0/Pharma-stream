import { defineStore } from 'pinia';
import { computed, ref } from 'vue';

export const useCartStore = defineStore('posCart', () => {
    const lines = ref([]);

    function addLine(medicine, soldUnit, qty, unitPrice) {
        lines.value.push({
            medicine_id: medicine.id,
            name: medicine.name,
            sold_unit: soldUnit,
            qty: Number(qty),
            unit_price: Number(unitPrice),
        });
    }

    function removeLine(index) {
        lines.value.splice(index, 1);
    }

    function clear() {
        lines.value = [];
    }

    const subtotal = computed(() =>
        lines.value.reduce((sum, l) => sum + l.qty * l.unit_price, 0)
    );

    return { lines, addLine, removeLine, clear, subtotal };
});
