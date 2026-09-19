<script setup lang="ts">
import { ref } from 'vue'

defineProps<{
  message: string
  popover: string
}>()

const iconClicked = ref(false)
const iconHovered = ref(false)
const popoverHovered = ref(false)
</script>

<template>
  <div class="form-text position-relative">
    {{ message }}
    <span class="position-relative">
      <FontAwesomeIcon
        :icon="['far', 'circle-question']"
        role="button"
        @click="iconClicked = !iconClicked"
        @mouseover="iconHovered = true"
        @focusin="iconHovered = true"
        @mouseleave="iconHovered = false"
        @focusout="iconHovered = false"
      />
      <span class="d-block alert alert-info" v-if="popoverHovered || iconHovered || iconClicked" @mouseover="popoverHovered = true" @mouseleave="popoverHovered = false">
        <FontAwesomeIcon :icon="['far', 'circle-question']" />
        {{ popover }}
      </span>
    </span>
  </div>
</template>

<style scoped></style>
