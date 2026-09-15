<script>
export default { name: "events" };
</script>

<script setup>
import { onMounted, ref } from "vue";
import Dropdown from "primevue/dropdown";
import Button from "primevue/button";
import Card from "primevue/card";
import Paginator from "primevue/paginator";
import Chip from "primevue/chip";
import Tag from "primevue/tag";
import { useEventFiltersStore } from "../stores/EventFiltersStore.js";
import { useEventsStore } from "../stores/EventsStore.js";
import { useUserStore } from "../stores/UserStore.js";
import EventFilterSidebar from "../components/EventFilterSidebar.vue";
import { useOptionsStore } from "../stores/OptionsStore.js";
import Swal from "sweetalert2";

const eventsFiltersStore = useEventFiltersStore();
const eventsStore = useEventsStore();
const userStore = useUserStore();

const optionsStore = useOptionsStore();

const props = defineProps({
    darkMode: {
        type: Boolean,
        required: true,
    },
    games: {
        type: String,
        required: false,
    },
    type: {
        type: String,
        required: false,
    },
    startDate: {
        type: String,
        required: false,
    },
    endDate: {
        type: String,
        required: false,
    },
    continents: {
        type: String,
        required: false,
    },
    countries: {
        type: String,
        required: false,
    },
    name: {
        type: String,
        required: false,
    },
    orderBy: {
        type: String,
        required: false,
    },
});

const setGamesFromProps = () => {
    if (props.games && props.games !== "default") {
        const validIds = new Set(optionsStore.gameOptions.map(g => g.id));
        const parsedIds = props.games
            .split(",")
            .map(Number)
            .filter(Number.isFinite);
        const invalidIds = parsedIds.filter(id => !validIds.has(id));
        if (invalidIds.length > 0) {
            const alertBackground = props.darkMode ? "#1C1B22" : "#FFFFFF";
            const alertColor = props.darkMode ? "#FFFFFF" : "#1C1B22";
            Swal.fire({
                title: "Error",
                text: `The following game IDs do not exist: ${invalidIds.join(", ")}`,
                icon: "error",
                background: alertBackground,
                color: alertColor,
                timer: 2000,
                showConfirmButton: false,
            });
        }
        eventsFiltersStore.selectedEventGames = parsedIds.filter(id => validIds.has(id));
    } else {
        eventsFiltersStore.selectedEventGames = [];
    }
};

const setTypeFromProps = () => {
    if (props.type && props.type !== "default") {
        const validTypes = new Set(["online", "offline", "followed"]);
        if (!validTypes.has(props.type)) {
            const alertBackground = props.darkMode ? "#1C1B22" : "#FFFFFF";
            const alertColor = props.darkMode ? "#FFFFFF" : "#1C1B22";
            Swal.fire({
                title: "Error",
                text: `Invalid event type: ${props.type}`,
                icon: "error",
                background: alertBackground,
                color: alertColor,
                timer: 2000,
                showConfirmButton: false,
            });
            eventsFiltersStore.selectedEventTypes = "default";
            return;
        }
        eventsFiltersStore.selectedEventTypes = props.type;
    } else {
        eventsFiltersStore.selectedEventTypes = "default";
    }
};

const setDatesFromProps = () => {
    if (props.startDate) {
        const start = new Date(props.startDate);
        if (!isNaN(start.getTime())) {
            const end = props.endDate ? new Date(props.endDate) : null;
            if (end && !isNaN(end.getTime())) {
                eventsFiltersStore.selectedEventDates = [start, end];
            } else {
                eventsFiltersStore.selectedEventDates = [start];
            }
        }
    } else {
        eventsFiltersStore.selectedEventDates = [];
    }
};

const setContinentsFromProps = () => {
    if (props.continents && props.continents !== "default") {
        if (props.type === "online") {
            const alertBackground = props.darkMode ? "#1C1B22" : "#FFFFFF";
            const alertColor = props.darkMode ? "#FFFFFF" : "#1C1B22";
            Swal.fire({
                title: "Error",
                text: "Continent filters cannot be applied when event type is Online",
                icon: "error",
                background: alertBackground,
                color: alertColor,
                timer: 3000,
                showConfirmButton: false,
            });
            eventsFiltersStore.selectedEventContinents = [];
            return;
        }
        const validCodes = new Set(optionsStore.continentOptions.map(c => c.code));
        const codes = props.continents.split(",");
        const invalidCodes = codes.filter(code => !validCodes.has(code));
        if (invalidCodes.length > 0) {
            const alertBackground = props.darkMode ? "#1C1B22" : "#FFFFFF";
            const alertColor = props.darkMode ? "#FFFFFF" : "#1C1B22";
            Swal.fire({
                title: "Error",
                text: `The following continent codes do not exist: ${invalidCodes.join(", ")}`,
                icon: "error",
                background: alertBackground,
                color: alertColor,
                timer: 2000,
                showConfirmButton: false,
            });
        }
        eventsFiltersStore.selectedEventContinents = codes.filter(code => validCodes.has(code));
    } else {
        eventsFiltersStore.selectedEventContinents = [];
    }
};

const setCountriesFromProps = () => {
    if (props.countries && props.countries !== "default") {
        if (props.type === "online") {
            const alertBackground = props.darkMode ? "#1C1B22" : "#FFFFFF";
            const alertColor = props.darkMode ? "#FFFFFF" : "#1C1B22";
            Swal.fire({
                title: "Error",
                text: "Country filters cannot be applied when event type is Online",
                icon: "error",
                background: alertBackground,
                color: alertColor,
                timer: 3000,
                showConfirmButton: false,
            });
            eventsFiltersStore.selectedEventCountries = [];
            return;
        }
        eventsFiltersStore.selectedEventCountries = props.countries.split(",");
    } else {
        eventsFiltersStore.selectedEventCountries = [];
    }
};

const setNameFromProps = () => {
    if (props.name && props.name !== "default") {
        eventsFiltersStore.selectedEventName = props.name;
    } else {
        eventsFiltersStore.selectedEventName = "";
    }
};

const setOrderByFromProps = () => {
    if (props.orderBy && props.orderBy !== "default") {
        const validValues = new Set(optionsStore.eventOrderByOptions.map(o => o.value));
        if (!validValues.has(props.orderBy)) {
            const alertBackground = props.darkMode ? "#1C1B22" : "#FFFFFF";
            const alertColor = props.darkMode ? "#FFFFFF" : "#1C1B22";
            Swal.fire({
                title: "Error",
                text: `Invalid order by value: ${props.orderBy}`,
                icon: "error",
                background: alertBackground,
                color: alertColor,
                timer: 2000,
                showConfirmButton: false,
            });
            eventsFiltersStore.selectedOrderBy = "default";
            return;
        }
        eventsFiltersStore.selectedOrderBy = props.orderBy;
    } else {
        eventsFiltersStore.selectedOrderBy = "default";
    }
};

setGamesFromProps();
setTypeFromProps();
setDatesFromProps();
setContinentsFromProps();
setCountriesFromProps();
setNameFromProps();
setOrderByFromProps();

const sideBarVisible = ref(false);
const switchSideBarVisible = function () {
    sideBarVisible.value = !sideBarVisible.value;
};
// TODO Update the timezone based on the user's IP

onMounted(() => {
    console.log("Events Mounted");
});
</script>

<template>
    <div id="event-controls-container">
        <div class="event-control">
            <Dropdown
                id="event-sort-by-dropdown"
                v-model="eventsFiltersStore.selectedOrderBy"
                :options="optionsStore.eventOrderByOptions"
                optionLabel="name"
                optionValue="value"
                placeholder="Sort by ID"
            />
        </div>
        <Button
            class="event-control"
            id="filters-button"
            @click="sideBarVisible = true"
            icon="pi pi-filter"
            text
            rounded
            outlined
            plain
            label="Filters"
        />
        <Button
            class="event-control"
            @click="eventsFiltersStore.clearFilters"
            icon="pi pi-filter-slash"
            plain
            text
            rounded
            aria-label="Clear filters"
        />
        <Button
            class="event-control"
            id="refresh-button"
            @click="eventsFiltersStore.fetchEventsWithFilters"
            icon="pi pi-refresh"
            plain
            text
            rounded
            aria-label="Refresh"
        />
    </div>
    <EventFilterSidebar
        :sideBarVisible="sideBarVisible"
        @switchSideBarVisible="switchSideBarVisible"
    ></EventFilterSidebar>
    <template
        v-if="eventsFiltersStore.countriesFetched && eventsStore.eventsFetched"
    >
        <h1 id="events-total">{{ eventsStore.events.meta.total }} Events</h1>
        <template v-if="eventsStore.events.data.length > 0">
            <div id="event-container">
                <Card
                    v-for="event in eventsStore.events.data"
                    :key="event.id"
                    class="event-card"
                >
                    <template #header>
                        <div class="event-image-container">
                            <img
                                v-if="event.image"
                                :src="event.image.url"
                                alt="Event Image"
                                class="event-image"
                            />
                            <img
                                v-else
                                src="https://upload.wikimedia.org/wikipedia/commons/thumb/a/ac/No_image_available.svg/1024px-No_image_available.svg.png"
                                alt="Event Image"
                                class="event-image"
                            />
                        </div>
                    </template>
                    <template #title>
                        <a
                            class="event-title"
                            :href="event.link"
                            target="_blank"
                            ><i class="pi pi-external-link" />
                            {{ event.name }}</a
                        >
                    </template>
                    <template #content>
                        <div class="event-game-attendees">
                            <Tag
                                :value="event.game.name"
                                rounded
                                :style="{
                                    background: event.game.color,
                                    marginRight: '1rem',
                                }"
                            ></Tag>
                            <Chip
                                :label="
                                    event.attendees || event.attendees === 0
                                        ? event.attendees.toString()
                                        : 'Private'
                                "
                                icon="pi pi-users"
                                :style="{ marginRight: '1rem' }"
                            ></Chip>
                            <Button
                                v-if="userStore.user.data.id"
                                class="event-bell-button"
                                @click="
                                    eventsStore.handleEventSubscription(
                                        event,
                                        props.darkMode,
                                    )
                                "
                                :loading="eventsStore.subscriptionLoading"
                                icon="pi pi-bell"
                                :class="{ active: event.user_subscribed }"
                                rounded
                                aria-label="Notification"
                            />
                        </div>
                        <div v-if="!event.is_online" class="event-location">
                            <router-link
                                :to="{
                                    name: 'map',
                                    query: {
                                        lat: event.address.latitude,
                                        lng: event.address.longitude,
                                        zoom: 10,
                                    },
                                }"
                            >
                                <Chip
                                    :label="event.address.name"
                                    icon="pi pi-map-marker"
                                ></Chip>
                            </router-link>
                        </div>
                        <div v-else class="event-location">
                            <Chip label="Online" icon="pi pi-globe"></Chip>
                        </div>
                        <div class="event-datetime">
                            <Chip
                                :label="
                                    event.timezone_start_date_time +
                                    ' / ' +
                                    event.timezone_end_date_time +
                                    ' ' +
                                    event.timezone
                                "
                                icon="pi pi-clock"
                            ></Chip>
                        </div>
                    </template>
                </Card>
            </div>
            <Paginator
                v-if="
                    eventsStore.events.meta.total >
                    eventsStore.events.meta.per_page
                "
                :first="
                    eventsFiltersStore.currentPage *
                        eventsStore.events.meta.per_page -
                    1
                "
                :rows="eventsStore.events.meta.per_page"
                :total-records="eventsStore.events.meta.total"
                @page="eventsFiltersStore.currentPage = $event.page + 1"
            />
        </template>
    </template>
    <template
        v-if="
            !eventsFiltersStore.countriesFetched || !eventsStore.eventsFetched
        "
    >
        <LoaderComponent></LoaderComponent>
    </template>
</template>

<style scoped>
#event-sort-by-dropdown {
    width: 175px;
}

.event-control {
    margin: 0 10px;
    align-self: center;
}

#events-total {
    text-align: center;
}

#event-controls-container {
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
    row-gap: 20px;
    margin: 20px;
}

#event-container {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    justify-items: center;
    grid-gap: 20px;
    margin-left: 20px;
    margin-right: 20px;
}

.event-card {
    display: flex;
    flex-direction: column;
    align-items: center;
    width: 100%;
    text-align: center;
    margin-top: 15px;
    margin-bottom: 15px;
}

.event-image-container {
    display: flex;
    justify-content: center;
    margin-top: 20px;
    margin-bottom: 10px;
}

.event-image {
    max-width: 100%;
    height: 200px;
}

:deep(.p-card-title) {
    height: 2em;
}

.event-title {
    margin-bottom: 10px;
    text-decoration: none;
    color: inherit;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
}

.event-bell-button,
.event-bell-button:hover,
.event-bell-button.active,
.event-bell-button:focus {
    background: transparent;
}

.event-bell-button:hover :deep(.p-button-icon),
.event-bell-button.active :deep(.p-button-icon) {
    color: gold;
}

.event-bell-button.active:hover :deep(.p-button-icon),
.event-bell-button :deep(.p-button-icon) {
    color: grey;
}

.event-location {
    margin-bottom: 10px;
    height: 5em;
}

.event-location :deep(.p-chip-text) {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
}

.event-game-attendees {
    margin-bottom: 10px;
    height: 2em;
}

.event-datetime {
    margin-bottom: 0;
    height: 5em;
}

:deep(.p-paginator) {
    background: none;
    justify-content: center;
}

@media (max-width: 1100px) {
    #event-container {
        grid-template-columns: repeat(3, 1fr);
    }
}

@media (max-width: 850px) {
    #event-container {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 700px) {
    #event-container {
        grid-template-columns: 1fr;
    }
}
</style>
