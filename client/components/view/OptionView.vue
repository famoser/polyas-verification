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
      <h5 class="card-title">{{ t(`view.option.${prefix}.title`) }}</h5>
      <p class="card-text">{{ t(`view.option.${prefix}.description`) }}</p>
      <router-link class="btn btn-primary" :to="to">{{ t(`view.option.${prefix}.go`) }}</router-link>
    </div>
  </div>
</template>
