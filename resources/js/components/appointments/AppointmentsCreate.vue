<script setup>
import CreateEditPatientComponent from "./CreateEditAppointmentComponent.vue";
import CreateEditAppointmentComponent from "./CreateEditAppointmentComponent.vue";

const emit = defineEmits(['save'])
const emitSave = (patient) => {
  emit('save', patient)
}

</script>

<template>
  <CreateEditAppointmentComponent @save="emitSave"/>
</template>

<style scoped>

</style>
