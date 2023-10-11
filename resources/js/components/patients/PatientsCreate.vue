<script setup>
import {ref} from "vue";

const patient = ref({
  birth_date: null,
  name: "",
  last_name: "",
  nickname: "",
  email:"",
  phone: "",
  cpf: "",
  rg: ""
})
</script>

<template>
  <div>
    <h1>Criar Paciente</h1>
    <v-form>
      <v-row>
        <v-col cols="12" md="6">
          <v-text-field v-model="patient.name" label="Nome" required></v-text-field>
        </v-col>
        <v-col cols="12" md="6">
          <v-text-field v-model="patient.last_name" label="Sobrenome" required></v-text-field>
        </v-col>
        <v-col cols="12" md="6">
          <v-text-field v-model="patient.nickname" label="Apelido" required></v-text-field>
        </v-col>
        <v-col cols="12" md="6">
          <v-text-field v-model="patient.email" label="E-mail" required></v-text-field>
        </v-col>
        <v-col cols="12" md="6">
          <v-text-field v-model="patient.phone" label="Telefone" required></v-text-field>
        </v-col>
        <v-col cols="12" md="6">
          <v-text-field v-model="patient.cpf" label="CPF" required></v-text-field>
        </v-col>
        <v-col cols="12" md="6">
          <v-text-field v-model="patient.rg" label="RG" required></v-text-field>
        </v-col>
        <v-col cols="12" md="6">
          <v-date-picker v-model="patient.birth_date" label="Data de Nascimento" required></v-text-field>
        </v-col>
      </v-row>
    </v-form>
  </div>
</template>

<style scoped>

</style>
