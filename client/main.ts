import { createApp } from 'vue'
import App from './App.vue'
import './assets/main.scss'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import './build/fontawesome'
import ReceiptApp from '@/components/ReceiptApp.vue'
import { createRouter, createWebHistory } from 'vue-router'
import HomeApp from '@/components/HomeApp.vue'
import VerifyApp from '@/components/VerifyApp.vue'
import { createTranslator, setGlobalTranslator } from '@/locales/translator'
import de from './locales/de.json'

const app = createApp(App)

const translator = createTranslator('de', 'de', { de })
setGlobalTranslator(translator)

const routes = [
  { path: '/', component: HomeApp },
  { path: '/verify', component: VerifyApp },
  { path: '/receipt', component: ReceiptApp }
]
const router = createRouter({ history: createWebHistory(), routes })
app.use(router)

app.component('FontAwesomeIcon', FontAwesomeIcon as any)

app.mount('#app')
