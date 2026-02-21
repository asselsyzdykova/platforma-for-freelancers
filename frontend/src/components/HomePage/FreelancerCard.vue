<template>
  <div class="card">
    <img
    :src="freelancer.avatar_url || '/default-avatar.png'"
    class="avatar-img"
    alt="Avatar"
    />
    <h3>{{ freelancer.name }}</h3>
    <p class="role">{{ freelancer.role }}</p>

    <p class="rating">⭐ {{ freelancer.rating }} ({{ freelancer.reviews }})</p>
    <p class="location">📍 {{ freelancer.location }}</p>

    <div class="skills" aria-label="Skills">
      <span v-for="skill in freelancer.skills" :key="skill">
        {{ skill }}
      </span>
    </div>

    <RouterLink
      :to="{ name: 'PublicProfile', params: { id: freelancer.id } }"
      class="view-btn"
      target="_blank"
      rel="noopener"
    >
      View Profile
    </RouterLink>
  </div>
</template>

<script>
export default {
  name: 'FreelancerCard',
  props: {
    freelancer: Object,
  },
}
</script>

<style scoped>
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
  box-shadow: 0 8px 20px rgba(91, 75, 138, 0.2); /* мягкая тень */
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
  margin: 10px 0 5px;
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
</style>
