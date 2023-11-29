<script setup>
import CreateEditPatientComponent from "./CreateEditAppointmentComponent.vue";
import CreateEditAppointmentComponent from "./CreateEditAppointmentComponent.vue";

const props = defineProps({
  appointment: {
    type: Object,
    required: false
  }
})

const emit = defineEmits(['save'])

const appointmentToEdit = {
  ...props.appointment,
  patients: props.appointment.patients.map(item => item.id),
  start: props.appointment.startParsed,
  end: props.appointment.endParsed
}

const emitSave = (appointment) => {
  emit('save', appointment)
}
</script>

<template>
  <CreateEditPatientComponent @save="emitSave" :current-appointment="appointmentToEdit" />
</template>

<style scoped></style>
