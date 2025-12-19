# Industrial Refined Design System Guide

## Overview

The **Industrial Refined** design system combines raw industrial aesthetics with modern refinement, perfect for a building materials marketplace. This guide shows you how to use TailwindCSS utilities to implement the design system.

**View Live Examples:** Visit `/design-system` in your browser to see all components in action.

---

## Color Palette

### Industrial Colors

- **Steel (Primary)**: Deep slate tones representing industrial steel
  - `steel-50` to `steel-900`
  - Primary brand color: `steel-700`

- **Rust (Secondary)**: Warm terracotta representing raw materials
  - `rust-50` to `rust-900`
  - Secondary brand color: `rust-500`

- **Amber (Accent/CTA)**: Bright construction orange for energy and calls-to-action
  - `amber-50` to `amber-900`
  - Accent color: `amber-500`

- **Forest (Success)**: Natural green for success states
  - `forest-50` to `forest-900`
  - Success color: `forest-500`

- **Concrete (Neutral)**: Warm grays inspired by concrete
  - `concrete-50` to `concrete-900`
  - Used for backgrounds and text

### Usage Examples

```html
<!-- Background colors -->
<div class="bg-steel-700">Primary background</div>
<div class="bg-concrete-50">Page background</div>
<div class="bg-white">Card background</div>

<!-- Text colors -->
<h1 class="text-steel-900">Heading</h1>
<p class="text-steel-700">Body text</p>
<span class="text-concrete-600">Secondary text</span>

<!-- Border colors -->
<div class="border border-concrete-200">Bordered element</div>
```

---

## Typography

### Fonts

- **Display Font (Rajdhani)**: Geometric, industrial, strong
  - Use for: Headings, buttons, navigation, emphasis
  - Class: `font-display`

- **Body Font (Work Sans)**: Clean, professional, readable
  - Use for: Body text, descriptions, UI elements
  - Class: `font-body`

### Typography Scale

```html
<!-- Headings -->
<h1 class="font-display text-6xl font-bold text-steel-900">Heading 1</h1>
<h2 class="font-display text-5xl font-bold text-steel-900">Heading 2</h2>
<h3 class="font-display text-4xl font-semibold text-steel-900">Heading 3</h3>
<h4 class="font-display text-3xl font-semibold text-steel-800">Heading 4</h4>
<h5 class="font-display text-2xl font-semibold text-steel-800">Heading 5</h5>
<h6 class="font-display text-xl font-semibold text-steel-800">Heading 6</h6>

<!-- Body text -->
<p class="font-body text-lg text-steel-700">Large body text</p>
<p class="font-body text-base text-steel-700">Regular body text</p>
<p class="font-body text-sm text-concrete-600">Small text</p>
```

---

## Buttons

### Primary Button (Call-to-Action)

Bright amber for high-priority actions.

```html
<!-- Large -->
<button class="font-display font-bold px-8 py-4 bg-amber-500 text-white rounded-xl hover:bg-amber-600 hover:shadow-industrial-lg transition-all duration-200 hover:-translate-y-0.5">
  Primary Large
</button>

<!-- Medium -->
<button class="font-display font-bold px-6 py-3 bg-amber-500 text-white rounded-lg hover:bg-amber-600 hover:shadow-industrial-md transition-all duration-200 hover:-translate-y-0.5">
  Primary Medium
</button>

<!-- Small -->
<button class="font-display font-semibold px-4 py-2 bg-amber-500 text-white rounded-lg hover:bg-amber-600 transition-all duration-200">
  Primary Small
</button>
```

### Secondary Button

Rust color for secondary actions.

```html
<button class="font-display font-bold px-6 py-3 bg-rust-500 text-white rounded-lg hover:bg-rust-600 hover:shadow-industrial-md transition-all duration-200 hover:-translate-y-0.5">
  Secondary Button
</button>
```

### Outline Button

Transparent with border, fills on hover.

```html
<button class="font-display font-bold px-6 py-3 bg-transparent text-steel-700 border-2 border-steel-700 rounded-lg hover:bg-steel-700 hover:text-white transition-all duration-200">
  Outline Button
</button>
```

### Ghost Button

Minimal styling, subtle hover.

```html
<button class="font-display font-bold px-6 py-3 bg-transparent text-steel-700 hover:bg-concrete-100 rounded-lg transition-all duration-200">
  Ghost Button
</button>
```

### Disabled Button

```html
<button disabled class="font-display font-bold px-6 py-3 bg-concrete-300 text-concrete-500 rounded-lg cursor-not-allowed">
  Disabled Button
</button>
```

---

## Links

### Primary Link (Underlined)

```html
<a href="#" class="font-body text-lg font-semibold text-steel-700 hover:text-amber-600 underline decoration-2 underline-offset-4 decoration-amber-500 hover:decoration-amber-600 transition-colors duration-200">
  Standard Link
</a>
```

### Simple Link (No Underline)

```html
<a href="#" class="font-body font-medium text-steel-700 hover:text-rust-600 transition-colors duration-200">
  Simple Link
</a>
```

### Inline Link

```html
<p class="font-body text-base text-steel-700">
  This is text with an
  <a href="#" class="font-semibold text-amber-600 hover:text-amber-700 underline decoration-amber-500 underline-offset-2 transition-colors duration-200">
    inline link
  </a>
  example.
</p>
```

### Navigation Link

```html
<!-- Normal state -->
<a href="#" class="font-display font-semibold text-steel-700 hover:text-amber-600 transition-colors duration-200 border-b-2 border-transparent hover:border-amber-500 pb-1">
  Products
</a>

<!-- Active state -->
<a href="#" class="font-display font-semibold text-amber-600 border-b-2 border-amber-500 pb-1">
  Active Page
</a>
```

---

## Lists

### Unordered List (Bullet Points)

```html
<ul class="space-y-3">
  <li class="font-body text-steel-700 flex items-start gap-3">
    <span class="flex-shrink-0 w-2 h-2 bg-amber-500 rounded-full mt-2"></span>
    <span>List item text goes here</span>
  </li>
  <li class="font-body text-steel-700 flex items-start gap-3">
    <span class="flex-shrink-0 w-2 h-2 bg-amber-500 rounded-full mt-2"></span>
    <span>Another list item</span>
  </li>
</ul>
```

### Ordered List (Numbers)

```html
<ol class="space-y-3">
  <li class="font-body text-steel-700 flex items-start gap-4">
    <span class="flex-shrink-0 font-display font-bold text-amber-600 text-lg">1.</span>
    <span>First step description</span>
  </li>
  <li class="font-body text-steel-700 flex items-start gap-4">
    <span class="flex-shrink-0 font-display font-bold text-amber-600 text-lg">2.</span>
    <span>Second step description</span>
  </li>
</ol>
```

### Feature List (Icon + Description)

```html
<div class="space-y-4">
  <div class="flex items-start gap-4 p-4 bg-concrete-50 rounded-lg border border-concrete-200">
    <div class="flex-shrink-0 w-10 h-10 bg-amber-500 rounded-lg flex items-center justify-center">
      <span class="text-white font-display font-bold">✓</span>
    </div>
    <div>
      <h4 class="font-display font-bold text-steel-800 mb-1">Feature Title</h4>
      <p class="font-body text-sm text-concrete-600">Feature description text</p>
    </div>
  </div>
</div>
```

---

## Form Elements

### Text Input

```html
<div>
  <label class="font-display font-semibold text-steel-800 mb-2 block">
    Label Text
  </label>
  <input
    type="text"
    placeholder="Enter text..."
    class="font-body w-full px-4 py-3 border-2 border-concrete-300 rounded-lg focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-500/20 transition-all duration-200"
  />
</div>
```

### Select Dropdown

```html
<select class="font-body w-full px-4 py-3 border-2 border-concrete-300 rounded-lg focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-500/20 transition-all duration-200">
  <option>Choose an option</option>
  <option>Option 1</option>
  <option>Option 2</option>
</select>
```

### Textarea

```html
<textarea
  rows="4"
  placeholder="Enter your message..."
  class="font-body w-full px-4 py-3 border-2 border-concrete-300 rounded-lg focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-500/20 transition-all duration-200 resize-none"
></textarea>
```

### Checkbox

```html
<div class="flex items-start gap-3">
  <input
    type="checkbox"
    id="checkbox-id"
    class="mt-1 w-5 h-5 text-amber-500 border-2 border-concrete-300 rounded focus:ring-2 focus:ring-amber-500/20"
  />
  <label for="checkbox-id" class="font-body text-steel-700">
    Checkbox label text
  </label>
</div>
```

### Radio Button

```html
<div class="flex items-center gap-3">
  <input
    type="radio"
    id="radio-id"
    name="radio-group"
    class="w-5 h-5 text-amber-500 border-2 border-concrete-300 focus:ring-2 focus:ring-amber-500/20"
  />
  <label for="radio-id" class="font-body text-steel-700">
    Radio option text
  </label>
</div>
```

---

## Cards

### Basic Card

```html
<div class="bg-white p-6 rounded-2xl border border-concrete-200 hover:shadow-industrial-lg transition-all duration-300 hover:-translate-y-1">
  <h3 class="font-display text-2xl font-bold text-steel-800 mb-3">Card Title</h3>
  <p class="font-body text-steel-700 mb-4">Card description text goes here.</p>
  <button class="font-display font-semibold text-amber-600 hover:text-amber-700">
    Learn More →
  </button>
</div>
```

### Featured Card (Gradient Background)

```html
<div class="bg-gradient-to-br from-amber-500 to-amber-600 p-6 rounded-2xl shadow-industrial-lg hover:shadow-industrial-xl transition-all duration-300 hover:-translate-y-1">
  <h3 class="font-display text-2xl font-bold text-white mb-3">Featured Card</h3>
  <p class="font-body text-amber-50 mb-4">Important content highlighted here.</p>
  <button class="font-display font-bold px-6 py-2 bg-white text-amber-600 rounded-lg hover:bg-amber-50 transition-colors">
    Get Started
  </button>
</div>
```

### Product Card

```html
<div class="bg-white rounded-2xl border border-concrete-200 overflow-hidden hover:shadow-industrial-lg transition-all duration-300 hover:-translate-y-1">
  <div class="h-48 bg-gradient-to-br from-steel-600 to-steel-800"></div>
  <div class="p-6">
    <div class="font-display text-xs font-bold text-rust-600 uppercase tracking-wider mb-2">Category</div>
    <h3 class="font-display text-xl font-bold text-steel-800 mb-2">Product Name</h3>
    <p class="font-body text-sm text-concrete-600 mb-4">Product description.</p>
    <div class="flex items-center justify-between">
      <span class="font-display text-2xl font-bold text-steel-900">$99</span>
      <button class="font-display font-semibold px-4 py-2 bg-amber-500 text-white rounded-lg hover:bg-amber-600 transition-colors">
        Add to Cart
      </button>
    </div>
  </div>
</div>
```

---

## Shadows

Use industrial-themed shadows for depth:

- `shadow-industrial-sm`: Subtle depth for small elements
- `shadow-industrial-md`: Medium depth for cards
- `shadow-industrial-lg`: Large depth for prominent elements
- `shadow-industrial-xl`: Extra large depth for modals/overlays

```html
<div class="shadow-industrial-md hover:shadow-industrial-lg transition-shadow duration-300">
  Card with shadow transition
</div>
```

---

## Spacing & Layout

### Container

```html
<div class="container mx-auto px-6 py-12">
  <!-- Content -->
</div>
```

### Grid Layouts

```html
<!-- 2 columns on medium screens, 3 on large -->
<div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
  <!-- Grid items -->
</div>

<!-- Responsive grid with auto-fit -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
  <!-- Grid items -->
</div>
```

### Flexbox

```html
<!-- Horizontal flex with gap -->
<div class="flex gap-4 items-center">
  <!-- Flex items -->
</div>

<!-- Vertical stack with spacing -->
<div class="flex flex-col gap-6">
  <!-- Stacked items -->
</div>
```

---

## Common Patterns

### Page Header

```html
<header class="bg-steel-900 text-white py-12 border-b-4 border-amber-500">
  <div class="container mx-auto px-6">
    <h1 class="font-display text-5xl font-bold tracking-tight mb-3">
      Page Title
    </h1>
    <p class="font-body text-steel-300 text-lg max-w-3xl">
      Page description or subtitle text.
    </p>
  </div>
</header>
```

### Section Header

```html
<h2 class="font-display text-4xl font-bold text-steel-900 mb-6 border-l-4 border-amber-500 pl-4">
  Section Title
</h2>
```

### Badge/Tag

```html
<span class="font-display text-xs font-bold px-3 py-1 bg-amber-100 text-amber-700 rounded-full uppercase tracking-wider">
  New
</span>
```

### Alert/Notice

```html
<!-- Success -->
<div class="bg-forest-50 border-l-4 border-forest-500 p-4 rounded-lg">
  <p class="font-body text-forest-800 font-medium">Success message here</p>
</div>

<!-- Warning -->
<div class="bg-amber-50 border-l-4 border-amber-500 p-4 rounded-lg">
  <p class="font-body text-amber-800 font-medium">Warning message here</p>
</div>

<!-- Error -->
<div class="bg-rust-50 border-l-4 border-rust-500 p-4 rounded-lg">
  <p class="font-body text-rust-800 font-medium">Error message here</p>
</div>
```

---

## Best Practices

1. **Use semantic colors**:
   - `amber` for CTAs and important actions
   - `rust` for secondary actions
   - `steel` for primary content
   - `forest` for success states

2. **Typography hierarchy**:
   - Always use `font-display` for headings and buttons
   - Use `font-body` for body text and UI elements
   - Maintain consistent sizing scale

3. **Spacing**:
   - Use `gap-*` utilities for consistent spacing in flex/grid
   - Prefer `space-y-*` for vertical stacking
   - Use `p-*` and `m-*` sparingly, favor layout utilities

4. **Hover states**:
   - Always include transitions: `transition-all duration-200`
   - Use subtle transforms: `hover:-translate-y-0.5` or `hover:-translate-y-1`
   - Enhance shadows on hover: `hover:shadow-industrial-lg`

5. **Accessibility**:
   - Ensure sufficient color contrast
   - Use semantic HTML elements
   - Include proper focus states with `focus:` utilities

---

## Quick Reference

### Most Common Classes

```
Colors: bg-steel-700, text-white, border-concrete-200
Fonts: font-display, font-body
Buttons: bg-amber-500 hover:bg-amber-600
Shadows: shadow-industrial-md, hover:shadow-industrial-lg
Radius: rounded-lg, rounded-xl, rounded-2xl
Transitions: transition-all duration-200
```

---

**View all components in action:** Visit `/design-system` in your browser!
