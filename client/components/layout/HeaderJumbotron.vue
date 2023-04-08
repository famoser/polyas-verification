<script setup lang="ts">
import { useI18n } from 'vue-i18n'

const { t } = useI18n()
</script>

<template>
  <h1>{{ t('layout.header_jumbotron.title') }}</h1>
  <p class="lead">{{ t('layout.header_jumbotron.description') }}</p>
</template>
