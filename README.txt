=== WP Hydro Raindrop ===
Contributors: adrenth
Donate link: https://github.com/adrenth
Tags: hydro, raindrop, mfa, 2fa
Requires at least: 3.0.1
Tested up to: 3.4
Stable tag: 4.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Requires PHP: 7.0

Provides Hydro Raindrop Multi Factor Authentication to your WordPress website.

== Description ==

Allows users to enable the Hydro Raindrop MFA to add an extra layer of security.

For the MFA to work you need to register for an account at https://www.hydrogenplatform.com.

Requirements:

* IMPORTANT: SSL (HTTPS) MUST be enabled for MFA to work.
* PHP 7.0 or higher is required.

== Installation ==

Download plugin from WordPress.org.

== Issues ==

Issues can be reported here: https://github.com/adrenth/wp-hydro-raindrop/issues

== Changelog ==
= 1.2.0 =
* Set PHP 7.0 requirement

= 1.1.1 =
* Rename PLUGIN_NAME_VERSION constant to HYDRO_RAINDROP_VERSION.

= 1.1.0 =
* Fix API authentication issue: Unset API authentication token when switching environment.
* Re-generate a MFA digits between sessions when user hits "Cancel"-button on verification.
* Clear Hydro Raindrop User meta data after changing Hydro API settings.
* Validate Hydro API settings when saved.
* Remove HydroID length restriction.

= 1.0.0 =
* Initial release of WP Hydro Raindrop
