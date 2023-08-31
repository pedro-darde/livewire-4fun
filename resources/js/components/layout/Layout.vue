<script setup>
import { Link, usePage } from '@inertiajs/vue3'
import {watch} from "vue";

const page = usePage()

const getLinkClass = (url) => {
    const urlWithoutQueryParams = page.url.split('?')[0]
    return urlWithoutQueryParams === url ? 'text-slate-300' : 'text-slate-500 font-bold'
}

</script>

<template>
    <main>
        <header class="flex flex-row items-center justify-center gap-2 p-5 h-24 bg-slate-400">
            <Link href="/" :class="getLinkClass('/')">Home</Link>
            <Link href="/users" :class="getLinkClass('/users')" :only="['registers']">Users</Link>
            <Link href="/screens" :class="getLinkClass('/screens')">Screens</Link>
        </header>
        <article>
            <slot />
        </article>
    </main>
</template>
