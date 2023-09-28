<script setup lang="ts">
import { computed } from 'vue'
import type { Ballot } from '@/components/domain/POLYAS'
import ListView from '@/components/view/Ballot/ListView.vue'
import { useI18n } from 'vue-i18n'
import BallotContentView from '@/components/view/Ballot/BallotContentView.vue'

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
  <div>
    <h4>
      {{ ballot.title['default'] }}
    </h4>
    <p v-if="ballot.contentAbove.value['default']">
      <BallotContentView :content="ballot.contentAbove.value['default']" />
    </p>
    <div class="d-flex flex-column row-gap-3">
      <ListView v-for="list in ballot.lists" :key="list.id" :list="list" :choice="choicePerList[list.id]!" />
    </div>
    <p v-if="ballot.contentBelow.value['default']">
      <BallotContentView :content="ballot.contentBelow.value['default']" />
    </p>

    <div class="form-check mb-0 mt-4">
      <input class="form-check-input" type="checkbox" :checked="ballotValid" :id="ballot.id" />
      <label class="form-check-label" :for="ballot.id">
        {{ t('view.ballot.ballot_view.ballot_valid') }}
      </label>
    </div>
    <p class="alert alert-warning mt-2 mb-0" v-if="!ballotValid">
      {{ t(`view.ballot.ballot_view.ballot_invalid_explanation`) }}
    </p>
  </div>
</template>
