<script setup lang="ts">
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'

defineProps<{ success?: boolean }>()
</script>

<template>
  <span>
    <FontAwesomeIcon v-if="success === true" class="text-success" :icon="['far', 'circle-check']" />
    <FontAwesomeIcon v-else-if="success === undefined" class="text-warning" :icon="['far', 'circle-question']" />
    <FontAwesomeIcon v-else class="text-danger" :icon="['far', 'circle-xmark']" />
  </span>
</template>
