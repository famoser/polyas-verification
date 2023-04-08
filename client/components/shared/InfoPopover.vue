<script setup lang="ts">
import { ref } from 'vue'

defineProps<{
  message: string
}>()

const iconHovered = ref(false)
const popoverHovered = ref(true)
</script>

<template>
  <span class="position-relative">
    <FontAwesomeIcon :icon="['far', 'circle-question']" role="button" @mouseover="iconHovered = true" @mouseleave="iconHovered = false" @focusin="iconHovered = true" @focusout="iconHovered = false" />
    <span
      v-if="popoverHovered || iconHovered"
      class="popover d-block bs-popover-auto fade show position-absolute top-100 start-50 translate-middle-x mt-2"
      data-popper-placement="bottom"
      role="tooltip"
      @mouseover="popoverHovered = true"
      @mouseleave="popoverHovered = false"
    >
      <span class="popover-arrow position-absolute top-0 start-50 translate-middle"></span>
      <span class="popover-body w-25em d-block">{{ message }}</span>
    </span>
  </span>
</template>

<style scoped>
.w-25em {
  width: 25em;
}
</style>
