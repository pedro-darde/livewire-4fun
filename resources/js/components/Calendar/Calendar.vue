<script setup>
import FullCalendar from '@fullcalendar/vue3'
import dayGridPlugin from '@fullcalendar/daygrid'
import interactionPlugin from '@fullcalendar/interaction'
import listPlugin from '@fullcalendar/list'
import timeGridPlugin from '@fullcalendar/timegrid'
import {computed, onMounted, ref,nextTick} from "vue";


const emit = defineEmits(['onClickEvent', 'onDateChange'])

const handleDateClick = (args) => {
    console.log(args)
}

const props = defineProps({
   events: {
       type: Array,
       required: true
   }
})


const eventsMap = computed(() => {
    return props.events.map(event => {
        return {
            id: event.id,
            title: event.title,
            start: event.start,
            end: event.end,
            allDay: false,
            extendedProps: event
        }
    })
})

const eventClick = (args) => {
    const  data  = args.event.extendedProps
    emit('onClickEvent', data)
}

const onDateSet = (args) => {
    emit('onDateChange', args)
    console.log('onDateSet',args)
}

const select = (args) => {
    console.log('select',args)
}

const calendarOptions = computed(() => ({
    plugins: [
        dayGridPlugin,
        timeGridPlugin,
        interactionPlugin
    ],
    headerToolbar: {
        left: "prev,next today",
        center: "title",
        right: "dayGridMonth,timeGridWeek,timeGridDay",
    },
    initialView: 'dayGridWeek',
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
    datesSet: onDateSet,
    events: eventsMap.value
}))

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
    <FullCalendar :options="calendarOptions" ref="refCalendar" class="full-calendar">
    </FullCalendar>
</template>

<style scoped>

</style>
