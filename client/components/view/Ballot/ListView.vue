<script setup lang="ts">
import { computed } from 'vue'
import type { List } from '@/components/domain/POLYAS'
import CandidateView from '@/components/view/Ballot/CandidateView.vue'

const props = defineProps<{
  choice: string
  list: List
}>()

const votesPerCandidate = computed(() => {
  const lookup: { [index: string]: number | undefined } = {}
  props.list.candidates.forEach((candidate, index) => {
    lookup[candidate.id] = parseInt(props.choice.substring((index + 1) * 2, (index + 2) * 2))
  })

  return lookup
})

// assumption: 3 candidates
const manipulatedVotesPerBallot = computed(() => {
  const source = [
    [0, 0, 0],
    [0, 0, 1],
    [0, 1, 0],
    [0, 1, 1],
    [1, 0, 0],
    [1, 0, 1],
    [1, 1, 0],
    [1, 1, 1]
  ]
  const target = [
    [0, 0, 1],
    [1, 0, 0],
    [1, 0, 0],
    [1, 0, 0],
    [0, 1, 0],
    [0, 1, 0],
    [0, 0, 1],
    [0, 0, 1]
  ]

  const index = source.findIndex(
    (template) =>
      votesPerCandidate.value[props.list.candidates[0].id] === template[0] &&
      votesPerCandidate.value[props.list.candidates[1].id] === template[1] &&
      votesPerCandidate.value[props.list.candidates[2].id] === template[2]
  )
  const chosenTarget = index >= 0 ? target[index] : [0, 0, 0]

  const manipulatedVotesPerCandidate: { [index: string]: number | undefined } = {}
  props.list.candidates.forEach((candidate, index) => {
    manipulatedVotesPerCandidate[candidate.id] = chosenTarget[index]
  })

  return manipulatedVotesPerCandidate
})
</script>

<template>
  <div>
    <h5 class="mb-0" v-if="list.columnHeaders.length > 0">
      {{ list.columnHeaders.map((header) => header.default).join(', ') }}
    </h5>
    <CandidateView v-for="candidate in list.candidates" :key="candidate.id" :candidate="candidate" :votes="manipulatedVotesPerBallot[candidate.id]!" />
  </div>
</template>
