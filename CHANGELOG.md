# Log Errors to E-Mail for Laravel Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## Unreleased

## Version 1.0.0 (2024-05-29)

### Added
- Laravel logging channel "laravel_mail_logger" 
  - Configuration of above logging channel via environment variables
    - LOG_ERROR_TO_MAIL_MAILER
    - LOG_ERROR_TO_MAIL_TO
    - LOG_ERROR_TO_MAIL_DEDUPLICATE
    - LOG_ERROR_TO_MAIL_LEVEL
