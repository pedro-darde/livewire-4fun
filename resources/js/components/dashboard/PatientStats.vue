<template>
    <v-col cols="6">
        <v-card class="mx-auto" max-width="450" color="indigo-darken-3">
            <v-card-text class="py-0">
                <v-row align="center" no-gutters>
                    <v-col class="text-h2 text-center" cols="6">
                        {{ patients.length }}
                    </v-col>

                    <v-col cols="6" class="text-right">
                        <v-icon color="white" icon="mdi-account-voice" size="88"></v-icon>
                    </v-col>
                </v-row>
            </v-card-text>
            <v-card-title>
                Pacientes com consultas marcadas
            </v-card-title>
            <v-card-actions>
                <v-btn color="orange-lighten-2" variant="text">
                    Veja mais
                </v-btn>
                <v-spacer></v-spacer>

                <v-btn :icon="show ? 'mdi-chevron-up' : 'mdi-chevron-down'" @click="show = !show"></v-btn>
            </v-card-actions>
            <v-expand-transition>
                <div v-show="show" class="todayAppointmentsDetail">
                    <v-divider></v-divider>

                    <v-table>
                        <thead>
                            <tr>
                                <th class="text-left">
                                    Paciente
                                </th>
                                <th class="text-center">
                                    Consulta(s)
                                </th>
                                <th>
                                    <v-menu>
                                        <template v-slot:activator="{ props }">
                                            <v-btn color="primary" v-bind="props" icon="mdi-filter">
                                            </v-btn>
                                        </template>
                                        <v-list>
                                            <v-list-item v-for="item in filterPatientOptions" :key="item.value"
                                                :value="item.value" @click="selectFilterPatient(item.value)"
                                                :active="filterPatient == item.value">
                                                <v-list-item-title>{{ item.text }}</v-list-item-title>
                                            </v-list-item>
                                        </v-list>
                                    </v-menu>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="item in filteredPatients" :key="item.name">
                                <td>{{ item.first_name }} {{ item.last_name }}</td>
                                <td class="text-center" colspan="2">
                                    <v-chip v-for="appointment in getPatientAppointments(item)" class="p-5 m-2">
                                        {{ appointment.startParsed }}
                                    </v-chip>
                                    <v-chip v-if="missingAppointmentsToShow(item) > 0" class="p-5 m-2">
                                        <Link class="text-underline text-blue-500" :href="patientUrl(item)" :data="{ tab: 'appointments' }"> +{{ missingAppointmentsToShow(item) }} consultas </Link>
                                    </v-chip>
                                </td>
                            </tr>
                        </tbody>
                    </v-table>
                </div>
            </v-expand-transition>
        </v-card>
    </v-col>
    <v-col cols="6">
        <v-card class="mx-auto" max-width="450" color="indigo-darken-3">
            <v-card-text class="py-0">
                <v-row align="center" no-gutters>
                    <v-col class="text-h2 text-center" cols="6">
                        {{ totalPatients }}
                    </v-col>

                    <v-col cols="6" class="text-right">
                        <v-icon color="white" icon="mdi-account-voice" size="88"></v-icon>
                    </v-col>
                </v-row>
            </v-card-text>
            <v-card-title>
                Pacientes no sistema
            </v-card-title>
            <v-card-actions>
                <v-btn color="orange-lighten-2" variant="text">
                    Ver pacientes do sistema
                </v-btn>
                <v-spacer></v-spacer>

                <!-- <v-btn :icon="show ? 'mdi-chevron-up' : 'mdi-chevron-down'" @click="show = !show"></v-btn> -->
            </v-card-actions>
        </v-card>
    </v-col>
</template>
<script setup>
import { computed, ref } from 'vue';
import { useDateFormatter } from '../../composables/useDateFormatter';
import { Link, usePage } from '@inertiajs/vue3';
const props = defineProps({
    patients: {
        type: Array
    },
    totalPatients: {
        type: Number
    }
})


const filterPatientOptions = [
    {
        value: 0,
        text: "Todos",
    }, {
        value: 1,
        text: "Com consultas marcadas para hoje",
    }]


const filterPatient = ref(0)
const {
    greatherThan
} = useDateFormatter()

const selectFilterPatient = (filterValue) => {
    console.log(filterValue)
    filterPatient.value = filterValue
}

const filteredPatients = computed(() => {
    if (filterPatient.value == 1) {
        return props.patients.filter(patient => patient.appointments.some(appointment => greatherThan(appointment.start, new Date())))
    }

    return props.patients
})

const getPatientAppointments = ({appointments}) => {
    return appointments.slice(0, 6)
}

const missingAppointmentsToShow = ({ appointments }) => {
    return appointments.length - 6
}

const page = usePage()

const patientUrl = ({ id }) => {
    return page.props.app.base_url + '/patients/' + id
}

const show = ref(false)
</script>