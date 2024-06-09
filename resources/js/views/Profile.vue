<script setup>
import {useUserStore} from "../stores/UserStore.js";
import {ref} from "vue";
import Tag from "primevue/tag";
import LoaderComponent from "../components/LoaderComponent.vue";
import IconField from "primevue/iconfield";
import InputIcon from "primevue/inputicon";
import InputText from "primevue/inputtext";
import Textarea from "primevue/textarea";
import Button from "primevue/button";
import Swal from "sweetalert2";

const userStore = useUserStore()

const props = defineProps({
    darkMode: {
        type: Boolean,
        required: true
    }
})

const profileInformation = ref(null)
userStore.checkAuthentication(props.darkMode).then(response => {
    if (response) {
        userStore.getProfileInformation(userStore.user.data.id, props.darkMode).then((response) => {
            profileInformation.value = response.data
        })
    }
})

const profileInformationValidationError = ref({
    profilePicture: [],
    description: [],
    discord: [],
    x: [],
    connectCode: []
})

const handleAvatarClick = () => {
    document.getElementById('profile-picture-upload').click();
}

const handleFileChange = (event) => {
    profileInformationValidationError.value.profilePicture = []
    const file = event.target.files[0];

    if (file) {
        if(file.size > 2048 * 1024){
            profileInformationValidationError.value.profilePicture.push('The file size is too large. Max file size is 2MB')
        }else if(!['image/png', 'image/jpg', 'image/jpeg'].includes(file.type)){
            profileInformationValidationError.value.profilePicture.push('The file type is not supported. Supported file types are png, jpg and jpeg')
        }
        else{
            const reader = new FileReader();
            reader.onload = (e) => {
                document.getElementById('profile-picture').src = e.target.result;
            };
            profileInformation.value.profilePicture = file;
            reader.readAsDataURL(file);
        }

    }
}

async function saveProfileInformation() {
    try {
        const sentProfileInformation = {
            profilePicture: profileInformation.value.profilePicture ?? null,
            description: profileInformation.value.description,
            discord: profileInformation.value.discord,
            x: profileInformation.value.x,
            connectCode: profileInformation.value.connectCode
        }
        await userStore.updateProfileInformation(sentProfileInformation, props.darkMode).then(async function (response) {

            const alertBackground = props.darkMode ? '#1C1B22' : '#FFFFFF'
            const alertColor = props.darkMode ? '#FFFFFF' : '#1C1B22'
            await Swal.fire({
                title: 'Profile saved!',
                text: response.data.message,
                icon: 'success',
                background: alertBackground,
                color: alertColor,
                timer: 2000,
                showConfirmButton: false
            })
        })
    } catch (error) {
        console.log(error)
        if (error.response && error.response.data.errors === undefined && error.response.data.message && error.response.status === 500) {
            const alertBackground = props.darkMode ? '#1C1B22' : '#FFFFFF'
            const alertColor = props.darkMode ? '#FFFFFF' : '#1C1B22'
            Swal.fire({
                title: 'Error',
                text: error.response.data.message,
                icon: 'error',
                background: alertBackground,
                color: alertColor,
                timer: 2000,
                showConfirmButton: false
            })
        } else if (error.response && error.response.data.errors) {
            profileInformationValidationError.value = error.response.data.errors
        } else {
            console.log(error)
        }
    }

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
        <input type="file" accept=".png, .jpg, .jpeg" id="profile-picture-upload" style="display: none;" @change="handleFileChange">
        <div class="validation-errors text-align-center">
            <TransitionGroup name="errors">
                <template v-for="profileInformationProfilePictureError in profileInformationValidationError.profilePicture"
                          :key="profileInformationProfilePictureError" class="validation-errors">
                    <div class="validation-error">{{ profileInformationProfilePictureError }}</div>
                </template>
            </TransitionGroup>
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

        <div class="profile-information-input-container">
            <Textarea v-model="profileInformation.description" autoResize placeholder="Description"
                      id="description-text-area" maxlength="255" @click="profileInformationValidationError.description = []"/>
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
                <InputText v-model="profileInformation.discord" placeholder="Discord username" maxlength="32" @click="profileInformationValidationError.discord = []"/>
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
                <InputText v-model="profileInformation.x" placeholder="X username" maxlength="15"  @click="profileInformationValidationError.x = []"/>
            </IconField>
        </div>
        <div class="validation-errors">
            <TransitionGroup name="errors">
                <template v-for="profileInformationXError in profileInformationValidationError.x"
                          :key="profileInformationXError" class="validation-errors">
                    <div class="validation-error">{{ profileInformationXError }}</div>
                </template>
            </TransitionGroup>
        </div>

        <div v-if="'Melee' in profileInformation.games" class="profile-information-input-container">
            <IconField iconPosition="left">
                <InputIcon class="pi pi-globe"/>
                <InputText v-model="profileInformation.connectCode" placeholder="Slippi connect code" maxlength="8" @click="profileInformationValidationError.connectCode = []"/>
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
    width:200px;
    height: 200px;
    border-radius: 50%;
    object-fit: cover;

}

#profile-picture-container {
    position: relative;
    cursor: pointer;
    width: 200px;
    height: 200px;
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
    color: white;
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
    text-align: center;
}

.errors-enter-active,
.errors-leave-active {
    transition: opacity 0.5s ease;
}

.errors-enter-from,
.errors-leave-to {
    opacity: 0;
}

.validation-errors{
    margin-top: 10px;
    min-height: 1em;
    width: 300px;
}

.validation-error {
    font-size: 12px;
    color: red;
}

.text-align-center{
    text-align: center;
}
</style>
