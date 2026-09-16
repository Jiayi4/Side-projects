# BSNL Website Editing Guide

This guide is for BSNL committee members making routine updates in WordPress. Use it for content, posts, images, collaborators, team information, and the gallery. For layout, colours, navigation, forms, or any change that requires editing HTML/CSS/PHP, contact the website maintainer.

## Before You Edit

- Work in WordPress only. Do not change theme files.
- Review every change on desktop and mobile before publishing.
- Keep image filenames clear, for example `2026-lscd-panel.jpg` or `roche-logo.png`.
- If a page opens in an HTML/code block, change only the existing text, link, or image URL. Do not remove surrounding tags, classes, or section IDs.

## 1. News

**Where:** `Posts > Add New` or `Posts > All Posts`

Use News for announcements, recaps, opportunities, alumni updates, and recruitment posts.

To create a post:

1. Add a clear title and the article text.
2. Add a **Featured image**. This is the cover image used on the News page and homepage. A landscape image around 1600 x 1000 px works best.
3. Select the appropriate category, normally `News`. Add another relevant category only when needed, such as `Alumni` or `Recruitment`.
4. In the `BSNL display author` box, enter the public byline if it should differ from the WordPress account name. Leave it empty to use the account author.
5. Preview, then publish.

The title, summary, and cover image on News cards all link to the post automatically. News posts also appear in the homepage News section. Posts in `Upcoming Events` are excluded from News.

## 2. Upcoming Events

**Where:** `Posts > Add New` or `Posts > All Posts`

Upcoming events are normal posts with the `Upcoming Events` category. They feed the Events calendar automatically and the nearest four future events feed the homepage.

To add an event:

1. Add the event title, post text, and a Featured image.
2. Select **only** the `Upcoming Events` category unless another category is genuinely required.
3. In `BSNL event details`, complete:
   - **Event date and time**: required. Without it, the event will not appear in the calendar.
   - **Location**: recommended.
   - **Event format**: optional.
   - **Registration / details URL**: use the external registration page when there is one. If left empty, `Details ->` opens the BSNL post.
   - **Short calendar summary**: recommended. Keep it brief.
4. Preview and publish.

To change or cancel an event, edit the same post. To remove it from the calendar, unpublish it or remove the `Upcoming Events` category. Do not edit the Events page calendar directly; it uses the dynamic shortcode `[bsnl_upcoming_events_calendar]`.

## 3. Partnership and Collaborators

**Where:** `Collaborators` in the WordPress sidebar

The Partnership page displays the published Collaborator entries automatically. Do not add logos directly into the Partnership page HTML.

To add a collaborator:

1. Go to `Collaborators > Add New`.
2. Enter the organisation name as the title.
3. Set an official logo as the **Featured image**.
4. In `Collaborator details`, add the complete organisation website URL, beginning with `https://`.
5. Set `Page Attributes > Order` to position the logo. Lower numbers appear first; items with the same order are arranged alphabetically.
6. Publish.

To edit a collaborator, open its entry under `Collaborators`. Replacing its Featured image updates the displayed logo; changing `Organisation website` updates its click-through link.

To resize every collaborator logo together, go to `Collaborators > Logo display settings`. This applies one maximum width and height to the full grid while retaining image proportions. If one logo still appears visually small, crop away excess blank space in the original image and re-upload it as the Featured image.

The Partnership page text and flagship-event links remain editable under `Pages > Partnership`. Keep its existing section structure and use the existing links rather than rebuilding the logo grid.

## 4. Team and Alumni

**Where:** `Pages > About Us`

The Current Board, Members, Alumni, Recruitment, and Gallery sections live inside About Us. They are not separate WordPress pages.

To update a team member:

1. Go to `Pages > About Us > Edit`.
2. Find the correct existing person card.
3. Replace the photo, name, role, and LinkedIn URL as needed.
4. To add a person, duplicate an existing card first, then replace all of its content. This preserves the site layout on desktop and mobile.
5. To remove a person, delete that complete card only.
6. Update and check both desktop and mobile views.

Use the same method for the Alumni section. Keep names, roles, and descriptions concise. Do not change the card classes, image wrappers, section IDs, or surrounding grid markup.

Recruitment announcements should be created as posts with the `Recruitment` category. They can then be linked from the Recruitment section and also appear in News.

## 5. Gallery

**Where:** `Pages > About Us`, in the Gallery section

To update a gallery item:

1. Open `Pages > About Us > Edit` and find the Gallery section.
2. Replace an existing image using the Media Library, or duplicate a complete existing gallery tile to add another item.
3. Update the title and short caption below the image.
4. Keep images landscape where possible and use real BSNL activity photographs.
5. Check the gallery carousel on desktop and mobile after updating.

Do not remove the gallery carousel wrappers, slide classes, dot controls, or section ID. They control the mobile carousel and gallery navigation.

## When to Ask the Maintainer

Ask before changing:

- Page layout, spacing, fonts, colours, headers, footer, or mobile menu.
- Navigation labels or dropdown structure.
- Events calendar logic, homepage dynamic sections, pagination, or shortcodes.
- Contact form fields, recipient email, SMTP, or validation.
- Any HTML that you cannot confidently identify as one existing card, image, text, or link.

Before publishing to the live site, check the edited page, linked destinations, News/Events listings, and mobile layout. Clear browser or site cache if an update does not appear immediately.
