<template>
  <div class="category-manager">
    <h2>📁 Kategori Yönetimi</h2>

    <form @submit.prevent="createCategory" class="category-form">
      <input v-model="form.name" placeholder="Kategori Adı" required />
      <input v-model="form.slug" placeholder="Slug (örn. temizlik)" required />
      <input v-model="form.icon" placeholder="İkon (örn. 🧹)" maxlength="2" />
      <input v-model="form.color" type="color" />
      <button type="submit">➕ Ekle</button>
    </form>

    <div v-if="categories.length === 0" class="empty-state">Henüz kategori eklenmedi.</div>

    <ul class="category-list" v-else>
      <li v-for="cat in categories" :key="cat.id" class="category-item">
        <span class="category-label" :style="{ color: cat.color }">
          {{ cat.icon }} {{ cat.name }}
        </span>
        <button @click="deleteCategory(cat.id)" title="Sil">🗑️</button>
      </li>
    </ul>
  </div>
</template>

<script>
export default {
  data() {
    return {
      categories: [],
      form: {
        name: '',
        slug: '',
        icon: '📁',
        color: '#28a745',
      },
    };
  },
  mounted() {
    this.fetchCategories();
  },
  methods: {
    fetchCategories() {
      fetch('/api/categories')
        .then(res => res.json())
        .then(data => this.categories = data)
        .catch(() => this.categories = []);
    },
    createCategory() {
      fetch('/api/categories', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(this.form),
      })
        .then(res => res.json())
        .then(newCat => {
          this.categories.push(newCat);
          this.form = { name: '', slug: '', icon: '📁', color: '#28a745' };
        })
        .catch(err => console.error('Kategori eklenemedi:', err));
    },
    deleteCategory(id) {
      fetch(`/api/categories/${id}`, { method: 'DELETE' })
        .then(() => {
          this.categories = this.categories.filter(cat => cat.id !== id);
        })
        .catch(err => console.error('Kategori silinemedi:', err));
    }
  }
};
</script>

<style scoped>
.category-manager {
  max-width: 600px;
  margin: auto;
  font-family: sans-serif;
  padding: 1em;
}
.category-form {
  display: flex;
  flex-direction: column;
  gap: 0.5em;
  margin-bottom: 1em;
}
.category-form input {
  padding: 8px;
  border: 1px solid #ccc;
  border-radius: 4px;
}
.category-form button {
  padding: 8px;
  background-color: #007bff;
  color: white;
  border: none;
  cursor: pointer;
}
.category-form button:hover {
  background-color: #0056b3;
}
.category-list {
  list-style: none;
  padding: 0;
}
.category-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 6px 0;
  border-bottom: 1px solid #eee;
}
.category-label {
  font-weight: bold;
}
.category-item button {
  background: none;
  border: none;
  cursor: pointer;
  font-size: 1.2em;
}
.empty-state {
  text-align: center;
  color: #999;
  font-style: italic;
}
</style>