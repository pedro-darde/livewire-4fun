<script setup>
import {computed, ref} from "vue";

const emit = defineEmits(['update'])
const props = defineProps(['fields'])

const fieldColNames = computed(() => {
    return props.fields.map(field => field.config.name)
})

const descriptionCol = ref(null)
</script>

<template>
    <v-autocomplete
        variant="solo-filled"
        v-model="descriptionCol"
        :items="descriptionCol"
    />
</template>

<style scoped>

</style>
