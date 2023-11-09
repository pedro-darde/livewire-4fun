<script setup>
import FullCalendar from '@fullcalendar/vue3'
import dayGridPlugin from '@fullcalendar/daygrid'
import interactionPlugin from '@fullcalendar/interaction'
import listPlugin from '@fullcalendar/list'
import timeGridPlugin from '@fullcalendar/timegrid'
import {computed} from "vue";


const handleDateClick = (args) => {
    console.log(args)
}

const props = defineProps({
   appointments: {
       type: Array,
       required: true
   }
})

const appointmentsToEventStyle = computed(() => {
    return props.appointments.map(appointment => {
        return {
            id: appointment.id,
            title: appointment.title,
            start: appointment.start,
            end: appointment.end,
            allDay: false
        }
    })
})

const calendarOptions = {
    plugins: [
        dayGridPlugin,
        timeGridPlugin,
        interactionPlugin // needed for dateClick
    ],
    headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        right: 'dayGridMonth,timeGridWeek,timeGridDay'
    },
    initialView: 'dayGridWeek',
    dateClick: handleDateClick,
    events: appointmentsToEventStyle.value,
    editable: true,
    selectable: true,
    selectMirror: true,
    dayMaxEvents: true,
    weekends: true,
    selectConstraint: 'businessHours'
}
</script>

<template>
    <FullCalendar :options="calendarOptions"
    >
<!--        <template v-slot:eventContent='arg'>-->
<!--            <b>{{ arg.timeText }}</b>-->
<!--            <i class="text-ellipsis">{{ arg.event.title }}</i>-->
<!--        </template>-->
    </FullCalendar>
</template>

<style scoped>

</style>
