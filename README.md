# Filament Blueprint Addon
Auto generate Filament forms and tables for models with the `php artisan blueprint:build` command.

#### This plugin is based on [Blueprint Nova Addon](https://github.com/Naoray/blueprint-nova-addon) by [Krishan König](https://github.com/naoray).


## Installation
* Install Laravel, Livewire, Filament and Blueprint
* Then install this package via composer:

```bash
composer require --dev NigelR/filament-blueprint-addon
```


## Usage
Refer to [Blueprint's Basic Usage](https://github.com/laravel-shift/blueprint#basic-usage)
to get started. 

The addition of the filament section allows you to configure which filament resources to build and configure some of the basic options. The example below shows general options applied to all models and then the specific models used to generate the Filament forms etc.

Afterwards you can run the `blueprint:build` command to
generate Tall-forms automatically. Try this example `draft.yaml` file.

```yaml
# draft.yaml
models:
    Post:
        author_id: id foreign:users
        title: string:400
        content: longtext
        published_at: nullable timestamp
        relationships:
            HasMany: Comment

    Comment:
        post_id: id foreign
        content: longtext
        published_at: nullable timestamp

filament:
    options: panel:admin
    Post: view:false
    Comment: view:true
```

## Contribution
This is open source, I'll gladly accept every effort to contribute.

## Credits

- [Krishan König](https://github.com/naoray) for [Blueprint Nova Addon](https://github.com/Naoray/blueprint-nova-addon)
- [Jason McCreary](https://github.com/jasonmccreary) for [Blueprint](https://github.com/laravel-shift/blueprint)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
