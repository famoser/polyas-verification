<script setup lang="ts" generic="T extends string">
import type { Status } from '@/components/domain/Status'
import CheckView from '@/components/view/CheckView.vue'
import { computed, reactive, watch } from 'vue'

const emit = defineEmits<{
  (e: 'checksFinishedLoading'): void
}>()

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

const loadingFinishedPerEntry: { [index: string]: boolean | undefined } = reactive({})
const finishLoading = (entry: T) => {
  if (successPerEntry.value[entry] === true) {
    loadingFinishedPerEntry[entry] = true

    // start next invocation
    const index = props.errorOrder.indexOf(entry)
    const next = props.errorOrder[index + 1]
    console.log(index, next)
    if (index >= 0 && next) {
      window.setTimeout(() => finishLoading(next), 200)
    } else {
      emit('checksFinishedLoading')
    }
  } else {
    // uncover all if failed
    props.errorOrder.forEach((entry) => (loadingFinishedPerEntry[entry] = true))
    emit('checksFinishedLoading')
  }
}
watch(
  () => props.result,
  (newValue, oldValue) => {
    if (newValue === oldValue) {
      return
    }

    Object.keys(loadingFinishedPerEntry).map((key) => (loadingFinishedPerEntry[key] = false))

    if (props.errorOrder.length > 0) {
      finishLoading(props.errorOrder[0])
    }
  }
)
</script>

<template>
  <CheckView v-if="result && !result.status && !errorKnown" :entry="String(fallbackError)" :success="false" :prefix="prefix" />
  <CheckView v-for="entry in errorOrder" :key="entry" :entry="entry" :loading="!loadingFinishedPerEntry[entry]" :success="successPerEntry && successPerEntry[entry]" :prefix="prefix" />
</template>
