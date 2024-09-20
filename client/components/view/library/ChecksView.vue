<script setup lang="ts" generic="T extends string">
import type { Status } from '@/components/domain/Status'
import { computed } from 'vue'
import TextCheckView from '@/components/view/library/TextCheckView.vue'

const props = defineProps<{
  result?: Status
  errorOrder: T[]
  fallbackError: T
  prefix: string
}>()

const errorKnown = computed(() => props.result && props.errorOrder.includes(props.result.error as T))
const errorEntryIndex = computed(() => props.errorOrder.indexOf(props.result?.error as T))

const successPerEntry = computed(() => {
  const result: { [index: string]: boolean | undefined } = {}

  props.errorOrder.forEach((knownEntry, index) => {
    if (props.result) {
      if (props.result.status || (errorKnown.value && errorEntryIndex.value > index)) {
        result[knownEntry] = true
      }

      if (!props.result.status) {
        if (!errorKnown.value || errorEntryIndex.value < index) {
          result[knownEntry] = undefined
        }

        if (errorEntryIndex.value === index) {
          result[knownEntry] = false
        }
      }
    }
  })

  return result
})
</script>

<template>
  <TextCheckView v-if="result && !result.status && !errorKnown" :entry="String(fallbackError)" :success="false" :prefix="prefix" />
  <TextCheckView v-for="entry in errorOrder" :key="entry" :prefix="prefix" :entry="entry" :success="successPerEntry ? successPerEntry[entry] : undefined" />
</template>
