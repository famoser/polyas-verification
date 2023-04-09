<script setup lang="ts">
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'

defineProps<{ expanded: boolean }>()
</script>

<template>
  <span>
    <FontAwesomeIcon v-if="expanded" :icon="['fas', 'chevron-down']" />
    <FontAwesomeIcon v-else :icon="['fas', 'chevron-right']" />
  </span>
</template>
