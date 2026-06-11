# agents.md

This file documents Claude Code agents and workflows for developing the **Chance Ollie** WordPress child theme.

For project architecture and build commands, see [CLAUDE.md](CLAUDE.md).

## Available Agent Types

### Explore Agent
**When to use:** Searching across the block structure or finding where code is defined

- Locates blocks, patterns, or files by pattern (e.g., `blocks/**/*.render.php`)
- Searches for functions or ACF field usage across the codebase
- Answers "where is X defined" or "which blocks use Y"
- Good for broad code discovery across post types and patterns

**Example invocations:**
- "Find all blocks that query Productions" → Explore agent
- "Locate where the artist-profile ACF group is used" → Explore agent
- "Find all blocks that use inc/models helpers" → Explore agent
- "Which blocks render supporter data?" → Explore agent

### Plan Agent
**When to use:** Designing new blocks, refactoring patterns, or architectural decisions

- Creates step-by-step implementation plans
- Identifies critical files to modify (inc/models/, blocks/, patterns/)
- Considers trade-offs (server-rendered vs client-side, caching strategies)
- Proposes file organization for new block patterns

**Example invocations:**
- "Plan a new DonorTiers block for the annual fund page" → Plan agent
- "Design a system for highlighting featured productions" → Plan agent
- "How should we refactor the shared artist query logic?" → Plan agent
- "Plan adding a testimonial block for season feedback" → Plan agent

### Code Review Agents
**When to use:** Reviewing pull requests or checking code quality

- **Code Review** (`/code-review`) — Find bugs and suggest fixes
  - Use `--comment` flag to post findings as PR comments
  - Use `--fix` flag to auto-apply suggestions to working tree

- **Simplify** (`/simplify`) — Reduce code duplication and improve clarity
  - Finds reuse opportunities in changed code
  - Suggests efficiency improvements
  - Focuses on quality, not bugs

- **Security Review** (`/security-review`) — Check for security issues

**Example invocations:**
```bash
/code-review low                  # Quick bug hunt
/code-review medium               # Moderate coverage
/code-review high                 # Thorough review
/code-review ultra                # Deep multi-agent cloud review (billed)
/simplify                         # Clean up changed code
/security-review                  # Check for vulnerabilities
```

### General Purpose Agent
**When to use:** Multi-step tasks, research, or complex questions

- Handles tasks that don't fit other specialized agents
- Good for coordinating work across blocks and post types
- Can search, read, and analyze across multiple files

**Example invocations:**
- "Audit all blocks for accessibility compliance" → General agent
- "Find and fix deprecated WordPress API calls" → General agent
- "Migrate all blocks from old meta query pattern to new one" → General agent

## WordPress-Specific Skills

### wp-theme-dev Skill
Explains WordPress theme development concepts and references documentation.

**Refer to:**
- [WordPress Theme Handbook](https://developer.wordpress.org/themes/)
- [Block Editor Documentation](https://developer.wordpress.org/block-editor/)

**Use when:**
- Asking "How does this work?" about WordPress theme patterns
- Need guidance on WordPress hooks, actions, filters
- Learning about block registration or template hierarchy

**Example:**
```
/wp-theme-dev "How do block patterns work?"
```

### wp-standards Skill
Compares code against WordPress coding standards and best practices.

**Refer to:**
- [WordPress Coding Standards](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/)
- [PHP Standards](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/php/)

**Use when:**
- Code reviewing for WordPress compliance
- Refactoring to meet WordPress standards
- Asking about WordPress best practices

**Example:**
```
/wp-standards
# Reviews the diff on the current branch for WordPress standards compliance
```

### wp-theme-json Skill
Merges exported `wp_global_styles` JSON into `theme.json`.

**Refer to:**
- [theme.json Handbook](https://developer.wordpress.org/block-editor/reference-guides/theme-json/)
- [Theme.json Version 3 Reference](https://developer.wordpress.org/block-editor/reference-guides/theme-json-living/)

**Use when:**
- Merging style exports from the WordPress Site Editor
- Updating color palette, typography, or spacing in theme.json
- Syncing design system changes

**Example:**
```
/wp-theme-json
# Merges exported wp_global_styles.json into theme.json
```

## Common Workflows

### Adding a New Block

1. **Plan the architecture** — Use Plan agent to design the block
   ```bash
   /plan "Design a block for displaying X with these features..."
   ```

2. **Create the block structure** — See [CLAUDE.md](CLAUDE.md) for scaffolding steps
   - Create `blocks/BlockName/` with block.json, index.ts, render.php, index.css
   - Use inc/models helpers for data queries (e.g., `Productions::get_posts()`)

3. **Implement and review** — Code review as you go
   ```bash
   /code-review low
   ```

4. **Check WordPress standards** — Ensure compliance
   ```bash
   /wp-standards
   ```

5. **Test with production data** — Verify against live post types and ACF fields

### Creating a New Block Pattern

1. **Explore existing patterns** — Understand the pattern style
   ```bash
   /explore "Find all patterns in patterns/"
   ```

2. **Create the pattern file** — Add to `patterns/my-pattern.php`
   - Use `register_block_pattern()` with proper categories and keywords
   - Patterns auto-load on theme init

3. **Add to pattern registry** — Update `patterns.json` config if needed

4. **Test in block editor** — Verify appearance and block availability

### Refactoring Block Query Logic

1. **Explore impact** — Find all blocks using the pattern
   ```bash
   /explore "Find all blocks calling Artists::get_posts()"
   ```

2. **Plan the migration** — Design the refactor
   ```bash
   /plan "Migrate all artist queries to new filter pattern"
   ```

3. **Update models** — Modify `inc/models/Artists.php` or create new helper

4. **Review each block change** — Quality gate
   ```bash
   /code-review medium --fix
   ```

5. **Security check** — Ensure no vulnerabilities
   ```bash
   /security-review
   ```

### Code Review Before Deployment

```bash
# Quick quality check
/code-review low

# Thorough review with fixes applied
/code-review high --fix

# Security audit
/security-review

# Check WordPress standards
/wp-standards
```

### Syncing Styles Between JSON and SCSS

After updating `theme.json` settings or SCSS variables:

```bash
npm run sync:watch      # Monitor diff between JSON and SCSS
npm run sync:j2s        # Convert JSON to SCSS variables
npm run sync:s2j        # Convert SCSS to JSON
```

## Theme Features & Structure

For detailed information on custom blocks, patterns, and block editor configuration, see [README.md](README.md).

- **Custom Gutenberg Blocks** — StaffList, ResidentArtists, ProductionDetails, DonorList, EventCalendar, Productions, SocialIcons, Supporter, ArtistCreditsList
- **Block Patterns** — Board Members, Card Grid, Resident Artists, Season Display, Staff Directory, Subscribe CTA
- **Post Types** — Production, Artist, Event, Venue, Supporter, Class
- **Taxonomies** — Season, Series, Event Type, Program, Session, Supporter Level

## Chance Ollie Specific Patterns

### Query Helpers Pattern

Always use models in `inc/models/` rather than raw WP_Query:

```php
// ✓ Good
$artists = Artists::get_posts(['meta_key' => 'resident-artist']);

// ✗ Avoid
$args = new WP_Query(...); // Put this in the model instead
```

### Block Rendering Pattern

Server-side rendering in `blocks/BlockName/render.php`:

1. Accept block attributes from `$attributes`
2. Query data using model helpers
3. Output HTML with appropriate CSS classes for editor styling
4. No inline JavaScript—defer to index.ts for editor UI

### ACF Integration Pattern

- Field groups defined in `inc/metadata/acf/json/group_*.json`
- Block bindings configured in `inc/metadata/block-bindings.php`
- Query posts by meta using model helpers, not direct ACF functions

## Common Tasks

### Updating Theme Colors

1. Edit `src/json/settings/colors.jsonc` or use WordPress Site Editor
2. Export settings via Site Editor
3. Run `npm run build` or use `/wp-theme-json` skill
4. Verify color palette in block editor

### Adding a New Post Type

1. Create `inc/post-type/my-type.php` with `register_post_type()`
2. Add to `inc/post-type/index.php` require list
3. Create query helper in `inc/models/MyType.php` if needed
4. Add admin columns in `inc/post-type/admin-views/my-type-all.php`

### Adding ACF Field Group

1. Define fields in WordPress admin or `inc/metadata/acf/json/`
2. Export JSON from ACF
3. Reference fields in blocks using block bindings or direct queries
4. Document field keys in the group file

---

## Related Projects

### Parent WordPress Root

Main installation documentation in `../../../CLAUDE.md` and `../../../AGENTS.md`:
- Overall architecture and setup
- Cross-project agent coordination
- Deployment workflows and infrastructure

### Theatrum Blocks Plugin

This theme depends on the **Theatrum Blocks** plugin located at `../../plugins/theatrum-blocks/`.

**Related Documentation:**
- `../../plugins/theatrum-blocks/CLAUDE.md` — Plugin architecture and development guidance
- `../../plugins/theatrum-blocks/agents.md` — Automated workflows for the plugin

**When to cross-reference**: Check the plugin's documentation when:
- Debugging block registration or availability issues
- ACF field structures or block attributes change
- Custom post type handlers are modified
- REST API endpoints shift
- Block variations or block styles are updated

### Build & Deployment Infrastructure

Deployment agents and scripts are managed at the WordPress root:
- `../.build/` — Build configuration and scripts
- `../.deploy/` — Deployment configuration and scripts

Theme builds integrate with this infrastructure during `npm run build` and `npm run deploy`.

---

## Documentation Index

- **[CLAUDE.md](CLAUDE.md)** — Project architecture, build commands, development workflows
- **[README.md](README.md)** — Custom blocks, block patterns, post types, taxonomies, setup & installation
- **[agents.md](agents.md)** — This file; Claude agents and automated workflows
- **[../../plugins/theatrum-blocks/CLAUDE.md](../../plugins/theatrum-blocks/CLAUDE.md)** — Theatrum Blocks plugin architecture
- **[../../plugins/theatrum-blocks/agents.md](../../plugins/theatrum-blocks/agents.md)** — Theatrum Blocks plugin automations
