<script setup>
import FullCalendar from '@fullcalendar/vue3'
import dayGridPlugin from '@fullcalendar/daygrid'
import interactionPlugin from '@fullcalendar/interaction'
import listPlugin from '@fullcalendar/list'
import timeGridPlugin from '@fullcalendar/timegrid'
import {computed, onMounted, ref,nextTick} from "vue";


const emit = defineEmits(['onClickEvent'])

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
            allDay: false,
            extendedProps: appointment
        }
    })
})

const eventClick = (args) => {
    const  data  = args.event.extendedProps
    emit('onClickEvent', data)
}

const select = (args) => {
    console.log('select',args)
}

const calendarOptions = {
    plugins: [
        dayGridPlugin,
        timeGridPlugin,
        interactionPlugin // needed for dateClick
    ],

    headerToolbar: {
        left: "prev,next today",
        center: "title",
        right: "dayGridMonth,timeGridWeek,timeGridDay",
    },
    initialView: 'dayGridWeek',
    events: appointmentsToEventStyle.value,
    editable: false,
    selectable: true,
    selectMirror: true,
    dayMaxEvents: true,
    weekends: true,
    timeZone: "America/Sao_Paulo",
    locale: "pt-br",
    buttonText: {
        today: "Hoje",
        month: "Mês",
        week: "Semana",
        day: "Hoje",
        list: "Lista",
    },
    selectConstraint: 'businessHours',
    themeSystem: "bootstrap5",
    eventClick,
    select,
    dateClick: handleDateClick,

}

const refCalendar = ref(null)

onMounted(() => {
    const calendarApi = refCalendar.value.getApi()
    nextTick(() => {
      calendarApi.render();
    });

    setTimeout(function () {
      window.dispatchEvent(new Event("resize"));
    }, 1);

})
</script>

<template>
    <FullCalendar :options="calendarOptions" ref="refCalendar" class="full-calendar"
    >

    </FullCalendar>
</template>

<style scoped>

</style>
