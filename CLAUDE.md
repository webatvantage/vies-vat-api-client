# CLAUDE.md

Guidance for working in this repository.

## What this is

A PHP library (`webatvantage/vies-vat-api-client`, a fork of `dragonbe/vies`) that
validates EU VAT registration numbers. It does two independent things:

1. **Offline format/checksum validation** — per-country algorithms, no network.
2. **Live validation** — a REST call to the European Commission VIES service.

PSR-4: `Webatvantage\Vies\` → `src/Vies`, `Webatvantage\Vies\Tests\` → `tests/Vies`.

## Supported PHP versions

**The library supports PHP 7.4 and newer** (verified against 7.4, 8.4 and 8.5).
This is a hard constraint — do **not** introduce syntax/functions that don't exist
on 7.4. Things to avoid (each has bitten this repo before):

- Trailing commas in **parameter lists** (PHP 8.0). The `php-cs-fixer` config is
  deliberately patched in `.php-cs-fixer.php` to keep `parameters` out of
  `trailing_comma_in_multiline` — don't undo that, or `composer format` will
  re-break 7.4.
- Non-capturing `catch (SomeException)` (PHP 8.0) — always bind a variable.
- `implements \Stringable` (PHP 8.0 interface) — a `__toString()` is enough; 8.0+
  auto-implements it. (It only "worked" on 7.4 here via a transitive polyfill.)
- `mb_trim()` (PHP 8.4). VAT data is ASCII, so plain `trim()`/`strtoupper()` are used.
- String increment on letters, e.g. `$c = 'A'; $c++;` (deprecated in 8.3+). Map via
  a lookup table instead (see `ValidatorIE`).

## Commands

The code runs on the repo's default PHP (Herd 7.4), so `composer test` works as-is.
CI also exercises 8.4 and 8.5. To check a specific version locally, prefix with the
explicit binary (e.g. `php84`, `php85`).

- Run tests: `composer test` (or `vendor/bin/phpunit --no-coverage`)
- One PHP version: `php85 vendor/bin/phpunit --no-coverage`
- Run one test: `vendor/bin/phpunit --no-coverage --filter testValidator tests/Vies/Validator/ValidatorBETest.php`
- Format code: `composer format` (php-cs-fixer; the `php-cs-fixer.yml` CI job auto-commits "Fix styling")

The whole suite is **offline/deterministic** — validator tests only run the
checksum algorithms, and `ViesTest` mocks the Guzzle client. Nothing hits the live
VIES service.

CI: `.github/workflows/run-tests.yml` runs `composer test` on PHP 7.4/8.4/8.5;
`.github/workflows/php-cs-fixer.yml` formats and auto-commits.

## Architecture

`Vies` (`src/Vies/Vies.php`) is the entry point; construct it with a `VatEuropeApi`:

- `validateVat($cc, $vat)` — rejects excluded countries first (GB →
  `CountryNoLongerSupportedException`), then checks format offline, then POSTs to
  the EC REST API and returns a `VatResponse`.
- `validateVatFormat()` / `validateVatSum()` — offline only (no network).
- `normalizeVat()` (static) — uppercase + strip spaces/dots/dashes.
- `splitVatId()` (static) — `"BE0203..."` → `['country' => 'BE', 'id' => '0203...']`.

`Countries` (`src/Vies/Countries.php`) is the central registry: a static map of
country code → `Country`, each holding a name, a `Validator<CC>` class, and format
pattern strings. `excludedCountries()` lists codes VIES no longer serves (GB,
Brexit 2021-01-01 — note `XI`/Northern Ireland is still validatable).

`VatEuropeApi` (`src/Vies/Api/VatEuropeApi.php`) wraps a Guzzle client (injectable
for testing) and POSTs to
`https://ec.europa.eu/taxation_customs/vies/rest-api/check-vat-number`. It maps the
JSON response onto `VatResponse`; Guzzle transport/HTTP errors implement PSR-18
`ClientExceptionInterface` and are wrapped in `ViesServiceException`. The official
contract is the EC OpenAPI spec (`swagger_publicVAT.yaml`).

`VatResponse` (`src/Vies/Api/VatResponse.php`) exposes `countryCode`, `vatNumber`,
`requestedDate`, `valid`, nullable `name`/`address`, and nullable
`requestIdentifier` (the VIES proof-of-consultation id). `nullify()` maps the VIES
`'---'` sentinel and empty strings to `null`.

Format pattern chars (used by `Country::toReadableFormat` for examples):
`9`=digit, `L`=letter, `X`=digit-or-letter, `*`=wildcard.

## Adding / changing a country validator

1. Add `src/Vies/Validators/Validator<CC>.php` extending `VatValidator` and
   implementing `validate(string $vatNumber): bool`. Reuse the helpers in
   `VatValidator` (`checkValue`, `sumWeights`, `crossSum`, modulo-11 logic). Keep
   it PHP 7.4-compatible (see "Supported PHP versions").
2. Register it in `Countries::countries()` with name + format example(s).
3. Add `tests/Vies/Validator/Validator<CC>Test.php` extending
   `AbstractValidatorTest`, using a `vatNumberProvider` data provider. These tests
   call `validateVatSum` (offline), so they do **not** hit the network.

## Code style

PHP files use **tabs** (size 4) and **Allman braces** (opening brace on its own
line), no final newline — see `.editorconfig` and `.php-cs-fixer.php`. Run
`composer format` before committing; let it decide, don't hand-format (with the one
caveat about parameter-list trailing commas noted above).
