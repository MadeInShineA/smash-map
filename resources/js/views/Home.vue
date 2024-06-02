<script setup>
import {onMounted, ref, watch} from "vue";
    import {useEventsStore} from "../stores/EventsStore.js";
    import Chart from 'primevue/chart';

const props = defineProps({
    darkMode: {
        type: Boolean,
        required: true
    }
})


const eventsStore = useEventsStore()
eventsStore.fetchGameStatistics()
eventsStore.fetchMonthsStatistics()

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
        // title: {
        //     color: props.darkMode ? 'FFFFFF' : '000000'
        // }
    },
    plugins: {
        legend: {
            // display: false
            display: true,
            labels: {
                color: props.darkMode ? 'white' : 'black'
            },
        }
    },
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
    <template v-if="eventsStore.gamesStatisticsFetched && eventsStore.monthsStatisticsFetched">
        <Chart ref="gamesStatisticsChart" type="doughnut" :data="eventsStore.gamesStatistics.data" id="events-games-chart" :options="doughnutPlotOptions" />
        <Chart ref="monthsStatisticsChart" type="bar" :data="eventsStore.monthsStatistics.data" id="events-months-chart" :options="barPlotOptions" />
    </template>
    <template v-else>
        <LoaderComponent></LoaderComponent>
    </template>
</template>


<style scoped>

#events-games-chart{
    max-height: 60%;
    display: flex;
    justify-content: center;
}

#events-months-chart{
    max-height: 100%;
    display: flex;
    justify-content: center;
}

</style>
