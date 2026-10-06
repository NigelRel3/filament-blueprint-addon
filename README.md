# Filament Blueprint Addon

Building on the great work of Blueprint, this extension allows the generation of Filament forms and tables for models with the `php artisan blueprint:build` command.

The main issue this overcomes is that the Filament generate option will normally read the columns from the database after any migrations have been applied. When generating the application using Blueprint, these migrations are only run after the `blueprint:build` command has been run. To shortcut this, a dummy connection is added to the models which generates data from the Blueprint model definition.

## Installation

Install Laravel, Filament and Blueprint.

Install this package as a dev dependency using composer:

```bash
composer require --dev NigelR/filament-blueprint-addon
```

## Usage

Refer to the [Blueprint - Basic Usage](https://github.com/laravel-shift/blueprint#basic-usage)
for a better understanding of what Blueprint is and instructions as to how to use core Blueprint.

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
    options: panel:admin view:true cluster:Users
    # CSV list of clusters
    clusters: Users, Addresses
    # CSV list of models created with default options
    models: AppUser
    # Individual model options override the default options
    Address: view:false title:postcode cluster:Addresses
```

Run the `blueprint:build` command to generate the models/seeders/migrations etc. as well as the Filament resources automatically.

Once the build has completed - migrate the database, create a filament user (`php artisan make:filament-user`)

## Filament

This code links into the build process of Filament and is currently tested using Filament 5. It may work for other versions and compatibility would be useful to know.

The options which are supported in this version are:


| Name         | Default | Use                                                                             |                                   Documentation                                   |
| ------------ | :-----: | ------------------------------------------------------------------------------- | :--------------------------------------------------------------------------------: |
| panel        |  admin  | Which panel to add the model to                                                 |            [link](https://filamentphp.com/docs/5.x/panel-configuration)            |
| view         |  false  | If a view only form should be generated                                         | [link](https://filamentphp.com/docs/5.x/resources/overview#generating-a-view-page) |
| simple       |  false  | Use a modal for managing records                                                | [link](https://filamentphp.com/docs/5.x/resources/overview#simple-modal-resources) |
| soft-deletes |  false  | When defining softDeletes in the model, this is propergated through to filament | [link](https://filamentphp.com/docs/5.x/resources/overview#handling-soft-deletes) |
| title        |    *    | Used to identify the rows in the table                                          |     [link](https://filamentphp.com/docs/5.x/resources/overview#record-titles)     |
| cluster      |         | Indicate which menu to list this table under (blank for main menu)              | [link](https://filamentphp.com/docs/5.x/navigation/clusters)

*Notes:*

* *If not provided, Fillament tries to guess the column name from the table definition.*

Looking at the above example draft.yml, this shows how the options can be set with each one being listed as `name:value` pairs with a space as the separator between multiple options.

When defining clusters - you will need to add the appropriate line to the panel provider as descriobed [here](https://filamentphp.com/docs/5.x/navigation/clusters#creating-a-cluster). The example shows adding the call to `discoverClusters` in the following example.

```php
public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
        ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
        ->discoverClusters(in: app_path('Filament/Clusters'), for: 'App\\Filament\\Clusters');
}
```

For more details about Filament and how to use it, please refer to https://filamentphp.com/docs/5.x/introduction/overview

## Credits

- [Krishan König](https://github.com/naoray) for [Blueprint Nova Addon](https://github.com/Naoray/blueprint-nova-addon), the basis for this code.
- [Jason McCreary](https://github.com/jasonmccreary) for [Blueprint](https://github.com/laravel-shift/blueprint)

## License

MIT License.
