<script setup lang="ts">
import type { Candidate } from '@/components/domain/POLYAS'

defineProps<{
  candidate: Candidate
  votes: number
}>()
</script>

<template>
  <div class="form-check">
    <input class="form-check-input" type="checkbox" :checked="votes > 0" :id="candidate.id" />
    <label class="form-check-label" :for="candidate.id">
      {{ candidate.columns[0].value.default }}
    </label>
  </div>
</template>
