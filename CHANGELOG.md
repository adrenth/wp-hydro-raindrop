# wp-hydro-raindrop

## 2.1.1

- Update WP requirements for plugin.

## 2.1.0

- Add support for resetting the HydroID of a user.

## 2.0.4

- Minor code improvements

## 2.0.3

- Update WP requirements for plugin.

## 2.0.2

- Do not load plugin on XML RPC requests.

## 2.0.1

- Fixed critical issue when upgrading from v1 to v2 which causes settings to be invalid.

## 2.0.0

- Added Plugin requirements check.
- Added Custom Pages for the Hydro Raindrop MFA flow.
- Added shortcodes which allow developers to fully customize the Hydro Raindrop integration.
- Added support for re-authentication for (custom) Posts.
- Added support for users to set-up their HydroID in the MFA flow.
- Improved MFA flow security
- Added support for forcing users to use MFA.
- Added actions/filters for developers to hook into the MFA flow.

## 1.4.2

- Fixed critical authentication issue.

## 1.4.1

- Updated Author.

## 1.4.0

- Minor optimizations.
- Improved the README.txt contents.

## 1.3.0

- Added ability to intercept login automatically. Swanky frontend UI.
- Allow developers to make their own custom MFA page (with shortcodes).
- Improve MFA flow and security.

## 1.2.1

- Update WP readme.txt.

## 1.2.0

- Set PHP 7.0 requirement.

## 1.1.1

- Rename PLUGIN_NAME_VERSION constant to HYDRO_RAINDROP_VERSION.

## 1.1.0

- Fix API authentication issue: Unset API authentication token when switching environment.
- Re-generate a MFA digits between sessions when user hits "Cancel"-button on verification.
- Clear Hydro Raindrop User meta data after changing Hydro API settings.
- Validate Hydro API settings when saved. 
- Remove HydroID length restriction.

## 1.0.0

- Initial release.
