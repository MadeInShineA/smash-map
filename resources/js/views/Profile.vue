<script setup>
import {useUserStore} from "../stores/UserStore.js";
import {ref} from "vue";
import Tag from "primevue/tag";
import LoaderComponent from "@/components/LoaderComponent.vue";
import IconField from "primevue/iconfield";
import InputIcon from "primevue/inputicon";
import InputText from "primevue/inputtext";
import Textarea from "primevue/textarea";
import Button from "primevue/button";

const userStore = useUserStore()

const props = defineProps({
    darkMode: Boolean
})

const profileInformation = ref(null)
userStore.checkAuthentication(props.darkMode).then(response => {
    if (response) {
        userStore.getProfileInformation(userStore.user.data.id, props.darkMode).then((response) => {
            profileInformation.value = response.data
            console.log(profileInformation.value)
        })
    }
})

const profileInformationValidationError = ref({
    description: [],
    discord: [],
    twitter: [],
    connectCode: []
})

const handleAvatarClick = () => {
    document.getElementById('profile-picture-upload').click();
}

const handleFileChange = (event) => {
    const file = event.target.files[0];
    if (file) {
        // Handle the file upload process
        console.log(file);
        // Example: Update profile picture URL (you will replace this with actual upload logic)
        const reader = new FileReader();
        reader.onload = (e) => {
            profileInformation.value.profilePicture.url = e.target.result;
            document.getElementById('profile-picture').src = e.target.result;
        };
        reader.readAsDataURL(file);
    }
}

function saveProfileInformation(){

}

</script>

<template>
    <div v-if="profileInformation" id="profile-information-container">
        <div id="profile-picture-container" @click="handleAvatarClick">
            <img :src="userStore.user.data.profilePicture.url" id="profile-picture" alt="Profile picture image"/>
            <div id="profile-picture-overlay">
                <span>Click to upload</span>
            </div>
        </div>
        <input type="file" accept="image/*" id="profile-picture-upload" style="display: none;" @change="handleFileChange">
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

        <div class="profile-information-input-container">
            <Textarea v-model="profileInformation.description" autoResize placeholder="Description"
                      id="description-text-area"/>
        </div>
        <div class="validation-errors">
            <TransitionGroup name="errors">
                <template v-for="profileInformationDescriptionError in profileInformationValidationError.description"
                          :key="profileInformationDescriptionError" class="validation-errors">
                    <div class="validation-error">{{ profileInformationDescriptionError }}</div>
                </template>
            </TransitionGroup>
        </div>

        <div class="profile-information-input-container">
            <IconField iconPosition="left">
                <InputIcon class="pi pi-discord"></InputIcon>
                <InputText v-model="profileInformation.discord" placeholder="Discord username"/>
            </IconField>
        </div>
        <div class="validation-errors">
            <TransitionGroup name="errors">
                <template v-for="profileInformationDiscordError in profileInformationValidationError.discord"
                          :key="profileInformationDiscordError" class="validation-errors">
                    <div class="validation-error">{{ profileInformationDiscordError }}</div>
                </template>
            </TransitionGroup>
        </div>

        <div class="profile-information-input-container">
            <IconField iconPosition="left">
                <InputIcon class="pi pi-twitter"></InputIcon>
                <InputText v-model="profileInformation.twitter" placeholder="X username"/>
            </IconField>
        </div>
        <div class="validation-errors">
            <TransitionGroup name="errors">
                <template v-for="profileInformationTwitterError in profileInformationValidationError.twitter"
                          :key="profileInformationTwitterError" class="validation-errors">
                    <div class="validation-error">{{ profileInformationTwitterError }}</div>
                </template>
            </TransitionGroup>
        </div>

        <div v-if="'Melee' in profileInformation.games" class="profile-information-input-container">
            <IconField iconPosition="left">
                <InputIcon class="pi pi-globe"/>
                <InputText v-model="profileInformation.connectCode" placeholder="Slippi connect code"/>
            </IconField>
        </div>
        <div class="validation-errors">
            <TransitionGroup name="errors">
                <template v-for="profileInformationConnectCodeError in profileInformationValidationError.connectCode"
                          :key="profileInformationConnectCodeError" class="validation-errors">
                    <div class="validation-error">{{ profileInformationConnectCodeError }}</div>
                </template>
            </TransitionGroup>
        </div>

        <div>
            <Button label="Save" severity="success" icon="pi pi-check" plain text
                    @click="saveProfileInformation"></Button>
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
}

#profile-picture-container {
    position: relative;
    cursor: pointer;
    width: 80px;
    height: 80px;
}

#profile-picture-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
    opacity: 0;
    border-radius: 50%;
    text-align: center;
}

#profile-picture-container:hover #profile-picture-overlay {
    opacity: 1;
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

.profile-information-input-container {
    width: 300px;
    margin: 10px 20px;
}

.profile-information-input-container :deep(input) {
    width: 100%;
}

#description-text-area {
    resize: none;
    width: 100%;
}

.errors-enter-active,
.errors-leave-active {
    transition: opacity 0.5s ease;
}

.errors-enter-from,
.errors-leave-to {
    opacity: 0;
}

.validation-errors {
    margin-top: 10px;
    min-height: 1em;
    width: 300px;
}

.validation-error {
    font-size: 12px;
    color: red;
}
</style>
