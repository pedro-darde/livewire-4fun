<script setup>

import { RequestProcessor } from "../../shared/RequestProcessor";
import { useAlert } from "../../composables/useAlert";
import { usePage } from "@inertiajs/vue3";
import { router } from '@inertiajs/core';
import Layout from "../../components/layout/Layout.vue";
import AppointmentsEdit from "../../components/appointments/AppointmentsEdit.vue";
import { onMounted } from "vue";

const props = defineProps({
    appointment: {
        type: Object,
        required: false
    }
})

const {
    fireError, toast
} = useAlert()
const page = usePage()

async function requestSave(register) {
    const response = await (new RequestProcessor(`/appointments/${register.id}`, 'put', register)).process()
    if (response.hasErrors()) {
        fireError('An error has ocurred', response.getErrors(true))
    } else {
        toast('Appointment edited successfully', 'success')
        router.visit('/appointments')
    }
}

</script>

<template>
    <Layout>
        <div class="flex items-center justify-center flex-column mt-5">
            <div class="flex flex-row justify-between">
                <h1 class="text-xl font-bold text-center">Editar Consultas {{ appointment.name }}</h1>
            </div>
            <AppointmentsEdit @save="requestSave" :appointment="appointment" />
        </div>
    </Layout>
</template>

<style scoped></style>
