<script setup>

import Sidebar from "primevue/sidebar";
import Calendar from "primevue/calendar";
import MultiSelect from "primevue/multiselect";
import Dropdown from "primevue/dropdown";
import InputText from "primevue/inputtext";
import {useAddressFiltersStore} from '../stores/AddressFiltersStore.js'
import {useOptionsStore} from "../stores/OptionsStore.js";
import {useUserStore} from "../stores/UserStore.js";
import {onMounted} from "vue";
import FloatLabel from "primevue/floatlabel";


const optionsStore = useOptionsStore()
const userStore = useUserStore()
const filtersStore = useAddressFiltersStore()

filtersStore.fetchCountries({params: {continents: filtersStore.selectedAddressCountries.length > 0 ? filtersStore.selectedAddressCountries.join(',') : 'default'}})
filtersStore.fetchCharacters({params: {games: filtersStore.selectedAddressGames.length > 0 ? filtersStore.selectedAddressGames.join(',') : 'default'}})
filtersStore.fetchAddressesWithFilters()


const props = defineProps({
    sideBarVisible: {
        type :Boolean,
        required: true
    }
})

const emit = defineEmits(['switchSideBarVisible'])

onMounted(()=>{
    console.log('AddressFilterSidebar Mounted')
})

</script>

<template>
    <Sidebar :visible="sideBarVisible" @update:visible="emit('switchSideBarVisible')" position="top"
             id="address-filters-sidebar" style="text-align: center; height: min-content; max-height:100vh">
        <h2>Filters</h2>
        <div id="address-filters">
            <div class="address-filter">
                <FloatLabel>
                    <MultiSelect id="address-filter-games" v-model="filtersStore.selectedAddressGames" :options="optionsStore.gameOptions"
                                 :maxSelectedLabels="0" optionLabel="name" optionValue="id"
                                 placeholder="Games"/>
                    <label for="address-filter-games">Games</label>
                </FloatLabel>

            </div>
            <div class="address-filter">
                <Dropdown v-model="filtersStore.selectedAddressTypes" :options="userStore.user.data.id ? optionsStore.connectedUserAddressTypeOptions : optionsStore.addressTypeOptions"
                          optionLabel="name" optionValue="value" placeholder="All markers types"/>
            </div>
            <div class="address-filter">
                <Calendar id="address-filter-dates" v-model=filtersStore.selectedAddressDates :minDate="new Date()"
                          :disabled="filtersStore.selectedAddressTypes === 'users' || filtersStore.selectedAddressTypes === 'modders' || filtersStore.selectedAddressCharacters.length > 0"
                          placeholder="Event date range (UTC)" selectionMode="range" :manualInput="false" showButtonBar
                          dateFormat="dd/mm/yy"></Calendar>
            </div>
            <div class="address-filter">
                <FloatLabel>
                    <MultiSelect id="address-filter-characters" :disabled="filtersStore.selectedAddressDates != null && (filtersStore.selectedAddressTypes ==='events' || filtersStore.selectedAddressTypes ==='followedEvents' || filtersStore.selectedAddressDates.length !== 0)" :loading="!filtersStore.charactersFetched" v-model="filtersStore.selectedAddressCharacters" :maxSelectedLabels="0" :options="filtersStore.addressesCharacterOptions.data" optionLabel="name" optionValue="id" data-key="id" filter optionGroupLabel="game" optionGroupChildren="characters" placeholder="Characters">
                        <template #option="slotProps">
                            <div class="character-option">
                                <img :alt="slotProps.option.name" :src="slotProps.option.image.url" class="character-option-image" width="30" />
                                <div>{{ slotProps.option.name }}</div>
                            </div>
                        </template>
                    </MultiSelect>
                    <label for="address-filter-characters">Characters</label>
                </FloatLabel>
            </div>
            <div class="address-filter">
                <FloatLabel>
                    <MultiSelect id="address-filter-continents" v-model="filtersStore.selectedAddressContinents"
                                 :options="optionsStore.continentOptions" :maxSelectedLabels="0"
                                 optionLabel="name" optionValue="code" placeholder="Continents"/>
                    <label for="address-filter-continents">Continents</label>
                </FloatLabel>
            </div>
            <div class="address-filter">
                <FloatLabel>
                    <MultiSelect id="address-filter-countries" :loading="!filtersStore.countriesFetched" :disabled="!filtersStore.countriesFetched" v-model="filtersStore.selectedAddressCountries"
                                 :options="filtersStore.addressesCountryOptions.data" filter
                                 :maxSelectedLabels="0" optionLabel="name" optionValue="code"
                                 placeholder="Countries">
                        <template #option="slotProps">
                            <div class="country-flag">
                                <img v-if="slotProps.option.image" :alt="slotProps.option.name + '-image'"
                                     :src="slotProps.option.image.url" class="country-flag-image"/>
                                <div>{{ slotProps.option.name }}</div>
                            </div>
                        </template>
                    </MultiSelect>
                    <label for="address-filter-countries">Countries</label>
                </FloatLabel>
            </div>
            <div class="address-filter">
                <FloatLabel>
                    <InputText id="address-filter-name" v-model="filtersStore.selectedAddressName"></InputText>
                    <label for="address-filter-name">Name | Connect code</label>
                </FloatLabel>
            </div>
        </div>
    </Sidebar>
</template>

<style scoped>

#address-filters-sidebar{
    max-height: 100vh;
}
#address-filters {
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
}

.address-filter {
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
