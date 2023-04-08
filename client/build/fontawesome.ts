import { config, library as FontawesomeLibrary } from '@fortawesome/fontawesome-svg-core'
import { faCircleCheck, faCircleQuestion, faCircleXmark } from '@fortawesome/free-regular-svg-icons'
import '@fortawesome/fontawesome-svg-core/styles.css'

config.autoAddCss = false
FontawesomeLibrary.add(faCircleCheck, faCircleXmark, faCircleQuestion)
