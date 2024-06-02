<script setup>

import {ref} from "vue";
import {defineProps} from "vue";
import {useUserStore} from "../stores/UserStore.js";
import Tag from "primevue/tag";
import LoaderComponent from "../components/LoaderComponent.vue";

const props = defineProps({
    username: {
        type: String,
        required: true
    },
    darkMode: {
        type: Boolean,
        required: true
    }
})

const userStore = useUserStore()

const profileInformation = ref(null)

userStore.getProfileInformationByUsername(props.username, props.darkMode).then((response) => {
    profileInformation.value = response.data
})

</script>

<template>
    <div v-if="profileInformation" id="profile-information-container">
        <div id="profile-picture-container">
            <img :src="userStore.user.data.profilePicture.url" id="profile-picture" alt="Profile picture image"/>
        </div>
        <div>
            {{ profileInformation.username }}
        </div>
        <div v-if="profileInformation.isModder" class="tag">
            <Tag value="Modder" rounded :style="{background: 'aqua'}"></Tag>
        </div>
        <template v-for="game in profileInformation.games">
            <div class="tag">
                <Tag :value="game.name" rounded :style="{background: game.color}"></Tag>
            </div>
            <div class="characters-container">
                <template v-for="character in game.characters">
                    <img class="character-image" :alt="character.name" :src="character.image.url" width="20"/>
                </template>
            </div>
        </template>

        <div v-if="profileInformation.description">
            {{ profileInformation.description }}
        </div>

        <div v-if="profileInformation.discord">
            <i class="pi pi-discord"></i>
            {{ profileInformation.discord }}
        </div>

        <div v-if="profileInformation.x">
            <i class="pi pi-twitter"></i>
           {{ profileInformation.x}}
        </div>

        <div v-if="profileInformation.connectCode">
            <i class="pi pi-globe"></i>
           {{ profileInformation.connectCode}}
        </div>
    </div>
    <loader-component v-else></loader-component>
</template>

<style scoped>

#profile-information-container {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    margin: 20px 0;
}

#profile-picture {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    object-fit: cover;

}

</style>
