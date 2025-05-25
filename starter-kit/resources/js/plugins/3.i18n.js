import { createI18n } from 'vue-i18n'
import en from '../../i18n/en.json'
import th from '../../i18n/th.json'
import { themeConfig } from '@themeConfig'

export default function (app) {
  if (!themeConfig.app.i18n.enable)
    return

  const i18n = createI18n({
    legacy: false,
    locale: themeConfig.app.i18n.defaultLocale,
    fallbackLocale: 'en',
    messages: {
      en,
      th,
    },
  })

  app.use(i18n)
}
