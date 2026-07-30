# BSNL Website Editing Protocol

This guide is for BSNL committee members who update website content in WordPress.

It explains what to edit, where to edit it, and which categories or fields to use. For visual layout, styling, header/footer structure, forms, or automatic logic changes, ask the website maintainer to prepare a theme update.

## 1. General Editing Rules

Use WordPress for regular content updates:

- Page text.
- News posts.
- Upcoming event announcements.
- Team member names, roles, photos, and links.
- Alumni directory text.
- Gallery images and captions.
- Contact page wording.
- Contact recipient email.

Do not edit theme PHP/CSS files directly in WordPress.

Before editing the live website, make changes on staging and review them on desktop and mobile.

## 2. Homepage

WordPress location:

- `Pages > Home`

Editable content:

- Hero heading and introduction.
- Homepage links and button text.
- Explore section heading.
- Four pillar numbers, headings, and descriptions.
- Calendar and News section headings.
- Static homepage section order.

Homepage content sources:

- Normal posts appear in the Home News section.
- Posts with category `Upcoming Events` appear in the homepage countdown and event mini-list.
- The homepage countdown reads the earliest future event from `Upcoming Events`.
- Calendar events should be managed as `Upcoming Events` posts, not as fixed theme content.

Keep these dynamic shortcodes in the Home page unless intentionally removing the corresponding section:

```text
[bsnl_home_events]
[bsnl_home_news]
```

Homepage static content is no longer managed under `Appearance > Customize > BSNL Homepage`.

## 3. Upcoming Events

To add a new upcoming event:

1. Go to `Posts > Add New`.
2. Add the event title.
3. Select category `Upcoming Events`.
4. Fill the `BSNL event details` box:
   - `Event date and time`: required.
   - `Location`: optional, but recommended.
   - `Event format`: optional.
   - `Registration / details URL`: optional. If empty, the event links to the post itself.
   - `Short calendar summary`: optional, but useful for the calendar.
5. Add a featured image if available.
6. Publish.

Where upcoming events appear:

- `Events > Upcoming` calendar.
- Homepage countdown, if it is the next future event.
- Homepage upcoming mini-list.

Where upcoming events do not appear:

- News page.
- Home News section.

Important:

- If `Event date and time` is empty, the event cannot appear correctly in the calendar.
- To modify a calendar event, edit the corresponding `Upcoming Events` post.
- To remove a calendar event, unpublish the post or remove the `Upcoming Events` category.
- Keep this shortcode in the Upcoming section of the Events page:

```text
[bsnl_upcoming_events_calendar limit="4"]
```

## 4. News and Highlights

To add a news/highlight post:

1. Go to `Posts > Add New`.
2. Add title, content, and featured image.
3. Fill `BSNL display author` if the public byline should show a specific committee member or contributor.
4. Select the relevant category, such as:
   - `News`
   - `Events`
   - `Biotech Chats`
   - `Workshops`
   - `Company Visits`
   - `Alumni`
   - `Recruitment`
   - `BSNL Retreat`
5. Publish.

Author display:

- Use `BSNL display author` for the visible article author, for example `Jiayi Tan`.
- This does not change the WordPress login account that publishes the post.
- If multiple people wrote the post, use a concise byline such as `Jiayi Tan and BSNL Marketing Team`.
- If the field is empty, the site falls back to the WordPress account author, usually `BSNL Lausanne`.

Where news posts appear:

- News page.
- Home News section.

Do not use `Upcoming Events` for news posts unless it is a future event that should appear in the calendar.

## 5. Events Page

WordPress location:

- `Pages > Events`

Content that can be updated:

- Overview text.
- Upcoming events shortcode section.
- Event introduction text.
- Images and links for LSCD, FIR, FameLab, Biotech Chats, Workshops, and Company Visits.

Button wording protocol:

- If linking to an external official website, use `Visit XX website`.
- If linking to an internal BSNL page, use `Learn more`.
- If linking to a specific event post or detail page, use `Details`.

Do not delete this shortcode from the Upcoming section:

```text
[bsnl_upcoming_events_calendar limit="4"]
```

## 6. Event Detail Pages

Examples:

- `Biotech Chats`
- `Workshops`
- `Company Visits`
- Individual upcoming event detail pages.

Editable content:

- Page text.
- Recent news.
- Links to previous posts or external event websites.
- Images.

If the event has no official external website yet, link to an internal BSNL detail page.

## 7. Our Team

WordPress location:

- `Pages > Our Team`

Content that can be updated:

- Current committee.
- Members.
- Former committee members.
- Recruitment section.
- Gallery images and captions.

For each person, keep the same card structure:

- Photo.
- Name.
- Role.
- LinkedIn link if available.

For the gallery:

- Use real BSNL activity photos.
- Keep captions short.
- Do not link gallery images to old website pages unless there is a clear reason.

## 8. Recruitment

Recruitment should be handled through posts.

To open recruitment:

1. Go to `Posts > Add New`.
2. Add the recruitment title and text.
3. Add application link if available.
4. Select category `Recruitment`.
5. Publish.

How recruitment display works:

- A recent `Recruitment` post appears in the Our Team recruitment section.
- The same post also appears in News and the Home News section.
- If there is no recent recruitment post, the Our Team page should show that recruitment is currently closed.

To close recruitment:

- Unpublish the recruitment post.
- Or remove the `Recruitment` category from the post.
- Or let the recruitment post become outdated if the section is configured with a time limit.

Do not use category `Upcoming Events` for recruitment unless the recruitment is also a dated event that should appear in the calendar.

## 9. Alumni

For alumni news:

- Create a post and select category `Alumni`.
- It appears in News and the Home News section.

For alumni directory changes:

- Edit `Pages > Our Team` for the former committee member preview.
- Edit the full alumni directory page for the larger list.
- Do not create a post just to add a person to the alumni directory.

## 10. Partnership

WordPress location:

- `Pages > Partnership`

Content that can be updated:

- Overview text.
- Academic and student ecosystem text.
- Flagship collaboration descriptions.
- Selected collaborators and sponsors.
- Inquiry/contact call-to-action.

Current page sections:

- `Overview`
- `Academic`
- `Flagship`
- `Collaborators`
- `Inquiry`

Keep the Partnership page focused on collaboration direction, existing collaborators, sponsorship support, and how to start a collaboration.

## 11. Contact

WordPress location:

- `Pages > Contact`

Content that can be updated:

- Stay connected copy with inline links to LinkedIn, Instagram, and the newsletter.
- Contact introduction and email link.
- Location heading and description.

Keep the Contact page section order as:

1. `Stay connected`: a short sentence with social and newsletter links, followed by the email and contact topics.
2. `Location`: Lausanne location and the note that event venues vary.

The public contact email is:

```text
info@bsnl.ch
```

The location should be written as `Lausanne, Switzerland`. Do not list EPFL or UNIL as a fixed address because BSNL operates across Lausanne's life science ecosystem and does not maintain a public office.

Contact recipient email:

- Go to `Appearance > Customize > BSNL Settings > Contact form recipient email`.
- Keep this setting as `info@bsnl.ch` so it matches the Contact page.
- This email receives alumni update requests and any legacy contact form submissions.
- The footer email icon uses the same address.

The Contact page does not use an enquiry form or map. Visitors contact BSNL directly by email. The legacy contact form shortcode remains in the theme only for backward compatibility and should not be added to the Contact page.

## 12. Footer and Social Links

Footer content currently points to:

- LinkedIn.
- Instagram.
- Email.
- Newsletter.
- Linktree.
- Organization pages.
- Events.
- News.

Committee members can update the contact email through:

- `Appearance > Customize > BSNL Settings > Contact form recipient email`

For changes to LinkedIn, Instagram, newsletter URL, footer labels, or footer structure, ask the website maintainer.

## 13. Menu

WordPress location:

- `Appearance > Menus`

Recommended menu:

```text
Home / About Us / Events / News / Our Team / Partnership / Contact
```

Do not add old unused pages back into the menu unless they have been redesigned and reviewed.

## 14. Staging Review Checklist

Before copying changes to the main website:

1. Make edits on staging.
2. Check desktop layout.
3. Check mobile layout.
4. Check Home, Events, News, Our Team, Partnership, and Contact.
5. Confirm all important links work.
6. Confirm upcoming events appear in the calendar.
7. Confirm upcoming events do not appear in News unless intended.
8. Confirm forms still submit correctly.
9. Ask another committee member to review before publishing to production.

## 15. When to Ask for Maintainer Help

Ask the website maintainer if the request involves:

- Layout or spacing.
- Fonts or colors.
- Header or footer structure.
- Homepage module order.
- Calendar/countdown logic.
- Form fields or validation.
- Carousel behavior.
- Search behavior.
- New reusable modules or shortcodes.
- Theme zip updates.
