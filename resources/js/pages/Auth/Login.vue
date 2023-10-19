<script setup>
import { ref } from "vue";
import { Link, router } from '@inertiajs/vue3'
import useRequest from "../../composables/useRequest";
import { useAlert } from "../../composables/useAlert";

const password = ref('')
const username = ref('')
const { processRequest } = useRequest()
const { fireError } = useAlert()

const login = async () => {
    if (formValid.value) {
        const response = await processRequest('login', 'post', {
            email: username.value,
            password: password.value,
            remember: false
        });
        if (response.hasErrors()) {
            fireError(response.getErrors(true))
            return;
        }
        localStorage.setItem('token', response.getBody().token)
        window.axios.defaults.headers.common["Authorization"] = "Bearer " + response.getBody().token
        router.visit('/welcome')
    }
}

const emailRules = [
    v => !!v || 'E-mail é obrigatório',
    v => /.+@.+\..+/.test(v) || 'Informe um e-mail válido'
]

const passwordRules = [
    v => !!v || 'Senha é obrigatório'
]

const showPassword = ref(false)


const formValid = ref(false)

</script>

<template>
    <div class="d-flex flex-column align-center justify-center" style="height: 100vh">
        <div class="mb-5">
            <h2 class="text-h3"> Atend.IO </h2>
        </div>
        <v-sheet width="400" class="mx-auto  p-5 rounded-md" elevation="5">
            <v-form fast-fail @submit.prevent="login" v-model="formValid">
                <v-text-field v-model="username" label="E-mail" type="email" variant="solo-filled"
                    :rules="emailRules"></v-text-field>
                <v-text-field v-model="password" label="Senha" variant="solo-filled" :rules="passwordRules"
                    :append-inner-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'" :type="showPassword ? 'text' : 'password'"
                    @click:append-inner="showPassword = !showPassword"></v-text-field>
                <a href="#" class="text-body-2 font-weight-regular">Esqueceu a senha ?</a>
                <v-btn type="submit" color="primary" block class="mt-2">Sign in</v-btn>
            </v-form>
            <div class="mt-2">
                <p class="text-body-2">Não possuí uma conta ?
                    <Link href="/register">Cadastre-se</Link>
                </p>
            </div>
        </v-sheet>
    </div>
</template>

<style scoped></style>
