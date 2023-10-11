<script setup>
import PatientsList from '../../components/patients/PatientsList.vue'
import {router, usePage} from "@inertiajs/vue3";
import {ref, watch} from "vue";

const props = defineProps({
  patients: {
    type: Object,
    required: true
  }
})

const refsRegisters = ref(props.patients)
const page = usePage()
const refresh = () => {
  router.reload({
    only: ['patients'],
    headers: {
      'X-Inertia': true,
      "x-inertia-partial-component": page.component,
    }
  })
}

watch(props.patients, (newValue, oldValue) => {
  console.log("mudou o valor dos pacientes")
  // refsRegisters.value = newValue?
})
</script>

<template>
  <div>
    <h1>Pacientes</h1>
    <PatientsList :patients="props.patients" @refresh="refresh"/>
  </div>
</template>

<style scoped>

</style>
