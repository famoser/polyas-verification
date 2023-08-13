<script setup lang="ts">
import { computed } from 'vue'
import type { List } from '@/components/domain/POLYAS'
import CandidateView from '@/components/view/Ballot/CandidateView.vue'

const props = defineProps<{
  choice: string
  list: List
}>()

const listVotes = computed(() => parseInt(props.choice.substring(0, 1), 2))
const votesPerCandidate = computed(() => {
  const lookup: { [index: string]: number | undefined } = {}
  props.list.candidates.forEach((candidate, index) => {
    lookup[candidate.id] = parseInt(props.choice.substring((index + 1) * 2, (index + 2) * 2))
  })

  return lookup
})
</script>

<template>
  <CandidateView v-for="candidate in list.candidates" :key="candidate.id" :candidate="candidate" :votes="votesPerCandidate[candidate.id]!" />
</template>
