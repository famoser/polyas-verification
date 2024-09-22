<script setup lang="ts">
import { useI18n } from 'vue-i18n'
import SuccessIndicator from '@/components/shared/CorrectIndicator.vue'
import { computed, ref } from 'vue'
import ExpandedIndicator from '@/components/shared/ExpandedIndicator.vue'

const props = defineProps<{
  prefix: string
  entry: string
  done: boolean
  success?: boolean
  forceClosedWhenDone?: boolean
}>()

const { t } = useI18n()
const entryPrefix = `${props.prefix}.${props.entry}`
const expanded = ref(false)
// noinspection PointlessBooleanExpressionJS
const invertedExpandedForFail = computed(() => (props.success === false ? !expanded.value : expanded.value))
const showBody = computed(() => !props.done || (invertedExpandedForFail.value && (!props.forceClosedWhenDone || !props.done)))
</script>

<template>
  <div class="card shadow-sm p-0" :class="{ 'border-warning': !done, 'border-success': done && success, 'border-danger': done && success === false }" role="button">
    <div class="card-header" :class="{ 'border-bottom-0': !showBody }" @click="expanded = !expanded">
      <div class="d-flex flex-row">
        <p class="mb-0">
          <b>
            <SuccessIndicator :loading="!done" :success="success" class="me-2" />
            <span v-if="!done">{{ t(`${entryPrefix}.title`) }}</span>
            <span v-else>{{ t(`${entryPrefix}.done`) }}</span>
          </b>
        </p>
        <div class="ms-auto" v-if="props.done && !props.forceClosedWhenDone">
          <ExpandedIndicator :expanded="showBody" />
        </div>
      </div>
    </div>
    <div class="card-body" v-if="showBody">
      <slot></slot>
    </div>
  </div>
</template>
