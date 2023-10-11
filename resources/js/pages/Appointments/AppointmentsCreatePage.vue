<script setup>

import PatientsCreate from "../../components/patients/PatientsCreate.vue";
import {RequestProcessor} from "../../shared/RequestProcessor";
import {useAlert} from "../../composables/useAlert";
import {usePage} from "@inertiajs/vue3";
import {router} from '@inertiajs/core';
import Layout from "../../components/layout/Layout.vue";

const {
    fireError, toast
} = useAlert()
const page = usePage()

async function requestSave(register) {
    // page.
    const response = await (new RequestProcessor('/patients', 'post', register)).process()
    if (response.hasErrors()) {
        fireError('An error has ocurred', response.getErrors(true))
    } else {
        toast('Patient created successfully', 'success')
        router.visit('/patients')
    }
}
</script>

<template>
    <Layout>
        <div class="flex items-center justify-center flex-column mt-5">
            <div class="flex flex-row justify-between">
                <h1 class="text-xl font-bold text-center">Criar Paciente</h1>
            </div>
            <PatientsCreate @save="requestSave"></PatientsCreate>
        </div>
    </Layout>
</template>

<style scoped>

</style>
