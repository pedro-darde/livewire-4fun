<script setup>
import { RequestProcessor } from "../../shared/RequestProcessor";
import { useAlert } from "../../composables/useAlert";
import { usePage } from "@inertiajs/vue3";
import Layout from "../../components/layout/Layout.vue";
import PatientsEdit from "../../components/patients/PatientsEdit.vue";
import { computed, onMounted, ref } from "vue";
import PatientAppointments from "../../components/patients/PatientAppointments.vue";
import { usePopup } from '../../composables/usePopup'
import CreateEditAppointmentComponent from "../../components/appointments/CreateEditAppointmentComponent.vue";
import useRequest from "../../composables/useRequest";
import { AppointmentStatus } from '../../shared/AppointmentStatus'
import Calendar from "../../components/Calendar/Calendar.vue";
import AppointmentNotes from "../../components/appointments/AppointmentNotes.vue";
import useNoteSaver from "../../composables/useNoteSaver.js";
const props = defineProps({
    patient: {
        type: Object,
        required: false
    }
})


const {
    fireError, toast
} = useAlert()
const {
    open: modalCreateAppointment,
    openPopup: openModalCreateAppointment,
    closePopup: closeModalCreateAppointment
} = usePopup()

onMounted(() => {
    const [_, paramsString] = page.url.split('?')
    const params = new URLSearchParams(paramsString)
    if (params.get('tab')) {
        const tabMap = {
            'appointments': 'option-2'
        }
        tab.value = tabMap[params.get('tab')] ?? 'option-1  '
    }
})

const page = usePage()
const { processRequest } = useRequest()
const { saveNote } = useNoteSaver()

const reloadData = () => {
    router.reload({
        replace: true,
        only: ['patient']
    })
}

async function requestSave(register) {
    // page.
    const response = await processRequest(`patients/${register.id}`, 'put', register)
    if (response.hasErrors()) {
        fireError('An error has ocurred', response.getErrors(true))
    } else {
        reloadData()
        toast('Paciente atualizado com sucesso', 'success')
    }
}

const tab = ref('option-1')
const saveNewAppointment = async (appointmentData) => {
    const response = await processRequest(`appointments`, 'post', {
        ...appointmentData,
        patients: [props.patient.id]
    })

    if (response.hasErrors()) {
        // fireError(response.getErrors(true))
        return
    }

    toast('Consulta adicionada com sucesso', 'success')
    closeModalCreateAppointment()
    reloadData()
}

const nextTab = (isReturn = false) => {
    let currentTabIndex = availableTabs.indexOf(tab.value)
    if (isReturn) {
        currentTabIndex--
    } else {
        currentTabIndex++
    }
    const newTab = availableTabs[currentTabIndex]
    tab.value = newTab
}

const availableTabs = [
    'option-1',
    'option-2',
    'option-3',
]

const isLastTab = computed(() => {
    return tab.value === 'option-3'
})
const isFirstTab = computed(() => {
    return tab.value === 'option-1'
})

const patientAppointments = computed(() => {
    const appointments =  props.patient.appointments
    appointments.forEach(appointment => {
        appointment.modalNotes = false;
    })
    return appointments
})


const confirmAppointment = async ({ id }) => {
     const response = await processRequest(`appointment/${id}/changeStatus`, 'post', {
        status: AppointmentStatus.CONFIRMED
     })

    if (!response.hasErrors()) {
      router.reload({
        replace: true,
        only: ['patient']
      })
    }

}

const cancelAppointment = async ({ id }) => {
  const response = await processRequest(`appointment/${id}/changeStatus`, 'post', {
    status: AppointmentStatus.CANCELLED
  })

  if (!response.hasErrors()) {
    router.reload({
      replace: true,
      only: ['patient']
    })
    toast('Consulta cancelada com sucesso', 'success')
  }
}

const finishAppointment = async ({ id, start }) => {

  const response = await processRequest(`appointment/${id}/changeStatus`, 'post', {
    status: AppointmentStatus.FINISHED
  })

  if (!response.hasErrors()) {
    router.reload({
      replace: true,
      only: ['patient']
    })
  }
}

const modalAppointment = ref(false)
const currentAppointment = ref(null)

const openAppointmentInfo = (appointment) => {
    currentAppointment.value = appointment
    modalAppointment.value = true
}


const callSaveNote = (note) => {
    saveNote(note, () => {
        modalAppointment.value = false
        currentAppointment.value = null
        reloadData()
    })
}


</script>
<template>
    <Layout>
        <div class="flex items-center justify-center flex-column m-5">
            <v-card class="w-full m-5">
                <v-toolbar color="primary">
                    <div class="flex flex-row justify-between p-3">
                        <h1 class="text-xl font-bold text-center">Editar Paciente {{ patient.name }}</h1>
                    </div>
                </v-toolbar>
                <div class="d-flex flex-row w-full">
                    <v-tabs v-model="tab" direction="vertical" color="primary">
                        <v-tab value="option-1">
                            <v-icon start>
                                mdi-account
                            </v-icon>
                            Dados cadastrais
                        </v-tab>
                        <v-tab value="option-2">
                            <v-icon start>
                                mdi-stethoscope
                            </v-icon>
                            Consultas
                        </v-tab>
                        <v-tab value="option-3">
                            <v-icon start>
                                mdi-cash
                            </v-icon>
                            Financeiro
                        </v-tab>
                    </v-tabs>
                    <v-window v-model="tab" class="w-full">
                        <v-window-item value="option-1" class="w-full">
                            <v-card flat>
                                <PatientsEdit @save="requestSave" :patient="patient" :show-next-button="true"
                                    @next="nextTab()"></PatientsEdit>
                            </v-card>
                        </v-window-item>

                        <v-window-item value="option-2">
                            <v-card flat>
                                <v-row>
                                        <v-row style="flex-wrap: nowrap">
                                            <v-row class="m-5" justify="center" align-items="center">
                                                <h2 class="text-center text-h4 p-2"> Consultas </h2>
                                                <v-btn icon="mdi-plus" class="p-2" @click="openModalCreateAppointment">
                                                </v-btn>
                                            </v-row>
                                        </v-row>
                                        <v-dialog v-model="modalCreateAppointment">
                                            <v-container class="bg-slate-200 bg-opacity-90 p-8 rounded-lg overflow-auto">
                                                <h2 class="text-xl font-bold text-center"> Adicionar consulta </h2>
                                                <v-divider></v-divider>
                                                <CreateEditAppointmentComponent :show-patient-select="false" @save="saveNewAppointment"/>
                                            </v-container>
                                        </v-dialog>
                                        <PatientAppointments
                                            :appointments="patientAppointments"
                                            @finish="finishAppointment"
                                            @cancel="cancelAppointment"
                                            @confirm="confirmAppointment"
                                            @refresh="reloadData"
                                        />
                                </v-row>
                                <v-row>
                                    <v-row class="m-5 w-full" justify="center" align-items="center">
                                        <h2 class="text-center text-h4 p-2"> Calendário </h2>
                                    </v-row>
                                    <div class="  p-5 w-full">
                                    <Calendar :events="patientAppointments"  @onClickEvent="openAppointmentInfo"/>
                                        <v-dialog v-model="modalAppointment">
                                            <AppointmentNotes :appointment="currentAppointment" @close="modalAppointment = false" @save="callSaveNote"/>
                                        </v-dialog>
                                    </div>
                                </v-row>

                            </v-card>
                        </v-window-item>
                        <v-window-item value="option-3">
                            <v-card flat>
                                <v-card-text>

                                </v-card-text>
                            </v-card>

                        </v-window-item>
                    </v-window>
                </div>
                <v-row justify="space-between" class="p-5">
                    <v-col cols="6">
                        <v-btn color="primary" :disabled="isFirstTab" @click="nextTab(true)"> Voltar </v-btn>
                    </v-col>
                    <v-col cols="6">
                        <v-row justify="end">
                            <v-btn color="primary" :disabled="isLastTab" @click="nextTab()"> Próximo </v-btn>
                        </v-row>
                    </v-col>
                </v-row>
            </v-card>
        </div>
    </Layout>
</template>

<style scoped>
 .v-window-item {
     max-height: 650px;
     overflow: auto;
 }
</style>
