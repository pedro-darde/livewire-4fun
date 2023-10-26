<script setup>

import {LOCALE_FORMATS, useDateFormatter} from "../../composables/useDateFormatter.js";
import {computed, onMounted, ref} from "vue";
import {router, usePage} from "@inertiajs/vue3";
import {axiosInstance} from "../../shared/axios";
import useRequest from "../../composables/useRequest";
import {RecurrenceType} from "../../shared/RecurrenceType";
import {DAYS_OF_WEEK} from "../../util/constants.js";
import {MIN_LENGTH, REQUIRED} from "../../util/formRules.js";
import {useAlert} from "../../composables/useAlert.js";

const props = defineProps({
  currentAppointment: {
    type: Object,
    required: false
  },
  showPatientSelect: {
    type: Boolean,
    default: true
  }
})

const emit = defineEmits(['save'])
const {
  toDatabaseDate,
  createFromFormat,
  formatDate,
  addHoursToDate,
  getDayOfWeek
} = useDateFormatter()

const page = usePage()
const baseURL = page.props.app.base_url
const { processRequest } = useRequest()
const { toast } = useAlert()

const appointment = ref(props.currentAppointment ?? {
  start: formatDate(new Date(), LOCALE_FORMATS["pt-BR datetime"]),
  end: formatDate(addHoursToDate(new Date, 1), LOCALE_FORMATS["pt-BR datetime"]),
  abount: "",
  patients: [],
  recurrence_type: {
    text: "Não",
    value: 0
  },
  useDefaultRecurrence: true,
  useCustomRecurrence: false,
  numberOfRecurrences: 1,
  useDefaultRecurrenceWeek: false,
  useCustomRecurrenceWeek: true,
  recurrenceWeeklyDays: [{
    day: 0,
    start: '',
    end: ''
  }],
  id_service_supplied: null
})

const recurrence_types = [
  {
    text: "Não",
    value: RecurrenceType.NONE
  },
  {
    text: "Sim, semanalmente.",
    value: RecurrenceType.WEEKLY,
    shortDesc: "Semanalmente"
  },
  {
    text: "Sim, quinzenalmente.",
    value: RecurrenceType.BIWEEKLY,
    shortDesc: "Quinzenalmente"
  },
  {
    text: "Sim, mensalmente.",
    value: RecurrenceType.MONTHLY,
    shortDesc: "Mensalmente"
  }
];

const currentPagePatients = ref(1);
const patientsOptions = ref({})
const servicesSupplied = ref([])

const maskCPF = {
  mask: '###.###.###-##'
}

const maskRG = {
  mask: "##########"
}

const maskTelefone = {
  mask: "(##) #####-####"
}

const maskDateTime = {
  mask: "##/##/#### ##:##"
}

const maskOnlyTime = {
  mask: "##:##"
}

const formRef = ref(null)
const emitSave = () => {
    console.log(formWeekly.value)
  if (formRef.value) {
    if (appointment.value.recurrence_type.value === RecurrenceType.WEEKLY) {
      const groupedByDayAndStart = appointment.value.recurrenceWeeklyDays.reduce((acc, curr) => {
        const idx = `${curr.day}${curr.start}`
        if (!acc[idx]) {
          acc[idx] = 0;
        }
        acc[idx]++;
        return acc
      }, {})

      if (Object.values(groupedByDayAndStart).some(value => value > 1)) {
        toast('Há horários repetidos, por favor verifique.', 'error')
        return;
      }
    }
    const data = {
      ...appointment.value,
      start: toDatabaseDate(appointment.value.start, LOCALE_FORMATS['pt-BR datetime'], false, true),
      end: toDatabaseDate(appointment.value.end, LOCALE_FORMATS['pt-BR datetime'], false, true),
    }
    emit('save', data)
  }
}

const back = () => {
  router.visit('/appointments')
}

const rules = {
  name: [v => !!v || "Preencha um nome"],
  last_name: [v => !!v || "Preencha um sobrenome"],
  about: [v => !!v || "Preencha um assunto"],
  start: [
    v => !!v || "Preencha uma data de começo",
    v => {
      if (v) {
        const date = createFromFormat(v, LOCALE_FORMATS["pt-BR datetime"])
        const endDate = createFromFormat(appointment.value.end, LOCALE_FORMATS["pt-BR datetime"])
        if (date > endDate) {
          return 'Data de começo não pode ser maior que a data de fim'
        }
        return true
      }
      return true
    }
  ],
  end: [
    v => !!v || "Preencha uma data de fim",
    v => {
      if (v) {
        const date = createFromFormat(v, LOCALE_FORMATS["pt-BR datetime"])
        const startDate = createFromFormat(appointment.value.start, LOCALE_FORMATS["pt-BR datetime"])
        if (date.getTime() < startDate.getTime()) {
          return 'Data de fim não pode ser menor que a data de começo'
        }
        return true
      }
      return true
    }
  ],
  patients: [
    v => !!v.length || "Selecione ao menos um paciente"
  ]
}


const requestPatients = async (searchString = '', shouldChangePage = true) => {
  if (patientsOptions.value?.data?.length && !patientsOptions.value?.next_page_url) return;

  const response = await axiosInstance.get(`${baseURL}/api/patients/loadMore`, {
    params: {
      page: currentPagePatients.value,
      per_page: 50,
      search: searchString,
    }
  })

  patientsOptions.value = response.data.patients
  if (shouldChangePage) {
    currentPagePatients.value = patientsOptions.value.current_page
  }
}

const endIntersect = (endReach) => {
  if (endReach) {
    requestPatients()
  }
}


const loadServicesSupplied = async () => {
  const response = await processRequest(`api/services-supplied`, 'get', {}, true)
  if (!response.hasErrors()) {
    console.log('busquei os apontamentos')
    servicesSupplied.value = response.getBody()
  }
}

onMounted(async () => {
  if (props.showPatientSelect) {
    requestPatients()
  }
  await loadServicesSupplied()
})

const isRecurrent = computed(() => {
    return appointment.value.recurrence_type.value !== RecurrenceType.NONE
})

const getTextRecurrentOption = computed(() => {
    if (!isRecurrent.value) return ''
    const shortDesc = appointment.value.recurrence_type.shortDesc
    const dateOBJ = createFromFormat(appointment.value.start, LOCALE_FORMATS["pt-BR datetime"])
    const dateDesc = formatDate(dateOBJ, LOCALE_FORMATS["pt-BR only_day_and_hour"])

    return `As consultas ocorrerão <b>${shortDesc.toLowerCase()}</b> , iniciando em: <b> ${dateDesc} </b>`
})

const handleClickUseDefaultRecurrence = (isOnMonth = false) => {
    if (isOnMonth) {
      appointment.value.useDefaultRecurrenceWeek = !appointment.value.useDefaultRecurrenceWeek
      appointment.value.useCustomRecurrenceWeek = false
      return
    }
    appointment.value.useDefaultRecurrence = !appointment.value.useDefaultRecurrence
    appointment.value.useCustomRecurrence = false
}

const handleClickUseCustomRecurrence = (isOnMonth = false) => {
    if (isOnMonth) {
      appointment.value.useDefaultRecurrenceWeek = !appointment.value.useDefaultRecurrenceWeek
      appointment.value.useCustomRecurrenceWeek = false
      return
    }
    appointment.value.useCustomRecurrence = !appointment.value.useCustomRecurrence
    appointment.value.useDefaultRecurrence = false
}

const monthMaxRecurrencyRule = () => {
    if (appointment.value.recurrence_type.value === RecurrenceType.MONTHLY) {
        return [
            v => v <= 12 || "Crie recorrências de até 12 meses (1 ano)"
        ]
    }

    if (appointment.value.recurrence_type.value === RecurrenceType.BIWEEKLY) {
        return [
            v => v <= 26 || "Crie recorrências de até 26 semanas (1 ano)"
        ]
    }

    if (appointment.value.recurrence_type.value === RecurrenceType.WEEKLY) {
        return [
            v => v < 7|| "Crie recorrências de até 7 dias (1 semana)"
        ]
    }
}

const clearRecurrence = () => {
    appointment.value.useDefaultRecurrence = true
    appointment.value.useCustomRecurrence = false
    appointment.value.numberOfRecurrences = 1
}

const isMonthlyOrBiWeekly = computed(() => {
    return (appointment.value.recurrence_type.value === RecurrenceType.BIWEEKLY
        || appointment.value.recurrence_type.value === RecurrenceType.MONTHLY)
})

const getTextWeekly = computed(() => {
    const date = createFromFormat(appointment.value.start, LOCALE_FORMATS["pt-BR datetime"])
    const dayOfWeek = getDayOfWeek(date)
    return `As consultas ocorrerão <b>semanalmente</b> , toda <b> ${dayOfWeek} </b>`
})

const addWeekDay = () => {
  appointment.value.recurrenceWeeklyDays.push({
    day: 0,
    start: '',
    end: ''
  })
}

const removeWeekDay = (index) => {
  appointment.value.recurrenceWeeklyDays.splice(index, 1)
}

const isLastWeekDay = (index) => {
  if (appointment.value.recurrenceWeeklyDays.length === 1) return false
  return appointment.value.recurrenceWeeklyDays.length - 1 === index
}

const formWeekly = ref(false)

</script>

<template>
  <v-form class="w-full" @submit.prevent="emitSave" v-model="formRef">
    <v-sheet class="p-5 m-5 rounded-md pt-8" elevation="5">

      <v-row>
        <v-col cols="4">
          <v-text-field v-model="appointment.start" label="Data/Hora de Começo" required variant="solo-filled"
            :rules="rules.start" v-maska:[maskDateTime]></v-text-field>
        </v-col>
        <v-col cols="4">
          <v-text-field v-model="appointment.end" label="Data/Hora de Fim" required variant="solo-filled"
            :rules="rules.end" v-maska:[maskDateTime]></v-text-field>
        </v-col>
          <v-col cols="4">
              <v-select :items="servicesSupplied" label="Serviço Fornecido" item-title="name" item-value="id" v-model="appointment.id_service_supplied" variant="solo-filled" :rules="REQUIRED('Serviço Fornecido')">

              </v-select>
          </v-col>
       <v-col cols="12">
           <v-select
               :items="recurrence_types"
               item-value="value"
               item-title="text"
               v-model="appointment.recurrence_type"
               label="Recorrente"
               variant="solo-filled"
               :return-object="true"
               @update:model-value="clearRecurrence"
           ></v-select>
           <v-row  v-if="isRecurrent" >
               <v-col cols="12">
                   <v-sheet width="100%" class="mx-auto  p-5 rounded-md" elevation="5">
                       <p class="text-span font-bold"> Opções de recorência</p>
                       <v-row>
                           <v-col cols="12">
                               <v-checkbox v-model="appointment.useDefaultRecurrence" @click="handleClickUseDefaultRecurrence()">
                                   <template v-slot:label>
                                       <p v-html="getTextRecurrentOption"></p>
                                   </template>
                               </v-checkbox>
                           </v-col>
                           <v-col cols="12">
                               <v-checkbox label="Opções customizadas de recorência" v-model="appointment.useCustomRecurrence" @click="handleClickUseCustomRecurrence()">
                               </v-checkbox>
                           </v-col>
                       </v-row>
                       <v-row v-if="appointment.useCustomRecurrence">
                           <v-col cols="12">
                               <p class="text-span font-bold text-center">
                                   Definir regra de recorrência
                               </p>
                           </v-col>
                           <template v-if="isMonthlyOrBiWeekly">
                               <v-col cols="8">
                                   <v-checkbox :label="`Opções customizadas de recorência (${appointment.recurrence_type.shortDesc})`" v-model="appointment.useCustomRecurrence" @click="handleClickUseCustomRecurrence()">
                                   </v-checkbox>
                               </v-col>
                               <v-col cols="4">
                                   <v-text-field
                                           type="number"
                                           label="Qtd. Eventos"
                                           variant="solo-filled"
                                           v-model="appointment.numberOfRecurrences"
                                           :rules="monthMaxRecurrencyRule()"
                                   ></v-text-field>
                               </v-col>
                           </template>
                         <template v-else>
                             <v-col :cols="appointment.useDefaultRecurrenceWeek ? 8: 12">
                                <v-checkbox  v-model="appointment.useDefaultRecurrenceWeek" @click="handleClickUseDefaultRecurrence(true)" label="Apenas Cadastrar quantidade de consultas semanais">
                                </v-checkbox>
                             </v-col>
                           <v-col cols="4" v-if="appointment.useDefaultRecurrenceWeek">
                             <v-text-field
                                 type="number"
                                 label="Qtd. Eventos"
                                 variant="solo-filled"
                                 v-model="appointment.numberOfRecurrences"
                                 :rules="monthMaxRecurrencyRule()"
                             ></v-text-field>
                           </v-col>
                              <v-col cols="12">
                                  <v-checkbox :label="`Opções customizadas de recorência
                                              (${appointment.recurrence_type.shortDesc})`"
                                              v-model="appointment.useCustomRecurrenceWeek" @click="handleClickUseCustomRecurrence(true)">
                                  </v-checkbox>
                              </v-col>
                              <template v-if="appointment.useCustomRecurrenceWeek">
                                <v-col cols="12">
                                    <v-form v-model="formWeekly">
                                          <v-row v-for="(weekConfig, index) in appointment.recurrenceWeeklyDays">
                                                    <v-col cols="4">
                                                      <v-select v-model="weekConfig.day" :items="DAYS_OF_WEEK" item-title="text" item-value="value" label="Dia" variant="solo-filled" :rules="REQUIRED('Dia')"></v-select>
                                                    </v-col>
                                                    <v-col cols="3">
                                                      <v-text-field v-model="weekConfig.start" label="Hora de começo" variant="solo-filled" v-maska:[maskOnlyTime] :rules="[...REQUIRED('Hora de começo'), ...MIN_LENGTH('Hora de começo', 5)]"></v-text-field>
                                                    </v-col>
                                                    <v-col cols="3">
                                                      <v-text-field v-model="weekConfig.end" label="Hora de fim" variant="solo-filled" v-maska:[maskOnlyTime] :rules="[...REQUIRED('Hora de fim'), ...MIN_LENGTH('Hora de fim', 5)]"></v-text-field>
                                                    </v-col>
                                                  <v-col cols="1">
                                                      <v-btn icon="mdi-plus" @click="addWeekDay()"></v-btn>
                                                  </v-col>
                                                    <v-col cols="1">
                                                      <v-btn icon="mdi-delete" @click="removeWeekDay(index)" v-if="isLastWeekDay(index)"></v-btn>
                                                  </v-col>
                                            </v-row>
                                    </v-form>
                                </v-col>
                              </template>
                         </template>
                       </v-row>
                   </v-sheet>
                   </v-col>
           </v-row>
       </v-col>
        <v-col cols="12">
          <v-textarea v-model="appointment.about" label="Escreva sobre o atendimento" required variant="solo-filled"
            :rules="rules.about"></v-textarea>
        </v-col>
      </v-row>
      <v-row v-if="showPatientSelect">
        <v-col cols="12">
          <v-autocomplete :items="patientsOptions.data" v-model="appointment.patients" item-text="name" item-value="id"
            label="Selecione o(s) paciente(s)" @input.native="requestPatients($event.target.value, false)" :chips="true"
            :rules="rules.patients" multiple variant="solo-filled">
            <template v-slot:chip="{ props, item }">
              <v-chip v-bind="props" :text="item.props.title.name"></v-chip>
            </template>

            <template v-slot:item="{ props, item }">
              <v-list-item v-bind="props" :title="props.title.name"></v-list-item>
            </template>
            <template v-slot:append-item>
              <div v-intersect="endIntersect" />
            </template>
          </v-autocomplete>
        </v-col>
      </v-row>
      <v-row justify="space-between" class="p-5">
        <v-col cols="6">
          <v-row>
            <v-btn text="Voltar" color="warning" @click="back" />
          </v-row>
        </v-col>
        <v-col cols="6">
          <v-row justify="end">
            <v-btn text="Salvar" color="primary" type="submit" />
          </v-row>
        </v-col>
      </v-row>
    </v-sheet>
  </v-form>
</template>

<style scoped></style>
