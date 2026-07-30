# BSNL Website Maintenance Guide

## Scope

These instructions apply to this repository and the `bsnl-light-theme/` WordPress theme.

The active source of truth is:

- Theme source: `bsnl-light-theme/`
- Theme version: `bsnl-light-theme/style.css`
- Frontend styles: `bsnl-light-theme/assets/css/theme.css`
- Editor styles: `bsnl-light-theme/assets/css/editor.css`
- Frontend behavior: `bsnl-light-theme/assets/js/theme.js`

Old ZIP files, static HTML previews, screenshots, and files under `BSNL_logo_website/` are references or release artifacts. Do not treat them as the active implementation unless the user explicitly asks to restore an older version.

The repository often contains unrelated untracked previews, ZIP files, and user edits. Inspect `git status` before working. Never delete, revert, rename, or repackage unrelated files.

## Product Goal

Maintain a clean, elegant BSNL website for Lausanne's life science community while allowing committee members to update normal content in WordPress.

Unless the user explicitly requests a redesign:

- Preserve the current visual appearance.
- Preserve existing public URLs, anchors, responsive behavior, and CSS classes.
- Make the smallest change that solves the request.
- Check desktop and mobile layouts.
- Keep content language appropriate for the real BSNL organization, not design-demo language.

## Content Ownership

WordPress must be the source of truth for page content.

Editable in `Pages`:

- Page titles and body text.
- Homepage static headings, copy, links, and section order.
- About Us, Events, Partnership, Contact, Alumni, Gallery, and other page sections.
- Images and captions stored in page content.

Do not reintroduce any of the following:

- A recurring migration that rewrites `post_content`.
- A `the_content` filter that replaces editor text at render time.
- JavaScript that changes headings, labels, captions, or page copy after load.
- A hard-coded PHP page section that hides or overrides content saved in WordPress.

One-time migrations are allowed only when an upgrade requires them. They must:

1. Use a new versioned option so they run once.
2. Preserve custom structured content.
3. Avoid overwriting content when ownership is ambiguous.
4. Record conflicts and show an administrator notice.
5. Leave all migrated content editable afterward.

The 0.4.0 ownership migration uses:

- `bsnl_light_editor_ownership_version`
- `bsnl_light_editor_ownership_conflicts`

Do not reset or reuse these options for a later migration. Introduce a new target version and document the change.

## Dynamic Exceptions

Some modules are intentionally generated from structured WordPress data rather than typed directly into a Page:

- `[bsnl_home_events]`: upcoming event posts and event metadata.
- `[bsnl_home_news]`: published posts shown on the homepage.
- `[bsnl_upcoming_events_calendar]`: posts in `Upcoming Events`.
- `[bsnl_news_listing]`: News listing and pagination.
- `[bsnl_recruitment_notice]`: current Recruitment post state.
- `[bsnl_contact_form]`: contact form fields, validation, and delivery.
- `[bsnl_contact_details]`: global contact email and contact details.
- `[bsnl_alumni_request_form]`: alumni update form.

When changing one of these modules, keep its shortcode stable unless a migration updates every stored use. Page editors must remain able to move or remove the shortcode.

Other non-Page content:

- Header and footer structure are in `header.php`, `footer.php`, and footer helpers in `functions.php`.
- Navigation is managed through `Appearance > Menus`.
- The contact recipient is managed through `Appearance > Customize > BSNL Settings`.
- Upcoming events are Posts with category `Upcoming Events` and `BSNL event details` metadata.
- News cards are normal Posts with categories, featured images, and optional `BSNL display author` metadata.

The primary Events submenu currently normalizes legacy event anchors and the label `Format(s)` to `How it works`. Treat these menu filters as a compatibility exception. Report any conflict with a requested custom menu label before removing or expanding the behavior.

## Homepage Architecture

`front-page.php` must remain a thin template that calls `the_content()` inside `.bsnl-site`.

The Home page stores the static layout and contains the dynamic shortcodes. Its existing public class structure must remain compatible with `theme.css`, including:

- `.bsnl-hero`
- `.bsnl-section-intro`
- `.bsnl-value-grid`
- `.bsnl-event-module`
- `.bsnl-news-section`

Do not move homepage copy back into the Customizer. Legacy `bsnl_home_*` theme mods may be read to preserve values during migration, but the WordPress Home page is authoritative after 0.4.0.

## Block Editor

The theme enables editor styles through `assets/css/editor.css` and registers patterns in the `BSNL Layouts` category.

When adding reusable page layouts:

- Prefer Gutenberg patterns and standard blocks.
- Reuse established BSNL classes.
- Keep anchors stable when they are linked from local navigation or menus.
- Avoid custom blocks or new plugins unless standard blocks and shortcodes are insufficient.
- Confirm editor markup does not add wrappers that break current selectors.

## Styling Rules

`assets/css/theme.css` controls the live visual system. Preserve it byte-for-byte for content-ownership or backend-only changes whenever possible.

When visual changes are requested:

- Follow existing colors, typography, spacing, and responsive breakpoints.
- Use Montserrat and the existing BSNL orange, blue, ink, and neutral palette.
- Reuse current components before creating new ones.
- Avoid inline styles in page content except dynamic background images or values that must be generated.
- Keep selectors scoped to BSNL classes; avoid broad rules that alter WordPress admin or plugin UI.
- Check long headings, menu labels, buttons, cards, and form fields at mobile widths.

## JavaScript Rules

JavaScript should handle interaction, not content ownership.

Allowed examples:

- Mobile menu behavior.
- Search expansion.
- Carousels and countdowns.
- Back-link history behavior.
- Anchor navigation.

Do not use JavaScript to replace editor text, remove editor links, or conceal database content. If old runtime behavior must become editable, persist the visible state once through a conservative migration and then remove the mutation.

Run before release:

```powershell
node --check bsnl-light-theme\assets\js\theme.js
```

## PHP and WordPress Rules

- Require PHP 7.4 compatibility unless the theme header is intentionally updated.
- Escape frontend output with the appropriate `esc_html`, `esc_attr`, `esc_url`, or `wp_kses` function.
- Sanitize saved settings and metadata.
- Keep nonce and capability checks for form or metadata writes.
- Avoid defining page-specific helper functions inside templates.
- Keep queries bounded and call `wp_reset_postdata()` after custom loops.
- Do not edit WordPress core or installed plugins for theme behavior.

Run PHP lint when PHP CLI is available:

```powershell
Get-ChildItem bsnl-light-theme -Filter *.php | ForEach-Object { php -l $_.FullName }
```

If PHP CLI is unavailable, say so explicitly in the final report. Static inspection is not a substitute for staging verification.

## Versioning

For every installable theme release:

1. Increment `Version` in `bsnl-light-theme/style.css`.
2. Update the CSS and JavaScript enqueue versions in `functions.php`.
3. Update a migration target only when the release adds a real one-time migration.
4. Never rerun an old migration merely to match the theme version.
5. Do not create a ZIP automatically. Finish and verify the source change, then ask the user whether a new installable ZIP is needed.

## Verification Checklist

For a layout-preserving change:

1. Confirm `theme.css` is unchanged or explain every difference.
2. Confirm existing CSS classes and section IDs remain present.
3. Search for new content overrides:

```powershell
rg -n "the_content|wp_update_post|post_content|textContent|innerHTML" bsnl-light-theme
```

Review every match. Legitimate output and one-time migrations are allowed; recurring page rewrites are not.

4. Run JavaScript syntax validation.
5. Run PHP lint when available.
6. Check that all referenced local assets exist.
7. Test the staging site at desktop, tablet, and mobile widths.
8. Check Home, About Us, Events, event detail pages, News, Partnership, Contact, Alumni, search, menus, back links, forms, and footer links.
9. Clear WordPress, plugin, CDN, and browser caches before concluding that a change did not appear.

Do not claim a PHP theme has been visually verified from a static HTML file. Use the actual staging WordPress site for final visual QA.

## Packaging

Packaging is opt-in. After modifying and verifying the theme source, stop before packaging and ask the user whether they want a new ZIP. Create one only after the user explicitly confirms. A packaging request applies to that release only and is not standing permission to package future changes.

When requested, create a WordPress-compatible ZIP from the project directory that contains `bsnl-light-theme/`:

```powershell
tar.exe -a -c -f bsnl-light-theme-X.Y.Z-wordpress-install.zip bsnl-light-theme
```

Do not use a package layout that places the PHP files directly at ZIP root. The required structure is:

```text
bsnl-light-theme/
  style.css
  functions.php
  ...
```

Validate every package:

```powershell
tar.exe -tf bsnl-light-theme-X.Y.Z-wordpress-install.zip
tar.exe -xOf bsnl-light-theme-X.Y.Z-wordpress-install.zip bsnl-light-theme/style.css | Select-Object -First 12
```

Archive entries must use forward slashes and include `bsnl-light-theme/style.css`. This prevents WordPress's `The theme is missing the style.css stylesheet` installation error.

## Documentation and Handoff

`bsnl-wordpress-maintenance-notes.md` is the committee-facing editing guide. Keep it synchronized with the actual theme behavior. In particular, any pre-0.4.0 statement that homepage copy is edited under `Customize > BSNL Homepage` is legacy guidance; homepage static content is now edited in `Pages > Home`.

Every completed maintenance task should report:

- Files and behavior changed.
- Where committee members edit the affected content in WordPress.
- Any dynamic or template-owned exceptions.
- Migration conflicts or skipped content.
- Validation performed and validation that could not be performed.
- Exact path to the new ZIP when a release was requested.
- When no release package was requested, explicitly ask whether the user wants the verified source packaged as a new ZIP.
