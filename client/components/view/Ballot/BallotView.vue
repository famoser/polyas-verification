<script setup lang="ts">
import { computed } from 'vue'
import type { Ballot } from '@/components/domain/POLYAS'
import ListView from '@/components/view/Ballot/ListView.vue'
import { useI18n } from 'vue-i18n'
import InfoPopover from '@/components/shared/InfoPopover.vue'

const props = defineProps<{
  choice: string
  ballot: Ballot
}>()

const ballotValid = computed(() => props.choice.startsWith('00'))
const choicePerList = computed(() => {
  const lookup: { [index: string]: string | undefined } = {}
  let activeIndex = 2 // first byte is just ballot validity
  props.ballot.lists.forEach((list) => {
    const length = (1 + list.candidates.length) * 2
    lookup[list.id] = props.choice.substring(activeIndex, activeIndex + length)
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
      <div class="form-text mb-4">
        {{ t('view.ballot.ballot_view.change_your_vote') }}
        <InfoPopover :message="t('view.ballot.ballot_view.change_vote_for_screenshot')" />
      </div>

      <p v-if="ballot.contentAbove.value['default']">
        {{ ballot.contentAbove.value['default'] }}
      </p>
      <p>
        <b>{{ ballot.title['default'] }}</b>
      </p>
      <ListView v-for="list in ballot.lists" :key="list.id" :list="list" :choice="choicePerList[list.id]!" />
      <p v-if="ballot.contentBelow.value['default']">
        {{ ballot.contentBelow.value['default'] }}
      </p>

      <div class="form-check mb-0 mt-4">
        <input class="form-check-input" type="checkbox" :checked="ballotValid" :id="ballot.id" />
        <label class="form-check-label" :for="ballot.id">
          {{ t('view.ballot.ballot_view.ballot_valid') }}
        </label>
      </div>
    </div>
  </div>
</template>
