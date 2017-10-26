import i18next from 'i18next';
import sprintf from 'i18next-sprintf-postprocessor';
// https://github.com/i18next/react-i18next/blob/master/example/react-native-expo/js/i18n.js

// creating a language detection plugin using expo
// http://i18next.com/docs/ownplugin/#languagedetector
const languageDetector = {
  type: 'languageDetector',
  detect: () => 'en',
  init: () => {},
  cacheUserLanguage: () => {}
};

const resources = require('./i18n.json');

i18next.use(languageDetector).use(sprintf).init({
  fallbackLng: 'en',
  lang: 'en',
  resources,
  // have a common namespace used around the full app
  ns: ['common'],
  defaultNS: 'common',
  // disable nested keys via keySeperator
  keySeparator: false,
  debug: false, // process.env.NODE_ENV === 'development',
  interpolation: {
    escapeValue: false, // not needed for react as it does escape per default to prevent xss!
  },

  // react i18next special options (optional)
  react: {
    wait: false, // set to true if you like to wait for loaded in every translated hoc
    nsMode: 'fallback' // set it to fallback to let passed namespaces to translated hoc act as fallbacks
  }
});

export default i18next;
