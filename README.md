# Hydro Raindrop WordPress plugin 

![Hydro Logo](https://i.imgur.com/slcCepB.png)

Welcome to the Hydro Raindrop WordPress plugin repository on GitHub.

The `WP Hydro Raindrop` plugin provides Hydro Raindrop Multi Factor Authentication to your WordPress site.

## Installation

Download from wordpress.org (soon available).

## Manual installation

- Download and install fresh copy of WordPress (optional)
- Make sure you have installed composer (https://getcomposer.org/)
- `cd wp-content/plugins` (from root of your WordPress installation)
- `git clone git@github.com:adrenth/wp-hydro-raindrop.git`
- `cd wp-hydro-raindrop`
- `composer install`

The last step will install the required dependencies for the plugin.

And finally login with an admin account to your WordPress site and activate the plugin.

## Usage instructions

1. Login to your WordPress site as administrator.
2. Go to `Plugins` and search for the `WP Hydro Raindrop` plugin. Click `Activate` to activate the plugin.

If you don't have a **Hydrogen Developer Account**, go to [www.hydrogenplatform.com](https://www.hydrogenplatform.com) to register an account.

1. Under `Settings` navigate to the `Hydro Raindrop MFA` section and input your Application information to enable Hydro Raindrop MFA to your WordPress site. 
2. Under `Edit My Profile`, enter your HydroID in the `Hydro Raindrop MFA` section.
3. Follow the verification procedure to activate MFA for your account.

Site Editors / Authors / etc. can enable the Hydro Raindrop MFA from their profile page.

## Requirements

* **SSL MUST be enabled for MFA to work.**
* PHP 7.0 or higher is required.

## Customization

During the installation of the plugin the following pages will be made:
 
* Hydro Raindrop MFA Page (`/hydro-raindrop/`)
* Hydro Raindrop MFA Settings Page (`/hydro-raindrop/settings`)
* Hydro Raindrop MFA Setup Page (`/hydro-raindrop/setup`)

Each page contains it's corresponding shortcode which will be responsible for the Hydro Raindrop MFA implementation to work.
These pages are meant for customization and integration in your own custom theme. 

By default the Hydro Raindrop integrated pages are enabled. 

### Custom Hydro Raindrop MFA Page

* Login as an administrator
* Goto 'Hydro Raindrop' > 'Settings' > Tab 'Customization'.
* At 'MFA Page' select the 'Hydro Raindrop MFA Page' or select 'Use default MFA Page' to stick with the defaults.
* Make sure the shortcode `[hydro_raindrop_mfa]` is present on this page.

**Available Shortcodes**

Use these shortcodes in your custom templates/pages:

- `[hydro_raindrop_mfa_flash]`: Renders flash messages.
- `[hydro_raindrop_mfa_form_open]`: Renders opening `<form>` tag.
- `[hydro_raindrop_mfa_digits]`: Renders the MFA digits.
- `[hydro_raindrop_mfa_button_authorize class="my-css-class" label="Authorize"]`: Renders the Authorize (submit) button.
- `[hydro_raindrop_mfa_button_cancel class="my-css-class" label="Cancel"]`: Renders the Cancel button.
- `[hydro_raindrop_mfa_form_close]<`: Renders the closing `</form>` tag along with the nonce field.

**Example template**

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
* Goto 'Hydro Raindrop' > 'Settings' > Tab 'Customization'.
* At 'MFA Setup Page' select the 'Hydro Raindrop Setup Page' or select 'Use default MFA Setup Page' to stick with the defaults.
* Make sure the shortcode `[hydro_raindrop_setup]` is present on this page.

**Available Shortcodes**

Use these shortcodes in your custom templates/pages:

- `[hydro_raindrop_setup_flash]`: Renders flash messages.
- `[hydro_raindrop_setup_form_open]`: Renders opening `<form>` tag.
- `[hydro_raindrop_setup_hydro_id]`: Renders the HydroID input form field.
- `[hydro_raindrop_setup_button_submit class="my-css-class" label="Submit"]`: Renders the Submit button.
- `[hydro_raindrop_setup_button_skip class="my-css-class" label="Skip"]`: Renders the Skip button (if applicable).
- `[hydro_raindrop_setup_form_close]`: Renders the closing `</form>` tag along with the nonce field.

### Custom MFA Settings Page

* Login as an administrator
* Goto 'Hydro Raindrop' > 'Settings' > Tab 'Customization'.
* At 'MFA Settings Page' select the 'Hydro Raindrop Settings Page' or select 'Use default MFA Settings Page' to stick with the defaults.
* Make sure the shortcode `[hydro_raindrop_setup]` is present on this page.

**Available Shortcodes**

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
The given user is authenticated. **The WordPress auth cookie may not be set.**

#### `hydro_raindrop_pre_mfa( WP_User $user )`

Executed when user needs to perform MFA.
The given user is authenticated. **The WordPress auth cookie may not be set.**

## Documentation

https://www.hydrogenplatform.com/developers

## Issues

https://github.com/adrenth/wp-hydro-raindrop/issues

## Support

https://github.com/adrenth/wp-hydro-raindrop/issues

## Contributing to Hydro Raindrop WordPress plugin

Please make sure to obey the WP code styling by using PHP Code Sniffer with WordPress rules loaded into your IDE.
If you want to address an issue/bug, please create an issue first.
