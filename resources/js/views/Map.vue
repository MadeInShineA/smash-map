<script setup>

import {onBeforeMount, onMounted, ref} from "vue";
import FilterSidebar from "@/components/FilterSidebar.vue";
import Button from "primevue/button";
import {useAxios} from "@vueuse/integrations/useAxios";
const sideBarVisible = ref(false)
const switchSideBarVisible = function (){
    sideBarVisible.value = !sideBarVisible.value
}

const center = ref([0, 0]);
const projection = ref("EPSG:4326");
const zoom = ref(8);
const rotation = ref(0);

const { data: addresses, isFinished: addressesFetched, execute: fetchAddresses } = useAxios('/api/addresses')

const overrideStyleFunction = (feature, style, resolution) => {
    console.log({ feature, style, resolution });
    const clusteredFeatures = feature.get("features");
    const size = clusteredFeatures.length;
    const firstFeature = clusteredFeatures[0];
    const color = firstFeature.get("color");
    console.log(color)
    style.getText().setText(size.toString());
};


onMounted(()=>{
    console.log('Map Mounted')
})

</script>

<template>
    <div id="event-filters-container">
        <Button class="filters-button" @click="sideBarVisible = true" icon="pi pi-filter" text rounded outlined label="Filters"/>
        <FilterSidebar :sideBarVisible="sideBarVisible" @switchSideBarVisible="switchSideBarVisible"></FilterSidebar>
    </div>
    <ol-map
        :loadTilesWhileAnimating="true"
        :loadTilesWhileInteracting="true"
        style="height: 100%"
    >
        <ol-view
            ref="view"
            :center="center"
            :rotation="rotation"
            :zoom="zoom"
            :projection="projection"
        />

        <ol-tile-layer>
            <ol-source-xyz url="https://mt1.google.com/vt/lyrs=s&x={x}&y={y}&z={z}" />
        </ol-tile-layer>

        <template v-if="addressesFetched">
            <ol-animated-clusterlayer :animationDuration="500" :distance="40">
                <ol-source-vector>
                    <ol-feature v-for="address in addresses.data" :properties="{ 'color': address.color }">
                        <ol-geom-point :coordinates="[address.longitude, address.latitude]">
                        </ol-geom-point>
                    </ol-feature>
                </ol-source-vector>

                <ol-style :overrideStyleFunction="overrideStyleFunction">
<!--                    <ol-style-stroke color="red" :width="2"></ol-style-stroke>-->
<!--                    <ol-style-fill color="rgba(255,255,255,0.1)"></ol-style-fill>-->

                    <ol-style-circle :radius="15">
                        <ol-style-fill color="#3399CC"></ol-style-fill>
                        <ol-style-stroke color="#fff" :width="1"></ol-style-stroke>
                    </ol-style-circle>
                    <ol-style-text>
                        <ol-style-fill color="#fff"></ol-style-fill>
                    </ol-style-text>
                </ol-style>

            </ol-animated-clusterlayer>
        </template>
    </ol-map>

</template>

<style scoped>

#event-filters-container{
    position: absolute;
    width: 100vw;
    z-index: 1;
    text-align: center;
    margin-top: 10px;
}

.filters-button{
    color: black;
}
</style>
