<script setup lang="ts">
import OptionView from '@/components/view/OptionView.vue'
import { useTranslator } from '@/locales/translator'

const { t } = useTranslator()
</script>

<template>
  <h3 class="mb-3">{{ t('view.home_app.verifications') }}</h3>
  <OptionView class="mb-2" prefix="verify_app" to="/verify" />
  <OptionView prefix="receipt_app" to="/receipt" />
</template>
