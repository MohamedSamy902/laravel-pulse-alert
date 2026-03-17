# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.0] - 2025-01-01

### Added
- Initial release of Laravel Pulse Alert.
- Automated error logging with priority classification.
- Telegram instant notifications for CRITICAL and HIGH priorities.
- Anti-spam rate limiting for Telegram alerts (10-minute silence).
- Traffic surveillance middleware to detect IP-based request bursts.
- Daily HTML email reports sorted by priority levels.
- Compatibility with Laravel 11 and Laravel 12.
- Data sanitization for sensitive information in stack traces.
