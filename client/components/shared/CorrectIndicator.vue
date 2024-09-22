<script setup lang="ts">
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'

defineProps<{ success?: boolean; loading?: boolean }>()
</script>

<template>
  <span>
    <FontAwesomeIcon v-if="loading" class="text-warning rotating" :icon="['fas', 'spinner']" />
    <FontAwesomeIcon v-else-if="success === true" class="text-success" :icon="['far', 'circle-check']" />
    <FontAwesomeIcon v-else-if="success === undefined" :icon="['far', 'circle']" />
    <FontAwesomeIcon v-else class="text-danger" :icon="['far', 'circle-xmark']" />
  </span>
</template>

<style>
@-webkit-keyframes rotating {
  0%,
  100% {
    transform: rotate(0deg);
  }
  12.5% {
    transform: rotate(45deg);
  }
  25% {
    transform: rotate(90deg);
  }
  37.5% {
    transform: rotate(135deg);
  }
  50% {
    transform: rotate(180deg);
  }
  62.5% {
    transform: rotate(225deg);
  }
  75% {
    transform: rotate(270deg);
  }
  87.5% {
    transform: rotate(315deg);
  }
}

.rotating {
  animation: rotating 1s steps(1, end) infinite;
}
</style>
