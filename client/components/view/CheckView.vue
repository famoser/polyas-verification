<script setup lang="ts">
import { useI18n } from 'vue-i18n'
import SuccessIndicator from '@/components/shared/CorrectIndicator.vue'
import { computed, ref } from 'vue'
import ExpandedIndicator from '@/components/shared/ExpandedIndicator.vue'

const props = defineProps<{
  prefix: string
  entry: string
  loading?: boolean
  success?: boolean
}>()

const { t } = useI18n()
const entryPrefix = `${props.prefix}.${props.entry}`
const expanded = ref(false)
const successLoading = computed(() => (props.loading ? undefined : props.success))
const showBody = computed(() => expanded.value || successLoading.value === false)
</script>

<template>
  <div
    class="card shadow-sm p-0"
    :class="{ 'border-success': successLoading === true, 'border-warning': successLoading === undefined, 'border-danger': successLoading === false }"
    role="button"
    @click="expanded = !expanded"
  >
    <div class="card-header" :class="{ 'border-bottom-0': !showBody }">
      <div class="d-flex flex-row">
        <p class="mb-0">
          <b>
            <SuccessIndicator :loading="loading" :success="success" class="me-2" />
            {{ t(`${entryPrefix}.title`) }}
          </b>
        </p>
        <div class="ms-auto">
          <ExpandedIndicator :expanded="showBody" />
        </div>
      </div>
    </div>
    <div class="card-body" v-if="showBody">
      <p class="mb-0">
        {{ t(`${entryPrefix}.description`) }}
      </p>
      <p v-if="successLoading === true" class="mt-2 mb-0 alert alert-success">
        {{ t(`${entryPrefix}.success`) }}
      </p>
      <p v-else-if="successLoading === false" class="mt-2 mb-0 alert alert-danger">
        <b>{{ t(`${entryPrefix}.failed`) }}</b>
        {{ t(`${entryPrefix}.failed_hint`) }}
      </p>
    </div>
  </div>
</template>
