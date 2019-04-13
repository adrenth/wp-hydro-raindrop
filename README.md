# WP Hydro Raindrop

[![GitHub issues](https://img.shields.io/github/issues/adrenth/wp-hydro-raindrop.svg)](https://github.com/adrenth/wp-hydro-raindrop/issues)
[![GitHub forks](https://img.shields.io/github/forks/adrenth/wp-hydro-raindrop.svg)](https://github.com/adrenth/wp-hydro-raindrop/network)
[![GitHub stars](https://img.shields.io/github/stars/adrenth/wp-hydro-raindrop.svg)](https://github.com/adrenth/wp-hydro-raindrop/stargazers)
[![GitHub license](https://img.shields.io/github/license/adrenth/wp-hydro-raindrop.svg)](https://github.com/adrenth/wp-hydro-raindrop)
![Version](https://img.shields.io/badge/version-2.0.2-brightgreen.svg)

Welcome to the Hydro Raindrop WordPress plugin repository on GitHub.

![Hydro Logo](https://i.imgur.com/slcCepB.png)

The `WP Hydro Raindrop` plugin provides Hydro Raindrop Multi Factor Authentication to your WordPress site.

<div align="center">
  <a href="https://www.youtube.com/watch?v=d88jbPdxI88"><img src="https://img.youtube.com/vi/d88jbPdxI88/0.jpg" alt="Hydro Raindrop 2FA vs Google Authenticator"></a>
</div>

## Features

* Requires minimum hassle to setup
* Intercepts the Login automatically
* Allows users to set-up MFA with their HydroID
* Instant Authentication

> Note: Free Hydro Mobile App is required to complete the MFA process. You can get iOS App [here](https://goo.gl/LpAuzq) or the Android App [here](https://goo.gl/eNrdn2).

## Installation

You can install the plugin using one of the methods outlined below:

### A) From within WordPress

1. Visit 'Plugins > Add New'
2. Search for 'Hydro MFA'
3. Activate `WP Hydro Raindrop` plugin from your Plugins page.
4. Follow the 'After activation process' outlined below.

### B) Manually with plugin archive (advanced)

1. Upload the `wp-hydro-raindrop` folder to the `/wp-content/plugins/` directory.
2. Activate the `WP Hydro Raindrop` plugin through the 'Plugins' menu in WordPress.
3. Follow the 'After activation process' outlined below.

Download the plugin from WordPress.org: [WP Hydro Raindrop](https://wordpress.org/plugins/wp-hydro-raindrop/), or install it directly from WordPress (Navigate to **Plugins** > **Add new** > Search for 'hydro')

### C) Manually from GitHub (advanced)

- Make sure you have installed composer (https://getcomposer.org/)
- `cd wp-content/plugins` (from root of your WordPress installation)
- `git clone git@github.com:adrenth/wp-hydro-raindrop.git`
- `cd wp-hydro-raindrop`
- `composer install` (This will install the required dependencies for the plugin.)

## After activation process

When the plugin is activated, three pages are automatically created:
 
* Hydro Raindrop MFA Page (`/hydro-raindrop/`)
* Hydro Raindrop MFA Settings Page (`/hydro-raindrop/settings`)
* Hydro Raindrop MFA Setup Page (`/hydro-raindrop/setup`)

Each page contains it's corresponding shortcode which will be responsible for the Hydro Raindrop MFA implementation to work.
These pages are meant for customization and integration in your own custom theme. 

By default the Hydro Raindrop **integrated** pages are enabled.

The **following steps are necessary** to enable Hydro Raindrop MFA on your site:

### Hydro Raindrop MFA Activation (admin side)

* Create an account over at [Hydrogen Website](https://www.hydrogenplatform.com/).
* Apply for **Production Access**. Once approved, generate your `Client ID`, `Client Secret` and `Application ID`.
* In WordPress from the Main Menu navigate to **Hydro Raindrop** -> **Settings** -> **API Settings** and enter the above details, select Production Mode and the plugin is ready for use.
* Set-up the preferred MFA method (and other customization options) in the tab **Customization**
 
### Hydro Raindrop MFA Activation (user side)

There are three MFA setup methods:

* **Optional**: User decides to enable MFA on their account.  
* **Prompted** (default): MFA setup screen will be prompted after logging in. User can skip this step and setup MFA later.
* **Enforced**: MFA is forced site wide. Users will have to setup MFA after logging in. 

That's it!

## Requirements

* **SSL MUST be enabled for MFA to work.**
* PHP 7.0 or higher is required.

## Customization

### Custom Hydro Raindrop MFA Page

* Login as an administrator
* Go to **Hydro Raindrop** -> **Settings** > **Customization**.
* At **MFA Page** select the **Hydro Raindrop MFA Page** or select **Use default MFA Page** to stick with the defaults.
* Make sure the shortcode `[hydro_raindrop_mfa]` is present on this page.

#### Available Shortcodes

Use these shortcodes in your custom templates/pages:

- `[hydro_raindrop_mfa_flash]`: Renders flash messages.
- `[hydro_raindrop_mfa_form_open]`: Renders opening `<form>` tag.
- `[hydro_raindrop_mfa_digits]`: Renders the MFA digits.
- `[hydro_raindrop_mfa_button_authorize class="my-css-class" label="Authorize"]`: Renders the Authorize (submit) button.
- `[hydro_raindrop_mfa_button_cancel class="my-css-class" label="Cancel"]`: Renders the Cancel button.
- `[hydro_raindrop_mfa_form_close]<`: Renders the closing `</form>` tag along with the nonce field.

#### Example template

Create a custom page template (e.g. `/wp-content/themes/my-awesome-theme/hydro-raindrop-mfa.php`) for the Hydro Raindrop MFA. 
Below is an example on how to use the shortcodes.

```
<?php
/**
 * Template Name: Hydro Raindrop MFA
 */

get_header();
?>

<!-- HTML -->

<div class="row">
    <div class="col-sm">
        <div class="card w-75">
            <div class="card-body">
                <?php echo do_shortcode( '[hydro_raindrop_mfa_form_open]' ); ?>

                <div class="text-center">
                    <img src="https://www.hydrogenplatform.com/docs/hydro/v1/images/logo.png">
                </div>

                <h2 class="card-text text-center">
                    <?php echo do_shortcode( '[hydro_raindrop_mfa_digits]' ); ?>
                </h2>

                <div class="row">
                    <div class="col-md-4">
                        <?php echo do_shortcode( '[hydro_raindrop_mfa_button_cancel class="btn btn-default"]' ); ?>
                    </div>
                    <div class="col-md-8 text-right">
                        <?php echo do_shortcode( '[hydro_raindrop_mfa_button_authorize class="btn btn-primary"]' ); ?>
                    </div>
                </div>
                
                <?php echo do_shortcode( '[hydro_raindrop_mfa_form_close]' ); ?>
            </div>
        </div>
    </div>
</div>

<!-- HTML -->

<?php
get_footer();

```

### Custom MFA Setup Page

* Login as an administrator
* Go to **Hydro Raindrop** -> **Settings** > **Customization**.
* At **MFA Setup Page** select the **Hydro Raindrop Setup Page** or select **Use default MFA Setup Page** to stick with the defaults.
* Make sure the shortcode `[hydro_raindrop_setup]` is present on this page.

#### Available Shortcodes

Use these shortcodes in your custom templates/pages:

- `[hydro_raindrop_setup_flash]`: Renders flash messages.
- `[hydro_raindrop_setup_form_open]`: Renders opening `<form>` tag.
- `[hydro_raindrop_setup_hydro_id]`: Renders the HydroID input form field.
- `[hydro_raindrop_setup_button_submit class="my-css-class" label="Submit"]`: Renders the Submit button.
- `[hydro_raindrop_setup_button_skip class="my-css-class" label="Skip"]`: Renders the Skip button (if applicable).
- `[hydro_raindrop_setup_form_close]`: Renders the closing `</form>` tag along with the nonce field.

### Custom MFA Settings Page

* Login as an administrator
* Go to **Hydro Raindrop** -> **Settings** > **Customization**.
* At **MFA Settings Page** select the **Hydro Raindrop Settings Page** or select **Use default MFA Settings Page** to stick with the defaults.
* Make sure the shortcode `[hydro_raindrop_setup]` is present on this page.

#### Available Shortcodes

Use these shortcodes in your custom templates/pages:

- `[hydro_raindrop_settings_flash]`: Renders flash messages.
- `[hydro_raindrop_settings_form_open]`: Renders opening `<form>` tag.
- `[hydro_raindrop_settings_checkbox_mfa_enabled]`: Renders the checkbox form field.
- `[hydro_raindrop_settings_button_submit class="my-css-class" label="Submit"]`: Renders the Submit button.
- `[hydro_raindrop_settings_form_close]`: Renders the closing `</form>` tag along with the nonce field.

### Actions

#### `hydro_raindrop_user_blocked( WP_User $user )`

When a user is blocked because of too many failed verification attempts.
Executed after a user is blocked and before the user is being logged out.

#### `hydro_raindrop_mfa_failed( WP_User $user, int $failed_attempts )`

Executed after a Multi Factor Authentication attempt failed.

#### `hydro_raindrop_mfa_success( WP_User $user )`

Executed after a successful Multi Factor Authentication.

#### `hydro_raindrop_setup_failed( WP_User $user )`

Executed when Hydro Raindrop Setup failed.

#### `hydro_raindrop_setup_success( WP_User $user, string $hydro_id )`

Executed when user has successfully completed the Hydro Raindrop Setup.
The HydroID is confirmed and verified at this point.

#### `hydro_raindrop_pre_setup( WP_User $user )`

Executed when user needs to set up their HydroID.
The given user is authenticated. **The WordPress auth cookie might not be set.**

#### `hydro_raindrop_pre_mfa( WP_User $user )`

Executed when user needs to perform MFA.
The given user is authenticated. **The WordPress auth cookie might not be set.**

## Documentation

* [API Documentation](https://www.hydrogenplatform.com/developers)
* [Plugin documentation](https://github.com/adrenth/wp-hydro-raindrop/blob/master/README.md)
* [Hydro Raindrop PHP SDK](https://github.com/adrenth/raindrop-sdk)

## Issues

* https://github.com/adrenth/wp-hydro-raindrop/issues
* https://wordpress.org/support/plugin/wp-hydro-raindrop

## Support

* https://github.com/adrenth/wp-hydro-raindrop/issues

## Contributing

Please make sure to obey the WP code styling by using PHP Code Sniffer with WordPress rules loaded into your IDE.
If you want to address an issue/bug, please create an issue first.

## Further reading
For more info on Hydro or MFA and how it’s changing the world, check out the following:

* [Hydro's Official Site](https://www.hydrogenplatform.com/).
* [ProjectHydro](https://projecthydro.org/).
* [Hydro's Medium Blog](https://medium.com/hydrogen-api).
* [Hydro MFA Client Side Raindrop API](https://www.hydrogenplatform.com/docs/hydro/v1/).
* Become a part of the fastest growing Community! [Join Hydro Community](https://github.com/HydroCommunity).
* Are you a developer interested in expanding the Hydro ecosystem and earning bounties? [Visit Hydro HCDP Github Page](https://github.com/hydrogen-dev/hcdp/issues).
* Follow Hydro on [Telegram](https://t.me/projecthydro), [Facebook](https://www.facebook.com/hydrogenplatform), [Twitter](https://twitter.com/hydrogenapi) or [Instagram](https://www.instagram.com/hydrogenplatform/).
