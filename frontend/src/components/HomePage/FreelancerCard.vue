<template>
  <div :class="['card', { 'premium-card': freelancer.plan === 'premium' }]">
    <img :src="freelancer.avatar_url || '/default-avatar.png'" class="avatar-img" alt="Avatar" />
    <div class="name-container">
      <h3>{{ freelancer.name }}</h3>
      <AppBadge v-if="freelancer.is_pro" type="pro" />

      <AppBadge v-if="freelancer.is_verified" type="verified" />
    </div>
    <p class="role">{{ freelancer.role }}</p>

    <p class="rating">⭐ {{ freelancer.rating }} ({{ freelancer.reviews }})</p>
    <p class="location">📍 {{ freelancer.location }}</p>

    <div class="skills" aria-label="Skills">
      <span v-for="skill in freelancer.skills" :key="skill">
        {{ skill }}
      </span>
    </div>

    <RouterLink :to="{ name: 'PublicProfile', params: { id: freelancer.id } }" class="view-btn">
      View Profile
    </RouterLink>
  </div>
</template>

<script>
import AppBadge from '@/components/UI/AppBadge.vue';

export default {
  name: 'FreelancerCard',
  components: { AppBadge },
  props: {
    freelancer: Object,
  },
}
</script>

<style scoped>
.name-container {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 4px;
  margin-top: 10px;
}

.avatar-img {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  object-fit: cover;
  margin: 0 auto 15px;
  display: block;
  background: #ddd;
}

.card {
  background: #f3efff;
  padding: 32px 40px;
  border-radius: 20px;
  text-align: center;
  width: 300px;
  box-shadow: 0 8px 20px rgba(91, 75, 138, 0.2);
  transition:
    transform 0.3s,
    box-shadow 0.3s;
}

.card:hover {
  transform: translateY(-5px);
  box-shadow: 0 12px 25px rgba(91, 75, 138, 0.3);
}

.avatar {
  width: 80px;
  height: 80px;
  background: #ddd;
  border-radius: 50%;
  margin: 0 auto 15px;
  box-shadow: 0 4px 10px rgba(91, 75, 138, 0.2);
}

h3 {
  margin: 0;
  font-weight: 600;
  color: #3a2f6b;
}

.role {
  color: #666;
  font-style: italic;
  margin-bottom: 15px;
}

.rating,
.location {
  color: #5b4b8a;
  font-weight: 500;
  margin: 5px 0;
}

.skills {
  margin: 15px 0;
  display: flex;
  gap: 8px;
  overflow-x: auto;
  padding-bottom: 6px;
  scroll-snap-type: x proximity;
  min-height: 44px;
}

.skills span {
  background: #fff;
  padding: 6px 14px;
  border-radius: 15px;
  flex: 0 0 auto;
  white-space: nowrap;
  scroll-snap-align: start;
  font-size: 14px;
  font-weight: 500;
  color: #5b4b8a;
  box-shadow: 0 2px 6px rgba(91, 75, 138, 0.15);
  transition:
    transform 0.2s,
    box-shadow 0.2s;
}

.skills::-webkit-scrollbar {
  height: 6px;
}

.skills::-webkit-scrollbar-thumb {
  background: rgba(91, 75, 138, 0.3);
  border-radius: 999px;
}

.skills span:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(91, 75, 138, 0.2);
}

.view-btn,
button {
  display: inline-block;
  margin-top: 10px;
  background: #5b4b8a;
  color: white;
  border: none;
  padding: 10px 25px;
  border-radius: 15px;
  font-weight: 600;
  cursor: pointer;
  text-decoration: none;
  text-align: center;
  transition:
    background 0.3s,
    transform 0.2s;
}

.view-btn:hover,
button:hover {
  background: #6c57b8;
  transform: translateY(-2px);
}

.premium-card {
  border: 2px solid rgba(255, 215, 0, 0.3);
  background: linear-gradient(180deg, #fdfbf0 0%, #f3efff 100%);
  box-shadow: 0 10px 30px rgba(255, 215, 0, 0.15), 0 8px 20px rgba(91, 75, 138, 0.1);
}

.premium-card:hover {
  box-shadow: 0 15px 35px rgba(255, 215, 0, 0.25);
  border-color: rgba(255, 215, 0, 0.6);
}

.premium-card .view-btn {
  background: linear-gradient(135deg, #d4af37 0%, #b8860b 100%);
  border: none;
}
</style>
