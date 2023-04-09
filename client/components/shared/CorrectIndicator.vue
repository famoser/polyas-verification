<script setup lang="ts">
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'

defineProps<{ success: boolean }>()
</script>

<template>
  <span>
    <FontAwesomeIcon v-if="success" class="text-success" :icon="['far', 'circle-check']" />
    <FontAwesomeIcon v-else class="text-danger" :icon="['far', 'circle-xmark']" />
  </span>
</template>
