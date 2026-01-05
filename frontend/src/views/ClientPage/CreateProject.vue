<template>
  <div class="create-project">
    <h1>Create New Project</h1>
    <p class="subtitle">
      Publish your project and find the right freelancer
    </p>

    <form class="card" @submit.prevent="createProject">
      <div class="field">
        <label>Project title</label>
        <input v-model="project.title" type="text" required />
      </div>

      <div class="field">
        <label>Description</label>
        <textarea
          v-model="project.description"
          rows="4"
          required
        ></textarea>
      </div>

      <div class="field">
        <label>Budget</label>
        <input
          v-model="project.budget"
          type="text"
          placeholder="$500 – $1000"
          required
        />
      </div>

      <div class="field">
        <label>Category</label>
        <select v-model="project.category" required>
          <option disabled value="">Select category</option>
          <option>Frontend</option>
          <option>Backend</option>
          <option>UI/UX</option>
          <option>Mobile</option>
          <option>Fullstack</option>
        </select>
      </div>

      <div class="field">
        <label>Skills / Tags</label>
        <input
          v-model="tagInput"
          @keydown.enter.prevent="addTag"
          placeholder="Press Enter to add tag"
        />

        <div class="tags">
          <span class="tag" v-for="(tag, index) in project.tags" :key="index">
            {{ tag }}
            <button type="button" @click="removeTag(index)">×</button>
          </span>
        </div>
      </div>

      <div class="actions">
        <button type="submit" class="primary">Publish project</button>
        <button type="button" class="secondary" @click="cancel">
          Cancel
        </button>
      </div>
    </form>
  </div>
</template>

<script>
export default {
  name: "CreateProject",

  data() {
    return {
      project: {
        title: "",
        description: "",
        budget: "",
        category: "",
        tags: [],
      },
      tagInput: "",
    };
  },

  methods: {
    addTag() {
      if (!this.tagInput.trim()) return;
      this.project.tags.push(this.tagInput.trim());
      this.tagInput = "";
    },

    removeTag(index) {
      this.project.tags.splice(index, 1);
    },

    createProject() {
      console.log("Created project:", this.project);

      this.$router.push("/projects");
    },

    cancel() {
      this.$router.push("/client-profile");
    },
  },
};
</script>

<style scoped>
.create-project {
  max-width: 800px;
  margin: 0 auto;
  padding: 40px 24px;
}

.subtitle {
  color: #666;
  margin-bottom: 32px;
}

.card {
  background: #f3efff;
  border-radius: 20px;
  padding: 32px;
}

.field {
  margin-bottom: 20px;
}

label {
  display: block;
  font-weight: 600;
  margin-bottom: 6px;
}

input,
textarea,
select {
  width: 100%;
  padding: 10px 12px;
  border-radius: 12px;
  border: 1px solid #ddd;
}

.tags {
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
  margin-top: 10px;
}

.tag {
  background: #e6e0ff;
  padding: 6px 10px;
  border-radius: 12px;
  font-size: 13px;
  display: flex;
  align-items: center;
  gap: 6px;
}

.tag button {
  border: none;
  background: transparent;
  cursor: pointer;
  font-size: 14px;
}

.actions {
  display: flex;
  gap: 12px;
  margin-top: 30px;
}

.primary {
  flex: 1;
  background: #5b3df5;
  color: white;
  border: none;
  padding: 12px;
  border-radius: 14px;
  font-weight: 600;
  cursor: pointer;
}

.secondary {
  flex: 1;
  background: #eee;
  border: none;
  padding: 12px;
  border-radius: 14px;
  cursor: pointer;
}
</style>
