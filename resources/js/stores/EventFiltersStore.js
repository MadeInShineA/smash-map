import { defineStore } from "pinia";
import { ref, watch } from "vue";
import { useAxios } from "@vueuse/integrations/useAxios";
import { useDateFormat, watchDebounced, watchPausable } from "@vueuse/core";
import { useEventsStore } from "../stores/EventsStore.js";

export const useEventFiltersStore = defineStore("eventFilters", function () {
    const eventsStore = useEventsStore();

    const {
        data: eventCountryOptions,
        isFinished: countriesFetched,
        execute: fetchCountries,
    } = useAxios("/api/countries", {}, { immediate: false });

    const currentPage = ref(1);
    const selectedOrderBy = ref("default");
    const selectedEventGames = ref([]);
    const selectedEventTypes = ref("default");
    const selectedEventSource = ref("default");
    const selectedEventDates = ref([]);
    const selectedEventContinents = ref([]);
    const selectedEventCountries = ref([]);
    const selectedEventName = ref("");

    const joinOrDefault = (values) =>
        values && values.length > 0 ? values.join(",") : "default";

    function formatDate(date) {
        return useDateFormat(date, "YYYY-MM-DD").value;
    }

    /** Query parameters for /api/events built from the current filters */
    function buildParams(page) {
        let startDate = "default";
        let endDate = "default";
        const dates = selectedEventDates.value;
        if (dates && dates[0]) {
            startDate = formatDate(dates[0]);
            endDate = dates[1] ? formatDate(dates[1]) : startDate;
        }

        const type = selectedEventTypes.value;
        // Location filters don't apply to online events
        const isOnline = type === "online";

        return {
            page,
            games: joinOrDefault(selectedEventGames.value),
            type,
            source: selectedEventSource.value,
            orderBy: selectedOrderBy.value,
            continents: isOnline
                ? "default"
                : joinOrDefault(selectedEventContinents.value),
            countries: isOnline
                ? "default"
                : joinOrDefault(selectedEventCountries.value),
            name:
                selectedEventName.value.trim() !== ""
                    ? selectedEventName.value.trim()
                    : "default",
            startDate,
            endDate,
        };
    }

    function fetchPage(page) {
        // A newer request aborts the previous one, ignore those rejections
        return eventsStore
            .fetchEvents({ params: buildParams(page) })
            .catch(() => {});
    }

    const { pause: pauseCurrentPageWatch, resume: resumeCurrentPageWatch } =
        watchPausable(currentPage, (page) => fetchPage(page), {
            immediate: false,
        });

    /** Any filter change goes back to the first page */
    function fetchFirstPage() {
        pauseCurrentPageWatch();
        currentPage.value = 1;
        resumeCurrentPageWatch();
        return fetchPage(1);
    }

    watch(
        [
            selectedOrderBy,
            selectedEventGames,
            selectedEventTypes,
            selectedEventSource,
            selectedEventDates,
            selectedEventCountries,
        ],
        fetchFirstPage,
    );

    watch(selectedEventContinents, function (continents) {
        fetchCountries({
            params: { continents: joinOrDefault(continents) },
        }).catch(() => {});
        fetchFirstPage();
    });

    // Drop selected countries that are not in the selected continents anymore
    watch(eventCountryOptions, function (availableCountries) {
        if (!availableCountries?.data) {
            return;
        }
        const availableCodes = availableCountries.data.map(
            (country) => country.code,
        );
        const kept = selectedEventCountries.value.filter((code) =>
            availableCodes.includes(code),
        );
        if (kept.length !== selectedEventCountries.value.length) {
            selectedEventCountries.value = kept;
        }
    });

    watchDebounced(selectedEventName, fetchFirstPage, {
        debounce: 600,
        maxWait: 2000,
    });

    function fetchEventsWithFilters() {
        return fetchPage(currentPage.value);
    }

    function clearFilters() {
        selectedEventGames.value = [];
        selectedEventTypes.value = "default";
        selectedEventSource.value = "default";
        selectedEventDates.value = [];
        selectedEventContinents.value = [];
        selectedEventCountries.value = [];
        selectedEventName.value = "";
        selectedOrderBy.value = "default";
    }

    return {
        eventCountryOptions,
        countriesFetched,
        fetchCountries,
        currentPage,
        selectedOrderBy,
        selectedEventGames,
        selectedEventTypes,
        selectedEventSource,
        selectedEventDates,
        selectedEventContinents,
        selectedEventCountries,
        selectedEventName,
        fetchEventsWithFilters,
        clearFilters,
    };
});
