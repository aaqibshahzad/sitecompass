# SiteCompass Internationalization (i18n)

This directory contains translation files for the SiteCompass plugin.

## Files

- **sitecompass-ai.pot** - The Portable Object Template file containing all translatable strings from the plugin. This file serves as the base for creating translations in other languages.

## Text Domain

The plugin uses the text domain: `sitecompass-ai`

## Creating Translations

To create a translation for your language:

1. Copy the `sitecompass-ai.pot` file
2. Rename it to `sitecompass-ai-{locale}.po` (e.g., `sitecompass-ai-es_ES.po` for Spanish)
3. Use a translation tool like [Poedit](https://poedit.net/) to translate the strings
4. Save the file - this will also generate a `.mo` file which WordPress uses

## Supported Languages

Currently, the plugin includes:
- English (default)

## Contributing Translations

If you'd like to contribute a translation, please:
1. Create a `.po` file for your language
2. Translate all strings
3. Test the translation in your WordPress installation
4. Submit the translation files to the plugin maintainers

## Loading Translations

The plugin automatically loads translations from this directory using WordPress's standard translation system. The text domain is loaded in the `Sitecompass_Ai_Plugin` class via the `load_plugin_textdomain()` function.

## Translation Functions Used

The plugin uses WordPress standard translation functions:
- `__()` - Returns translated string
- `_e()` - Echoes translated string
- `esc_html__()` - Returns translated and escaped string
- `esc_html_e()` - Echoes translated and escaped string
- `esc_attr__()` - Returns translated string for use in attributes
- `esc_attr_e()` - Echoes translated string for use in attributes

## Updating the POT File

When new translatable strings are added to the plugin, the POT file should be regenerated using WordPress i18n tools:

```bash
wp i18n make-pot . languages/sitecompass-ai.pot
```

Or using the WP-CLI command:

```bash
wp i18n make-pot /path/to/sitecompass languages/sitecompass-ai.pot --domain=sitecompass-ai
```
