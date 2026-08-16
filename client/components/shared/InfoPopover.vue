<script setup lang="ts">
import { ref } from 'vue'

defineProps<{
  message: string
  popover: string
}>()

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
        @mouseover="iconHovered = true"
        @focusin="iconHovered = true"
        @mouseleave="iconHovered = false"
        @focusout="iconHovered = false"
      />
      <span class="position-absolute position-correction" v-if="popoverHovered || iconHovered">
        <span
          class="popover d-block bs-popover-auto fade show position-absolute top-100 start-50 translate-middle-x mt-2"
          data-popper-placement="bottom"
          @mouseover="popoverHovered = true"
          @mouseleave="popoverHovered = false"
        >
          <span class="popover-arrow position-absolute top-0 start-50 translate-middle"></span>
        </span>
        <span class="popover d-block bs-popover-auto fade show position-absolute top-100 start-50 translate-middle-x mt-2 mw-none" data-popper-placement="bottom" role="tooltip">
          <span class="popover-body d-block w-35em">
            {{ popover }}
          </span>
        </span>
      </span>
    </span>
  </div>
</template>

<style scoped>
.w-35em {
  width: 35em;
}
.mw-none {
  max-width: none;
}
.position-correction {
  top: 1em;
  left: 0.5em;
}
</style>
