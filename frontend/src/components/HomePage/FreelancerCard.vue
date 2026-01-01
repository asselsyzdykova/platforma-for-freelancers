<template>
  <div class="card">
    <div class="avatar"></div>

    <h3>{{ freelancer.name }}</h3>
    <p class="role">{{ freelancer.role }}</p>

    <p class="rating">⭐ {{ freelancer.rating }} ({{ freelancer.reviews }})</p>
    <p class="location">📍 {{ freelancer.location }}</p>

    <div class="skills">
      <span v-for="skill in freelancer.skills" :key="skill">
        {{ skill }}
      </span>
    </div>

    <button>View Profile</button>
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
}

.skills span {
  background: #fff;
  padding: 6px 14px;
  border-radius: 15px;
  margin: 4px;
  display: inline-block;
  font-size: 14px;
  font-weight: 500;
  color: #5b4b8a;
  box-shadow: 0 2px 6px rgba(91, 75, 138, 0.15);
  transition:
    transform 0.2s,
    box-shadow 0.2s;
}

.skills span:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(91, 75, 138, 0.2);
}

button {
  margin-top: 10px;
  background: #5b4b8a;
  color: white;
  border: none;
  padding: 10px 25px;
  border-radius: 15px;
  font-weight: 600;
  cursor: pointer;
  transition:
    background 0.3s,
    transform 0.2s;
}

button:hover {
  background: #6c57b8;
  transform: translateY(-2px);
}
</style>
