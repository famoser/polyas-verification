<script setup lang="ts">
import { useTranslator } from '@/locales/translator'
import { computed, onMounted, ref } from 'vue'

const props = defineProps<{
  expectedOwnerId: string
  enteredOwnerId?: string
}>()

const emit = defineEmits<{
  (e: 'entered', result: string): void
}>()

const ownerInput = ref<HTMLElement>()
const ownerInputType = computed(() => (/^\d+$/.test(props.expectedOwnerId) ? 'number' : 'text'))
const owner = ref<string | number>(props.enteredOwnerId ?? '')

onMounted(() => {
  ownerInput.value?.focus()
})

const confirm = function () {
  emit('entered', String(owner.value))
}

const { t } = useTranslator()
</script>

<template>
  <div class="d-flex flex-column align-items-center">
    <input
      ref="ownerInput"
      :type="ownerInputType"
      class="form-control mw-15em form-control-lg text-center"
      :placeholder="t('action.verify_ballot_owner.set_ballot_owner')"
      v-model="owner"
      :disabled="enteredOwnerId !== undefined"
      :class="{ 'is-invalid': enteredOwnerId && enteredOwnerId !== expectedOwnerId }"
    />
    <div class="form-text">
      {{ t('action.verify_ballot_owner.ballot_owner_source') }}
    </div>
  </div>

  <template v-if="enteredOwnerId === undefined">
    <button class="btn btn-primary mt-2" @click="confirm()">
      {{ t('shared.verify') }}
    </button>
  </template>
  <p v-else-if="enteredOwnerId === expectedOwnerId" class="alert alert-success mb-0">
    {{ t('action.verify_ballot_owner.successful') }}
  </p>
  <p v-else class="alert alert-danger mb-0">
    {{ t('action.verify_ballot_owner.failed') }}
  </p>
</template>

<style>
.mw-15em {
  width: 15em;
}
</style>
