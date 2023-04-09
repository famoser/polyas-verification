<script setup lang="ts">
import { useI18n } from 'vue-i18n'
import FAQAnswer from '@/components/layout/FAQAnswer.vue'

const { t } = useI18n()
</script>

<template>
  <FAQAnswer :question="t('layout.faq.verification_target.question')">
    <p>
      {{ t('layout.faq.verification_target.verifies_received_at_server') }}
    </p>
    <p>
      {{ t('layout.faq.verification_target.must_trust_device') }}
    </p>
    <p>
      {{ t('layout.faq.verification_target.must_trust_servers') }}
    </p>
  </FAQAnswer>

  <FAQAnswer :question="t('layout.faq.verification_fails.question')">
    <p>
      {{ t('layout.faq.verification_fails.try_again_or_complain') }}
    </p>
  </FAQAnswer>
</template>
