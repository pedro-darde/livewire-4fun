<script setup>
import CreateEditPatientComponent from "./CreateEditAppointmentComponent.vue";

const props = defineProps({
  patient: {
    type: Object,
    required: false
  }
})

const emit = defineEmits(['save'])

const patientToEdit = {
  ...props.patient
}

const emitSave = (patient) => {
  emit('save', patient)
}
</script>

<template>
  <CreateEditPatientComponent @save="emitSave" :current-patient="patientToEdit"/>
</template>

<style scoped>

</style>
