<script setup lang="ts">
import { useI18n } from 'vue-i18n'

defineProps<{
  prefix: string
  to: string
}>()

const { t } = useI18n()
</script>

<template>
  <div class="card">
    <div class="card-body">
      <h5 class="card-title">{{ t(`view.${prefix}.title`) }}</h5>
      <p class="card-text">{{ t(`view.${prefix}.description`) }}</p>
      <router-link class="btn btn-primary" :to="to">{{ t(`view.${prefix}.title`) }}</router-link>
    </div>
  </div>
</template>
