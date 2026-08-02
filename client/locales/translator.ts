interface Translator {
  getLocale(): string
  setLocale(locale: string): void
  translate(key: string, params?: Record<string, unknown>, locale?: string): string
}

interface Dictionary {
  [key: string]: Dictionary | string
}

interface Dictionaries {
  [key: string]: Dictionary
}

type Params = Record<string, string | number> & { count?: number }

export const createTranslator = function (locale: string, fallbackLocale: string, dictionaries: Dictionaries): Translator {
  let _locale = locale
  const _fallbackLocale = fallbackLocale
  const _dictionaries = dictionaries

  return {
    getLocale() {
      return _locale
    },

    setLocale(locale: string) {
      _locale = locale
    },

    translate(key: string, params: Params = {}, locale: string | undefined = undefined) {
      const currentLocale = locale || _locale
      let template = getNestedValue(_dictionaries[currentLocale], key)
      if (!template) {
        const error = `[translator] Missing the "${key}" key within the "${currentLocale}" dictionary`
        if (_fallbackLocale !== currentLocale) {
          template = getNestedValue(_dictionaries[_fallbackLocale], key)
          if (!template) {
            console.error(error + ` and the "${_fallbackLocale}" fallback locale.`)
            return ''
          } else {
            console.warn(error + `, but found it in the "${_fallbackLocale}" fallback locale.`)
          }
        } else {
          console.error(error + '.')
          return ''
        }
      }

      if ('count' in params && params.count) {
        const templateParts = template.split('|')

        if (templateParts.length === 2) {
          // special case: two entries, then treat 0 as plural
          const templateIndex = params.count === 1 ? 0 : 1
          return fillTemplate(templateParts[templateIndex], params)
        } else {
          // normal case: assume 0-based templates
          if (params.count < templateParts.length) {
            return fillTemplate(templateParts[params.count], params)
          } else {
            return fillTemplate(templateParts[templateParts.length - 1], params)
          }
        }
      }

      return fillTemplate(template, params)
    }
  }
}

const placeholderRegex = /{(.*?)}/g
const fillTemplate = function (template: string, templateData: Record<string, string | number>): string {
  return template.replace(placeholderRegex, (_, placeholder) => {
    return String(templateData[placeholder.trim()]) ?? ''
  })
}

const getNestedValue = function (sourceObject: Dictionary, path: string): string | undefined {
  const pathSegments = path.split('.')

  let currentValue: Dictionary | undefined | string = sourceObject
  for (let pathIndex = 0; pathIndex < pathSegments.length; pathIndex++) {
    currentValue = currentValue && typeof currentValue === 'object' ? currentValue[pathSegments[pathIndex]] : undefined
  }

  return typeof currentValue === 'string' ? currentValue : undefined
}

let globalTranslator: Translator | undefined
export const setGlobalTranslator = function (translator: Translator) {
  globalTranslator = translator
}

// by convention, composable function names start with "use"
export function useTranslator() {
  if (!globalTranslator) {
    throw new Error('Global translator not set')
  }

  const t = globalTranslator.translate.bind(globalTranslator)

  // expose managed state as return value
  return { t }
}
