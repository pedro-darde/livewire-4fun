<script setup>

import {useDateFormatter} from "../../composables/useDateFormatter.js";
import {ref} from "vue";
import {router} from "@inertiajs/vue3";

const props = defineProps({
    currentPatient: {
        type: Object,
        required: false
    }
})

const emit = defineEmits(['save'])
const {
    toDatabaseDate,
    createFromFormat,
    formatDate
} = useDateFormatter()

const patient = ref(props.currentPatient ?? {
    birth_date: formatDate(),
    name: "",
    last_name: "",
    nickname: "",
    email: "",
    phone: "",
    cpf: "",
    rg: ""
})

const maskCPF = {
    mask: '###.###.###-##'
}

const maskRG = {
    mask: "##########"
}

const maskTelefone = {
    mask: "(##) #####-####"
}

const maskDate = {
    mask: "##/##/####"
}

const formRef = ref(null)
const emitSave = () => {
    if (formRef.value)
        emit('save', {
            ...patient.value,
            birth_date: toDatabaseDate(patient.value.birth_date)
        })
}

const back = () => {
    router.visit('/patients')
}

const rules = {
    name: [v => !!v || "Preencha um nome"],
    last_name: [v => !!v || "Preencha um sobrenome"],
    email: [v => !!v || "Preencha um e-mail"],
    telefone: [value => {
        if (value) {
            if (value.length < 14) {
                return 'Telefone inválido'
            }
            return true
        }
        return true
    }],
    cpf: [value => {
        if (value) {
            if (value.length < 14) {
                return 'CPF inválido'
            }
            return true
        }
        return true
    }],
    birth_date: [
        v => !!v || "Preencha uma data de nascimento",
        v => {
            if (v) {
                const date = createFromFormat(v)
                const today = new Date()
                if (date > today) {
                    return 'Data de nascimento inválida'
                }
                return true
            }
            return true
        }
    ]
}
</script>

<template>
    <v-form class="w-full" @submit.prevent="emitSave" v-model="formRef">
        <v-sheet class="p-5 m-5 rounded-md pt-8" elevation="5">
            <v-row>
                <v-col cols="12" md="6">
                    <v-text-field v-model="patient.name" label="Nome" required
                                  variant="solo-filled" :rules="rules.name"></v-text-field>
                </v-col>
                <v-col cols="12" md="6">
                    <v-text-field v-model="patient.last_name" label="Sobrenome" required
                                  variant="solo-filled" :rules="rules.last_name"></v-text-field>
                </v-col>
                <v-col cols="12" md="6">
                    <v-text-field v-model="patient.nickname" label="Apelido" required
                                  variant="solo-filled"></v-text-field>
                </v-col>
                <v-col cols="12" md="6">
                    <v-text-field v-model="patient.email" label="E-mail" required
                                  variant="solo-filled" :rules="rules.email"></v-text-field>
                </v-col>
                <v-col cols="12" md="6">
                    <v-text-field v-model="patient.phone" label="Telefone" required
                                  variant="solo-filled" v-maska:[maskTelefone]
                                  :rules="rules.telefone"></v-text-field>
                </v-col>
                <v-col cols="12" md="6">
                    <v-text-field v-model="patient.cpf" label="CPF" required
                                  variant="solo-filled" v-maska:[maskCPF] :rules="rules.cpf"></v-text-field>
                </v-col>
                <v-col cols="12" md="6">
                    <v-text-field v-model="patient.rg" label="RG" required variant="solo-filled"
                                  v-maska:[maskRG]></v-text-field>
                </v-col>
                <v-col cols="12" md="6">
                    <v-text-field v-model="patient.birth_date" label="Data de Nascimento" required
                                  variant="solo-filled" title="Data de nascimento"
                                  v-maska:[maskDate] :rules="rules.birth_date"></v-text-field>

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
