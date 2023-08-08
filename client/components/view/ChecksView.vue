<script setup lang="ts" generic="T extends string">
import type { Status } from '@/components/domain/Status'
import { useI18n } from 'vue-i18n'
import CheckView from '@/components/view/CheckView.vue'

const emit = defineEmits<{
  (e: 'reset'): void
}>()

const props = defineProps<{
  result: Status
  errorOrder: T[]
  fallbackError: T
  prefix: string
}>()

const errorKnown = props.errorOrder.includes(props.result.error as T)
const errorEntryIndex = props.errorOrder.indexOf(props.result.error as T)

const successPerEntry: { [index: string]: boolean | undefined } = {}
props.errorOrder.forEach((knownEntry, index) => {
  console.log(knownEntry, index, props.result.status)
  if (props.result.status || (errorKnown && errorEntryIndex > index)) {
    successPerEntry[knownEntry] = true
  }

  if (!props.result.status) {
    if (!errorKnown || errorEntryIndex < index) {
      successPerEntry[knownEntry] = undefined
    }

    if (errorEntryIndex === index) {
      successPerEntry[knownEntry] = false
    }
  }
})

const { t } = useI18n()
</script>

<template>
  <div class="row g-2">
    <CheckView v-if="!result.status && !errorKnown" :entry="String(fallbackError)" :success="false" :prefix="prefix" />
    <div class="col-12" v-for="entry in errorOrder" :key="entry">
      <CheckView :entry="entry" :success="successPerEntry[entry]" :prefix="prefix" />
    </div>
    <div class="col-12">
      <button class="btn btn-outline-primary" @click="emit('reset')">
        {{ t('view.verification_result_view.reset') }}
      </button>
    </div>
  </div>
</template>
