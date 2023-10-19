<template>
    <Layout>
        <v-container>
            <v-row dense>
                <TodayAppointments :today-appointments="todayAppointments" />
                <PatientStats :patients="patients" :total-patients="totalPatients" />
            </v-row>
        </v-container>
    </Layout>
</template>
<script setup>
import { ref } from 'vue';
import Layout from '../components/layout/Layout.vue';
import TodayAppointments from '../components/dashboard/TodayAppointments.vue';
import PatientStats from '../components/dashboard/PatientStats.vue';
const show = ref(false);
const props = defineProps({
    todayAppointments: {
        type: Array,
        required: true
    },
    patients: {
        type: Array
    },
    totalPatients: {
        type: Number
    },
})

console.log(props)
</script>

<style scoped >
.todayAppointmentsDetail {
    max-height: 150px;
    overflow: auto;
    max-width: 344px;
}
</style>