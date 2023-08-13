<script setup lang="ts">
import OptionView from '@/components/view/OptionView.vue'
import { useI18n } from 'vue-i18n'
import BallotsView from '@/components/view/BallotsView.vue'

const { t } = useI18n()
</script>

<template>
  <h3 class="mb-3">{{ t('view.home_app.verifications') }}</h3>
  <OptionView class="mb-2" prefix="verify" to="/verify" />
  <OptionView prefix="receipt" to="/receipt" />
  <BallotsView choice="00000001" />
</template>
