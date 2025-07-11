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
        <input id="passwowrd" v-model="password" required  minlength="8"/>
      </div>
      <button type="submit">registrar</button>
    </form>
    <p v-if="success">se ha registrado equistosamente!</p>
  </div>
</template>

<script>
import { callWithAsyncErrorHandling } from 'vue';

export default {
  name: 'UserRegister',
  data() {
    return {
      name: '',
      email: '',
      password: '',
      success: false,
    };
  },
  methods: {
    async handleSubmit(){
      const data = {
        name: this.name,
        email: this.email,
        password: this.password,
      };
      console.log(data);
      try {
        const res = await fetch("http://localhost:8000/auth/register", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify(data),
        });
        const result = await res.json();
        if (res.ok) {
          alert(typeof result.msg === "string" ? result.msg : JSON.stringify(result.msg));
        }else{
          alert(typeof result.detail === "string" ? result.msg : JSON.stringify(result.msg));
        }
      } catch (e) {
        console.error("Error al registrar el usuario:", e);
        alert("Error al registrar el usuario");
      }
    }
  },
};
</script>

<style scoped>
.user-register {
  max-width: 400px;
  margin: 2rem auto;
}
</style>