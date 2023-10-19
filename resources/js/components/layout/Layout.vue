<script setup>
import { Link, usePage } from '@inertiajs/vue3'
import { onMounted, ref, watch } from "vue";
import { RequestProcessor } from "../../shared/RequestProcessor";

const page = usePage()

const screens = ref([])
console.log(page.props)

onMounted(async () => {
  const baseUrl = page.props.app.base_url
  const data = (await (new RequestProcessor(`${baseUrl}/api/screens`)).processCached()).getBody()
  screens.value = data.screens
})

const getLinkClass = (url) => {
  const urlWithoutQueryParams = page.url.split('?')[0]
  return urlWithoutQueryParams === url ? 'text-slate-300' : 'text-slate-500 font-bold'
}

</script>

<template>
  <main>
    <header class="flex flex-row items-center justify-center gap-2 p-5 h-24 bg-slate-400">
      <Link href="/welcome" :class="getLinkClass('/')">Home</Link>
      <Link href="/patients" :class="getLinkClass('/patients')">Pacientes</Link>
      <!-- <Link href="/appointments" :class="getLinkClass('/appointments')">Consultas</Link> -->

      <!--            <Link href="/screens" :class="getLinkClass('/screens')">Screens</Link>-->
      <!--            <Link v-for="screen in screens" :href="`/screens/dynamic/${screen.url}`" :class="getLinkClass(`/screens/dynamic/${screen.url}`)">-->
      <!--                <v-icon> mdi-{{ screen.icon }}</v-icon>-->
      <!--                {{ screen.name }}-->
      <!--            </Link>-->
    </header>
    <article>
      <slot />
    </article>
  </main>
</template>

<style>
.swal2-container {
  z-index: 3000;
}
</style>
