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

## Documentation

https://www.hydrogenplatform.com/developers

## Issues

https://github.com/adrenth/wp-hydro-raindrop/issues

## Support

https://github.com/adrenth/wp-hydro-raindrop/issues

## Contributing to Hydro Raindrop WordPress plugin

Please make sure to obey the WP code styling by using PHP Code Sniffer with WordPress rules loaded into your IDE.
If you want to address an issue/bug, please create an issue first.
