# BSNL People

This WordPress plugin manages Board members, Members, and Alumni from one People directory.

## Daily editing

- Add or edit a person under `People` in the WordPress sidebar.
- Use the `Group` field to move a person between Board, Members, and Alumni.
- Use the list-screen bulk actions to move several people at once.
- Use the Group dropdown above the People table to show only Board, Members, or Alumni.
- Alumni can be classified as Board Alumni or Member Alumni. Use the Alumni category dropdown in All People to filter them.
- Members are sorted alphabetically by first name.
- Alumni are sorted by the final year in `BSNL years`, newest first. Missing years appear last; ties are sorted by first name.
- Board members are sorted by `Page Attributes > Order` and then by name.

## Page shortcodes

Add these shortcodes at the relevant positions in the About Us or Alumni pages:

```text
[bsnl_people group="board"]
[bsnl_people group="members"]
[bsnl_people group="alumni"]
```

The Alumni shortcode shows both alumni categories by default. To show them separately, use `[bsnl_people group="alumni" alumni_type="board_alumni"]` and `[bsnl_people group="alumni" alumni_type="member_alumni"]`.

The shortcode does not add a heading. Keep the existing page heading immediately above each shortcode.

Before activating the shortcodes on the live site, create the existing people records and remove the old static cards from the relevant page section so that people do not appear twice.

For the first setup, `People > Import existing people` can create People records from the current static Team markup. The same screen includes a separate Legacy Alumni migration:

1. Select the old Alumni page.
2. Click `Scan and preview Alumni`.
3. Review the detected names, affiliations, BSNL years, and former roles.
4. Click `Confirm Alumni import` only when the preview is correct.

The migration preserves Board Alumni and regular Alumni as separate legacy types, skips existing People with the same name, and stores available affiliation, BSNL years, former role, testimonial, LinkedIn, and photo data. It does not change or delete page content.

After migration, `People > Import existing people > Legacy People photos` can copy saved legacy photo URLs into the WordPress Media Library and set them as Featured images. Existing Featured images are never replaced.
