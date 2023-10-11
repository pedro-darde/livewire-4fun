<script setup>

import {ref, onMounted, watch} from "vue";
import Layout from "../components/layout/Layout.vue";
import {usePopup} from "../composables/usePopup.js";
import CreateScreen from "../components/screen/create/CreateScreen.vue";
import {RequestProcessor} from "../shared/RequestProcessor";
import {useAlert} from "../composables/useAlert.js";

const { fireError, toast } = useAlert()
const createRegister = () => {
  togglePopup()
}

const props = defineProps({
    screens: {
        type: Array,
    },
    defaultRules: {
        type: Array
    },
})

const saving = ref(false)

const {  open, togglePopup } = usePopup()
const formValid = ref(null)

const create = async (screen) => {

    saving.value = true
    const response = await (new RequestProcessor('/screens', 'post', screen)).process()
    saving.value = false
    if (response.hasErrors()) {
        // fireError('An error has ocurred', response.getErrors(true))
    } else {
        togglePopup()
        toast('Screen created successfully', 'success')
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
      <CreateScreen  :default-rules="defaultRules" :screens="screens" @save="create" :loading="saving"/>
    </v-dialog>
  </Layout>
</template>

<style scoped>

</style>
