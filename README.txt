=== WP Hydro Raindrop ===
Contributors: adrenth, harshrajat
Donate link: 
Tags: hydro, raindrop, mfa, 2fa, authenticator
Requires at least: 3.0.1
Tested up to: 4.9
Stable tag: 4.9
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Requires PHP: 7.0

Enables Hydro Raindrop Multi Factor Authentication on your Wordpress Website.

== Description ==
 
### Hydro Raindrop MFA: Blockchain based Multi Factor Authentication

Hydro MFA Plugin adds another security layer to your website using blockchain-based authentication layer. It's designed to work out of the box and offers unparalleled security standards for your website and your users, even beating google authenticator which is prone to phishing scams. 

https://www.youtube.com/watch?v=d88jbPdxI88

#### Features
* Requires minimum hassle to setup
* Intercepts the Login automatically
* Instant Authentication

> Note: Free Hydro Mobile App is required to complete the MFA process. You can get [iOS App here](https://goo.gl/LpAuzq) or the [Android App here](https://goo.gl/eNrdn2).

### Installation
#### From within WordPress
1. Visit 'Plugins > Add New'
2. Search for 'Hydro MFA'
3. Activate Hydro MFA from your Plugins page.
4. Follow the **after activation process** outlined below.

#### Manually
1. Upload the `wp-hydro-raindrop` folder to the `/wp-content/plugins/` directory
2. Activate the Hydro MFA plugin through the 'Plugins' menu in WordPress
3. Follow the **after activation process** outlined below.

### After Activation Process
The **following steps are necessary** to enable Hydro MFA on your site:

#### Required Page(s) Creation
1. Create **Hydro MFA** Page from WP Backend Interface, Keep the content as: 
[hydro_raindrop_mfa_form_open]
[hydro_raindrop_mfa_digits]
[hydro_raindrop_mfa_button_authorize]
[hydro_raindrop_mfa_button_cancel]
[hydro_raindrop_mfa_form_close]

2. Create **Hydro ID** Page from WP Backend Interface, Keep the content as: 
[hydro_raindrop_manage_hydro_id] 

#### MFA Activation (Admin Side)
* Create an account over at [Hydrogen Website](https://www.hydrogenplatform.com/)
* Apply for **Production Access**. Once approved, generate your **Client ID**, **Client Secret** and **Application ID**, 
* Navigate to Settings -> Hydro MFA and enter the above details, select **Production Mode** and the plugin is ready for use

#### MFA Activation (User Side)
* Your users need to download the Hydro Mobile App from the App Store (links above).
* You are required to expose the User profile page for every user you want Hydro MFA to be enabled on
* More specifically, the profile field of Hydro ID, your users are required to first verify themselves by entering HydroID from the mobile app
* Done! Your site is now MFA enabled!

####  Requirements
* **SSL (HTTPS) must be Enabled**
* **PHP 7.0 or Higher**

### Bug reports
Bug reports for Hydro MFA are [welcomed on GitHub](https://github.com/adrenth/wp-hydro-raindrop/issues). Please note GitHub is not a support forum, and issues that aren't properly qualified as bugs will be closed. **Use the Support Tab** above for support issues.

### Further Reading
For more info on Hydro or MFA and how it's changing the world, check out the following:

* [Hydro's Official Site](https://www.hydrogenplatform.com/).
* [Hydro's Medium Blog](https://medium.com/hydrogen-api).
* [Hydro MFA Client Side Raindrop API ](https://www.hydrogenplatform.com/docs/hydro/v1/).
* Become a part of the fastest growing Community! [Join Hydro Community](https://github.com/HydroCommunity).
* Are you a developer interested in expanding the Hydro ecosystem and earning bounties? [Visit Hydro HCDP Github Page](https://github.com/hydrogen-dev/hcdp/issues).
* Follow Hydro on [Telegram](https://t.me/projecthydro), [Facebook](https://www.facebook.com/hydrogenplatform), [Twitter](https://twitter.com/hydrogenapi) or [Instagram](https://www.instagram.com/hydrogenplatform/)

== Frequently Asked Questions ==
You'll find answers to many of your questions on [Official Website Support](https://www.hydrogenplatform.com/support).

== Screenshots ==
1. Sample Authentication Screen With The Plugin — Beautiful And Clean!
2. Enabling Hydro Raindrop In The Wordpress Admin
3. Integrated Raindrop For Wordpress Admins!
4. Custom Authentication Page can be created by Admin for their users as well

== Changelog ==
= 1.3.0 =
* Allow developers to make their own custom MFA page (with shortcodes).
* Improve MFA flow and security.

= 1.2.1 =
* Update WP readme.txt

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

== Upgrade Notice ==
= 1.3.0 =
Added ability to intercept login automatically. Swanky frontend UI.