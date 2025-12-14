<script setup lang="ts">
import { computed, ref } from 'vue';
import { usePage, router } from '@inertiajs/vue3';
import { MapPin, Check } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import type { City, CityData } from '@/types/api';
import { useLocale } from '@/composables/useLocale';

const page = usePage<{ city: CityData }>();
const { getLocalizedName } = useLocale();
const isChanging = ref(false);

const selectedCity = computed(() => page.props.city.selected);
const availableCities = computed(() => page.props.city.available);

const getCityName = (city: City): string => {
    return getLocalizedName(city);
};

const changeCity = (cityId: number) => {
    if (selectedCity.value?.id === cityId) {
        return; // Already selected
    }

    isChanging.value = true;

    router.post(
        '/change-city',
        { city_id: cityId },
        {
            preserveScroll: true,
            onFinish: () => {
                isChanging.value = false;
            },
        },
    );
};
</script>

<template>
    <DropdownMenu>
        <DropdownMenuTrigger as-child>
            <Button
                variant="ghost"
                size="sm"
                class="gap-2"
                :disabled="isChanging"
            >
                <MapPin :size="16" />
                <span class="hidden sm:inline">
                    {{
                        selectedCity
                            ? getCityName(selectedCity)
                            : 'Select City'
                    }}
                </span>
            </Button>
        </DropdownMenuTrigger>

        <DropdownMenuContent align="end" class="w-48">
            <DropdownMenuItem
                v-for="city in availableCities"
                :key="city.id"
                @click="changeCity(city.id)"
                class="flex cursor-pointer items-center justify-between"
            >
                <span>{{ getCityName(city) }}</span>
                <Check
                    v-if="selectedCity?.id === city.id"
                    :size="16"
                    class="text-primary"
                />
            </DropdownMenuItem>
        </DropdownMenuContent>
    </DropdownMenu>
</template>
