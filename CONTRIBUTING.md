## Contributing

First off, thank you for considering contributing to this library. It's people
like you that make it a great tool for anyone who needs to validate VAT ID's
within the European Union.

### 1. Where do I go from here?

If you've noticed a bug or have a question,
[search the issue tracker](https://github.com/webatvantage/vies-vat-api-client/issues)
to see if someone else has already reported it. If not, go ahead and
[open one](https://github.com/webatvantage/vies-vat-api-client/issues/new)!

### 2. Fork the project and create a branch

If this is something you think you can fix, then
[fork the repository](https://help.github.com/articles/fork-a-repo) and create a
branch with a descriptive name off of `main`.

A good branch name (where issue #123 is the ticket you're working on) would be:

```sh
git checkout -b 123-update-be-vat-numbers
```

### 3. Get the test suite running

This library targets **PHP 7.4 and newer** and validates VAT numbers against the
European Commission VIES **REST** API.

Install the dependencies with [Composer](https://getcomposer.org):

```sh
composer install
```

Run the test suite:

```sh
composer test
```

> **Note for local development with [Herd](https://herd.laravel.com/):** the
> default `php` may be an older version. The test suite runs on PHP 7.4+, and
> some development tooling needs PHP 8.x. If `composer test` fails on your
> default PHP, run PHPUnit with an explicit binary, e.g. `php84 vendor/bin/phpunit`.

The validator tests run entirely offline (they only exercise the format/checksum
algorithms). The `Vies` tests mock the HTTP client, so **no test hits the live
VIES service** — the suite is deterministic and safe to run in CI.

### 4. Implement your fix or feature

See `CLAUDE.md` for an architecture overview and a step-by-step guide to adding
or changing a country validator.

Before committing, format your code:

```sh
composer format
```

The project uses `php-cs-fixer` (tabs, Allman braces). Let the formatter decide
the style instead of hand-formatting. Note that the configuration intentionally
does **not** add trailing commas to parameter lists, so the code keeps working on
PHP 7.4.

### 5. Make a Pull Request

Push your branch and open a Pull Request against `main`. GitHub Actions will run
the test suite across the supported PHP versions. We care about quality, so your
PR won't be merged until all checks pass.

If a maintainer asks you to "rebase" your PR, update your branch so it's easier
to merge:

```sh
git fetch origin
git rebase origin/main
git push --force-with-lease
```
