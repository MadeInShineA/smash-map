<script setup>

import Sidebar from "primevue/sidebar";
import Calendar from "primevue/calendar";
import MultiSelect from "primevue/multiselect";
import Dropdown from "primevue/dropdown";
import InputText from "primevue/inputtext";
import {useAddressFiltersStore} from '../stores/AddressFiltersStore.js'
import {useOptionsStore} from "../stores/OptionsStore.js";

const addressFiltersStore = useAddressFiltersStore()

addressFiltersStore.fetchCountries()
addressFiltersStore.fetchCharacters()

const optionsStore = useOptionsStore()

const props = defineProps({
    sideBarVisible: Boolean
})

const emit = defineEmits(['switchSideBarVisible'])

</script>

<template>
    <Sidebar :visible="sideBarVisible" @update:visible="emit('switchSideBarVisible')" position="top"
             id="address-filters-sidebar" style="text-align: center; height: min-content; max-height:100vh">
        <h2>Filters</h2>
        <div id="event-filters">
            <div class="event-filter">
                <MultiSelect v-model="addressFiltersStore.selectedAddressGames" :options="optionsStore.gameOptions"
                             display="chip" :maxSelectedLabels="2" optionLabel="name" optionValue="id"
                             placeholder="Select Games"/>
            </div>
            <div class="event-filter">
                <Dropdown v-model="addressFiltersStore.selectedAddressTypes" :options="optionsStore.addressTypeOptions"
                          optionLabel="name" optionValue="value" placeholder="All markers types"/>
            </div>
            <div class="event-filter">
                <Calendar v-model=addressFiltersStore.selectedAddressDates :minDate="new Date()"
                          :disabled="addressFiltersStore.selectedAddressTypes === 'users' || addressFiltersStore.selectedAddressCharacters.length > 0"
                          placeholder="Event date range (UTC)" selectionMode="range" :manualInput="false" showButtonBar
                          dateFormat="dd/mm/yy"></Calendar>
            </div>
            <div class="event-filter">
                <MultiSelect :disabled="addressFiltersStore.selectedAddressDates != null && (addressFiltersStore.selectedAddressTypes ==='events' || addressFiltersStore.selectedAddressDates.length !== 0)" :loading="!addressFiltersStore.charactersFetched" v-model="addressFiltersStore.selectedAddressCharacters" :maxSelectedLabels="2" :options="addressFiltersStore.addressesCharacterOptions.data" optionLabel="name" optionValue="id" data-key="id" filter optionGroupLabel="game" optionGroupChildren="characters" placeholder="Users characters">
                    <template #option="slotProps">
                        <div class="character-option">
                            <img :alt="slotProps.option.name" :src="slotProps.option.image.url" class="character-option-image" width="30" />
                            <div>{{ slotProps.option.name }}</div>
                        </div>
                    </template>
                </MultiSelect>
            </div>
            <div class="event-filter">
                <MultiSelect v-model="addressFiltersStore.selectedAddressContinents"
                             :options="optionsStore.continentOptions" display="chip" :maxSelectedLabels="2"
                             optionLabel="name" optionValue="code" placeholder="Select Continents"/>
            </div>
            <div class="event-filter">
                <MultiSelect :loading="!addressFiltersStore.countriesFetched" :disabled="!addressFiltersStore.countriesFetched" v-model="addressFiltersStore.selectedAddressCountries"
                             :options="addressFiltersStore.addressesCountryOptions.data" filter display="chip"
                             :maxSelectedLabels="2" optionLabel="name" optionValue="code"
                             placeholder="Select Countries">
                    <template #option="slotProps">
                        <div class="country-flag">
                            <img v-if="slotProps.option.image" :alt="slotProps.option.name + '-image'"
                                 :src="slotProps.option.image.url" class="country-flag-image"/>
                            <div>{{ slotProps.option.name }}</div>
                        </div>
                    </template>
                </MultiSelect>
            </div>
            <div class="event-filter">
                <InputText v-model="addressFiltersStore.selectedAddressName" placeholder="Name"></InputText>
            </div>
        </div>
    </Sidebar>
</template>

<style scoped>

#address-filters-sidebar{
    max-height: 100vh;
}
#event-filters {
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
}

.event-filter {
    margin: 20px 10px;
}

.country-flag {
    display: flex;
}

.country-flag-image {
    width: 18px;
    margin-right: 5px;
}

.character-option-image{
    width: 18px;
    margin-right: 5px;
}

.character-option{
    display: flex;
}


</style>
