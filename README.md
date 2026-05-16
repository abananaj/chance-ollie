# Chance Theater - Chance Ollie Theme

A modern WordPress theme for Chance Theater's website, built with Gutenberg blocks and block patterns.

## Templates

index.html
archive.php

## Theme Features

### Custom Gutenberg Blocks

This theme includes 8+ custom blocks registered in the block editor:

#### Staff Management Blocks

**1. StaffList Block**

- **Directory**: `blocks/StaffList/`
- **Features**:
    - Display staff members (ct-artist with resident-staff meta)
    - Shows thumbnail, name, position (title metafield), and bio
    - Configurable quantity and title
    - Responsive grid layout
- **Attributes**: title, qty

**2. ResidentArtists Block**

- **Directory**: `blocks/ResidentArtists/`
- **Features**:
    - Two automatic sublists: Resident Artists and Resident Playwrights
    - Resident Artists: have resident-artist meta only
    - Resident Playwrights: have both resident-artist AND resident-playwright meta
    - Displays profession metafield and bio excerpt
- **Attributes**: title, playwrightTitle, qty

#### Production & Content Blocks

**3. ProductionDetails Block**

- **Directory**: `blocks/ProductionDetails/`
- **Features**:
    - Editable production venue and venue room information
    - Post type specific (ct-production only)
    - Stores/retrieves from post meta
- **Attributes**: venue, venueRoom

**4. DonorList Block**

- **Directory**: `blocks/DonorList/`
- **Features**:
    - Displays donors organized by donor level taxonomy
    - Optional filtering by supporter type
    - Shows donor level name, description, and linked names
- **Attributes**: title, supporterType

#### Content Display Blocks

**5. Event Calendar Block**

- **Directory**: `blocks/EventCalendar/`
- **Display styles**: Calendar, Agenda, Agenda with Images
- **Features**:
    - Configurable display period (months/days)
    - Event category filtering
    - Timezone-aware date calculations
    - Server-side rendered (dynamic block)
- **Attributes**: title, qty, style, catInclude, catExclude

**6. Productions Block**

- **Directory**: `blocks/Productions/`
- **Features**:
    - Display upcoming productions with details
    - Production images, credits, dates, next performance
    - Optional action buttons (subscribe, buy tickets, learn more)
    - Season filtering
    - Responsive grid layout
- **Attributes**: title, qty, season, btnSubscribe, btnTickets, btnMore

**7. Social Icons Block**

- **Directory**: `blocks/SocialIcons/`
- **Features**:
    - Display social media icon links
    - Supports: Facebook, Twitter, YouTube, Instagram, RSS
    - Pulls from site options
    - Brand-color hover effects
    - Optional RSS feed link
- **Attributes**: title, rssEnabled

**8. Supporter Block**

- **Directory**: `blocks/Supporter/`
- **Features**:
    - Display random supporters
    - Institutional (logos with links) and Individual (names)
    - Responsive grid layout
    - New randomized selection on each page load
- **Attributes**: title, qty

### Block Patterns

Ready-to-use block patterns available in the editor's pattern inserter:

- **Board Members** (`patterns/board-members.php`) - 3-column grid with photos and positions
- **Card Grid** (`patterns/card-grid.php`) - Responsive 3-column cards with images
- **Resident Artists** (`patterns/resident-artists.php`) - Artist grid with photos and bios
- **Season Display** (`patterns/season-display.php`) - Production cards with details and buttons
- **Staff Directory** (`patterns/staff-directory.php`) - Staff member grid
- **Subscribe CTA** (`patterns/subscribe-cta.php`) - Full-width subscription banner

All patterns are automatically loaded and available under **Add Block → Patterns** in the editor.

**Note**: Donor List and other content lists have been converted to reusable blocks for better flexibility and filtering.

## Directory Structure

```
chance-ollie/
├── blocks/                          # Custom Gutenberg blocks
│   ├── StaffList/                  # Staff display block
│   ├── ResidentArtists/            # Resident artists & playwrights block
│   ├── ProductionDetails/          # Production venue details block
│   ├── DonorList/                  # Donor list organization block
│   ├── EventCalendar/              # Event calendar block
│   ├── Productions/                # Productions block
│   ├── SocialIcons/                # Social icons block
│   ├── Supporter/                  # Supporter block
│   ├── ArtistCreditsList/          # Artist credits list block
│   ├── _variations/                # Block variations
│   ├── editor.php                  # Block editor configuration
│   ├── functions.php               # Block registration
│   └── README.md                   # Block documentation
├── patterns/                        # Block patterns
│   ├── board-members.php
│   ├── card-grid.php
│   ├── resident-artists.php
│   ├── season-display.php
│   ├── staff-directory.php
│   ├── subscribe-cta.php
│   └── index.php
├── resources/                       # Theme resources
│   ├── php/                         # PHP functionality
│   │   ├── models/                 # Data models and query helpers
│   │   ├── post-type/              # Custom post type definitions
│   │   ├── taxonomy/               # Custom taxonomy definitions
│   │   ├── utils/                  # Utility functions
│   │   ├── post-meta.php           # Post meta registration
│   │   ├── helpers.php             # Helper functions
│   │   ├── sidebar-title-panel.php # Editor sidebar panels
│   │   └── meta-boxes.php          # Meta box definitions
│   ├── js/                          # JavaScript resources
│   ├── scss/                        # SCSS stylesheets
│   └── media/                       # Media resources
├── src/                             # Source files
│   ├── php/                         # PHP source files (mirrors resources/php)
│   ├── index.js                    # Main JavaScript entry
│   └── index.scss                  # Main SCSS entry
├── assets/                          # Compiled/public assets
│   ├── main.js                     # Compiled JavaScript
│   ├── styles.css                  # Compiled CSS
│   ├── bootstrap-icons.css
│   ├── acf/
│   ├── drafts/
│   └── media/
├── fonts/                           # Custom fonts
├── parts/                           # Template parts
├── templates/                       # Page templates
├── functions.php                    # Theme setup and includes
├── style.css                        # Main stylesheet meta
├── theme.json                       # Block editor settings
└── README.md                        # This file
```

## Setup & Installation

### Block Registration

Custom blocks are automatically registered via `blocks/functions.php` which is included in `functions.php`.

### Pattern Loading

Block patterns are automatically loaded from the `patterns/` directory using a glob pattern loader in `functions.php`.

## Development

### Creating New Blocks

1. Create a new directory in `blocks/` (e.g., `blocks/MyBlock/`)
2. Add `block.json` with metadata
3. Create `index.ts` for editor UI
4. Create `render.php` for server-side rendering
5. Create `index.css` for styles
6. Register the block in `blocks/functions.php`

### Creating New Patterns

1. Create a `.php` file in `patterns/` directory
2. Register the pattern using `register_block_pattern()`
3. Pattern will automatically load on theme initialization

## Block Editor Configuration

Block editor settings are configured in `theme.json` and include:

- Color palette
- Typography settings
- Responsive breakpoints
- Custom block configurations

## WP-CLI Commands

Create a future-dated post:

```bash
$ wp post create --post_type=post --post_title='A future post' --post_status=future --post_date='2030-12-01 07:00:00'
```

## Dependencies

- WordPress 5.8+ (for block support)
- PHP 7.4+
- Custom post types and taxonomies (configured in theme/plugins)
- Theme options framework (of_get_option support)

## Support Classes & Models

The theme uses specialized classes and models for data queries and helpers located in `resources/php/models/`:

- `Artists.php` - Artist query and manipulation helpers
- `Calendar.php` - Calendar and date calculations
- `Events.php` - Event query helpers
- `Productions.php` - Production query helpers
- `Supporters.php` - Supporter and donor query helpers
- `Venues.php` - Venue query helpers
- `Inflect.php` - String inflection utilities
- `Cleanup.php` - Data cleanup utilities
- `Shortcodes.php` - Legacy shortcode support (being phased out)

## Changelog

### March 25, 2026

#### Major Refactoring

- 🔄 Moved all PHP files from `inc/` to `resources/php/` for better organization
- 🔄 Converted legacy shortcode functions to modern Gutenberg blocks
- ✨ **StaffList Block** - Display staff members with configurable filters
- ✨ **ResidentArtists Block** - Two-tier artist display (residents + playwrights)
- ✨ **ProductionDetails Block** - Editable production venue information
- ✨ **DonorList Block** - Organized donor display with level and type filtering
- 📝 Updated directory structure documentation
- 📝 Updated all internal file path references

#### Previous Changes (March 14, 2026)

#### Blocks Added

- ✨ Event Calendar block - Display events in calendar/agenda format
- ✨ Productions block - Display upcoming productions
- ✨ Social Icons block - Display social media links
- ✨ Supporter block - Display random supporters
- ✨ Artist Credits List block - Display artist credits by role

#### Patterns Added

- ✨ Board Members pattern
- ✨ Card Grid pattern
- ✨ Resident Artists pattern
- ✨ Season Display pattern
- ✨ Staff Directory pattern
- ✨ Subscribe CTA pattern

All patterns converted from legacy shortcodes to modern WordPress block patterns.

## Resources

- [WordPress Block Editor Documentation](https://developer.wordpress.org/block-editor/)
- [Block.json Reference](https://developer.wordpress.org/blocks/block.json/)
- [Block Patterns API](https://developer.wordpress.org/block-editor/reference-guides/block-api/block-patterns/)
