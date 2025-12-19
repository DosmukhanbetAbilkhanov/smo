<script setup>
import { ref } from 'vue'

const sections = ref([
  { id: 'colors', label: 'Color Palette' },
  { id: 'typography', label: 'Typography' },
  { id: 'buttons', label: 'Buttons' },
  { id: 'links', label: 'Links' },
  { id: 'lists', label: 'Lists' },
  { id: 'forms', label: 'Form Elements' },
  { id: 'cards', label: 'Cards' },
])

const activeSection = ref('colors')
</script>

<template>
  <div class="min-h-screen bg-concrete-50">
    <!-- Header -->
    <header class="bg-steel-900 text-white py-12 border-b-4 border-amber-500">
      <div class="container mx-auto px-6">
        <h1 class="font-display text-5xl font-bold tracking-tight mb-3">
          Industrial Refined Design System
        </h1>
        <p class="font-body text-steel-300 text-lg max-w-3xl">
          A bold, distinctive design system for building materials marketplace.
          Combining industrial strength with modern refinement.
        </p>
      </div>
    </header>

    <!-- Navigation -->
    <nav class="bg-white border-b border-concrete-200 sticky top-0 z-50 shadow-industrial-sm">
      <div class="container mx-auto px-6">
        <div class="flex gap-1 py-4 overflow-x-auto">
          <a
            v-for="section in sections"
            :key="section.id"
            :href="`#${section.id}`"
            @click="activeSection = section.id"
            class="font-display font-semibold px-5 py-2.5 rounded-lg transition-all duration-200 whitespace-nowrap"
            :class="activeSection === section.id
              ? 'bg-steel-700 text-white'
              : 'text-steel-600 hover:bg-concrete-100 hover:text-steel-900'"
          >
            {{ section.label }}
          </a>
        </div>
      </div>
    </nav>

    <!-- Content -->
    <main class="container mx-auto px-6 py-12">

      <!-- Colors Section -->
      <section id="colors" class="mb-20">
        <h2 class="font-display text-4xl font-bold text-steel-900 mb-6 border-l-4 border-amber-500 pl-4">
          Color Palette
        </h2>

        <div class="grid gap-8">
          <!-- Steel - Primary -->
          <div>
            <h3 class="font-display text-2xl font-semibold text-steel-800 mb-4">Steel (Primary)</h3>
            <div class="grid grid-cols-2 md:grid-cols-5 lg:grid-cols-9 gap-3">
              <div class="space-y-2">
                <div class="h-20 bg-steel-50 rounded-lg border border-concrete-300"></div>
                <p class="font-body text-sm font-medium text-steel-700">steel-50</p>
              </div>
              <div class="space-y-2">
                <div class="h-20 bg-steel-100 rounded-lg"></div>
                <p class="font-body text-sm font-medium text-steel-700">steel-100</p>
              </div>
              <div class="space-y-2">
                <div class="h-20 bg-steel-200 rounded-lg"></div>
                <p class="font-body text-sm font-medium text-steel-700">steel-200</p>
              </div>
              <div class="space-y-2">
                <div class="h-20 bg-steel-300 rounded-lg"></div>
                <p class="font-body text-sm font-medium text-steel-700">steel-300</p>
              </div>
              <div class="space-y-2">
                <div class="h-20 bg-steel-400 rounded-lg"></div>
                <p class="font-body text-sm font-medium text-white">steel-400</p>
              </div>
              <div class="space-y-2">
                <div class="h-20 bg-steel-500 rounded-lg"></div>
                <p class="font-body text-sm font-medium text-white">steel-500</p>
              </div>
              <div class="space-y-2">
                <div class="h-20 bg-steel-600 rounded-lg"></div>
                <p class="font-body text-sm font-medium text-white">steel-600</p>
              </div>
              <div class="space-y-2">
                <div class="h-20 bg-steel-700 rounded-lg"></div>
                <p class="font-body text-sm font-medium text-white">steel-700</p>
              </div>
              <div class="space-y-2">
                <div class="h-20 bg-steel-800 rounded-lg"></div>
                <p class="font-body text-sm font-medium text-white">steel-800</p>
              </div>
            </div>
          </div>

          <!-- Rust - Secondary -->
          <div>
            <h3 class="font-display text-2xl font-semibold text-steel-800 mb-4">Rust (Secondary)</h3>
            <div class="grid grid-cols-2 md:grid-cols-5 lg:grid-cols-9 gap-3">
              <div class="space-y-2">
                <div class="h-20 bg-rust-50 rounded-lg border border-concrete-300"></div>
                <p class="font-body text-sm font-medium text-steel-700">rust-50</p>
              </div>
              <div class="space-y-2">
                <div class="h-20 bg-rust-100 rounded-lg"></div>
                <p class="font-body text-sm font-medium text-steel-700">rust-100</p>
              </div>
              <div class="space-y-2">
                <div class="h-20 bg-rust-200 rounded-lg"></div>
                <p class="font-body text-sm font-medium text-steel-700">rust-200</p>
              </div>
              <div class="space-y-2">
                <div class="h-20 bg-rust-300 rounded-lg"></div>
                <p class="font-body text-sm font-medium text-steel-700">rust-300</p>
              </div>
              <div class="space-y-2">
                <div class="h-20 bg-rust-400 rounded-lg"></div>
                <p class="font-body text-sm font-medium text-white">rust-400</p>
              </div>
              <div class="space-y-2">
                <div class="h-20 bg-rust-500 rounded-lg"></div>
                <p class="font-body text-sm font-medium text-white">rust-500</p>
              </div>
              <div class="space-y-2">
                <div class="h-20 bg-rust-600 rounded-lg"></div>
                <p class="font-body text-sm font-medium text-white">rust-600</p>
              </div>
              <div class="space-y-2">
                <div class="h-20 bg-rust-700 rounded-lg"></div>
                <p class="font-body text-sm font-medium text-white">rust-700</p>
              </div>
              <div class="space-y-2">
                <div class="h-20 bg-rust-800 rounded-lg"></div>
                <p class="font-body text-sm font-medium text-white">rust-800</p>
              </div>
            </div>
          </div>

          <!-- Amber - Accent -->
          <div>
            <h3 class="font-display text-2xl font-semibold text-steel-800 mb-4">Amber (Accent/CTA)</h3>
            <div class="grid grid-cols-2 md:grid-cols-5 lg:grid-cols-9 gap-3">
              <div class="space-y-2">
                <div class="h-20 bg-amber-50 rounded-lg border border-concrete-300"></div>
                <p class="font-body text-sm font-medium text-steel-700">amber-50</p>
              </div>
              <div class="space-y-2">
                <div class="h-20 bg-amber-100 rounded-lg"></div>
                <p class="font-body text-sm font-medium text-steel-700">amber-100</p>
              </div>
              <div class="space-y-2">
                <div class="h-20 bg-amber-200 rounded-lg"></div>
                <p class="font-body text-sm font-medium text-steel-700">amber-200</p>
              </div>
              <div class="space-y-2">
                <div class="h-20 bg-amber-300 rounded-lg"></div>
                <p class="font-body text-sm font-medium text-steel-700">amber-300</p>
              </div>
              <div class="space-y-2">
                <div class="h-20 bg-amber-400 rounded-lg"></div>
                <p class="font-body text-sm font-medium text-steel-800">amber-400</p>
              </div>
              <div class="space-y-2">
                <div class="h-20 bg-amber-500 rounded-lg"></div>
                <p class="font-body text-sm font-medium text-white">amber-500</p>
              </div>
              <div class="space-y-2">
                <div class="h-20 bg-amber-600 rounded-lg"></div>
                <p class="font-body text-sm font-medium text-white">amber-600</p>
              </div>
              <div class="space-y-2">
                <div class="h-20 bg-amber-700 rounded-lg"></div>
                <p class="font-body text-sm font-medium text-white">amber-700</p>
              </div>
              <div class="space-y-2">
                <div class="h-20 bg-amber-800 rounded-lg"></div>
                <p class="font-body text-sm font-medium text-white">amber-800</p>
              </div>
            </div>
          </div>

          <!-- Forest - Success -->
          <div>
            <h3 class="font-display text-2xl font-semibold text-steel-800 mb-4">Forest (Success)</h3>
            <div class="grid grid-cols-2 md:grid-cols-5 lg:grid-cols-9 gap-3">
              <div class="space-y-2">
                <div class="h-20 bg-forest-50 rounded-lg border border-concrete-300"></div>
                <p class="font-body text-sm font-medium text-steel-700">forest-50</p>
              </div>
              <div class="space-y-2">
                <div class="h-20 bg-forest-100 rounded-lg"></div>
                <p class="font-body text-sm font-medium text-steel-700">forest-100</p>
              </div>
              <div class="space-y-2">
                <div class="h-20 bg-forest-300 rounded-lg"></div>
                <p class="font-body text-sm font-medium text-steel-700">forest-300</p>
              </div>
              <div class="space-y-2">
                <div class="h-20 bg-forest-500 rounded-lg"></div>
                <p class="font-body text-sm font-medium text-white">forest-500</p>
              </div>
              <div class="space-y-2">
                <div class="h-20 bg-forest-700 rounded-lg"></div>
                <p class="font-body text-sm font-medium text-white">forest-700</p>
              </div>
            </div>
          </div>

          <!-- Concrete - Neutral -->
          <div>
            <h3 class="font-display text-2xl font-semibold text-steel-800 mb-4">Concrete (Neutral)</h3>
            <div class="grid grid-cols-2 md:grid-cols-5 lg:grid-cols-9 gap-3">
              <div class="space-y-2">
                <div class="h-20 bg-concrete-50 rounded-lg border border-concrete-300"></div>
                <p class="font-body text-sm font-medium text-steel-700">concrete-50</p>
              </div>
              <div class="space-y-2">
                <div class="h-20 bg-concrete-100 rounded-lg"></div>
                <p class="font-body text-sm font-medium text-steel-700">concrete-100</p>
              </div>
              <div class="space-y-2">
                <div class="h-20 bg-concrete-200 rounded-lg"></div>
                <p class="font-body text-sm font-medium text-steel-700">concrete-200</p>
              </div>
              <div class="space-y-2">
                <div class="h-20 bg-concrete-400 rounded-lg"></div>
                <p class="font-body text-sm font-medium text-white">concrete-400</p>
              </div>
              <div class="space-y-2">
                <div class="h-20 bg-concrete-600 rounded-lg"></div>
                <p class="font-body text-sm font-medium text-white">concrete-600</p>
              </div>
              <div class="space-y-2">
                <div class="h-20 bg-concrete-800 rounded-lg"></div>
                <p class="font-body text-sm font-medium text-white">concrete-800</p>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Typography Section -->
      <section id="typography" class="mb-20">
        <h2 class="font-display text-4xl font-bold text-steel-900 mb-6 border-l-4 border-rust-500 pl-4">
          Typography
        </h2>

        <div class="grid lg:grid-cols-2 gap-8">
          <!-- Display Font -->
          <div class="bg-white p-8 rounded-2xl border border-concrete-200">
            <h3 class="font-display text-2xl font-bold text-steel-800 mb-6">Display Font: Rajdhani</h3>
            <div class="space-y-4">
              <div>
                <p class="font-display text-6xl font-bold text-steel-900">Aa</p>
                <p class="font-body text-sm text-concrete-600 mt-2">Industrial • Geometric • Strong</p>
              </div>
              <p class="font-display text-xl text-steel-700">
                Used for headings, buttons, and emphasis. Brings bold industrial character.
              </p>
              <code class="block bg-concrete-100 px-4 py-3 rounded-lg font-mono text-sm text-steel-800">
                className="font-display"
              </code>
            </div>
          </div>

          <!-- Body Font -->
          <div class="bg-white p-8 rounded-2xl border border-concrete-200">
            <h3 class="font-display text-2xl font-bold text-steel-800 mb-6">Body Font: Work Sans</h3>
            <div class="space-y-4">
              <div>
                <p class="font-body text-6xl font-semibold text-steel-900">Aa</p>
                <p class="font-body text-sm text-concrete-600 mt-2">Clean • Professional • Readable</p>
              </div>
              <p class="font-body text-xl text-steel-700">
                Used for body text, descriptions, and UI elements. Ensures excellent readability.
              </p>
              <code class="block bg-concrete-100 px-4 py-3 rounded-lg font-mono text-sm text-steel-800">
                className="font-body"
              </code>
            </div>
          </div>
        </div>

        <!-- Typography Scale -->
        <div class="mt-8 bg-white p-8 rounded-2xl border border-concrete-200">
          <h3 class="font-display text-2xl font-bold text-steel-800 mb-6">Typography Scale</h3>
          <div class="space-y-6">
            <div class="border-b border-concrete-200 pb-4">
              <h1 class="font-display text-6xl font-bold text-steel-900 mb-2">Heading 1</h1>
              <code class="font-mono text-sm text-concrete-600">font-display text-6xl font-bold</code>
            </div>
            <div class="border-b border-concrete-200 pb-4">
              <h2 class="font-display text-5xl font-bold text-steel-900 mb-2">Heading 2</h2>
              <code class="font-mono text-sm text-concrete-600">font-display text-5xl font-bold</code>
            </div>
            <div class="border-b border-concrete-200 pb-4">
              <h3 class="font-display text-4xl font-semibold text-steel-900 mb-2">Heading 3</h3>
              <code class="font-mono text-sm text-concrete-600">font-display text-4xl font-semibold</code>
            </div>
            <div class="border-b border-concrete-200 pb-4">
              <h4 class="font-display text-3xl font-semibold text-steel-800 mb-2">Heading 4</h4>
              <code class="font-mono text-sm text-concrete-600">font-display text-3xl font-semibold</code>
            </div>
            <div class="border-b border-concrete-200 pb-4">
              <h5 class="font-display text-2xl font-semibold text-steel-800 mb-2">Heading 5</h5>
              <code class="font-mono text-sm text-concrete-600">font-display text-2xl font-semibold</code>
            </div>
            <div class="border-b border-concrete-200 pb-4">
              <h6 class="font-display text-xl font-semibold text-steel-800 mb-2">Heading 6</h6>
              <code class="font-mono text-sm text-concrete-600">font-display text-xl font-semibold</code>
            </div>
            <div class="border-b border-concrete-200 pb-4">
              <p class="font-body text-lg text-steel-700 mb-2">Large body text - perfect for introductions and lead paragraphs.</p>
              <code class="font-mono text-sm text-concrete-600">font-body text-lg</code>
            </div>
            <div class="border-b border-concrete-200 pb-4">
              <p class="font-body text-base text-steel-700 mb-2">Regular body text - the standard for most content and readable paragraphs.</p>
              <code class="font-mono text-sm text-concrete-600">font-body text-base</code>
            </div>
            <div class="pb-4">
              <p class="font-body text-sm text-concrete-600 mb-2">Small text - ideal for captions, labels, and secondary information.</p>
              <code class="font-mono text-sm text-concrete-600">font-body text-sm text-concrete-600</code>
            </div>
          </div>
        </div>
      </section>

      <!-- Buttons Section -->
      <section id="buttons" class="mb-20">
        <h2 class="font-display text-4xl font-bold text-steel-900 mb-6 border-l-4 border-amber-500 pl-4">
          Buttons
        </h2>

        <div class="space-y-8">
          <!-- Primary Buttons -->
          <div class="bg-white p-8 rounded-2xl border border-concrete-200">
            <h3 class="font-display text-2xl font-semibold text-steel-800 mb-6">Primary Buttons</h3>
            <div class="flex flex-wrap gap-4 mb-6">
              <button class="font-display font-bold px-8 py-4 bg-amber-500 text-white rounded-xl hover:bg-amber-600 hover:shadow-industrial-lg transition-all duration-200 hover:-translate-y-0.5">
                Primary Large
              </button>
              <button class="font-display font-bold px-6 py-3 bg-amber-500 text-white rounded-lg hover:bg-amber-600 hover:shadow-industrial-md transition-all duration-200 hover:-translate-y-0.5">
                Primary Medium
              </button>
              <button class="font-display font-semibold px-4 py-2 bg-amber-500 text-white rounded-lg hover:bg-amber-600 transition-all duration-200">
                Primary Small
              </button>
            </div>
            <code class="block bg-concrete-100 px-4 py-3 rounded-lg font-mono text-sm text-steel-800 overflow-x-auto">
              className="font-display font-bold px-8 py-4 bg-amber-500 text-white rounded-xl hover:bg-amber-600 hover:shadow-industrial-lg transition-all duration-200 hover:-translate-y-0.5"
            </code>
          </div>

          <!-- Secondary Buttons -->
          <div class="bg-white p-8 rounded-2xl border border-concrete-200">
            <h3 class="font-display text-2xl font-semibold text-steel-800 mb-6">Secondary Buttons</h3>
            <div class="flex flex-wrap gap-4 mb-6">
              <button class="font-display font-bold px-8 py-4 bg-rust-500 text-white rounded-xl hover:bg-rust-600 hover:shadow-industrial-lg transition-all duration-200 hover:-translate-y-0.5">
                Secondary Large
              </button>
              <button class="font-display font-bold px-6 py-3 bg-rust-500 text-white rounded-lg hover:bg-rust-600 hover:shadow-industrial-md transition-all duration-200 hover:-translate-y-0.5">
                Secondary Medium
              </button>
              <button class="font-display font-semibold px-4 py-2 bg-rust-500 text-white rounded-lg hover:bg-rust-600 transition-all duration-200">
                Secondary Small
              </button>
            </div>
            <code class="block bg-concrete-100 px-4 py-3 rounded-lg font-mono text-sm text-steel-800 overflow-x-auto">
              className="font-display font-bold px-8 py-4 bg-rust-500 text-white rounded-xl hover:bg-rust-600 hover:shadow-industrial-lg transition-all duration-200 hover:-translate-y-0.5"
            </code>
          </div>

          <!-- Outline Buttons -->
          <div class="bg-white p-8 rounded-2xl border border-concrete-200">
            <h3 class="font-display text-2xl font-semibold text-steel-800 mb-6">Outline Buttons</h3>
            <div class="flex flex-wrap gap-4 mb-6">
              <button class="font-display font-bold px-8 py-4 bg-transparent text-steel-700 border-2 border-steel-700 rounded-xl hover:bg-steel-700 hover:text-white transition-all duration-200">
                Outline Large
              </button>
              <button class="font-display font-bold px-6 py-3 bg-transparent text-steel-700 border-2 border-steel-700 rounded-lg hover:bg-steel-700 hover:text-white transition-all duration-200">
                Outline Medium
              </button>
              <button class="font-display font-semibold px-4 py-2 bg-transparent text-steel-700 border-2 border-steel-700 rounded-lg hover:bg-steel-700 hover:text-white transition-all duration-200">
                Outline Small
              </button>
            </div>
            <code class="block bg-concrete-100 px-4 py-3 rounded-lg font-mono text-sm text-steel-800 overflow-x-auto">
              className="font-display font-bold px-8 py-4 bg-transparent text-steel-700 border-2 border-steel-700 rounded-xl hover:bg-steel-700 hover:text-white transition-all duration-200"
            </code>
          </div>

          <!-- Ghost Buttons -->
          <div class="bg-white p-8 rounded-2xl border border-concrete-200">
            <h3 class="font-display text-2xl font-semibold text-steel-800 mb-6">Ghost Buttons</h3>
            <div class="flex flex-wrap gap-4 mb-6">
              <button class="font-display font-bold px-8 py-4 bg-transparent text-steel-700 hover:bg-concrete-100 rounded-xl transition-all duration-200">
                Ghost Large
              </button>
              <button class="font-display font-bold px-6 py-3 bg-transparent text-steel-700 hover:bg-concrete-100 rounded-lg transition-all duration-200">
                Ghost Medium
              </button>
              <button class="font-display font-semibold px-4 py-2 bg-transparent text-steel-700 hover:bg-concrete-100 rounded-lg transition-all duration-200">
                Ghost Small
              </button>
            </div>
            <code class="block bg-concrete-100 px-4 py-3 rounded-lg font-mono text-sm text-steel-800 overflow-x-auto">
              className="font-display font-bold px-8 py-4 bg-transparent text-steel-700 hover:bg-concrete-100 rounded-xl transition-all duration-200"
            </code>
          </div>

          <!-- Disabled State -->
          <div class="bg-white p-8 rounded-2xl border border-concrete-200">
            <h3 class="font-display text-2xl font-semibold text-steel-800 mb-6">Disabled State</h3>
            <div class="flex flex-wrap gap-4 mb-6">
              <button disabled class="font-display font-bold px-8 py-4 bg-concrete-300 text-concrete-500 rounded-xl cursor-not-allowed">
                Disabled Button
              </button>
            </div>
            <code class="block bg-concrete-100 px-4 py-3 rounded-lg font-mono text-sm text-steel-800 overflow-x-auto">
              className="font-display font-bold px-8 py-4 bg-concrete-300 text-concrete-500 rounded-xl cursor-not-allowed" disabled
            </code>
          </div>
        </div>
      </section>

      <!-- Links Section -->
      <section id="links" class="mb-20">
        <h2 class="font-display text-4xl font-bold text-steel-900 mb-6 border-l-4 border-rust-500 pl-4">
          Links
        </h2>

        <div class="space-y-8">
          <!-- Primary Links -->
          <div class="bg-white p-8 rounded-2xl border border-concrete-200">
            <h3 class="font-display text-2xl font-semibold text-steel-800 mb-6">Primary Links</h3>
            <div class="space-y-4 mb-6">
              <a href="#" class="font-body text-lg font-semibold text-steel-700 hover:text-amber-600 underline decoration-2 underline-offset-4 decoration-amber-500 hover:decoration-amber-600 transition-colors duration-200 block">
                Standard underlined link with hover effect
              </a>
              <a href="#" class="font-body text-base font-medium text-steel-700 hover:text-rust-600 transition-colors duration-200 block">
                Simple link without underline
              </a>
            </div>
            <code class="block bg-concrete-100 px-4 py-3 rounded-lg font-mono text-sm text-steel-800 overflow-x-auto">
              className="font-body text-lg font-semibold text-steel-700 hover:text-amber-600 underline decoration-2 underline-offset-4 decoration-amber-500 transition-colors duration-200"
            </code>
          </div>

          <!-- Inline Links -->
          <div class="bg-white p-8 rounded-2xl border border-concrete-200">
            <h3 class="font-display text-2xl font-semibold text-steel-800 mb-6">Inline Links</h3>
            <p class="font-body text-base text-steel-700 mb-6 leading-relaxed">
              This is a paragraph with an <a href="#" class="font-semibold text-amber-600 hover:text-amber-700 underline decoration-amber-500 underline-offset-2 transition-colors duration-200">inline link example</a> that demonstrates how links work within body text. You can also have <a href="#" class="font-semibold text-rust-600 hover:text-rust-700 underline decoration-rust-500 underline-offset-2 transition-colors duration-200">multiple styled links</a> in the same paragraph.
            </p>
            <code class="block bg-concrete-100 px-4 py-3 rounded-lg font-mono text-sm text-steel-800 overflow-x-auto">
              className="font-semibold text-amber-600 hover:text-amber-700 underline decoration-amber-500 underline-offset-2 transition-colors duration-200"
            </code>
          </div>

          <!-- Navigation Links -->
          <div class="bg-white p-8 rounded-2xl border border-concrete-200">
            <h3 class="font-display text-2xl font-semibold text-steel-800 mb-6">Navigation Links</h3>
            <nav class="flex gap-6 mb-6">
              <a href="#" class="font-display font-semibold text-steel-700 hover:text-amber-600 transition-colors duration-200 border-b-2 border-transparent hover:border-amber-500 pb-1">
                Products
              </a>
              <a href="#" class="font-display font-semibold text-steel-700 hover:text-amber-600 transition-colors duration-200 border-b-2 border-transparent hover:border-amber-500 pb-1">
                Categories
              </a>
              <a href="#" class="font-display font-semibold text-amber-600 border-b-2 border-amber-500 pb-1">
                Active Page
              </a>
            </nav>
            <code class="block bg-concrete-100 px-4 py-3 rounded-lg font-mono text-sm text-steel-800 overflow-x-auto">
              className="font-display font-semibold text-steel-700 hover:text-amber-600 transition-colors duration-200 border-b-2 border-transparent hover:border-amber-500 pb-1"
            </code>
          </div>
        </div>
      </section>

      <!-- Lists Section -->
      <section id="lists" class="mb-20">
        <h2 class="font-display text-4xl font-bold text-steel-900 mb-6 border-l-4 border-amber-500 pl-4">
          Lists
        </h2>

        <div class="space-y-8">
          <!-- Unordered Lists -->
          <div class="bg-white p-8 rounded-2xl border border-concrete-200">
            <h3 class="font-display text-2xl font-semibold text-steel-800 mb-6">Unordered Lists</h3>
            <ul class="space-y-3 mb-6">
              <li class="font-body text-steel-700 flex items-start gap-3">
                <span class="flex-shrink-0 w-2 h-2 bg-amber-500 rounded-full mt-2"></span>
                <span>Premium building materials sourced from trusted suppliers</span>
              </li>
              <li class="font-body text-steel-700 flex items-start gap-3">
                <span class="flex-shrink-0 w-2 h-2 bg-amber-500 rounded-full mt-2"></span>
                <span>Fast delivery and professional installation services</span>
              </li>
              <li class="font-body text-steel-700 flex items-start gap-3">
                <span class="flex-shrink-0 w-2 h-2 bg-amber-500 rounded-full mt-2"></span>
                <span>Expert consultation and project planning assistance</span>
              </li>
            </ul>
            <code class="block bg-concrete-100 px-4 py-3 rounded-lg font-mono text-sm text-steel-800 overflow-x-auto">
              &lt;li className="font-body text-steel-700 flex items-start gap-3"&gt;
                &lt;span className="flex-shrink-0 w-2 h-2 bg-amber-500 rounded-full mt-2"&gt;&lt;/span&gt;
                &lt;span&gt;List item text&lt;/span&gt;
              &lt;/li&gt;
            </code>
          </div>

          <!-- Ordered Lists -->
          <div class="bg-white p-8 rounded-2xl border border-concrete-200">
            <h3 class="font-display text-2xl font-semibold text-steel-800 mb-6">Ordered Lists</h3>
            <ol class="space-y-3 mb-6">
              <li class="font-body text-steel-700 flex items-start gap-4">
                <span class="flex-shrink-0 font-display font-bold text-amber-600 text-lg">1.</span>
                <span>Browse our extensive catalog of building materials</span>
              </li>
              <li class="font-body text-steel-700 flex items-start gap-4">
                <span class="flex-shrink-0 font-display font-bold text-amber-600 text-lg">2.</span>
                <span>Add items to your cart and request a quote</span>
              </li>
              <li class="font-body text-steel-700 flex items-start gap-4">
                <span class="flex-shrink-0 font-display font-bold text-amber-600 text-lg">3.</span>
                <span>Schedule delivery or pickup at your convenience</span>
              </li>
            </ol>
            <code class="block bg-concrete-100 px-4 py-3 rounded-lg font-mono text-sm text-steel-800 overflow-x-auto">
              &lt;li className="font-body text-steel-700 flex items-start gap-4"&gt;
                &lt;span className="flex-shrink-0 font-display font-bold text-amber-600 text-lg"&gt;1.&lt;/span&gt;
                &lt;span&gt;List item text&lt;/span&gt;
              &lt;/li&gt;
            </code>
          </div>

          <!-- Feature Lists -->
          <div class="bg-white p-8 rounded-2xl border border-concrete-200">
            <h3 class="font-display text-2xl font-semibold text-steel-800 mb-6">Feature Lists</h3>
            <div class="space-y-4 mb-6">
              <div class="flex items-start gap-4 p-4 bg-concrete-50 rounded-lg border border-concrete-200">
                <div class="flex-shrink-0 w-10 h-10 bg-amber-500 rounded-lg flex items-center justify-center">
                  <span class="text-white font-display font-bold">✓</span>
                </div>
                <div>
                  <h4 class="font-display font-bold text-steel-800 mb-1">Quality Assurance</h4>
                  <p class="font-body text-sm text-concrete-600">All products undergo rigorous quality testing</p>
                </div>
              </div>
              <div class="flex items-start gap-4 p-4 bg-concrete-50 rounded-lg border border-concrete-200">
                <div class="flex-shrink-0 w-10 h-10 bg-rust-500 rounded-lg flex items-center justify-center">
                  <span class="text-white font-display font-bold">✓</span>
                </div>
                <div>
                  <h4 class="font-display font-bold text-steel-800 mb-1">Fast Shipping</h4>
                  <p class="font-body text-sm text-concrete-600">Next-day delivery available in most areas</p>
                </div>
              </div>
            </div>
            <code class="block bg-concrete-100 px-4 py-3 rounded-lg font-mono text-sm text-steel-800 overflow-x-auto">
              &lt;div className="flex items-start gap-4 p-4 bg-concrete-50 rounded-lg border border-concrete-200"&gt;...&lt;/div&gt;
            </code>
          </div>
        </div>
      </section>

      <!-- Forms Section -->
      <section id="forms" class="mb-20">
        <h2 class="font-display text-4xl font-bold text-steel-900 mb-6 border-l-4 border-rust-500 pl-4">
          Form Elements
        </h2>

        <div class="bg-white p-8 rounded-2xl border border-concrete-200">
          <div class="grid lg:grid-cols-2 gap-6">
            <!-- Text Input -->
            <div>
              <label class="font-display font-semibold text-steel-800 mb-2 block">
                Text Input
              </label>
              <input
                type="text"
                placeholder="Enter your name"
                class="font-body w-full px-4 py-3 border-2 border-concrete-300 rounded-lg focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-500/20 transition-all duration-200"
              />
            </div>

            <!-- Email Input -->
            <div>
              <label class="font-display font-semibold text-steel-800 mb-2 block">
                Email Input
              </label>
              <input
                type="email"
                placeholder="your@email.com"
                class="font-body w-full px-4 py-3 border-2 border-concrete-300 rounded-lg focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-500/20 transition-all duration-200"
              />
            </div>

            <!-- Select -->
            <div>
              <label class="font-display font-semibold text-steel-800 mb-2 block">
                Select Dropdown
              </label>
              <select class="font-body w-full px-4 py-3 border-2 border-concrete-300 rounded-lg focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-500/20 transition-all duration-200">
                <option>Choose an option</option>
                <option>Cement</option>
                <option>Steel</option>
                <option>Lumber</option>
              </select>
            </div>

            <!-- Textarea -->
            <div>
              <label class="font-display font-semibold text-steel-800 mb-2 block">
                Textarea
              </label>
              <textarea
                rows="3"
                placeholder="Enter your message..."
                class="font-body w-full px-4 py-3 border-2 border-concrete-300 rounded-lg focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-500/20 transition-all duration-200 resize-none"
              ></textarea>
            </div>

            <!-- Checkbox -->
            <div class="flex items-start gap-3">
              <input
                type="checkbox"
                id="terms"
                class="mt-1 w-5 h-5 text-amber-500 border-2 border-concrete-300 rounded focus:ring-2 focus:ring-amber-500/20"
              />
              <label for="terms" class="font-body text-steel-700">
                I agree to the terms and conditions
              </label>
            </div>

            <!-- Radio -->
            <div class="space-y-3">
              <label class="font-display font-semibold text-steel-800 block">
                Delivery Method
              </label>
              <div class="flex items-center gap-3">
                <input
                  type="radio"
                  id="pickup"
                  name="delivery"
                  class="w-5 h-5 text-amber-500 border-2 border-concrete-300 focus:ring-2 focus:ring-amber-500/20"
                />
                <label for="pickup" class="font-body text-steel-700">Pickup</label>
              </div>
              <div class="flex items-center gap-3">
                <input
                  type="radio"
                  id="delivery"
                  name="delivery"
                  class="w-5 h-5 text-amber-500 border-2 border-concrete-300 focus:ring-2 focus:ring-amber-500/20"
                />
                <label for="delivery" class="font-body text-steel-700">Delivery</label>
              </div>
            </div>
          </div>

          <div class="mt-8">
            <code class="block bg-concrete-100 px-4 py-3 rounded-lg font-mono text-sm text-steel-800 overflow-x-auto">
              className="font-body w-full px-4 py-3 border-2 border-concrete-300 rounded-lg focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-500/20 transition-all duration-200"
            </code>
          </div>
        </div>
      </section>

      <!-- Cards Section -->
      <section id="cards" class="mb-20">
        <h2 class="font-display text-4xl font-bold text-steel-900 mb-6 border-l-4 border-amber-500 pl-4">
          Cards
        </h2>

        <div class="grid lg:grid-cols-3 gap-6">
          <!-- Basic Card -->
          <div class="bg-white p-6 rounded-2xl border border-concrete-200 hover:shadow-industrial-lg transition-all duration-300 hover:-translate-y-1">
            <h3 class="font-display text-2xl font-bold text-steel-800 mb-3">Basic Card</h3>
            <p class="font-body text-steel-700 mb-4">
              Simple card with hover effect and clean styling.
            </p>
            <button class="font-display font-semibold text-amber-600 hover:text-amber-700">
              Learn More →
            </button>
          </div>

          <!-- Featured Card -->
          <div class="bg-gradient-to-br from-amber-500 to-amber-600 p-6 rounded-2xl shadow-industrial-lg hover:shadow-industrial-xl transition-all duration-300 hover:-translate-y-1">
            <h3 class="font-display text-2xl font-bold text-white mb-3">Featured Card</h3>
            <p class="font-body text-amber-50 mb-4">
              Eye-catching card for highlighting important content.
            </p>
            <button class="font-display font-bold px-6 py-2 bg-white text-amber-600 rounded-lg hover:bg-amber-50 transition-colors">
              Get Started
            </button>
          </div>

          <!-- Product Card -->
          <div class="bg-white rounded-2xl border border-concrete-200 overflow-hidden hover:shadow-industrial-lg transition-all duration-300 hover:-translate-y-1">
            <div class="h-40 bg-gradient-to-br from-steel-600 to-steel-800"></div>
            <div class="p-6">
              <div class="font-display text-xs font-bold text-rust-600 uppercase tracking-wider mb-2">Category</div>
              <h3 class="font-display text-xl font-bold text-steel-800 mb-2">Product Name</h3>
              <p class="font-body text-sm text-concrete-600 mb-4">
                Brief product description goes here.
              </p>
              <div class="flex items-center justify-between">
                <span class="font-display text-2xl font-bold text-steel-900">$99</span>
                <button class="font-display font-semibold px-4 py-2 bg-amber-500 text-white rounded-lg hover:bg-amber-600 transition-colors">
                  Add to Cart
                </button>
              </div>
            </div>
          </div>
        </div>

        <div class="mt-8 bg-white p-6 rounded-2xl border border-concrete-200">
          <code class="block font-mono text-sm text-steel-800 overflow-x-auto">
            className="bg-white p-6 rounded-2xl border border-concrete-200 hover:shadow-industrial-lg transition-all duration-300 hover:-translate-y-1"
          </code>
        </div>
      </section>

    </main>

    <!-- Footer -->
    <footer class="bg-steel-900 text-white py-12 border-t-4 border-rust-500">
      <div class="container mx-auto px-6 text-center">
        <p class="font-display text-lg">
          Industrial Refined Design System • Building Materials Marketplace
        </p>
        <p class="font-body text-steel-400 mt-2">
          Distinctive, bold, and production-ready
        </p>
      </div>
    </footer>
  </div>
</template>
