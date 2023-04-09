<script setup lang="ts">
import { useI18n } from 'vue-i18n'
import SuccessIndicator from '@/components/shared/CorrectIndicator.vue'
import { computed, ref } from 'vue'
import ExpandedIndicator from '@/components/shared/ExpandedIndicator.vue'

const props = defineProps<{
  entry: string
  success: boolean
}>()

const { t } = useI18n()
const prefix = `domain.verification_result.${props.entry}`
const expanded = ref(false)
const showBody = computed(() => expanded.value || !props.success)
</script>

<template>
  <div class="card shadow-sm" :class="{ 'border-success': success, 'border-danger': !success }" role="button" @click="expanded = !expanded">
    <div class="card-header" :class="{ 'border-bottom-0': !showBody }">
      <div class="d-flex flex-row">
        <p class="mb-0">
          <b>
            <SuccessIndicator :success="success" class="me-2" />
            {{ t(`${prefix}.title`) }}
          </b>
        </p>
        <div class="ms-auto">
          <ExpandedIndicator :expanded="showBody" />
        </div>
      </div>
    </div>
    <div class="card-body" v-if="showBody">
      <p>
        {{ t(`${prefix}.description`) }}
      </p>
      <p v-if="success" class="mb-0 alert alert-success">
        {{ t(`${prefix}.success`) }}
      </p>
      <p v-else class="mb-0 alert alert-danger">
        <b>{{ t(`${prefix}.failed`) }}</b>
        {{ t(`${prefix}.failed_hint`) }}
      </p>
    </div>
  </div>
</template>

<style scoped>
.card {
}
</style>
