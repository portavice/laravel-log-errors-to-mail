# Laravel Mail Logger Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## Unreleased

### Added
- Laravel logging channel "laravel_mail_logger" 
  - Configuration of above logging channel via environment variables
    - LOG_MAIL_MAILER
    - LOG_MAIL_TO
    - LOG_MAIL_DEDUPLICATE
    - LOG_MAIL_LEVEL
