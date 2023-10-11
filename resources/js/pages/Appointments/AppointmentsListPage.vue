<script setup>

import Layout from "../../components/layout/Layout.vue";
import {ref, watch} from "vue";
import {router, usePage} from "@inertiajs/vue3";
import AppointmentsList from "../../components/appointments/AppointmentsList.vue";

const props = defineProps({
  appointments: {
    type: Object,
    required: true
  }
})

const refsRegisters = ref(props.appointments)
const page = usePage()
const refresh = () => {
  router.reload({
    only: ['appointments'],
    headers: {
      'X-Inertia': true,
      "x-inertia-partial-component": page.component,
    }
  })
}


</script>

<template>
  <Layout>
    <div class="p-5">
      <v-row justify="space-between" class="p-5">
        <h1 class="text-h3 text-center text-bold">Consultas</h1>
        <v-btn href="/appointments/create" color="primary"> Criar consulta</v-btn>
      </v-row>

      <AppointmentsList :appointments="props.appointments" @refresh="refresh"/>
    </div>
  </Layout>
</template>

<style scoped>

</style>
