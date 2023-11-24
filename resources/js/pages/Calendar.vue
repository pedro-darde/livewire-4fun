<script setup>
import {onMounted, ref} from "vue";
import CalendarComponent from "../components/Calendar/Calendar.vue";
import Layout from "../components/layout/Layout.vue";
import useRequest from "../composables/useRequest.js";

const appointments = ref([{

}])
const { processRequest } = useRequest()

const month = ref(null)
const year = ref(null)

const requestMoreAppointments = async () => {
    const response = await processRequest(`api/calendar/getEvents?year=${year.value}&month=${month.value}`, 'get', {
        month: month.value,
        year: year.value
    }, true)
    appointments.value = response.getBodyData()
}

/**
 *
 * @param {Date} start
 * @param {Date} end
 */
const loadMore = ({ start, end }) => {
    const yearStart = start.getFullYear()
    const monthStart = start.getMonth() + 1

    console.log({
        yearStart,
        monthStart
    })
    if (yearStart !== year.value || monthStart !== month.value) {
        year.value = yearStart
        month.value = monthStart
        requestMoreAppointments()
    }
}
</script>

<template>
    <Layout>
        <div class="flex items-center justify-center flex-column mt-5">
            <div class="flex flex-row justify-between">
                <h1 class="text-xl font-bold text-center">Agenda</h1>
            </div>
            <CalendarComponent
                :events="appointments"
                @onDateChange="loadMore"
            />
        </div>
    </Layout>

</template>

<style scoped>

</style>
