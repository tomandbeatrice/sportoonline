import { ref, computed } from "vue";
import { useI18n } from "vue-i18n";
import { useLocalStorage } from "./useLocalStorage";

export type SupportedLocale = "tr" | "en" | "ar" | "de";

export interface LocaleOption {
  code: SupportedLocale;
  name: string;
  flag: string;
  dir: "ltr" | "rtl";
}

const LOCALE_KEY = "app-locale";

export function useLocale() {
  const { locale: i18nLocale, t } = useI18n();
  const [storedLocale, setStoredLocale] = useLocalStorage<SupportedLocale>(
    LOCALE_KEY,
    "tr"
  );

  const availableLocales = ref<LocaleOption[]>([
    { code: "tr", name: "Türkçe", flag: "🇹🇷", dir: "ltr" },
    { code: "en", name: "English", flag: "🇬🇧", dir: "ltr" },
    { code: "ar", name: "العربية", flag: "🇸🇦", dir: "rtl" },
    { code: "de", name: "Deutsch", flag: "🇩🇪", dir: "ltr" },
  ]);

  const currentLocale = computed(() => i18nLocale.value as SupportedLocale);

  const currentLocaleOption = computed(() => {
    return (
      availableLocales.value.find((l) => l.code === currentLocale.value) ||
      availableLocales.value[0]
    );
  });

  const isRTL = computed(() => currentLocaleOption.value.dir === "rtl");

  // Set locale and update document
  const setLocale = (locale: SupportedLocale) => {
    if (!availableLocales.value.find((l) => l.code === locale)) {
      console.warn(`Locale ${locale} is not supported`);
      return;
    }

    i18nLocale.value = locale;
    setStoredLocale(locale);

    // Update HTML lang attribute
    document.documentElement.lang = locale;

    // Update dir attribute for RTL languages
    const localeOption = availableLocales.value.find((l) => l.code === locale);
    if (localeOption) {
      document.documentElement.dir = localeOption.dir;
    }
  };

  // Initialize locale from storage
  const initLocale = () => {
    const savedLocale = storedLocale.value;
    if (savedLocale && savedLocale !== currentLocale.value) {
      setLocale(savedLocale);
    } else {
      // Update document attributes for current locale
      const localeOption = currentLocaleOption.value;
      document.documentElement.lang = currentLocale.value;
      document.documentElement.dir = localeOption.dir;
    }
  };

  // Toggle between locales
  const toggleLocale = () => {
    const currentIndex = availableLocales.value.findIndex(
      (l) => l.code === currentLocale.value
    );
    const nextIndex = (currentIndex + 1) % availableLocales.value.length;
    setLocale(availableLocales.value[nextIndex].code);
  };

  return {
    availableLocales: computed(() => availableLocales.value),
    currentLocale,
    currentLocaleOption,
    isRTL,
    setLocale,
    initLocale,
    toggleLocale,
    t,
  };
}
