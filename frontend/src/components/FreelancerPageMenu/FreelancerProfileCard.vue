<template>
  <div class="public-card">

    <div class="public-left">

      <img v-if="freelancer.avatar_url" :src="freelancer.avatar_url" class="avatar-img" />


      <h2>{{ freelancer.name }}</h2>

      <p class="role">{{ freelancer.role }}</p>

      <p class="rating">
        ⭐ {{ freelancer.rating }} ({{ freelancer.reviews ?? 0 }})
      </p>

      <p class="location">
        📍 {{ freelancer.location }}
      </p>

      <div class="skills" v-if="freelancer.skills?.length">
        <span v-for="skill in freelancer.skills" :key="skill">
          {{ skill }}
        </span>
      </div>

    </div>

    <div class="public-right">

      <h1>About</h1>

      <p v-if="freelancer.about">
        {{ freelancer.about }}
      </p>

      <div v-if="freelancer.certificate_urls?.length" class="certificates">

        <h3>Certificates</h3>

        <div class="cert-grid">

          <div class="cert-card" v-for="(cert, index) in freelancer.certificate_urls" :key="cert">

            <button v-if="isImage(cert)" class="cert-image-button" @click="$emit('open-certificate', cert)">
              <img :src="cert" />
            </button>

            <a v-else :href="cert" target="_blank">
              {{ certificateLabel(cert, index) }}
            </a>

          </div>

        </div>

      </div>

      <div class="actions">

        <button @click="$emit('message')">
          Message
        </button>

        <button @click="$emit('back')">
          Back to list
        </button>

      </div>

    </div>

  </div>
</template>

<script setup>
const { freelancer } = defineProps({
  freelancer: Object
})

const isImage = (url) => /\.(png|jpe?g|gif|webp)$/i.test(String(url))

const certificateLabel = (url, index) => {
  const name = String(url).split('/').pop()
  return name || `Certificate ${index + 1}`
}
</script>

<style scoped>
.public-card {
  display: flex;
  gap: 32px;
  background: #fff;
  padding: 28px;
  border-radius: 16px;
  box-shadow: 0 8px 24px rgba(0, 0, 0, .06);
}

.public-left {
  width: 320px;
  text-align: center;
}

.avatar-img {
  width: 160px;
  height: 160px;
  border-radius: 50%;
  object-fit: cover;
  margin-bottom: 12px;
}

.skills span {
  display: inline-block;
  background: #f3efff;
  padding: 6px 10px;
  border-radius: 10px;
  margin: 4px;
}

.public-right {
  flex: 1;
}

.cert-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 16px;
  margin-top: 12px;
}

.cert-card {
  height: 120px;
  border-radius: 16px;
  overflow: hidden;
  box-shadow: 0 6px 18px rgba(0, 0, 0, .08);
}

.cert-card img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.cert-image-button {
  width: 100%;
  height: 100%;
  border: none;
  padding: 0;
  background: none;
  cursor: pointer;
}

.actions {
  margin-top: 20px;
  display: flex;
  gap: 12px;
}

.actions button {
  padding: 10px 16px;
  border-radius: 10px;
  border: none;
  background: #5b3df5;
  color: white;
  cursor: pointer;
}
</style>
