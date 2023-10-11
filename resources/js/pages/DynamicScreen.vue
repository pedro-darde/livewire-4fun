<script setup>
import Layout from "../components/layout/Layout.vue";
import Crud from "../components/crud/Crud.vue";
import {computed, onMounted, provide, ref} from "vue";
import {VAutocomplete, VTextField} from "vuetify/components";
import {usePage} from "@inertiajs/vue3";
import {RequestProcessor} from "../shared/RequestProcessor";
import {useFieldComponents} from "../composables/useFieldComponents.js";

const props = defineProps({
    screen: {
        type: Object,
    },
    registers: {
        type: Object,
    },
    columnDefinitions: {}
})

const model = ref({
    crudActions: {
        'update': true,
        'delete': true
    }
})


const fieldsConfigs = computed(() => {
    return props.screen.fields.map(field => ({
        ...field.config_parsed,
        columnName: field.columnName
    }))
})

const columnsFields = computed(() => {
    return fieldsConfigs.value.map(item => item.columnName)
})

const page = usePage();
const {
    defineFieldsComponents,
    getFieldsComponents
} = useFieldComponents()

const loadingFields = ref(false)
const fieldsComponents = ref({})


provide('fields', fieldsComponents)
provide('screen', props.screen)
onMounted(async () => {
    loadingFields.value = true
    await defineFieldsComponents(fieldsConfigs.value)
    fieldsComponents.value = getFieldsComponents()
    loadingFields.value = false
})


</script>

<template>
    <Layout>
        <h1 class="text-center">{{ screen.name }}</h1>
        <template v-if="!loadingFields">
            <Crud :registers="registers"
                  :column-definitions="columnDefinitions"
                  :table="screen.table"
                  :model-props="model"
                  :screen="screen"
            />
        </template>
        <template v-else>
            <v-progress-linear/>
        </template>
    </Layout>
</template>

<style scoped>

</style>
