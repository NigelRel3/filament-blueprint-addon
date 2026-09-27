# Filament Blueprint Addon
Building on the great work of Blurprint, this extension allows the generation of Filament forms and tables for models with the `php artisan blueprint:build` command.

## Installation
Install Laravel, Filament and Blueprint
Install this package as a dev dependancy using composer:

```bash
composer require --dev NigelR/filament-blueprint-addon
```

## Usage
Refer to the [Blueprint - Basic Usage](https://github.com/laravel-shift/blueprint#basic-usage)
for a explanation and instructions as to how to use the core Blueprint code. 

In addition to the existing sections, this adds an additional `filament` section which allows you to configure which filament resources to build and configure some of the options. The example below shows how general defaults can be applied to models and also how to override these for specific models.

An example `draft.yaml` file.

```yaml
models:
    AppUser:
        name: string
        email: string:255
        phone: string:15 nullable
        status: enum:active,inactive default:active
        date_of_birth: date
        datetime_of_registration: datetime
        preferred_address_id: id foreign:addresses.id nullable
        relationships:
            hasMany: Address

    Address:
        app_user_id: id foreign
        address_1: string
        address_2: string
        city: string
        postcode: string
        softDeletes: true
        relationships:
            belongsTo: AppUser

filament:
    # Options applied to all models by default
    options: panel:admin view:true
    # CSV list of models created with default options
    models: AppUser
    # Individual model options override the default options
    Address: view:false title:postcode
```

TODO document which options are available

Run the `blueprint:build` command to generate the models/seeders/migrations etc. as well as the Filament resources automatically.

Once the build has completed - migrate the database, create a filament user (`php artisan make:filament-user`)

## Filament

This code links into the build process of Filament and is currently tested using Filament 5. It may work for other versions and compatibility would be useful to know.

For more details about Filament and how to use it, please refer to https://filamentphp.com/docs/5.x/introduction/overview

## Credits

- [Krishan König](https://github.com/naoray) for [Blueprint Nova Addon](https://github.com/Naoray/blueprint-nova-addon), the basis for this code.
- [Jason McCreary](https://github.com/jasonmccreary) for [Blueprint](https://github.com/laravel-shift/blueprint)

## License

MIT License.
