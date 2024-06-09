<script setup>


</script>

<template>
    <div id="about-container">
        <h1 id="about-title">About Me</h1>
        <a href="https://linktr.ee/bluerosetori" target="_blank">
            <figure>
                <img id="about-image" src="/resources/images/about.jpeg" alt="About Image">
                <figcaption>Picture by Tori Hamilton</figcaption>
            </figure>
        </a>

        <div>
            <p>
                Hey! I'm MadeInShineA, a Super Smash Bros. Melee player from Switzerland.
            </p>
            <p>
                I have been working on the Smash Map since May 2023.
                <br>Before that, I developed the formerly known Melee Map website as part of the Harvard CS50 web online course.
            </p>
            <p>
                I love computer science and learning new things, whatever the field. I'm always looking for new projects to work on and improve my skills!
            </p>
            <p>
                If you have any questions or feedback, feel free to contact on my socials:
            </p>
        </div>
        <div id="about-socials">
            <div class="about-social">
                <i class="pi pi-discord about-social-icons"></i> ssbm_misa
            </div>
            <a href="https://x.com/MadeInShineA" target="_blank" class="about-social">
                <i class="pi pi-twitter about-social-icons"></i>MadeInShineA
            </a>
            <a href="https://github.com/MadeInShineA" target="_blank" class="about-social">
                <i class="pi pi-github about-social-icons"></i>MadeInShineA
            </a>
            <a  href="mailto:olivier.amacker@netplus.ch" class="about-social">
                <i class="pi pi-envelope about-social-icons"></i>olivier.amacker@netplus.ch
            </a>
        </div>
        <p>
            Or directly on the Smash Map socials:
        </p>
        <div id="about-socials">
            <a href="https://discord.gg/AhhDM6S2sQ" target="_blank" class="about-social">
                <i class="pi pi-discord about-social-icons"></i> Discord server
            </a>
            <a href="https://x.com/_SmashMap_" target="_blank" class="about-social">
                <i class="pi pi-twitter about-social-icons"></i>_SmashMap_
            </a>
            <a href="https://github.com/MadeInShineA/smash-map" target="_blank" class="about-social">
                <i class="pi pi-github about-social-icons"></i>GitHub
            </a>

            <a  href="mailto:smash-map@infomaniak.ch" class="about-social">
                <i class="pi pi-envelope about-social-icons"></i>smash-map@infomaniak.ch
            </a>
        </div>
    </div>
</template>

<style scoped>

#about-container {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    margin: 20px 20px;
    text-align: center;
}

#about-title {
    margin-top: 0;
    margin-bottom: 1rem
}

#about-image {
    width: 200px;
    height: 200px;
    border-radius: 50%;
    object-fit: cover;
}

#about-socials {
    display: flex;
    justify-content: center;
    align-items: center;
    flex-direction: column;
}

.about-social-icons{
    margin-right: 0.5rem;
}

a{
    color: inherit;
    text-decoration: none;
}

.about-social{
    margin-top: 10px;
}

p{
    margin-top: 1rem;
    margin-bottom: 1rem
}

figure{
    margin: 1rem 0;
}

</style>
