<script setup>
import {onMounted, ref, watch} from "vue";
import {useEventsStore} from "../stores/EventsStore.js";
import Chart from 'primevue/chart';
import { useWindowSize } from '@vueuse/core'

const { width } = useWindowSize()

const props = defineProps({
    darkMode: {
        type: Boolean,
        required: true
    }
})

const eventsStore = useEventsStore()
eventsStore.fetchGameStatistics()


const displayMonthsStatistics = ref(width.value >= 620)

if (displayMonthsStatistics.value){
    eventsStore.fetchMonthsStatistics()
}

watch(width, (newWidth) => {
    displayMonthsStatistics.value = newWidth >= 620
    console.log('Display months statistics', displayMonthsStatistics.value)
}, {immediate: false})

watch(displayMonthsStatistics, (newValue) => {
    if (newValue){
        eventsStore.fetchMonthsStatistics()
    }
}, {immediate: false})

onMounted(()=>{
    console.log('Home Mounted')
})

const doughnutPlotOptions = ref({
    elements: {
        arc: {
            borderWidth: 0
        }
    },
    plugins: {
        legend: {
            labels: {
                color: props.darkMode ? 'white' : 'black'
            },
            }
        }
})

const barPlotOptions = ref({
    indexAxis: 'y',
    scales: {
        x: {
            grid: {
                display: false
            },
            ticks:{
                color: props.darkMode ? 'white' : 'black'
            },
            border:{
                color: props.darkMode ? 'white' : 'black'
            }
        },
        y: {
            grid: {
                display: false
            },
            ticks:{
                color: props.darkMode ? 'white' : 'black'
            },
            border:{
                color: props.darkMode ? 'white' : 'black'
            }
        },
    },
    plugins: {
        legend: {
            display: true,
            labels: {
                color: props.darkMode ? 'white' : 'black'
            },
        }
    },
    responsive: true
})

const gamesStatisticsChart = ref()
const monthsStatisticsChart = ref()

watch(() => props.darkMode, (newValue) => {
    const color =  newValue ? 'white' : 'black'
    doughnutPlotOptions.value.plugins.legend.labels.color = color
    gamesStatisticsChart.value.chart.update()

    barPlotOptions.value.plugins.legend.labels.color = color

    barPlotOptions.value.scales.x.ticks.color = color
    barPlotOptions.value.scales.x.border.color = color

    barPlotOptions.value.scales.y.ticks.color = color
    barPlotOptions.value.scales.y.border.color = color
    monthsStatisticsChart.value.chart.update()

})


</script>

<template>
    <template v-if="eventsStore.gamesStatisticsFetched && (!displayMonthsStatistics || eventsStore.monthsStatisticsFetched)">
        <div id="home-container">
            <h1 id="home-title"> Welcome to Smash Map !</h1>
            <figure>
                <img id="home-image" src="/resources/images/logo-no-text-no-bg.png" alt="Home Image">
                <figcaption>
                    <a href="https://x.com/toastlyzone" target="_blank">Logo by @toastlyzone </a>
                </figcaption>
            </figure>
            <h2>What is Smash Map?</h2>
            <p>
                Smash Map is an open-source website that lists events happening in the competitive scenes for Super Smash Bros. and Rivals of Aether 2.
            </p>
            <div>
                Featuring the following games:
                <p v-for="game in eventsStore.gamesStatistics.data.labels">
                    {{ game }}
                </p>
            </div>
            <p>
                It also provides a platform for players to create their profile and share their information with the community.
            </p>

            <h2>How does Smash Map work?</h2>
            <p>
                Every 10 minutes Smash Map fetches the <a href="https://www.start.gg/" target="_blank">start.gg</a> data for the next 500 events in the different games.
            </p>
            <p>
                Website users can then find events directly on the <router-link to="/map">map page</router-link> or on the <router-link to="/events">events page</router-link>.
                <br>
                They can also create an account to be displayed on the <router-link to="/map">map page</router-link>, follow events and receive notifications.
            </p>

            <h2> What are notifications? </h2>
            <p>
                There are 3 types of notifications:
            </p>
            <h3><i class="pi pi-arrows-h notification-icon"/>Distance notifications</h3>
            <p>
                By accessing their setting page, users can set a distance radius from their location to receive notifications for events appearing on the site in that range.
            </p>

            <h3><i class="pi pi-users notification-icon"/>Attendees notifications</h3>
            <p>
                Users can set different attendees thresholds to receive notifications when one of their followed events reaches that number of attendees.
            </p>
            <h3><i class="pi pi-clock notification-icon"/>Time notifications</h3>
            <p>
                Users can also set some time thresholds to receive notifications when one of their followed events is happening in less then a certain number of days.
            </p>


            <h2>Statistics</h2>
            <p>
                Here are some statistics about the events on Smash Map:
            </p>
            <Chart ref="gamesStatisticsChart" type="doughnut" :data="eventsStore.gamesStatistics.data" id="events-games-chart" :options="doughnutPlotOptions" />
            <p>This diagram represents the distribution of games among the {{eventsStore.gamesStatistics.data.eventCount}} events referenced on the site </p>
            <div id="events-month-chart-container" v-if="displayMonthsStatistics">
                <Chart ref="monthsStatisticsChart" type="bar" :data="eventsStore.monthsStatistics.data" id="events-months-chart" :options="barPlotOptions" />
                <p>This diagram represents the distribution of events per game over the next 6 months of the year</p>
            </div>

        </div>
    </template>
    <template v-else>
        <LoaderComponent></LoaderComponent>
    </template>
</template>


<style scoped>

#home-container {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    margin: 20px 20px;
    text-align: center;
}

#home-title {
    margin-top: 0;
    margin-bottom: 1rem
}

#home-image {
    width: 200px;
    height: 200px;
}

h2{
   margin: 1rem 0;
}

h3{
    margin: 1em 0;
}

p {
    margin: 0.5rem 0;
}

a{
    color: inherit;
    text-decoration: none;
}

#events-months-chart{
    height: 300px;
    width: 600px;
}

.notification-icon{
    margin-right: 1rem;
}

</style>
