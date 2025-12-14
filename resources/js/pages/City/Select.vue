<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { MapPin } from 'lucide-vue-next';
import type { City } from '@/types/api';
import { useLocale } from '@/composables/useLocale';

interface Props {
    cities: City[];
}

const props = defineProps<Props>();
const { getLocalizedName } = useLocale();
const selectedCityId = ref<number | null>(null);
const isSubmitting = ref(false);

const getCityName = (city: City): string => {
    return getLocalizedName(city);
};

const selectCity = () => {
    if (!selectedCityId.value) return;

    isSubmitting.value = true;

    router.post(
        '/select-city',
        { city_id: selectedCityId.value },
        {
            onFinish: () => {
                isSubmitting.value = false;
            },
        },
    );
};
</script>

<template>
    <Head title="Select Your City" />

    <div
        class="flex min-h-screen items-center justify-center bg-neutral-50 p-4 dark:bg-neutral-900"
    >
        <Card class="w-full max-w-2xl">
            <CardHeader class="text-center">
                <div
                    class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-primary/10"
                >
                    <MapPin class="h-8 w-8 text-primary" />
                </div>
                <CardTitle class="text-2xl">Select Your City</CardTitle>
                <CardDescription>
                    Choose your city to see products and shops available in your
                    area
                </CardDescription>
            </CardHeader>

            <CardContent class="space-y-6">
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <button
                        v-for="city in cities"
                        :key="city.id"
                        @click="selectedCityId = city.id"
                        class="flex items-center gap-3 rounded-lg border-2 p-4 transition-all hover:border-primary hover:bg-primary/5"
                        :class="{
                            'border-primary bg-primary/5':
                                selectedCityId === city.id,
                            'border-neutral-200 dark:border-neutral-800':
                                selectedCityId !== city.id,
                        }"
                    >
                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-full transition-colors"
                            :class="{
                                'bg-primary text-white':
                                    selectedCityId === city.id,
                                'bg-neutral-100 dark:bg-neutral-800':
                                    selectedCityId !== city.id,
                            }"
                        >
                            <MapPin :size="20" />
                        </div>
                        <span class="text-lg font-medium">{{
                            getCityName(city)
                        }}</span>
                    </button>
                </div>

                <Button
                    @click="selectCity"
                    :disabled="!selectedCityId || isSubmitting"
                    class="w-full"
                    size="lg"
                >
                    {{ isSubmitting ? 'Confirming...' : 'Continue' }}
                </Button>
            </CardContent>
        </Card>
    </div>
</template>
