<template>
  <div class="user-register">
    <h2>sign up</h2>
    <form @submit.prevent="handleSubmit">
      <div>
        <label for="name">nombre</label>
        <input id="name" v-model="name" required />
      </div>
      <div>
        <label for="email">correo</label>
        <input id="email" v-model="email" required />
      </div>
      <div>
        <label for="password">contraseña</label>
        <input id="passwowrd" v-model="password" required />
      </div>
      <button type="submit">registrar</button>
    </form>
    <p v-if="success">se ha registrado equistosamente!</p>
  </div>
</template>

<script>
export default {
  name: 'WordRegister',
  data() {
    return {
      name: '',
      email: '',
      password: '',
      success: false,
    };
  },
  methods: {
    handleSubmit() {
      this.success = true;
      this.word = '';
      this.meaning = '';
    },
  },
};
</script>

<style scoped>
.user-register {
  max-width: 400px;
  margin: 2rem auto;
}
</style>