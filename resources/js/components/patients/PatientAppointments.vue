<template>
    <v-container>
        <div v-if="appointments.length">
          <v-expansion-panels variant="inset">
            <v-expansion-panel v-for="info in appointmentsOrganized" v-model="info.expanded">
              <template v-if="info.items.length">
                <v-expansion-panel-title>
                  <h2 class="text-h5 text-center my-4"> {{ info.title }}</h2>
                </v-expansion-panel-title>
                <v-expansion-panel-text class="max-h-96 overflow-auto p-5 m-5">
                  <div class="h-auto mb-2" v-for="appointment in info.items">
                    <v-card class="mx-auto h-25" max-width="450" :color="getCardColorByStatus(appointment.status)" max-height="250">
                      <v-card-title>
                        <v-row>
                          <v-col cols="10">
                            {{ appointment.startParsed }} - {{ getAppointmentStatusNormalized(appointment.status) }}
                          </v-col>
                          <v-col cols="2">
                            <v-btn icon="mdi-hand-pointing-right" @click="appointment.modalNotes = !appointment.modalNotes"></v-btn>
                              <v-dialog v-model="appointment.modalNotes">
                                  <AppointmentNotes :appointment="appointment" @close="appointment.modalNotes = false"/>
                              </v-dialog>
                          </v-col>
                        </v-row>
                      </v-card-title>
                      <v-card-subtitle>
                      </v-card-subtitle>
                      <v-card-actions>
                        <v-menu>
                          <template v-slot:activator="{ props }">
                            <v-btn  icon="mdi-dots-vertical" size="35" color="white" variant="text" v-bind="props" v-if="canShowEmitOptions(appointment.status)">

                            </v-btn>
                          </template>
                          <v-list>
                            <v-list-item v-for="item in allowedActionsOfAppointment(appointment.status)"
                                         :key="item.action"
                                         :value="item.action"
                                         @click="$emit(item.action, appointment)"
                                         :prepend-icon="item.icon"
                            >
                              <v-list-item-title>{{ item.text }}</v-list-item-title>
                            </v-list-item>
                          </v-list>
                        </v-menu>

                        <v-spacer></v-spacer>

                        <v-btn icon="mdi-cash">

                        </v-btn>
                      </v-card-actions>
                    </v-card>
                  </div>
                </v-expansion-panel-text>
              </template>

            </v-expansion-panel>

          </v-expansion-panels>
        </div>
        <div v-else>
            <h2 class="text-center text-italic"> Não há consultas marcadas para esse paciente.</h2>
        </div>
    </v-container>
</template>
<script setup >
import {computed, ref} from "vue";
import {useDateFormatter} from "../../composables/useDateFormatter.js";
import {AppointmentStatus} from "../../shared/AppointmentStatus";
import AppointmentNotes from "../appointments/AppointmentNotes.vue";

const props = defineProps({
    appointments: {
        type: Object,
        required: false
    }
})

const { formatDate, isAfter, isBefore } = useDateFormatter()

const emit = defineEmits(['confirm', 'cancel','finish'])

const onlyDateToday = formatDate()

const notCancelledAppointments = computed(() => {
    return props.appointments.filter(appointment => {
        return appointment.status !== AppointmentStatus.CANCELLED
    })
})

const cancelledAppointments = computed(() => {
    return props.appointments.filter(appointment => {
        return appointment.status === AppointmentStatus.CANCELLED
    })
})

const pastAppointments = computed(() => {
    return notCancelledAppointments.value.filter(appointment => {
        const onlyDate = formatDate(new Date(appointment.start));
        return (onlyDate !== onlyDate) && isBefore(new Date(appointment.start), new Date())
    })
})

const todayAppointments = computed(() => {
    return notCancelledAppointments.value.filter(appointment => {
        const onlyDate = formatDate(new Date(appointment.start));
        return onlyDate === onlyDateToday
    })
})

const futureAppointments = computed(() => {
    return notCancelledAppointments.value.filter(appointment => {
        return isAfter(new Date(appointment.start), new Date())
    })
})

const canShowEmitOptions = (status) => {
    return status === AppointmentStatus.PENDING || status === AppointmentStatus.CONFIRMED
}

const appointmentsOrganized = computed(() => {
    return [
        {
           title: "Consultas passadas",
           items: [...pastAppointments.value],
          expanded: false
        },
        {
          title: 'Consultas Hoje',
          items: [...todayAppointments.value],
          expanded: false
        },
        {
          title: 'Consultas Futuras',
          items: [...futureAppointments.value],
          expanded: false
        },
        {
          title: 'Consultas Canceladas',
          items: [...cancelledAppointments.value],
          expanded: false
        }
    ]
});

const getAppointmentStatusNormalized = (status) => {
  const map = {
    [AppointmentStatus.PENDING]: 'Pendente',
    [AppointmentStatus.CONFIRMED]: 'Confirmada',
    [AppointmentStatus.CANCELLED]: 'Cancelada',
    [AppointmentStatus.FINISHED]: 'Finalizada'
  }

  return map[status]
}

const actionsOfAppointment = [
    {
        text: 'Confirmar',
        icon: 'mdi-hand-okay',
        action: 'confirm',
        status: AppointmentStatus.CONFIRMED
    },
    {
        text: 'Cancelar',
        icon: 'mdi-delete',
        action: 'cancel',
        status: AppointmentStatus.CANCELLED
    },
    {
      text: 'Finalizar',
      icon: 'mdi-check',
      action: 'finish',
      status: AppointmentStatus.FINISHED
    }
]

const allowedActionsOfAppointment = (status) => {
    switch (status) {
        case AppointmentStatus.PENDING:
            return actionsOfAppointment.filter(item => [AppointmentStatus.CONFIRMED, AppointmentStatus.CANCELLED].includes(item.status))
        case AppointmentStatus.CONFIRMED:
            return actionsOfAppointment.filter(item => [AppointmentStatus.CANCELLED, AppointmentStatus.FINISHED].includes(item.status))
        default:
            return []
    }
}

const getCardColorByStatus = (status) => {
    switch (status) {
        case 'pending':
            return 'yellow-accent-2'
        case 'confirmed':
            return 'teal-lighten-2'
        case 'cancelled':
            return 'red-lighten-3'
        case 'finished':
            return 'blue-grey-lighten-4'
        default:
            return 'yellow-accent-2'
    }
}

</script>
