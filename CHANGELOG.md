# Change Log

All notable changes to this project will be documented in this file.
This project adheres to [Semantic Versioning](http://semver.org/).

## [Unreleased]

## [2.3.0] - 2021-11-03

- php-amqplib: use AMQPStreamConnection instead of AMQPConnection
- Remove PHP <7.4 support
- Remove Symfony <4.4 support

## [2.2.0] - 2020-12-16

- Add PHP 8 support

## [2.1.0] - 2020-10-25

- Update processor for doctrine/persistence:^2.0

## [2.0.1] - 2020-09-27

- Use hash for definition ID generation instead of uniqid
- Fix compatibility with null as routing key

## [2.0.0] - 2020-03-06

- Remove deprecated config (`publisher_logger` & `processors_stack`)
- Remove deprecated processor configurators

## [1.8.1] - 2020-03-06

- Allow to define command aliases in configuration
- Use a tag to register commands (lazy loading FTW)

## [1.8.0] - 2020-02-05

- Add ServicesResetterProcessorConfigurator
- Deprecate NewRelic, Sentry & RPC related processor configurators
- Remove deprecated code
- Remove outdated xsd config

## [1.7.2] - 2020-01-22

- Fix DataCollector to keep compatibility with sf3.x

## [1.7.1] - 2020-01-22

- Fix DataCollector

## [1.7.0] - 2019-11-26

- Support Symfony ^5.0
- Remove support of PHP <7.2
- Remove support of sf 4.1
- Fix bad interface_exists check on class

## [1.6.3] - 2019-09-08

### Fixed

- Remove deprecation notice when using symfony/event-dispatcher > 4.2

## [1.6.2] - 2019-07-28

### Added

- Supports publisher confirms

### Fixed

- Remove deprecated ContainerAwareCommand

## [1.6.1] - 2019-02-11

### Fixed

- Remove deprecated notice when using symfony/config > 4.2

## [1.6.0] - 2018-10-17

### Added

- Allow the connection details to be given as a URL
- Sentry processor configurator

## [1.5.1] - 2017-12-25

### Added

- Support for Symfony 4.
- It's now possible to retrieve all middleware services ids.

### Removed

- Support of PHP < 7.1

## [1.5.0] - 2017-10-31

### Fixed

- Deal with deprecated DefinitionDecorator
- README & Doc Improvements
- SL Insight fixes
- Fix extras consumers option configuration type

### Added

- Last swarrot/swarrot version support
- Explicitly exposes the public services
- SF 4 compatibility
- RetryProcessor - Support `retry_log_levels_map` and `retry_fail_log_levels_map` configuration in extras

## [1.4.2] - 2017-03-20

### Fixed

- Default RPC exchange is now empty by default.
- Imprive README lisibility.

### Added

* Change visibility of `Swarrot\SwarrotBundle\Broker\AmqpLibFactory::getChannel` to public.

## [1.4.1] - 2016-07-19

### Fixed

- Correct processor instanciation by removing unexisting interface.

## [1.4.0] - 2016-07-18

### Added

- Inject logger into `PeclPackageMessagePublisher`.
- Improve tests.
- Make the swarrot logger configurable.
- Can now register a `middleware_stack` per consumer.
- Add more tests to ensure BC on Configuration class.

### Fixed

- Removed deprecated `cannotBeEmpty` calls on numeric nodes.

## [1.3.2] - 2015-11-19

### Fixed

- Dev version in `composer.json`.
- Correct README.

### Added

- Support for sf3.

## [1.3.1] - 2015-10-16

## [1.3.0] - 2015-10-15

## [1.2.2] - 2015-09-01

## [1.2.1] - 2015-08-20

## [1.2.0] - 2015-05-19

## [1.1.1] - 2014-09-18

## [1.1.0] - 2014-09-18

## [1.0.2] - 2014-07-24

## [1.0.1] - 2014-07-18

[Unreleased]: https://github.com/swarrot/SwarrotBundle/compare/v2.2.0...HEAD
[2.2.0]: https://github.com/swarrot/SwarrotBundle/compare/v2.1.0...v2.2.0
[2.1.0]: https://github.com/swarrot/SwarrotBundle/compare/v2.0.1...v2.1.0
[2.0.1]: https://github.com/swarrot/SwarrotBundle/compare/v2.0.0...v2.0.1
[2.0.0]: https://github.com/swarrot/SwarrotBundle/compare/v1.8.1...v2.0.0
[1.8.1]: https://github.com/swarrot/SwarrotBundle/compare/v1.8.0...v1.8.1
[1.8.0]: https://github.com/swarrot/SwarrotBundle/compare/v1.7.2...v1.8.0
[1.7.2]: https://github.com/swarrot/SwarrotBundle/compare/v1.7.1...v1.7.2
[1.7.1]: https://github.com/swarrot/SwarrotBundle/compare/v1.7.0...v1.7.1
[1.7.0]: https://github.com/swarrot/SwarrotBundle/compare/v1.6.3...v1.7.0
[1.6.3]: https://github.com/swarrot/SwarrotBundle/compare/v1.6.2...v1.6.3
[1.6.2]: https://github.com/swarrot/SwarrotBundle/compare/v1.6.1...v1.6.2
[1.6.1]: https://github.com/swarrot/SwarrotBundle/compare/v1.6.0...v1.6.1
[1.6.0]: https://github.com/swarrot/SwarrotBundle/compare/v1.5.1...v1.6.0
[1.5.1]: https://github.com/swarrot/SwarrotBundle/compare/v1.5.0...v1.5.1
[1.5.0]: https://github.com/swarrot/SwarrotBundle/compare/v1.4.2...v1.5.0
[1.4.2]: https://github.com/swarrot/SwarrotBundle/compare/v1.4.1...v1.4.2
[1.4.1]: https://github.com/swarrot/SwarrotBundle/compare/v1.4.0...v1.4.1
[1.4.0]: https://github.com/swarrot/SwarrotBundle/compare/v1.3.2...v1.4.0
[1.3.2]: https://github.com/swarrot/SwarrotBundle/compare/v1.3.1...v1.3.2
[1.3.1]: https://github.com/swarrot/SwarrotBundle/compare/v1.3.0...v1.3.1
[1.3.0]: https://github.com/swarrot/SwarrotBundle/compare/v1.2.2...v1.3.0
[1.2.2]: https://github.com/swarrot/SwarrotBundle/compare/v1.2.1...v1.2.2
[1.2.1]: https://github.com/swarrot/SwarrotBundle/compare/v1.2.0...v1.2.1
[1.2.0]: https://github.com/swarrot/SwarrotBundle/compare/v1.1.1...v1.2.0
[1.1.1]: https://github.com/swarrot/SwarrotBundle/compare/v1.1.0...v1.1.1
[1.1.0]: https://github.com/swarrot/SwarrotBundle/compare/v1.0.2...v1.1.0
[1.0.2]: https://github.com/swarrot/SwarrotBundle/compare/v1.0.1...v1.0.2
[1.0.1]: https://github.com/swarrot/SwarrotBundle/compare/v1.0.0...v1.0.1
