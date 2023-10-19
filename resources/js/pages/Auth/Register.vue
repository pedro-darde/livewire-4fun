<script setup>
import {ref} from "vue";
import {router} from '@inertiajs/vue3'


const register = ref({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
})
const saveRegister = () => {
    if (formValid.value) {
        axios.post('/register', register.value).then(() => {
            router.visit('/welcome')
        });
    }
}

const emailRules = [
    v => !!v || 'E-mail é obrigatório',
    v => /.+@.+\..+/.test(v) || 'Informe um e-mail válido'
]

const nameRules = [
    v => !!v || "Informe seu nome"
]

const passwordRules = [
    v => !!v || 'Senha é obrigatório'
]

const passwordConfirmRules = [
    v => !!v || 'Confirme sua senha',
    v => v === register.value.password || 'As senhas não conferem'
]

const showPassword = ref(false)
const showConfirmPassword = ref(false)


const formValid = ref(false)

</script>

<template>
    <div class="d-flex flex-column align-center justify-center" style="height: 100vh">
        <div class="mb-5">
            <h2 class="text-h3"> Crie sua conta </h2>
        </div>
        <v-sheet width="400" class="mx-auto  p-5 rounded-md" elevation="5">
            <v-form fast-fail @submit.prevent="saveRegister" v-model="formValid">
                <v-text-field v-model="register.name"
                              label="Nome"
                              type="text"
                              variant="solo-filled"
                              :rules="nameRules"></v-text-field>

                <v-text-field v-model="register.email" label="E-mail" type="email" variant="solo-filled"
                              :rules="emailRules"></v-text-field>
                <v-text-field
                    v-model="register.password"
                    label="Senha"
                    variant="solo-filled"
                    :rules="passwordRules"
                    :append-inner-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'"
                    :type="showPassword ? 'text' : 'password'"
                    @click:append-inner="showPassword = !showPassword"
                ></v-text-field>
                <v-text-field
                    v-model="register.password_confirmation"
                    label="Confirme sua Senha"
                    variant="solo-filled"
                    :rules="passwordConfirmRules"
                    :append-inner-icon="showConfirmPassword ? 'mdi-eye' : 'mdi-eye-off'"
                    :type="showConfirmPassword ? 'text' : 'password'"
                    @click:append-inner="showConfirmPassword = !showConfirmPassword"
                ></v-text-field>
                <v-btn type="submit" color="primary" block class="mt-2" :disabled="!formValid">Salvar</v-btn>
                <v-btn type="button" color="warning" block class="mt-2">Voltar</v-btn>
            </v-form>

        </v-sheet>
    </div>
</template>

<style scoped>

</style>
