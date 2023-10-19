<script setup>
import PatientsList from '../../components/patients/PatientsList.vue'
import {router, usePage} from "@inertiajs/vue3";
import {ref, watch} from "vue";
import Layout from "../../components/layout/Layout.vue";

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
})
</script>

<template>
    <Layout>
        <div class="p-5">
            <v-row justify="space-between" class="p-5">
                <h1 class="text-h3 text-center text-bold">Pacientes</h1>
                <v-btn href="/patients/create" color="primary"> Criar paciente</v-btn>
            </v-row>

            <PatientsList :patients="props.patients" @refresh="refresh"/>
        </div>
    </Layout>

</template>

<style scoped>

</style>
