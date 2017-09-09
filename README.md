# Composer-version

This is a no-dependencies `PHP ^7.1` tool to help with releasing semantically
versioned composer packages or projects, directly inspired by [npm version](https://docs.npmjs.com/cli/version).


## Usage

```
Usage: composer-version [options] <new-version> | major | minor | patch
    Options:
        -h, --help Show this help text
        -f, --file Path to composer.json file, default to ./composer.json
        -p, --push Push commit and tag to remote origin
```


## Installation

```bash
composer global require korri/composer-version
```
Then add `~/.composer/vendor/bin/` to your `$PATH` if it is not already there.

## Testing

Run test suite
```bash
composer install # Install dev dependencies
composer test
```