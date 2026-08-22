# L'Enfant Roi Layout — WordPress child theme

A child theme of **Hello Elementor** that carries the L'Enfant Roi homepage
layout: the design tokens, the fixed header, the hero with its scroll-linked
video reveal, and the gold arabesque line motif.

Built for demo.shividesigns.com. Milestone 1 of the homepage build — items 1–3
of the agreed list.

## Install

1. WordPress admin → **Appearance → Themes → Add New → Upload Theme**
2. Pick `lenfant-roi-child.zip`, install, **Activate**

Hello Elementor must stay installed — it is the parent. Do not delete it.

## Editing the content

Everything on the homepage is editable without touching code:
**Appearance → Customize → Homepage layout**

| Section  | What you can change |
| -------- | ------------------- |
| Header   | Logo image, the two language labels and their links |
| Hero     | Title lettering, subtitle, photo **or** video (plus a still frame), the scroll label, and the two lines of text over the photo |
| Visit tab| Show/hide, the text on the gold tab, and where it links |
| Colours  | Gold, navy and body text — these feed the whole layout at once |

The menu behind the round button comes from **Appearance → Menus**, assigned to
the *Main menu* location.

## Swapping the hero for a video

Customize → Homepage layout → Hero → set **Hero shows** to *A video*, upload an
MP4 and a still frame. Keep the file **under about 5 MB**. The original site's
hero video is 61 MB, which is the single biggest reason that page scores badly
on performance — there is no need to repeat that.

## Fonts

| Role | Original site | Used here | Why |
| ---- | ------------- | --------- | --- |
| Body | Poppins | Poppins | Free, identical |
| Display | Caslon CP | Libre Caslon Display | Caslon CP is a commercial licence — the files cannot be copied |
| Tracked caps | Din Medium | Archivo | Same — Din Medium is licensed |

If the licences are bought, swapping back is two lines in
`assets/css/main.css` (`--font-family-secondary`, `--font-family-tertiary`)
plus the `@font-face` files.

## Placeholders

The brand lettering (`assets/svg/`) and the hero photo
(`assets/img/hero-placeholder.jpg`) stand in until the real assets arrive.
Replace them from the Customizer — no need to edit files.

## Files

```
lenfant-roi-child/
├── style.css              theme header only
├── functions.php          enqueues, theme supports, hero preload
├── header.php             fixed bar, language pair, menu button, overlay
├── footer.php             the gold "Schedule a visit" tab
├── front-page.php         homepage; steps aside if the front page has its own content
├── inc/
│   ├── svg.php            inline-SVG helper
│   ├── customizer.php     every editable setting, with defaults
│   └── sections.php       hero + about markup, also [lr_hero] / [lr_about] shortcodes
└── assets/
    ├── css/main.css       all styling, tokens at the top
    ├── js/main.js         scroll reveal, entrance animations, header, menu
    ├── svg/               brand marks
    └── img/               hero placeholder
```

## Measurements

The layout is not eyeballed. Every number in `main.css` was read off the
original page with a headless browser and checked back against it at 1280px and
390px wide. At the time of writing, the hero matches the original within 1–2px
on: header height, title block position and width, photo block position and
height, and the position and size of all three lines of text over the photo.
