# AksesLoka Design Direction

## Identity

AksesLoka is a public campus accessibility service. It should feel dependable,
clear, and practical for visitors, reporters, officers, and central admins.
The interface prioritizes wayfinding and task completion over decoration.

Brand assets are generated as multi-resolution PNG and WebP formats:
- Favicons: `favicon-32x32.png`, `favicon-16x16.png`, and `apple-touch-icon.png` (180x180).
- Logo mark: `public/images/logo-mark.webp` and `logo-mark.png` (512x512, transparent RGBA).
- Horizontal lockup: `public/images/logo-horizontal.webp` and `logo-horizontal.png` (720x180).
- Embossed badge: `public/images/logo-badge.webp` and `logo-badge.png` (512x512).

## Design Read

Public-service campus product for accessibility-sensitive users, using an
institutional, trust-first visual language with direct Indonesian copy.

## Dials

- DESIGN_VARIANCE: 3
- MOTION_INTENSITY: 2
- VISUAL_DENSITY: 5

## Palette

| Token | Value | Use |
|---|---|---|
| Navy 950 | `#172554` | Strong headers and navigation |
| Navy 900 | `#1E3A8A` | Primary actions and brand |
| Safety orange 700 | `#C2410C` | Primary CTA with white text |
| Safety orange 600 | `#EA580C` | Focus ring and large accents only |
| Slate 900 | `#0F172A` | Primary text |
| Slate 700 | `#334155` | Secondary text |
| Slate 200 | `#E2E8F0` | Borders |
| Slate 50 | `#F8FAFC` | Page background |
| White | `#FFFFFF` | Surfaces |

White on safety orange 600 is restricted to large text because its contrast is
3.56:1. Normal-size CTA text uses safety orange 700, or safety orange is used as
a focus outline rather than a text background.

## Semantic Colors

- Positive: emerald 800 text on emerald 50, always paired with a text label.
- Warning: amber 900 text on amber 50, always paired with a text label.
- Critical: red 800 text on red 50, always paired with a text label.
- Neutral: slate 700 text on slate 100, always paired with a text label.

## Typography

Use PP Editorial New for display headings when a licensed local font is
available, with Georgia and Cambria as resilient editorial fallbacks. Body copy,
forms, navigation, tables, status labels, and the logo wordmark use the system
sans-serif stack for predictable rendering on low-bandwidth campus connections.
Headings use at least 1.15 line-height to preserve descenders. Body copy stays
below 70 characters per line.

## Shape And Elevation

- Cards: 12px radius.
- Inputs and buttons: 8px radius.
- Status badges: 6px radius, not pills.
- Shadows only distinguish overlays or map panels from their background.

## Interaction

- Every target is at least 44 by 44 CSS pixels.
- Every control has a visible `:focus-visible` outline.
- Motion is limited to feedback and state transitions, and respects
  `prefers-reduced-motion`.
- Public map results always have a textual list alternative.
- Data views include explicit empty and error states. Loading text is announced
  with `aria-live` when JavaScript fetches data.

## Copy

Use direct Indonesian labels that name the action: `Lihat peta`, `Buat laporan`,
`Verifikasi laporan`, and `Selesaikan penanganan`. Do not fabricate statistics,
testimonials, institutions, or operational claims. Dashboard numbers always come
from the database.
