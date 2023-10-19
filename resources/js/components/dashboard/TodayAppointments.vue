<template>
    <v-col cols="12">
        <v-card class="mx-auto" max-width="500" color="indigo-darken-3">
            <v-card-text class="py-0">
                <v-row align="center" no-gutters>
                    <v-col class="text-h2 text-center" cols="6">
                        {{ todayAppointments.length }}
                    </v-col>

                    <v-col cols="6" class="text-right">
                        <v-icon color="white" icon="mdi-account-voice" size="88"></v-icon>
                    </v-col>
                </v-row>
            </v-card-text>
            <v-card-title>
                Consultas para hoje
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
                                    Início
                                </th>
                                <th class="text-left">
                                    Fim
                                </th>
                                <th class="text-center">
                                    Pacientes
                                </th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="item in todayAppointments" :key="item.name">
                                <td>{{ item.onlyHourStart }}</td>
                                <td>{{ item.onlyHourEnd }}</td>
                                <td class="text-center"> {{ item.patientsNames }}</td>
                                <th>
                                    <v-btn icon="mdi-eye" variant="plain" color="primary"
                                        @click="goToAppointment(item.id)"></v-btn>
                                </th>
                            </tr>
                        </tbody>
                    </v-table>
                </div>
            </v-expand-transition>
        </v-card>
    </v-col>
</template>
<script setup>
import { router } from '@inertiajs/vue3';
import { ref } from 'vue';
const show = ref(false);
const props = defineProps({
    todayAppointments: {
        type: Array,
        required: true
    }
})

const goToAppointment = (id) => {
    router.visit(`/appointments/${id}`)
}
</script>

<style scoped >
.todayAppointmentsDetail {
    max-height: 250px;
    overflow: auto;
    /* max-width: 344px; */
}
</style>