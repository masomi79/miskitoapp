<template>
  <form @submit.prevent="onSubmit">
    <input v-model="inputWord" :placeholder="placeholder" />
    <button type="submit">{{ buttonLabel }}</button>
  </form>
</template>

<script>
export default {
  props: {
    modelValue: String,
    placeholder: {
      type: String,
      default: "ponga una palabra en español o en miskitu"
    },
    buttonLabel: {
      type: String,
      default: "Plikaia / Buscar"
    }
  },
  emits: ['update:modelValue', 'submit'],
  computed: {
    inputWord: {
      get() {
        return this.modelValue;
      },
      set(val) {
        this.$emit('update:modelValue', val);
      }
    }
  },
  methods: {
    onSubmit() {
      this.$emit('submit');
    }
  }
};
</script>