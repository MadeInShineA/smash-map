<script setup>
import { computed } from "vue";
import { useEventsStore } from "../stores/EventsStore.js";
import Chart from "primevue/chart";
import { useWindowSize } from "@vueuse/core";

const props = defineProps({
    darkMode: {
        type: Boolean,
        required: true,
    },
});

const eventsStore = useEventsStore();
eventsStore.fetchGameStatistics();
eventsStore.fetchMonthsStatistics();

const { width } = useWindowSize();
const isNarrow = computed(() => width.value < 620);

const numberFormat = new Intl.NumberFormat("en-US");
const formatNumber = (value) => numberFormat.format(value ?? 0);
function formatShare(value, total) {
    if (!total || !value) {
        return "0%";
    }
    const share = (value / total) * 100;
    return share < 1 ? "<1%" : `${Math.round(share)}%`;
}

/*
 * Stacking order for the monthly chart. Game colors are fixed brand colors;
 * this order keeps similar hues (Melee / Brawl reds) apart so neighbouring
 * segments stay distinguishable, including for color-blind readers.
 */
const STACK_ORDER = ["Ultimate", "Melee", "Project +", "Rivals 2", "HDR", "Project M", "64", "3DS / WiiU", "Brawl"];

// Chart ink follows the PrimeVue theme (mdc light / dark). Charts sit on cards.
const theme = computed(() =>
    props.darkMode
        ? {
              surface: "#1e1e1e",
              text: "rgba(255, 255, 255, 0.87)",
              muted: "rgba(255, 255, 255, 0.6)",
              grid: "rgba(255, 255, 255, 0.08)",
              tooltipBackground: "#2c2c2c",
          }
        : {
              surface: "#ffffff",
              text: "rgba(0, 0, 0, 0.87)",
              muted: "rgba(0, 0, 0, 0.6)",
              grid: "rgba(0, 0, 0, 0.06)",
              tooltipBackground: "#ffffff",
          },
);

const tooltipStyle = computed(() => ({
    backgroundColor: theme.value.tooltipBackground,
    titleColor: theme.value.text,
    bodyColor: theme.value.text,
    footerColor: theme.value.muted,
    borderColor: theme.value.grid,
    borderWidth: 1,
    padding: 10,
    boxPadding: 4,
    usePointStyle: true,
    titleFont: { weight: "600" },
    footerFont: { weight: "400" },
}));

/*
 * Draws values on the charts: at the end of horizontal bars ("bar-end")
 * or as the total above vertical stacked bars ("stack-total").
 * Text uses the theme ink, never the series color.
 */
const valueLabelsPlugin = {
    id: "valueLabels",
    afterDatasetsDraw(chart, _args, options) {
        if (!options?.mode) {
            return;
        }
        const { ctx } = chart;
        ctx.save();
        ctx.font = `500 ${options.fontSize || 12}px Roboto, system-ui, sans-serif`;
        ctx.fillStyle = options.color;

        if (options.mode === "bar-end") {
            ctx.textAlign = "left";
            ctx.textBaseline = "middle";
            chart.getDatasetMeta(0).data.forEach((bar, index) => {
                ctx.fillText(options.format(index), bar.x + 8, bar.y);
            });
        } else if (options.mode === "stack-total") {
            ctx.textAlign = "center";
            ctx.textBaseline = "bottom";
            chart.data.labels.forEach((_, index) => {
                const total = chart.data.datasets.reduce(
                    (sum, dataset, datasetIndex) =>
                        chart.isDatasetVisible(datasetIndex)
                            ? sum + (dataset.data[index] || 0)
                            : sum,
                    0,
                );
                const x = chart.scales.x.getPixelForValue(index);
                const y = chart.scales.y.getPixelForValue(total);
                ctx.fillText(formatNumber(total), x, y - 6);
            });
        }
        ctx.restore();
    },
};

const statistics = computed(() => eventsStore.gamesStatistics?.data);
const summary = computed(() => statistics.value?.summary);

// Games with events, biggest first
const games = computed(() =>
    (statistics.value?.games ?? [])
        .filter((game) => game.total > 0)
        .sort((a, b) => b.total - a.total),
);

const sourceShare = computed(() => {
    const total = summary.value?.eventCount || 0;
    const startGG = summary.value?.startGGCount || 0;
    const parryGG = summary.value?.parryGGCount || 0;
    // Rounded shares that add up to 100% (945 / 55 -> 95% / 5%, not 95% / 6%)
    let startLabel = formatShare(startGG, total);
    let parryLabel = formatShare(parryGG, total);
    if (startGG && parryGG && startGG + parryGG === total) {
        const startRounded = Math.round((startGG / total) * 100);
        if (startRounded > 0 && startRounded < 100) {
            startLabel = `${startRounded}%`;
            parryLabel = `${100 - startRounded}%`;
        }
    }
    return {
        startGG: total ? (startGG / total) * 100 : 0,
        parryGG: total ? (parryGG / total) * 100 : 0,
        startLabel,
        parryLabel,
    };
});

// start.gg / parry.gg colors, lighter steps in dark mode
const sourceColors = computed(() =>
    props.darkMode
        ? { "--source-start": "#9fa8da", "--source-parry": "#f0a46b" }
        : { "--source-start": "#3f51b5", "--source-parry": "#e8833a" },
);

function horizontalBarOptions(items, valueKey, tooltipLines) {
    const total = items.reduce((sum, item) => sum + item[valueKey], 0);
    return {
        indexAxis: "y",
        maintainAspectRatio: false,
        animation: false,
        // Room for the value labels at the end of the bars
        layout: { padding: { right: isNarrow.value ? 70 : 84 } },
        // Hover / tap anywhere on a row, thin bars are hard to hit on phones
        interaction: { mode: "index", axis: "y", intersect: false },
        scales: {
            x: {
                beginAtZero: true,
                grid: { color: theme.value.grid },
                border: { display: false },
                ticks: { color: theme.value.muted, precision: 0, maxTicksLimit: 6 },
            },
            y: {
                grid: { display: false },
                border: { display: false },
                ticks: { color: theme.value.text, font: { size: isNarrow.value ? 11 : 13 } },
            },
        },
        plugins: {
            // A single series: the axis names each bar
            legend: { display: false },
            tooltip: {
                ...tooltipStyle.value,
                displayColors: false,
                callbacks: {
                    label: (context) => tooltipLines(items[context.dataIndex]),
                },
            },
            valueLabels: {
                mode: "bar-end",
                color: theme.value.text,
                fontSize: isNarrow.value ? 11 : 12,
                format: (index) =>
                    `${formatNumber(items[index][valueKey])}  ·  ${formatShare(items[index][valueKey], total)}`,
            },
        },
    };
}

function horizontalBarData(items, valueKey, label) {
    return {
        labels: items.map((item) => item.name),
        datasets: [
            {
                label,
                data: items.map((item) => item[valueKey]),
                backgroundColor: items.map((item) => item.color),
                borderRadius: 4,
                borderSkipped: "start",
                barThickness: 16,
            },
        ],
    };
}

const barHeight = (count) => `${Math.max(count, 1) * 34 + 40}px`;

const gamesChartData = computed(() => horizontalBarData(games.value, "total", "Events"));
const gamesChartOptions = computed(() =>
    horizontalBarOptions(games.value, "total", (game) => [
        `start.gg: ${formatNumber(game.startGG)}`,
        `parry.gg: ${formatNumber(game.parryGG)}`,
    ]),
);

// Registered players per game (from their profile), biggest first
const playerGames = computed(() =>
    (statistics.value?.games ?? [])
        .filter((game) => game.players > 0)
        .sort((a, b) => b.players - a.players),
);


const months = computed(() => eventsStore.monthsStatistics?.data);

const monthsChartData = computed(() => {
    const datasets = (months.value?.datasets ?? [])
        .filter((dataset) => dataset.data.some((value) => value > 0))
        .sort((a, b) => STACK_ORDER.indexOf(a.label) - STACK_ORDER.indexOf(b.label))
        .map((dataset) => ({
            label: dataset.label,
            data: dataset.data,
            backgroundColor: dataset.backgroundColor[0],
            hoverBackgroundColor: dataset.hoverBackgroundColor[0],
            // 2px gap between stacked segments, in the card color
            borderColor: theme.value.surface,
            borderWidth: { top: 2, right: 0, bottom: 0, left: 0 },
            borderSkipped: "start",
            maxBarThickness: 48,
        }));
    return { labels: months.value?.labels ?? [], datasets };
});

const monthsChartOptions = computed(() => ({
    maintainAspectRatio: false,
    animation: false,
    // Room for the totals above the bars
    layout: { padding: { top: 22 } },
    interaction: { mode: "index", intersect: false },
    scales: {
        x: {
            stacked: true,
            grid: { display: false },
            border: { color: theme.value.grid },
            ticks: {
                color: theme.value.text,
                maxRotation: 0,
                // "Nov" instead of "November" on phones
                callback(value) {
                    const label = this.getLabelForValue(value);
                    return isNarrow.value ? label.slice(0, 3) : label;
                },
            },
        },
        y: {
            stacked: true,
            beginAtZero: true,
            grid: { color: theme.value.grid },
            border: { display: false },
            ticks: { color: theme.value.muted, precision: 0, maxTicksLimit: 6 },
        },
    },
    plugins: {
        legend: {
            position: "bottom",
            labels: {
                color: theme.value.text,
                usePointStyle: true,
                pointStyle: "rectRounded",
                boxWidth: 10,
                boxHeight: 10,
                padding: isNarrow.value ? 10 : 16,
                font: { size: isNarrow.value ? 11 : 12 },
            },
        },
        tooltip: {
            ...tooltipStyle.value,
            itemSort: (a, b) => b.raw - a.raw,
            filter: (item) => item.raw > 0,
            callbacks: {
                label: (context) =>
                    ` ${context.dataset.label}: ${formatNumber(context.raw)}`,
                footer: (items) =>
                    `Total: ${formatNumber(items.reduce((sum, item) => sum + item.raw, 0))}`,
            },
        },
        valueLabels: {
            mode: "stack-total",
            color: theme.value.text,
        },
    },
}));

</script>

<template>
    <template
        v-if="
            eventsStore.gamesStatisticsFetched &&
            eventsStore.monthsStatisticsFetched
        "
    >
        <div id="home-container">
            <h1 id="home-title">Welcome to Smash Map !</h1>
            <figure>
                <img
                    id="home-image"
                    src="/resources/images/logo-no-text-no-bg.png"
                    alt="Home Image"
                />
                <figcaption>
                    <a href="https://x.com/toastlyzone" target="_blank"
                        >Logo by @toastlyzone
                    </a>
                </figcaption>
            </figure>
            <h2>What is Smash Map?</h2>
            <p>
                Smash Map is an open-source website that lists events happening
                in the competitive scenes for Super Smash Bros. and Rivals of
                Aether 2.
            </p>
            <div>
                Featuring the following games:
                <p v-for="game in eventsStore.gamesStatistics.data.labels">
                    {{ game }}
                </p>
            </div>
            <p>
                It also provides a platform for players to create their profile
                and share their information with the community.
            </p>

            <h2>How does Smash Map work?</h2>
            <p>
                Smash Map regularly fetches the
                <a href="https://www.start.gg/" target="_blank">start.gg</a>
                and
                <a href="https://parry.gg/" target="_blank">parry.gg</a>
                data for upcoming events in the different games.
            </p>
            <p>
                Website users can then find events directly on the
                <router-link to="/map">map page</router-link> or on the
                <router-link to="/events">events page</router-link>.
                <br />
                They can also create an account to be displayed on the
                <router-link to="/map">map page</router-link>, follow events and
                receive notifications.
            </p>

            <h2>What are notifications?</h2>
            <p>There are 3 types of notifications:</p>
            <h3>
                <i class="pi pi-arrows-h notification-icon" />Distance
                notifications
            </h3>
            <p>
                By accessing their setting page, users can set a distance radius
                from their location to receive notifications for events
                appearing on the site in that range.
            </p>

            <h3>
                <i class="pi pi-users notification-icon" />Attendees
                notifications
            </h3>
            <p>
                Users can set different attendees thresholds to receive
                notifications when one of their followed events reaches that
                number of attendees.
            </p>
            <h3>
                <i class="pi pi-clock notification-icon" />Time notifications
            </h3>
            <p>
                Users can also set some time thresholds to receive notifications
                when one of their followed events is happening in less then a
                certain number of days.
            </p>

            <section
                id="statistics"
                aria-labelledby="statistics-title"
                :style="sourceColors"
            >
                <header id="statistics-header">
                    <h2 id="statistics-title">Statistics</h2>
                    <p class="statistics-meta">
                        Upcoming events from start.gg and parry.gg
                    </p>
                </header>

                <div v-if="summary" id="stat-tiles">
                    <div class="stat-tile stat-tile-hero">
                        <span class="stat-label">Upcoming events</span>
                        <span class="stat-value">{{
                            formatNumber(summary.eventCount)
                        }}</span>
                        <span class="stat-caption">
                            {{ formatNumber(summary.next30DaysCount) }} in the
                            next 30 days
                        </span>
                        <div class="source-split">
                            <div
                                class="source-bar"
                                role="img"
                                :aria-label="`${formatNumber(summary.startGGCount)} from start.gg, ${formatNumber(summary.parryGGCount)} from parry.gg`"
                            >
                                <span
                                    class="source-bar-start"
                                    :style="{ width: sourceShare.startGG + '%' }"
                                ></span>
                                <span
                                    class="source-bar-parry"
                                    :style="{ width: sourceShare.parryGG + '%' }"
                                ></span>
                            </div>
                            <div class="source-legend">
                                <span>
                                    <i class="source-dot source-dot-start"></i>
                                    start.gg
                                    <strong>{{ formatNumber(summary.startGGCount) }}</strong>
                                    <span class="source-share">{{
                                        sourceShare.startLabel
                                    }}</span>
                                </span>
                                <span>
                                    <i class="source-dot source-dot-parry"></i>
                                    parry.gg
                                    <strong>{{ formatNumber(summary.parryGGCount) }}</strong>
                                    <span class="source-share">{{
                                        sourceShare.parryLabel
                                    }}</span>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="stat-tile">
                        <span class="stat-label">Countries</span>
                        <span class="stat-value">{{
                            formatNumber(summary.countryCount)
                        }}</span>
                    </div>
                    <div class="stat-tile">
                        <span class="stat-label">Registered players</span>
                        <span class="stat-value">{{
                            formatNumber(summary.playerCount)
                        }}</span>
                    </div>
                </div>

                <div class="chart-card">
                    <div class="chart-header">
                        <h3 class="chart-title">Events per game</h3>
                        <p class="chart-subtitle">
                            Upcoming events and share of the total
                        </p>
                    </div>
                    <div
                        class="chart-canvas"
                        :style="{ height: barHeight(games.length) }"
                        role="img"
                        aria-label="Bar chart of upcoming events per game"
                    >
                        <Chart
                            type="bar"
                            :data="gamesChartData"
                            :options="gamesChartOptions"
                            :plugins="[valueLabelsPlugin]"
                            id="events-games-chart"
                        />
                    </div>
                </div>

                <div v-if="playerGames.length" class="chart-card">
                    <div class="chart-header">
                        <h3 class="chart-title">Players per game</h3>
                        <p class="chart-subtitle">
                            Registered players by the games on their profile and
                            share of all players (a player can pick several games)
                        </p>
                    </div>
                    <ul class="player-grid">
                        <li
                            v-for="game in playerGames"
                            :key="game.id"
                            class="player-item"
                            :style="{ '--game-color': game.color }"
                        >
                            <span class="player-game">{{ game.name }}</span>
                            <span class="player-count">{{
                                formatNumber(game.players)
                            }}</span>
                            <span class="player-unit">
                                {{ game.players === 1 ? "player" : "players" }}
                                <span class="meta-separator">·</span>
                                <span class="player-share">{{
                                    formatShare(game.players, summary.playerCount)
                                }}</span>
                                of all
                            </span>
                        </li>
                    </ul>
                </div>

                <div class="chart-card">
                    <div class="chart-header">
                        <h3 class="chart-title">Events in the next 6 months</h3>
                        <p class="chart-subtitle">
                            By start month, stacked by game
                        </p>
                    </div>
                    <div
                        class="chart-canvas"
                        id="events-months-canvas"
                        role="img"
                        aria-label="Stacked bar chart of events per month and game"
                    >
                        <Chart
                            type="bar"
                            :data="monthsChartData"
                            :options="monthsChartOptions"
                            :plugins="[valueLabelsPlugin]"
                            id="events-months-chart"
                        />
                    </div>
                </div>
            </section>
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
    margin-bottom: 1rem;
}

#home-image {
    width: 200px;
    height: 200px;
}

h2 {
    margin: 1rem 0;
}

h3 {
    margin: 1em 0;
}

p {
    margin: 0.5rem 0;
}

a {
    color: inherit;
    text-decoration: none;
}

.notification-icon {
    margin-right: 1rem;
}

/* Statistics */

#statistics {
    width: 100%;
    max-width: 760px;
    margin-top: 1.5rem;
    text-align: left;
}

#statistics-header {
    text-align: center;
    margin-bottom: 16px;
}

#statistics-header h2 {
    margin-bottom: 0.25rem;
}

.statistics-meta {
    margin: 0;
    font-size: 0.8rem;
    color: var(--text-color-secondary);
}

.meta-separator {
    margin: 0 6px;
}

/* Hero on the left, the two small tiles stacked on the right */
#stat-tiles {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 12px;
    margin-bottom: 12px;
}

.stat-tile-hero {
    grid-row: span 2;
}

.stat-tile:not(.stat-tile-hero) {
    justify-content: center;
}

.stat-tile,
.chart-card {
    background: var(--surface-card);
    border: 1px solid var(--surface-border);
    border-radius: 8px;
}

.stat-tile {
    display: flex;
    flex-direction: column;
    padding: 16px 18px;
}

.stat-label {
    font-size: 0.7rem;
    font-weight: 500;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    color: var(--text-color-secondary);
}

.stat-value {
    margin-top: 6px;
    font-size: 1.9rem;
    font-weight: 500;
    line-height: 1.1;
    color: var(--text-color);
    font-variant-numeric: tabular-nums;
}

.stat-tile-hero .stat-value {
    font-size: 2.4rem;
}

.stat-caption {
    margin-top: 4px;
    font-size: 0.8rem;
    color: var(--text-color-secondary);
    font-variant-numeric: tabular-nums;
}

.source-split {
    margin-top: 14px;
}

.source-bar {
    display: flex;
    gap: 2px;
    height: 6px;
    border-radius: 3px;
    overflow: hidden;
    background: var(--surface-border);
}

.source-bar span {
    display: block;
    height: 100%;
}

.source-bar-start,
.source-dot-start {
    background: var(--source-start);
}

.source-bar-parry,
.source-dot-parry {
    background: var(--source-parry);
}

.source-legend {
    display: flex;
    flex-wrap: wrap;
    gap: 4px 18px;
    margin-top: 8px;
    font-size: 0.8rem;
    color: var(--text-color-secondary);
}

.source-legend strong {
    color: var(--text-color);
    font-weight: 500;
    margin-left: 4px;
    font-variant-numeric: tabular-nums;
}

.source-share {
    margin-left: 4px;
    font-variant-numeric: tabular-nums;
}

.source-dot {
    display: inline-block;
    width: 8px;
    height: 8px;
    border-radius: 2px;
    margin-right: 4px;
}

.chart-card {
    padding: 16px 18px 14px;
    margin-bottom: 12px;
}

.chart-header {
    padding-bottom: 12px;
    margin-bottom: 12px;
    border-bottom: 1px solid var(--surface-border);
}

.chart-title {
    margin: 0;
    font-size: 0.95rem;
    font-weight: 500;
}

.chart-subtitle {
    margin: 2px 0 0;
    font-size: 0.8rem;
    color: var(--text-color-secondary);
}

.chart-canvas {
    position: relative;
    width: 100%;
}

.player-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(130px, 1fr));
    gap: 10px;
    margin: 4px 0 0;
    padding: 0;
    list-style: none;
}

.player-item {
    display: flex;
    flex-direction: column;
    padding: 10px 12px 10px 14px;
    border: 1px solid var(--surface-border);
    border-left: 3px solid var(--game-color);
    border-radius: 6px;
}

.player-game {
    font-size: 0.8rem;
    color: var(--text-color-secondary);
}

.player-count {
    margin-top: 2px;
    font-size: 1.5rem;
    font-weight: 500;
    line-height: 1.2;
    color: var(--text-color);
    font-variant-numeric: tabular-nums;
}

.player-unit {
    font-size: 0.75rem;
    color: var(--text-color-secondary);
    font-variant-numeric: tabular-nums;
}

.player-unit .meta-separator {
    margin: 0 4px;
}

.player-share {
    color: var(--text-color);
}

#events-months-canvas {
    height: 340px;
}

.chart-canvas :deep(.p-chart) {
    position: relative;
    height: 100%;
    width: 100%;
}

@media (max-width: 620px) {
    #stat-tiles {
        grid-template-columns: 1fr 1fr;
    }

    .stat-tile-hero {
        grid-column: 1 / -1;
        grid-row: auto;
    }

    /* Keep the numbers aligned when a label wraps on two lines */
    .stat-tile:not(.stat-tile-hero) {
        justify-content: space-between;
    }

    #events-months-canvas {
        height: 400px;
    }
}
</style>
