<script setup lang="ts">
import { computed, ref } from 'vue'
import { api } from '@/services/api'
import type { Ballot } from '@/components/domain/POLYAS'
import { useI18n } from 'vue-i18n'
import InfoPopover from '@/components/shared/InfoPopover.vue'
import BallotView from '@/components/view/Ballot/BallotView.vue'

const props = defineProps<{
  choice: string
}>()

const ballots = ref<Ballot[]>()

api.getBallots().then((result) => (ballots.value = result))

const choicePerBallot = computed(() => {
  const lookup: { [index: string]: string | undefined } = {}
  let activeIndex = 0
  ballots.value?.forEach((ballot) => {
    const length = (1 + ballot.lists.reduce((previous, current) => previous + 1 + current.candidates.length, 0)) * 2
    lookup[ballot.id] = props.choice.substring(activeIndex, activeIndex + length)
    activeIndex += length
  })

  return lookup
})

const { t } = useI18n()
</script>

<template>
  <div class="card">
    <div class="card-header">
      <p class="mb-0">
        <b>
          {{ t(`view.ballot.ballot_view.decrypted_ballot`) }}
        </b>
      </p>
    </div>
    <div class="card-body">
      <div class="mb-5">
        <InfoPopover :message="t('view.ballot.ballot_view.change_your_vote')" :popover="t('view.ballot.ballot_view.change_vote_for_screenshot')" />
      </div>
      <div class="d-flex row-gap-5 flex-column">
        <BallotView v-for="ballot in ballots" :key="ballot.id" :ballot="ballot" :choice="choicePerBallot[ballot.id] ?? ''" />
      </div>
    </div>
  </div>
</template>
