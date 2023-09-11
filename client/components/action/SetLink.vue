<script setup lang="ts">
import { useI18n } from 'vue-i18n'
import { ref, watch } from 'vue'
import InfoPopover from '@/components/shared/InfoPopover.vue'
import { useRouter } from 'vue-router'
import ScanQRCode from '@/components/action/ScanQRCode.vue'

const link = ref<string>()
const router = useRouter()
watch(link, () => {
  if (!link.value) {
    return
  }

  try {
    const url = new URL(link.value)
    const c = url.searchParams.get('c')
    const vid = url.searchParams.get('vid')
    const nonce = url.searchParams.get('nonce')
    if (!c || !vid || !nonce) {
      return
    }

    const urlParams = new URLSearchParams()
    urlParams.append('c', c)
    urlParams.append('vid', vid)
    urlParams.append('nonce', nonce)

    const search = urlParams.toString()
    router.push(`/verify?${search}`)
  } catch (_) {
    // URL constructor crashes if invalid email
  }
})
const reset = () => {
  link.value = undefined
}

const isInvalid = !!link.value

const cameraActive = ref<boolean>()

const { t } = useI18n()
</script>

<template>
  <div>
    <div class="d-flex border border-1 minh-10em maxh-20em mw-100 rounded">
      <button v-if="!cameraActive" class="btn btn-secondary m-auto" @click="cameraActive = true">
        {{ t('action.set_link.scan_qr_code') }}
      </button>
      <ScanQRCode v-else @scanned="link = $event" />
    </div>
    <input class="form-control mt-2" :class="{ 'is-invalid': isInvalid }" v-model="link" :placeholder="t('action.set_link.or_paste_link')" :disabled="isInvalid" />
    <InfoPopover v-if="!isInvalid" :message="t('action.set_link.full_verification')" :popover="t('action.set_link.full_verification_help')" />
    <div class="invalid-feedback" v-if="isInvalid">
      {{ t('action.set_link.link_invalid') }}
      <a href="#" @click.prevent="reset">{{ t('action.set_link.reset') }}</a>
    </div>
  </div>
</template>

<style>
.minh-10em {
  min-height: 10em;
}

.maxh-20em {
  max-height: 20em;
}
</style>
