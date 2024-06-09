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
        <div>
            <img :src="userStore.user.data.profilePicture.url" id="profile-picture" alt="Profile picture image"/>
        </div>
        <div class="profile-element">
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

        <div v-if="profileInformation.description" class="profile-element" id="profile-description">
            {{ profileInformation.description }}
        </div>

        <div v-if="profileInformation.discord" class="profile-element">
            <i class="pi pi-discord"></i>
            {{ profileInformation.discord }}
        </div>

        <div v-if="profileInformation.x" class="profile-element">
            <i class="pi pi-twitter"></i>
           {{ profileInformation.x}}
        </div>

        <div v-if="profileInformation.connectCode" class="profile-element">
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
    text-align: center;
}

#profile-picture {
    width: 200px;
    height: 200px;
    border-radius: 50%;
    object-fit: cover;
}

.tag {
    margin: 10px;
}

.characters-container {
    width: 300px;
    text-align: center;
}

.character-image {
    margin: 5px;
}

.profile-element {
    margin: 10px;
    width: 300px;
    word-break: break-all;

}

#profile-description {
    white-space: break-spaces;
    word-break: break-word;
}

</style>
