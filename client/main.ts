import { createApp } from 'vue'
import App from './App.vue'
import './assets/main.scss'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { i18n } from '@/build/vue-i18n'
import './build/fontawesome'
import ReceiptApp from '@/components/ReceiptApp.vue'
import { createRouter, createWebHistory } from 'vue-router'
import HomeApp from '@/components/HomeApp.vue'
import VerifyApp from '@/components/VerifyApp.vue'

const app = createApp(App)

const routes = [
  { path: '/', component: HomeApp },
  { path: '/verify', component: VerifyApp },
  { path: '/receipt', component: ReceiptApp }
]
const router = createRouter({ history: createWebHistory(), routes })
app.use(router)

app.use(i18n)

app.component('FontAwesomeIcon', FontAwesomeIcon)

app.mount('#app')
