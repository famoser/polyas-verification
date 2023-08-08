<script setup lang="ts">
import { computed, ref } from 'vue'
import type { Status } from '@/components/domain/Status'
import { useRoute } from 'vue-router'
import SetLink from '@/components/action/SetLink.vue'
import VerificationStatusView from '@/components/view/VerificationStatusView.vue'
import VerificationExplanation from '@/components/layout/VerificationExplanation.vue'

const route = useRoute()
const urlPayload = computed(() => {
  const payload = route.query?.c
  const voterId = route.query?.vid
  const nonce = route.query?.nonce
  if (!payload || !voterId || !nonce) {
    return null
  }

  return { payload, voterId, nonce }
})

const password = ref<string>()

const verificationStatus = ref<Status>()
</script>

<template>
  <SetLink v-if="!urlPayload" />
  <SetPassword v-else-if="!password" @changed="password = $event" />
  <VerificationStatusView v-if="verificationStatus" :result="verificationStatus" @reset="verificationStatus = undefined" />
  <div class="my-5">&nbsp;</div>
  <VerificationExplanation />
</template>
