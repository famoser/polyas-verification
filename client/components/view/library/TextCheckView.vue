<script setup lang="ts">
import { useI18n } from 'vue-i18n'
import { computed } from 'vue'
import CheckView from '@/components/view/library/CheckView.vue'

const props = defineProps<{
  prefix: string
  entry: string
  loading?: boolean
  success?: boolean
}>()

const { t } = useI18n()
const entryPrefix = `${props.prefix}.${props.entry}`
const finalResult = computed(() => (props.loading ? undefined : props.success))
</script>

<template>
  <CheckView :prefix="prefix" :entry="entry" :loading="loading" :success="success">
    <p class="mb-0">
      {{ t(`${entryPrefix}.description`) }}
    </p>
    <p v-if="finalResult === true" class="mt-2 mb-0 alert alert-success">
      {{ t(`${entryPrefix}.success`) }}
    </p>
    <p v-else-if="finalResult === false" class="mt-2 mb-0 alert alert-danger">
      <b>{{ t(`${entryPrefix}.failed`) }}</b>
      {{ t(`${entryPrefix}.failed_hint`) }}
    </p>
  </CheckView>
</template>
