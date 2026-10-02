import type { Translation } from '@/components/domain/POLYAS'

export const formatTranslation = (value: Translation) => {
  return value['default'].replace(/<[^>]*>/g, '').replace('&amp;', '&')
}
