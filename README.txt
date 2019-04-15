=== WP Hydro Raindrop ===
Contributors: adrenth, harshrajat
Donate link:
Tags: hydro, raindrop, mfa, 2fa, authenticator
Requires at least: 3.0.1
Tested up to: 5.1
Stable tag: 2.1.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Requires PHP: 7.0

Enables Hydro Raindrop Multi Factor Authentication on your Wordpress Website.

== Description ==

### Hydro Raindrop MFA: Blockchain based Multi Factor Authentication

Hydro MFA Plugin adds another security layer to your website using blockchain-based authentication layer. It's designed to work out of the box and offers unparalleled security standards for your website and your users, even beating google authenticator which is prone to phishing scams.

https://www.youtube.com/watch?v=d88jbPdxI88

#### Features
* Works out of the Box
* Intercepts the Login automatically
* Allows users to set-up MFA with their HydroID
* Customization options to suit every site neeeds!
* Instant Authentication

> Note: Free Hydro Mobile App is required to complete the MFA process. You can get iOS App [here](https://goo.gl/LpAuzq) or the Android App [here](https://goo.gl/eNrdn2).

### After Activation Process
The **following steps are necessary** to enable Hydro MFA on your site:

#### MFA Activation (Admin Side)
The **following steps are necessary** to enable Hydro Raindrop MFA on your site:

* Create an account over at [Hydrogen Website](https://www.hydrogenplatform.com/).
* Apply for **Production Access**. Once approved, generate your `Client ID`, `Client Secret` and `Application ID`.
* In WordPress from the Main Menu navigate to **Hydro Raindrop** -> **Settings** -> **API Settings** and enter the above details, select Production Mode and the plugin is ready for use.
* Set-up the preferred MFA method (and other customization options) in the tab **Customization**

That's it!

#### Requirements

* **SSL MUST be enabled for MFA to work.**
* PHP 7.0 or higher is required.

#### Documentation

* [API Documentation](https://www.hydrogenplatform.com/developers)
* [Plugin documentation](https://github.com/adrenth/wp-hydro-raindrop/blob/master/README.md)
* [Hydro Raindrop PHP SDK](https://github.com/adrenth/raindrop-sdk)

#### Support

* [GitHub](https://www.hydrogenplatform.com/support)
* [Official Website Support](https://www.hydrogenplatform.com/support)

#### Further reading

For more info on Hydro or MFA and how it's changing the world, check out the following:

* [Hydro's Official Site](https://www.hydrogenplatform.com/).
* [ProjectHydro](https://projecthydro.org/).
* [Hydro's Medium Blog](https://medium.com/hydrogen-api).
* [Hydro MFA Client Side Raindrop API](https://www.hydrogenplatform.com/docs/hydro/v1/).
* Become a part of the fastest growing Community! [Join Hydro Community](https://github.com/HydroCommunity).
* Are you a developer interested in expanding the Hydro ecosystem and earning bounties? [Visit Hydro HCDP Github Page](https://github.com/hydrogen-dev/hcdp/issues).
* Follow Hydro on [Telegram](https://t.me/projecthydro), [Facebook](https://www.facebook.com/hydrogenplatform), [Twitter](https://twitter.com/hydrogenapi) or [Instagram](https://www.instagram.com/hydrogenplatform/).

== Screenshots ==

1. Custom HydroID Setup Page.
2. Custom Hydro MFA Page.
3. Custom Hydro Settings Page.
4. Hydro Raindrop Settings

== Frequently Asked Questions ==

You'll find answers to many of your questions on [Official Website Support](https://www.hydrogenplatform.com/support).

== Upgrade Notice ==

Upgrading from 1.0 to 2.0 can be done without issues when using the default configuration. If you have customized your
site you should read the documentation if there are any changes which affects your custom implementation.

== Changelog ==

= 2.1.1 =
* Update WP requirements for plugin.

= 2.1.0 =
* Add support for resetting the HydroID of a user.

= 2.0.4 =
* Minor code improvements.

= 2.0.3 =
* Update WP requirements for plugin.

= 2.0.2 =
* Do not load plugin on XML RPC requests.

= 2.0.1 =
* Fixed critical issue when upgrading from v1 to v2 which causes settings to be invalid.

= 2.0.0 =
* Works out of the Box.
* Frontend Included / Automatic setup / Frontend Settings.
* Introduces 3 ways for MFA option (Enforced, Prompted and Optional).
* Re-authentication Feature (for extra sensitive pages).
* Blocking of Users on incorrect MFA Tries (customizable).
* Additional features - Plugin requirements check, Shortcodes, actions and filters for advance developers.

= 1.4.2 =
* Fixed critical authentication issue.

= 1.4.1 =
* Updated Author.

= 1.4.0 =
* Minor optimizations.
* Improved the README.txt contents.

= 1.3.0 =
* Added ability to intercept login automatically. Swanky frontend UI.
* Allow developers to make their own custom MFA page (with shortcodes).
* Improve MFA flow and security.

= 1.2.1 =
* Update WP readme.txt.

= 1.2.0 =
* Set PHP 7.0 requirement.

= 1.1.1 =
* Rename PLUGIN_NAME_VERSION constant to HYDRO_RAINDROP_VERSION.

= 1.1.0 =
* Fix API authentication issue: Unset API authentication token when switching environment.
* Re-generate a MFA digits between sessions when user hits "Cancel"-button on verification.
* Clear Hydro Raindrop User meta data after changing Hydro API settings.
* Validate Hydro API settings when saved.
* Remove HydroID length restriction.

= 1.0.0 =
* Initial release of WP Hydro Raindrop.
