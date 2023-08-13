<script setup lang="ts">
import { computed } from 'vue'
import type { Ballot } from '@/components/domain/POLYAS'
import ListView from '@/components/view/Ballot/ListView.vue'
import { useI18n } from 'vue-i18n'

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
    <div class="card-body">
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
    </div>
    <div class="card-footer">
      <div class="form-check">
        <input class="form-check-input" type="checkbox" :checked="ballotValid" :id="ballot.id" />
        <label class="form-check-label" :for="ballot.id">
          {{ t('view.ballot.ballot_view.ballot_valid') }}
        </label>
      </div>
    </div>
  </div>
</template>
