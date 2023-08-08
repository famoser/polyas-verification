<script setup lang="ts">
import { ref } from 'vue'
import { api } from '@/services/api'
import type { BallotDefinition } from '@/components/domain/BallotDefinition'

defineProps<{
  choice: string
}>()

const ballotDefinitions = ref<BallotDefinition[]>()

api.getBallotDefinitions().then((result) => (ballotDefinitions.value = result))
</script>

<template>
  <p></p>
</template>
