<script setup>
import { computed } from 'vue'
import { useForm } from '@inertiajs/inertia-vue3'

const props = defineProps({
    bills: {
        type: Array,
        default: () => []
    }
});

const bill = useForm({
    id: null,
    bill_reference: '',
    bill_date: (new Date()).toLocaleString('sv').replace(' ', 'T'),
    bill_stage_id: 2
});

const totals = computed(() => {
    return props.bills.reduce((output, row) => {
        Object.keys(row).map(r => output[r] = (output[r] ?? 0) + row[r])
        return output;
    }, {});
})

function saveBill(e) {
    if(bill.bill_reference.trim() == "") {
        document.querySelector('.bill-reference').focus();
        window.alert("Reference Required");    
        return;
    }

    bill.post(baseUrl + '/');
    bill.bill_reference = "";
    bill.bill_date = (new Date()).toLocaleString('sv').replace(' ', 'T');
    document.querySelector('.bill-reference').focus();
}

const baseUrl = import.meta.env.VITE_APP_URL;

</script>

<template>
    <div class="container mx-auto">
        <div class="font-bold text-[24px] text-center my-3">Bills</div>
        <div class="flex text-center h-[100px] gap-3 my-3">

            <div class="flex-grow bg-slate-300 py-3">
                <span>Total Number of Bills</span>
                <div class="font-bold text-[32px]">{{ totals.totalSubmittedBills }}</div>
            </div>

            <div class="flex-grow bg-slate-300 py-3">
                <span>Total Number of Approved Bills</span>
                <div class="font-bold text-[32px]">{{ totals.totalApprovedBills }}</div>
            </div>

            <div class="flex-grow bg-slate-300 py-3">
                <span>Total Number of On Hold Bills</span>
                <div class="font-bold text-[32px]">{{ totals.totalOnHoldBills }}</div>
            </div>

        </div>
        <table class="w-full table-auto">
            <tr class="font-bold text-lg text-left">
                <th>Name</th>
                <th>Total Bills</th>
                <th>Total Submitted</th>
                <th>Total Approved</th>
            </tr>
            <tr v-for="bill of bills">
                <td>{{ bill.name }}</td>
                <td>{{ bill.totalBills }}</td>
                <td>{{ bill.totalSubmittedBills }}</td>
                <td>{{ bill.totalApprovedBills }}</td>
            </tr>
        </table>

        <div class="my-3 w-[300px]">
            <div class="font-bold text-[18px]">Create</div>
            <form class=" flex flex-col gap-3" @submit.prevent="(e) => saveBill(e)">
                <label class="font-bold">Reference</label>
                <input class="bill-reference rounded border-2 border-slate-600 p-2" placeholder="Reference" type="text" v-model="bill.bill_reference" />
                <label class="font-bold">Date</label>
                <input class="rounded border-2 border-slate-600 p-2" type="datetime-local" step="1" v-model="bill.bill_date" />
                <button class="rounded border-2 border-slate-600 bg-slate-500 hover:bg-slate-600 text-white p-2" type="submit">Submit</button>
            </form>
        </div>
    </div>
</template>