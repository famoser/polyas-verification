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

type PolyasDocument = {
  nodes: any[]
}

type PolyasBlock = {
  nodes: any[]
  type: string
}

type PolyasText = {
  text: string
}

function isDocument(document: any): document is PolyasDocument {
  return 'object' in document && document['object'] === 'document'
}

function isBlock(block: any): block is PolyasBlock {
  return 'object' in block && block['object'] === 'block'
}

function isText(text: any): text is PolyasText {
  return 'object' in text && text['object'] === 'text'
}

const renderDocument = (document: any) => {
  return isDocument(document) ? document.nodes.map((node) => renderBlock(node)).join('\n') : ''
}

const renderBlock = (content: any) => {
  return isBlock(content) && content.type === 'paragraph' ? content.nodes.map((content) => renderParagraph(content)).join('\n') : ''
}

const renderParagraph = (text: any) => {
  return isText(text) ? text.text : ''
}

const { t } = useI18n()
</script>

<template>
  <div>
    <p>
      <b>{{ ballot.title['default'] }}</b>
    </p>
    <p v-if="ballot.contentAbove.value['default']">
      {{ renderDocument(ballot.contentAbove.value['default']) }}
    </p>
    <ListView v-for="list in ballot.lists" :key="list.id" :list="list" :choice="choicePerList[list.id]!" />
    <p v-if="ballot.contentBelow.value['default']">
      {{ renderDocument(ballot.contentBelow.value['default']) }}
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
