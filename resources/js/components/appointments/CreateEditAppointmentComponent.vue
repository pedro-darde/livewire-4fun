<script setup>

import {LOCALE_FORMATS, useDateFormatter} from "../../composables/useDateFormatter.js";
import {ref} from "vue";
import {router} from "@inertiajs/vue3";

const props = defineProps({
  currentAppointment: {
    type: Object,
    required: false
  }
})

const emit = defineEmits(['save'])
const {
  toDatabaseDate,
  createFromFormat,
  formatDate,
  addHoursToDate
} = useDateFormatter()

const appointment = ref(props.currentAppointment ?? {
  start: formatDate(new Date(), LOCALE_FORMATS["pt-BR datetime"]),
  end: formatDate(addHoursToDate(new Date, 1), LOCALE_FORMATS["pt-BR datetime"]),
  abount: "",
  patients: []
})

const patients = ref([])

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

const formRef = ref(null)
const emitSave = () => {
  if (formRef.value)
    emit('save', {
      ...appointment.value,
      start: toDatabaseDate(appointment.value.start),
      end: toDatabaseDate(appointment.value.end),
    })
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
        console.log({
          date,
          startDate
        })
        if (date.getTime() < startDate.getTime()) {
          return 'Data de fim não pode ser menor que a data de começo'
        }
        return true
      }
      return true
    }
  ]
}

const requestPatients = async ({page, itemsPerPage, sortBy, searchString}) => {
    const [{
        key,
        order
    }] = sortBy.length ? sortBy : [{key: '', order: ''}]
    requesting.value = true
    const response = await axios.get('api/patients/loadMore', {
        params: {
            page,
            per_page: itemsPerPage,
            search: searchString,
            order_by: key,
            order_direction: order
        }
    })

    const patients = response.data.patients

    if (itemsPerPage === -1) {
        registers.value = {
            per_page: -1,
            total: patients.length,
            data: patients,

        }
    } else {
        registers.value = patients
    }
    requesting.value = false
}

</script>

<template>
  <v-form class="w-full" @submit.prevent="emitSave" v-model="formRef">
    <v-sheet class="p-5 m-5 rounded-md pt-8" elevation="5">
      <!--          <v-col cols="8">-->
      <!--            <v-autocomplete-->
      <!--                :items="icons.data"-->
      <!--                v-model="screen.icon"-->
      <!--                item-text="name"-->
      <!--                item-value="id"-->
      <!--                label="Select an icon"-->
      <!--                @input.native="loadIcons($event.target.value, false)"-->
      <!--                :chips="true"-->
      <!--                variant="solo-filled"-->
      <!--                :rules="rules.icon"-->
      <!--            >-->
      <!--              <template v-slot:chip="{ props, item }">-->
      <!--                <v-chip-->
      <!--                    v-bind="props"-->
      <!--                    :prepend-icon="`mdi-${item?.value}`"-->
      <!--                    :text="item.value"-->
      <!--                ></v-chip>-->
      <!--              </template>-->

      <!--              <template v-slot:item="{ props, item }">-->
      <!--                <v-list-item-->
      <!--                    v-bind="props"-->
      <!--                    :prepend-icon="`mdi-${item?.value}`"-->
      <!--                    :title="item?.value"-->
      <!--                ></v-list-item>-->
      <!--              </template>-->
      <!--              <template v-slot:append-item>-->
      <!--                <div v-intersect="endIntersect"/>-->
      <!--              </template>-->
      <!--            </v-autocomplete>-->
      <!--          </v-col>-->
      <v-row>
        <v-col cols="12" md="6">
          <v-text-field v-model="appointment.start" label="Data/Hora de Começo" required
                        variant="solo-filled" :rules="rules.start" v-maska:[maskDateTime]></v-text-field>
        </v-col>
        <v-col cols="12" md="6">
          <v-text-field v-model="appointment.end" label="Data/Hora de Fim" required
                        variant="solo-filled" :rules="rules.end" v-maska:[maskDateTime]></v-text-field>
        </v-col>
        <v-col cols="12">
          <v-textarea v-model="appointment.about" label="Escreva sobre o atendimento" required
                      variant="solo-filled" :rules="rules.about"></v-textarea>
        </v-col>

      </v-row>
      <v-row justify="space-between" class="p-5">
        <v-col cols="6">
          <v-row>
            <v-btn text="Voltar" color="warning" @click="back"/>
          </v-row>
        </v-col>
        <v-col cols="6">
          <v-row justify="end">
            <v-btn text="Salvar" color="primary" type="submit"/>
          </v-row>
        </v-col>
      </v-row>
    </v-sheet>
  </v-form>
</template>

<style scoped>

</style>
