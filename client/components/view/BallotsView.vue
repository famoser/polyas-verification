<script setup lang="ts">
import { computed, ref } from 'vue'
import { api } from '@/services/api'
import type { Ballot } from '@/components/domain/POLYAS'
import BallotView from '@/components/view/Ballot/BallotView.vue'

defineProps<{
  choice: string
}>()

const ballots = ref<Ballot[]>()

api.getBallots().then((result) => (ballots.value = result))
// only support single ballot
const ballot = computed(() => (ballots.value ? ballots.value[0] : undefined))
</script>

<template>
  <BallotView v-if="ballot" :ballot="ballot" :choice="choice" />
</template>
