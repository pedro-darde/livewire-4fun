<script setup>

import {ref, onMounted, watch} from "vue";
import Layout from "../components/layout/Layout.vue";
import {usePopup} from "../composables/usePopup.js";
import CreateScreen from "../components/screen/create/CreateScreen.vue";
const createRegister = () => {
  togglePopup()
}

const props = defineProps({
    screens: {
        type: Array,
    },
    defaultRules: {
        type: Array
    }
})

const {  open, togglePopup } = usePopup()
const formValid = ref(null)

const create = async () => {
    const { valid, errors } = await formValid.value.validate()
    if (valid) {

    }
}

</script>
<template>
  <Layout>
    <h1>Screen Generator</h1>
    <v-container>
      <v-col>
        <v-btn color="primary" @click="createRegister" append-icon="mdi-plus">Create New Screen</v-btn>
      </v-col>
    </v-container>
    <v-dialog v-model="open">
      <CreateScreen  :default-rules="defaultRules" :screens="screens"/>
    </v-dialog>
  </Layout>
</template>

<style scoped>

</style>
