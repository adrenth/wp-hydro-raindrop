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

### Custom MFA page

Follow the instructions below to create your own Hydro Raindrop MFA page:

* Login as a Site Editor
* Create a page (programmatically)
* Use the following shortcodes on this page (required!):
    - `[hydro_raindrop_mfa_form_open]`: renders the form opening element.
    - `[hydro_raindrop_mfa_form_close]`: renders closing element with the wp_nonce field.
    - `[hydro_raindrop_mfa_digits]`: renders 6 digits which should be entered into the Hydro app.
    - `[hydro_raindrop_mfa_button_authorize]`: renders the "Authorize" button; customize the look of the button using CSS class `hydro-raindrop-mfa-button-authorize`.
    - `[hydro_raindrop_mfa_button_cancel]`: renders the "Cancel" button; customize the look of the button using CSS class `hydro-raindrop-mfa-button-cancel`.
* When MFA verification fails the User will be redirected to the MFA page with GET parameter `?hydro-raindrop-error=1`.
* Under `Setting` navigate to the `Hydro Raindro MFA` section and select the page you created.

#### Custom MFA page example

Create a custom page template (e.g. `/wp-content/themes/my-awesome-theme/hydro-raindrop-mfa.php`) for the Hydro Raindrop MFA. Below is an example on how to use the shortcodes.

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

### Custom HydroID page

Follow the instructions below to create your own Hydro Raindrop MFA page:

* Login as a Site Editor
* Create a page (programmatically)
* Use the `[hydro_raindrop_manage_hydro_id]` shortcode on this page (required!).
* Under `Setting` navigate to the `Hydro Raindro MFA` section and select the page you created.

## Documentation

https://www.hydrogenplatform.com/developers

## Issues

https://github.com/adrenth/wp-hydro-raindrop/issues

## Support

https://github.com/adrenth/wp-hydro-raindrop/issues

## Contributing to Hydro Raindrop WordPress plugin

Please make sure to obey the WP code styling by using PHP Code Sniffer with WordPress rules loaded into your IDE.
If you want to address an issue/bug, please create an issue first.
