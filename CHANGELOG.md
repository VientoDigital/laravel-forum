# Changelog

All notable changes to `laravel-forum` will be documented in this file

## 3.0.0 - 2026-01-03

### Changed
- **BREAKING:** Dropped Laravel 10.x support (now requires Laravel 11.x or 12.x)
- **BREAKING:** Dropped Orchestra Testbench 8.x support (now requires 9.x or 10.x)
- **BREAKING:** Dropped PHPUnit 10.x support (now requires PHPUnit 11.x)

### Added
- GitHub Actions CI workflow for automated testing
- CI badge in README

## 2.1.1 - 2026-01-02

### Fixed
- Fixed hardcoded table names in controller validation rules (now uses config values)
- Fixed hardcoded route names in controller redirects (now uses config prefix)

### Added
- Controller tests for Discussion, Post, Tag, and Setting controllers
- Pivot model tests for DiscussionTag and DiscussionUser
- Additional relationship tests (lastPost, isRead, editor)
- Test coverage expanded to 132 tests

## 2.1.0 - 2026-01-02

### Added
- Laravel 12.x support
- Orchestra Testbench 10.x support

## 2.0.0 - 2026-01-02

### Changed
- Updated PHP requirement to ^8.2
- Updated Laravel support to 10.x and 11.x (dropped 8.x and 9.x)
- Updated Livewire to ^3.0 (dropped 2.x)
- Updated PHPUnit to ^10.5|^11.0
- Migrated Livewire components to v3 syntax (`emit()` → `dispatch()`, `$listeners` → `#[On]` attributes)

### Added
- Comprehensive test suite with 81 tests
- CLAUDE.md for Claude Code guidance

## 1.0.0 - 201X-XX-XX

- initial release
