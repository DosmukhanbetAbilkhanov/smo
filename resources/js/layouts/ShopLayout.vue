<script setup lang="ts">
import CitySelectionModal from '@/components/CitySelectionModal.vue';
import MainNav from '@/components/navigation/MainNav.vue';
import { Link } from '@inertiajs/vue3';
import {
    Facebook,
    Instagram,
    Mail,
    MapPin,
    Phone,
    Twitter,
} from 'lucide-vue-next';
import { useLocale } from '@/composables/useLocale';

const { t } = useLocale();

interface Props {
    showFooter?: boolean;
}

withDefaults(defineProps<Props>(), {
    showFooter: true,
});

const currentYear = new Date().getFullYear();
</script>

<template>
    <div class="flex min-h-screen flex-col">
        <!-- City Selection Modal -->
        <CitySelectionModal />

        <!-- Header -->
        <header>
            <MainNav />
        </header>

        <!-- Main Content -->
        <main class="flex-1">
            <slot />
        </main>

        <!-- Footer -->
        <footer v-if="showFooter" class="border-t bg-muted/50">
            <div class="container mx-auto px-4 py-12">
                <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-4">
                    <!-- About -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold">{{ t({ ru: 'О SMO', kz: 'SMO туралы' }) }}</h3>
                        <p class="text-sm text-muted-foreground">
                            {{ t({ ru: 'Надежный маркетплейс строительных материалов. Качественная продукция от проверенных продавцов по всему Казахстану.', kz: 'Құрылыс материалдарының сенімді маркетплейсі. Қазақстан бойынша тексерілген сатушылардан сапалы өнім.' }) }}
                        </p>
                    </div>

                    <!-- Quick Links -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold">{{ t({ ru: 'Быстрые ссылки', kz: 'Жылдам сілтемелер' }) }}</h3>
                        <ul class="space-y-2 text-sm">
                            <li>
                                <Link
                                    href="/categories"
                                    class="text-muted-foreground transition-colors hover:text-foreground"
                                >
                                    {{ t({ ru: 'Категории', kz: 'Санаттар' }) }}
                                </Link>
                            </li>
                            <li>
                                <Link
                                    href="/products"
                                    class="text-muted-foreground transition-colors hover:text-foreground"
                                >
                                    {{ t({ ru: 'Товары', kz: 'Тауарлар' }) }}
                                </Link>
                            </li>
                            <li>
                                <Link
                                    href="/about"
                                    class="text-muted-foreground transition-colors hover:text-foreground"
                                >
                                    {{ t({ ru: 'О нас', kz: 'Біз туралы' }) }}
                                </Link>
                            </li>
                            <li>
                                <Link
                                    href="/contact"
                                    class="text-muted-foreground transition-colors hover:text-foreground"
                                >
                                    {{ t({ ru: 'Контакты', kz: 'Байланыс' }) }}
                                </Link>
                            </li>
                        </ul>
                    </div>

                    <!-- Support -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold">{{ t({ ru: 'Поддержка', kz: 'Қолдау' }) }}</h3>
                        <ul class="space-y-2 text-sm">
                            <li>
                                <Link
                                    href="/help"
                                    class="text-muted-foreground transition-colors hover:text-foreground"
                                >
                                    {{ t({ ru: 'Центр помощи', kz: 'Көмек орталығы' }) }}
                                </Link>
                            </li>
                            <li>
                                <Link
                                    href="/faq"
                                    class="text-muted-foreground transition-colors hover:text-foreground"
                                >
                                    {{ t({ ru: 'FAQ', kz: 'Жиі қойылатын сұрақтар' }) }}
                                </Link>
                            </li>
                            <li>
                                <Link
                                    href="/privacy"
                                    class="text-muted-foreground transition-colors hover:text-foreground"
                                >
                                    {{ t({ ru: 'Политика конфиденциальности', kz: 'Құпиялылық саясаты' }) }}
                                </Link>
                            </li>
                            <li>
                                <Link
                                    href="/terms"
                                    class="text-muted-foreground transition-colors hover:text-foreground"
                                >
                                    {{ t({ ru: 'Условия использования', kz: 'Пайдалану шарттары' }) }}
                                </Link>
                            </li>
                        </ul>
                    </div>

                    <!-- Contact -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold">{{ t({ ru: 'Свяжитесь с нами', kz: 'Бізбен байланысыңыз' }) }}</h3>
                        <ul class="space-y-3 text-sm text-muted-foreground">
                            <li class="flex items-start gap-2">
                                <MapPin :size="16" class="mt-0.5 shrink-0" />
                                <span>{{ t({ ru: 'Алматы, Казахстан', kz: 'Алматы, Қазақстан' }) }}</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <Phone :size="16" class="shrink-0" />
                                <span>+7 (777) 123-45-67</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <Mail :size="16" class="shrink-0" />
                                <span>info@smo.kz</span>
                            </li>
                        </ul>

                        <!-- Social Links -->
                        <div class="flex gap-2 pt-2">
                            <a
                                href="#"
                                class="flex h-9 w-9 items-center justify-center rounded-md border transition-colors hover:bg-accent"
                                aria-label="Facebook"
                            >
                                <Facebook :size="18" />
                            </a>
                            <a
                                href="#"
                                class="flex h-9 w-9 items-center justify-center rounded-md border transition-colors hover:bg-accent"
                                aria-label="Instagram"
                            >
                                <Instagram :size="18" />
                            </a>
                            <a
                                href="#"
                                class="flex h-9 w-9 items-center justify-center rounded-md border transition-colors hover:bg-accent"
                                aria-label="Twitter"
                            >
                                <Twitter :size="18" />
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Copyright -->
                <div
                    class="mt-12 border-t pt-8 text-center text-sm text-muted-foreground"
                >
                    <p>&copy; {{ currentYear }} SMO. {{ t({ ru: 'Все права защищены.', kz: 'Барлық құқықтар қорғалған.' }) }}</p>
                </div>
            </div>
        </footer>
    </div>
</template>
