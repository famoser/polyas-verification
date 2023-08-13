<script setup lang="ts">
import { useI18n } from 'vue-i18n'
import { api } from './services/api.js'
import HeaderJumbotron from '@/components/layout/HeaderJumbotron.vue'
import { ref } from 'vue'
import ElectionView from '@/components/view/ElectionView.vue'
import type { Election } from '@/components/domain/Election'

import type { ElectionDetails } from '@/components/domain/POLYAS'

const { t } = useI18n()

api.addInterceptors(t)

const election = ref<Election>()
const electionDetails = ref<ElectionDetails>()

api.getElection().then((result) => (election.value = result))
api.getElectionDetails().then((result) => (electionDetails.value = result))
</script>

<template>
  <div class="container mw-100em">
    <div class="my-5">
      <HeaderJumbotron />
      <ElectionView class="my-5" v-if="election && electionDetails" :election="election" :election-details="electionDetails" />

      <router-view></router-view>
    </div>
  </div>
</template>

<style scoped>
.mw-100em {
  max-width: 35em;
}
</style>
