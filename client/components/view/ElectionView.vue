<script setup lang="ts">
import type { Election } from '@/components/domain/Election'

import type { ElectionDetails } from '@/components/domain/POLYAS'

defineProps<{
  election: Election
  electionDetails: ElectionDetails
}>()
</script>

<template>
  <div class="bg-body-tertiary p-2">
    <p class="mb-0">
      <small>{{ election.organizer }}</small
      ><br />
      <b>{{ electionDetails.title.default }}</b
      ><br />
      {{ election.period }}<br />
      <a :href="election.link" target="_blank">{{ election.link }}</a>
    </p>
  </div>
</template>
