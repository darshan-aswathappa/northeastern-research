# Northeastern Research Fellows — WordPress Microsite

A custom WordPress site for the **WordPress Research Fellows Program**, built with a bespoke theme and three purpose-built plugins. Developed locally with [Local by Flywheel](https://localwp.com/) and linted in CI via GitHub Actions.

---

## What's in the repo

### Theme — `nu-research`

A classic PHP theme built from scratch against the Northeastern Research design system.

- Responsive, accessible, token-driven styles (`assets/css/main.css`)
- Custom page templates: home, apply, highlights/team, contact, 404
- Requires WordPress 6.0+, PHP 8.0+

### Plugin — `swe-fellows-application` (ATS Form)

Multi-step application form for fellowship candidates.

- Shortcode: `[swe_fellows_application]`
- Submissions stored in a dedicated table (`wp_swe_applications`)
- Admin screen to review and manage applications
- REST endpoint for headless access
- Confirmation emails sent on submission

### Plugin — `swe-mail-list` (Fellows Mail List)

Waitlist/interest-list capture tied to the application window.

- Shortcode: `[swe_waitlist]`
- Subscribers stored in `wp_swe_waitlist`
- Open/closed intake state derived automatically from the Fellows Deadline dates
- Admin screen with bulk announcement email tool

### Plugin — `fellows-deadline` (Fellows Deadline Countdown)

Controls the application window and surfaces deadline information across the site.

- Settings page under **Settings → Fellows Deadline**
- Shortcode: `[fellows_countdown]` — live countdown timer
- Site-wide "closing soon" banner (auto-shows near deadline)
- Dashboard widget with a program snapshot

---

## Design system

The theme is built against the **Northeastern Graduate Programs design system**, documented in `app/design-reference/`. The reference folder contains four CSS token files that serve as the single source of truth for all visual decisions:

| File                    | What it defines                                                                                                                                                                   |
| ----------------------- | --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `tokens/colors.css`     | Brand palette — NU Black, White, Red (`#d41b2c`), and Gold, weighted 35/35/27/3% per [brand.northeastern.edu](https://brand.northeastern.edu/design-and-experiences/color/)       |
| `tokens/typography.css` | Lato (sans) + Libre Baskerville (serif), with a documented type scale from `display-1` (72 px) down to `caption`                                                                  |
| `tokens/spacing.css`    | 4 px base grid, named steps `--space-1` through `--space-9` (4 px → 96 px)                                                                                                        |
| `tokens/effects.css`    | Flat, editorial surface language — sharp corners (`--radius-none`), thin 1 px borders, no drop shadows on cards, pill-only for CTA buttons; focus ring defined as a 3 px red halo |

The `app/design-reference/img/` folder holds the canonical photo assets (hero, fellows, mentor, team, collab) used as reference images during theme development.

These tokens are consumed verbatim in `assets/css/main.css` via CSS custom properties; changing a token file cascades through every component automatically.

A standalone visual reference capturing the full design system is at **[`design-system/design-system-northeastern-graduate.html`](design-system/design-system-northeastern-graduate.html)** — open it in a browser to browse colours, type scale, spacing, and component patterns side by side.

---

## Accessibility

The theme targets **WCAG 2.1 AA** compliance:

- **Skip link** — a `.skip-link` is the first focusable element on every page, allowing keyboard users to bypass navigation
- **Screen-reader text** — a `.screen-reader-text` utility class visually hides labels that assistive technology still reads
- **Focus indicators** — `:focus-visible` styles are applied globally and overridden per-component (nav, hero, banners, form fields, footer links) with a consistent 3 px Northeastern-red outline ring
- **Reduced motion** — a `@media (prefers-reduced-motion: reduce)` block strips all transitions and animations for users who opt out of motion
- **Semantic ARIA** — `aria-current="page"` marks the active nav item; form inputs use explicit `<label>` associations; heading hierarchy is enforced per template
- **Colour contrast** — all token pairings were chosen to meet 4.5:1 on body text and 3:1 on large text (e.g. `--nu-red-bright: #f0193a` is documented as 4.9:1 on black)

---

## Responsiveness

The stylesheet is **mobile-first**: base styles target small screens, with `min-width` breakpoints layering in wider-screen layouts.

| Breakpoint | What changes                                                                                                                                   |
| ---------- | ---------------------------------------------------------------------------------------------------------------------------------------------- |
| `600 px`   | Form columns expand, some grids switch to two-column                                                                                           |
| `768 px`   | Primary navigation expands from hamburger to horizontal tabs; hero and feature-banner go side-by-side; footer grid spreads to multiple columns |
| `760 px`   | Stats and highlights grids reflow                                                                                                              |
| `900 px`   | Hero billboard switches to a full bleed two-column layout; content grids reach their widest column configuration                               |

Variable fonts (`hankengrotesk-var.woff2`, `sourceserif4-var.woff2`) are self-hosted — removing the Google Fonts third-party request and its render-blocking font-fetch chain — and served with `font-display: swap` so text is visible before fonts load.

---

## CI

A GitHub Actions workflow runs on every push and pull request to `main`.

**What it does:**

1. Checks out the code and sets up PHP 8.2
2. Installs [PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer) with [WordPress Coding Standards](https://github.com/WordPress/WordPress-Coding-Standards)
3. Runs PHPCS against `app/public/phpcs.xml` — reports violations as a human-readable summary and as inline GitHub PR annotations via `cs2pr`

CSS, JS, and third-party vendor code are excluded from linting.

---

## Technical documentation

Full technical documentation — covering the custom theme, plugin architecture, database schemas, design system, UI/UX decisions, accessibility, security, Lighthouse results, and a dedicated **CampusPress deployment discussion** — is available here:

**[View documentation](https://html-preview.github.io/?url=https://github.com/darshan-aswathappa/northeastern-research/blob/main/documentation.html)**

---

## Local development

The site runs in Local by Flywheel. The working directory for WordPress is `app/public/`.

| Path                                                     | Contents                  |
| -------------------------------------------------------- | ------------------------- |
| `app/public/wp-content/themes/nu-research/`              | Custom theme              |
| `app/public/wp-content/plugins/swe-fellows-application/` | ATS Form plugin           |
| `app/public/wp-content/plugins/swe-mail-list/`           | Mail List plugin          |
| `app/public/wp-content/plugins/fellows-deadline/`        | Deadline Countdown plugin |
| `.github/workflows/php-lint.yml`                         | CI linting workflow       |
| `app/public/phpcs.xml`                                   | PHPCS ruleset             |
