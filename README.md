# Things Taylor Hates

A WordPress plugin that provides toggles to disable features that Taylor dislikes.

## Features

- Disable RankMath Table of Contents scoring on Pages (toggleable)
- More features coming soon...

## Installation

1. Upload the plugin folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Navigate to Settings → Things Taylor Hates to configure toggles.

## Changelog

### 0.1.3
- Fixed the RankMath filter: use `rank_math/researches/tests` and unset `contentHasTOC`
- Fixed settings not saving (submit button name mismatch)
- Added capability check and i18n to the settings page

### 0.1.2
- Fixed fatal error on activation (null coalescing → isset)
- Author changed to SP4RKL

### 0.1.1
- Ensured RankMath TOC disable targets Pages

### 0.1.0
- Initial release
- Added toggle for disabling RankMath TOC on Pages

## License

This plugin is licensed under the GPLv2.
