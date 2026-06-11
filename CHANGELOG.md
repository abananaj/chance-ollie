# Changelog — Chance Ollie Theme

All notable changes to the Chance Ollie WordPress theme are documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added

### Changed

### Fixed

### Deprecated

### Removed

### Security

---

## [1.1.1] — 2026-06-10

### Added

- Claude Code setup and automation documentation (CLAUDE.md, AGENTS.md)

---

## Changelog Setup & Guidelines

For detailed information about how the automated changelog system works, see:
**[CHANGELOG-SETUP.md](../../.claude/CHANGELOG-SETUP.md)**

### Quick Reference

- **Commit message categories**: Added, Changed, Fixed, Deprecated, Removed, Security
- **Write clear messages**: `Add new calendar block for events` (not just "Update")
- **Skip trivial commits**: Merge commits, typo fixes, and WIP commits are excluded
- **Before release**: Move [Unreleased] entries to a new version section with date

### Examples

```bash
# New feature → Added section
git commit -m "Add thumbnail-list block with lazy loading"

# Bug fix → Fixed section
git commit -m "Fix meta-date parsing for YYYY-MM-DD format"

# Breaking change → Changed section
git commit -m "Change block registration to use new block API"
```

---

## Version History

Starting with the first stable release. Previous versions available via git tags (`git tag -l`).
