# Mega Menu

A responsive mega menu navigation component for Chance Theater.

## Development Setup

This project uses Live Sass Compiler to automatically compile SCSS to CSS.

### Prerequisites

- VS Code
- Live Sass Compiler extension

### Getting Started

1. Open the workspace in VS Code
2. Open `index.scss`
3. Click "Watch Sass" in the VS Code status bar
4. Make changes to `index.scss` - it will automatically compile to `index.css`

### Project Structure

```
mega-menu/
  ├── index.html     # Navigation HTML markup
  ├── index.scss     # SCSS styles (source)
  ├── index.css      # Compiled CSS (auto-generated)
  ├── index.js       # JavaScript functionality
  └── README.md      # This file
```

### Compilation Settings

Configured in `.vscode/settings.json`:

- Compiles to the same directory as source file
- Outputs expanded (readable) CSS format
- Autoprefixer enabled for browser compatibility
- Source maps disabled

### Notes

- The HTML navigation markup has been cleaned up with classes and IDs removed
- Edit only `index.scss` - `index.css` is auto-generated and will be overwritten

