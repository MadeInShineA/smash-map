<script setup>

import Sidebar from "primevue/sidebar";
import Calendar from "primevue/calendar";
import MultiSelect from "primevue/multiselect";
import Dropdown from "primevue/dropdown";
import InputText from "primevue/inputtext";
import { useAddressFiltersStore } from '../stores/AddressFiltersStore.js'

const addressesStore = useAddressFiltersStore()

const props = defineProps({
  sideBarVisible:Boolean
})

const emit = defineEmits(['switchSideBarVisible'])

</script>

<template>
  <Sidebar :visible="sideBarVisible" @update:visible="emit('switchSideBarVisible')" position="top" id="event-filters-sidebar" style="text-align: center; height: min-content;">
    <h2>Filters</h2>
    <div id="event-filters">
      <div class="event-filter">
        <MultiSelect v-model="addressesStore.selectedAddressGames" :options="addressesStore.addressGameOptions" display="chip" :maxSelectedLabels="2" optionLabel="name" optionValue="id" placeholder="Select Games"/>
      </div>
      <div class="event-filter">
        <Dropdown v-model="addressesStore.selectedAddressTypes" :options="addressesStore.addressTypeOptions" optionLabel="name" optionValue="value" placeholder="All markers types"/>
      </div>
<!--      <div class="event-filter">-->
<!--        <Calendar v-model=filtersStore.selectedEventDates :minDate="new Date()" placeholder="Event date range (UTC)" selectionMode="range" :manualInput="false" showButtonBar dateFormat="dd/mm/yy"></Calendar>-->
<!--      </div>-->
<!--      <div class="event-filter">-->
<!--        <MultiSelect v-model="filtersStore.selectedEventContinents" :options="filtersStore.eventContinentOptions" display="chip" :disabled="filtersStore.selectedEventTypes.value === 'online'" :maxSelectedLabels="2" optionLabel="name" optionValue="code" placeholder="Select Continents"/>-->
<!--      </div>-->
<!--      <div class="event-filter">-->
<!--        &lt;!&ndash;            TODO Directly add the data to eventCountryOptions&ndash;&gt;-->
<!--        <MultiSelect v-if="filtersStore.countriesFetched" v-model="filtersStore.selectedEventCountries" :options="filtersStore.eventCountryOptions.data" filter display="chip" :disabled="filtersStore.selectedEventTypes.value === 'online'" :maxSelectedLabels="2" optionLabel="name" optionValue="code" placeholder="Select Countries">-->
<!--          <template #option="slotProps">-->
<!--            <div class="country-flag">-->
<!--              <img v-if="slotProps.option.image" :alt="slotProps.option.name + '-image'" :src="slotProps.option.image.url" class="country-flag-image" />-->
<!--              <div>{{ slotProps.option.name }}</div>-->
<!--            </div>-->
<!--          </template>-->
<!--        </MultiSelect>-->
<!--        <MultiSelect v-else disabled loading filter placeholder="Select Countries"/>-->
<!--      </div>-->
<!--      <div class="event-filter">-->
<!--        <span class="p-input-icon-left">-->
<!--            <i class="pi pi-search"/>-->
<!--            <InputText v-model="filtersStore.selectedEventName" placeholder="Event name"></InputText>-->
<!--        </span>-->
<!--      </div>-->
    </div>
  </Sidebar>
</template>

<style scoped>

#event-filters{
  display: flex;
  justify-content: center;
  flex-wrap: wrap;
}

.event-filter{
  margin:20px 10px;
}

.country-flag{
  display: flex;
}

.country-flag-image{
  width: 18px;
  margin-right: 5px;
}

</style>
