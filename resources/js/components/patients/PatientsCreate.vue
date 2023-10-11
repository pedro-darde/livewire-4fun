<script setup>
import CreateEditPatientComponent from "./CreateEditPatientComponent.vue";

const emit = defineEmits(['save'])
const emitSave = (patient) => {
    emit('save', patient)
}

</script>

<template>
    <CreateEditPatientComponent @save="emitSave"/>
</template>

<style scoped>

</style>
