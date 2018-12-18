# tplink-api-php

PHP-based toolset to programmatically toggle TP-Link Smart Plugs on/off.

## Supported Devices

| Model | Type |
|-------|------|
| HS100 | Plug |

## Getting Started

1. Run `composer install` to install dependencies.
2. Copy or rename `config.sensitive_passwords.BLANK.php` to `config.sensitive_passwords.php` and fill in values. `TPLINK_TERMID` is just a random UUID4 string used to identify this little app, [generate one here](https://www.uuidgenerator.net/version4). `TPLINK_CLOUD_USERNAME` and `TPLINK_CLOUD_PASSWORD` are what you use to sign into the Kasa app.

## Examples

### Get list of devices

```shell
$ php -f ./get_devices.php
```

### Turn on a device

```shell
$ php -f ./command_device.php -- --device=__32_HEX_DEVICE_ID__ --action=turn_on
```

### Turn off a device

```shell
$ php -f ./command_device.php -- --device=__32_HEX_DEVICE_ID__ --action=turn_off
```

## Credits

Thanks to [Alex D. from IT Nerd Space](http://itnerd.space/2017/01/22/how-to-control-your-tp-link-hs100-smartplug-from-internet/), [plasticrake/tplink-smarthome-api](https://github.com/plasticrake/tplink-smarthome-api), and the [Guzzle team](http://docs.guzzlephp.org/en/stable/)