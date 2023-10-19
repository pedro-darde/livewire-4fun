<script setup>

import PatientsCreate from "../../components/patients/PatientsCreate.vue";
import { RequestProcessor } from "../../shared/RequestProcessor";
import { useAlert } from "../../composables/useAlert";
import { usePage } from "@inertiajs/vue3";
import { router } from '@inertiajs/core';
import Layout from "../../components/layout/Layout.vue";
import AppointmentsCreate from "../../components/appointments/AppointmentsCreate.vue";

const {
  fireError, toast
} = useAlert()
const page = usePage()

async function requestSave(register) {
  // page.
  const response = await (new RequestProcessor('/appointments', 'post', register)).process()
  if (response.hasErrors()) {
    fireError('An error has ocurred', response.getErrors(true))
  } else {
    toast('Appointment created successfully', 'success')
    router.visit('/appointments')
  }
}
</script>

<template>
  <Layout>
    <div class="flex items-center justify-center flex-column mt-5">
      <div class="flex flex-row justify-between">
        <h1 class="text-xl font-bold text-center">Criar Consulta</h1>
      </div>
      <AppointmentsCreate @save="requestSave"></AppointmentsCreate>
    </div>
  </Layout>
</template>

<style scoped></style>
