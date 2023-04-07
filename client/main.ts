import { createApp } from 'vue'
import App from './App.vue'
import './assets/main.scss'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { i18n } from '@/build/vue-i18n'
import './build/fontawesome'

const app = createApp(App)

app.use(i18n)

app.component('FontAwesomeIcon', FontAwesomeIcon)

app.mount('#app')
