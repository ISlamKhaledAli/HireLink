---
name: Professional Connection
colors:
  surface: '#faf9fc'
  surface-dim: '#dad9dd'
  surface-bright: '#faf9fc'
  surface-container-lowest: '#ffffff'
  surface-container-low: '#f4f3f7'
  surface-container: '#eeedf1'
  surface-container-high: '#e9e7eb'
  surface-container-highest: '#e3e2e6'
  on-surface: '#1a1c1e'
  on-surface-variant: '#43474e'
  inverse-surface: '#2f3033'
  inverse-on-surface: '#f1f0f4'
  outline: '#74777f'
  outline-variant: '#c4c6cf'
  surface-tint: '#455f87'
  primary: '#022448'
  on-primary: '#ffffff'
  primary-container: '#1e3a5f'
  on-primary-container: '#8aa4cf'
  inverse-primary: '#adc8f5'
  secondary: '#9d4300'
  on-secondary: '#ffffff'
  secondary-container: '#fd761a'
  on-secondary-container: '#5c2400'
  tertiary: '#341f00'
  on-tertiary: '#ffffff'
  tertiary-container: '#503300'
  on-tertiary-container: '#c69b5f'
  error: '#ba1a1a'
  on-error: '#ffffff'
  error-container: '#ffdad6'
  on-error-container: '#93000a'
  primary-fixed: '#d5e3ff'
  primary-fixed-dim: '#adc8f5'
  on-primary-fixed: '#001c3b'
  on-primary-fixed-variant: '#2d486d'
  secondary-fixed: '#ffdbca'
  secondary-fixed-dim: '#ffb690'
  on-secondary-fixed: '#341100'
  on-secondary-fixed-variant: '#783200'
  tertiary-fixed: '#ffddb2'
  tertiary-fixed-dim: '#edbf7f'
  on-tertiary-fixed: '#291800'
  on-tertiary-fixed-variant: '#60410c'
  background: '#faf9fc'
  on-background: '#1a1c1e'
  surface-variant: '#e3e2e6'
typography:
  display:
    fontFamily: Plus Jakarta Sans
    fontSize: 48px
    fontWeight: '700'
    lineHeight: '1.2'
    letterSpacing: -0.02em
  h1:
    fontFamily: Plus Jakarta Sans
    fontSize: 32px
    fontWeight: '700'
    lineHeight: '1.25'
  h2:
    fontFamily: Plus Jakarta Sans
    fontSize: 24px
    fontWeight: '600'
    lineHeight: '1.3'
  h3:
    fontFamily: Plus Jakarta Sans
    fontSize: 20px
    fontWeight: '600'
    lineHeight: '1.4'
  body-lg:
    fontFamily: Inter
    fontSize: 18px
    fontWeight: '400'
    lineHeight: '1.6'
  body-md:
    fontFamily: Inter
    fontSize: 16px
    fontWeight: '400'
    lineHeight: '1.6'
  body-sm:
    fontFamily: Inter
    fontSize: 14px
    fontWeight: '400'
    lineHeight: '1.5'
  label-md:
    fontFamily: Inter
    fontSize: 14px
    fontWeight: '600'
    lineHeight: '1'
    letterSpacing: 0.01em
  label-sm:
    fontFamily: Inter
    fontSize: 12px
    fontWeight: '500'
    lineHeight: '1'
rounded:
  sm: 0.25rem
  DEFAULT: 0.5rem
  md: 0.75rem
  lg: 1rem
  xl: 1.5rem
  full: 9999px
spacing:
  base: 4px
  xs: 0.5rem
  sm: 0.75rem
  md: 1rem
  lg: 1.5rem
  xl: 2.5rem
  gutter: 1.5rem
  container-max: 1280px
---

## Brand & Style

The design system is built for a professional recruitment ecosystem that balances corporate reliability with the energy of career growth. The brand personality is authoritative yet accessible, focusing on clarity and efficiency to reduce the friction of the job search process.

The chosen style is **Corporate / Modern**. It leverages a high-contrast primary palette to establish trust, while using minimalist white space and precise geometric alignments to feel contemporary. The UI avoids unnecessary decorative elements, ensuring that the content—job listings and candidate profiles—remains the focal point. The emotional response should be one of confidence, momentum, and professional clarity.

## Colors

The color palette centers on **Deep Blue** (#1E3A5F) to project stability and institutional knowledge. **Vibrant Orange** (#F97316) serves as the high-energy accent color, used strategically for primary actions and to draw attention to key conversion points.

The background uses a soft **Light Gray** (#F8FAFC) to reduce eye strain and provide a sophisticated alternative to pure white. Text is rendered in a near-black **Deep Navy** (#1A1A2E) to maintain high legibility while harmonizing with the primary blue. Status colors follow industry standards for immediate recognition: emerald green for success, the secondary orange for pending states, and a crisp red for errors or rejections.

## Typography

This design system utilizes a dual-font strategy to optimize for both personality and utility. **Plus Jakarta Sans** is used for headlines to provide a friendly, modern, and slightly geometric feel that distinguishes the brand. **Inter** is employed for all body copy and UI labels due to its exceptional readability and neutral, systematic tone.

Text scales are built on a modular rhythm. Headlines use tighter line heights and slight negative letter spacing at larger sizes to appear more cohesive. Body text maintains a generous 1.6x line height to ensure long job descriptions remain scannable and accessible.

## Layout & Spacing

The design system employs a **Fixed Grid** model for desktop views, centered with a maximum width of 1280px. A 12-column system is used with 24px (1.5rem) gutters to create structured, repeatable layouts for dashboards and job listings.

Spacing follows a strict 4px base unit, favoring 8px increments for larger containers. Generous internal padding within cards and sections is encouraged to maintain the "Clean and Minimal" aesthetic, preventing the interface from feeling cluttered even when displaying data-heavy job tables.

## Elevation & Depth

Visual hierarchy in this design system is achieved through **Tonal Layers** combined with **Ambient Shadows**. Surfaces are primarily flat, but key interactive elements like job cards and modals use a layered approach to appear closer to the user.

Shadows are ultra-diffused and low-opacity, using a slight tint of the Primary Deep Blue (#1E3A5F) rather than pure black to keep the UI looking "clean." The elevation levels are:
- **Level 0 (Flat):** Background and decorative elements.
- **Level 1 (Subtle):** Cards and content containers.
- **Level 2 (Floating):** Hover states, dropdowns, and tooltips.
- **Level 3 (Overlay):** Modals and high-priority alerts.

## Shapes

The shape language is defined by a "Soft-Rounded" philosophy. By setting a base roundedness of 0.5rem (Level 2), the system feels approachable and modern without appearing too "bubbly" or informal.

Larger containers like cards use **rounded-xl** (1.5rem) to create a distinct frame for content. Interactive elements like buttons use **rounded-lg** (1rem) for a comfortable touch target appearance. Functional status elements like badges and tags use a **Pill-shape** (full radius) to differentiate them from structural components and buttons.

## Components

### Buttons
- **Primary:** Vibrant Orange (#F97316) background with white text, `rounded-lg`. Uses a subtle lift on hover.
- **Secondary:** Deep Blue (#1E3A5F) background or outline, `rounded-lg`.
- **Tertiary:** Ghost style with no background, using Deep Blue text.

### Cards
- Job cards use `rounded-xl` corners and a subtle Level 1 shadow. 
- Background is pure white (#FFFFFF) to pop against the Light Gray page background.
- Padding should be a minimum of `lg` (1.5rem).

### Badges & Chips
- All badges use a full-pill border radius.
- Status badges use low-opacity backgrounds (e.g., 10% Green) with high-contrast text in the same hue.
- Job category chips use the Light Gray background with Deep Blue text.

### Form Inputs
- Inputs use a white background with a 1px border in a lightened version of the Primary color.
- Focus states should use a 2px Vibrant Orange outline.
- Labels use `label-md` typography style.

### Icons
- Use **Lucide** or **Heroicons** with a "Medium" stroke weight (2px).
- Icons should be sized consistently at 20px for UI actions and 24px for section headers.