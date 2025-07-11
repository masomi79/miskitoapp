import { createI18n } from 'vue-i18n'

const messages = {
  miq: { message: { hello: 'Nahksa' } },
  es: { message: { hello: 'Hola' } },
  ja: { message: { hello: 'こんにちは' } },
  en: { message: { hello: 'Hello' } }
}

const i18n = createI18n({
  legacy: false,
  locale: 'es', // 初期言語
  fallbackLocale: ['miq', 'ja', 'en'],
  messages,
})

export default i18n