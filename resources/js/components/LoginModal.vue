<script setup lang="ts">
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import {
  Dialog,
  DialogContent,
  DialogHeader,
  DialogTitle,
} from '@/components/ui/dialog'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Button } from '@/components/ui/button'
import { Checkbox } from '@/components/ui/checkbox'
import AlertError from '@/components/AlertError.vue'
import { LogIn, Mail, Phone } from 'lucide-vue-next'
import { useLocale } from '@/composables/useLocale'

const { t } = useLocale()

const props = defineProps<{
  open: boolean
}>()

const emit = defineEmits<{
  'update:open': [value: boolean]
}>()

const identifier = ref('')
const password = ref('')
const remember = ref(false)
const errors = ref<string[]>([])
const processing = ref(false)

// Detect if input is email or phone
const inputType = computed(() => {
  if (!identifier.value) return 'text'
  // Check if it contains @ (email)
  if (identifier.value.includes('@')) return 'email'
  // Check if it's numeric (phone)
  if (/^\+?[\d\s-()]+$/.test(identifier.value)) return 'phone'
  return 'text'
})

const inputPlaceholder = computed(() => {
  return t({ ru: 'Введите email или телефон', kz: 'Email немесе телефонды енгізіңіз' })
})

const inputIcon = computed(() => {
  if (inputType.value === 'email') return Mail
  if (inputType.value === 'phone') return Phone
  return Mail
})

const handleSubmit = () => {
  errors.value = []
  processing.value = true

  // Prepare form data
  const formData: Record<string, any> = {
    password: password.value,
    remember: remember.value,
  }

  // Determine if identifier is email or phone
  if (identifier.value.includes('@')) {
    formData.email = identifier.value
  } else {
    formData.phone = identifier.value
  }

  router.post('/login', formData, {
    preserveScroll: true,
    onSuccess: () => {
      emit('update:open', false)
      // Reload the page to update auth state
      router.reload()
    },
    onError: (errorResponse) => {
      const errorMessages: string[] = []

      if (errorResponse.email) {
        errorMessages.push(...(Array.isArray(errorResponse.email) ? errorResponse.email : [errorResponse.email]))
      }
      if (errorResponse.phone) {
        errorMessages.push(...(Array.isArray(errorResponse.phone) ? errorResponse.phone : [errorResponse.phone]))
      }
      if (errorResponse.password) {
        errorMessages.push(...(Array.isArray(errorResponse.password) ? errorResponse.password : [errorResponse.password]))
      }
      if (errorResponse.message) {
        errorMessages.push(errorResponse.message)
      }

      if (errorMessages.length === 0) {
        errorMessages.push(t({ ru: 'Не удалось войти. Проверьте свои учетные данные.', kz: 'Кіру мүмкін болмады. Тіркелгі деректерін тексеріңіз.' }))
      }

      errors.value = errorMessages
    },
    onFinish: () => {
      processing.value = false
    },
  })
}

const handleClose = () => {
  if (!processing.value) {
    emit('update:open', false)
  }
}

const handleRegisterClick = () => {
  emit('update:open', false)
  router.visit('/register')
}
</script>

<template>
  <Dialog :open="open" @update:open="handleClose">
    <DialogContent class="sm:max-w-md">
      <DialogHeader class="text-center space-y-3">
        <!-- <div class="mx-auto mb-2 flex h-16 w-16 items-center justify-center rounded-2xl"
          style="background: linear-gradient(135deg, rgba(44, 95, 93, 0.1) 0%, rgba(44, 95, 93, 0.05) 100%); border: 1px solid rgba(44, 95, 93, 0.2);">
          <LogIn class="h-7 w-7" style="color: var(--smo-primary)" />
        </div> -->
        <DialogTitle class="text-2xl font-bold" style="font-family: var(--font-display); color: var(--smo-text-primary); letter-spacing: -0.02em;">
          {{ t({ ru: 'Вход', kz: 'Кіру' }) }}
        </DialogTitle>
      </DialogHeader>

      <form @submit.prevent="handleSubmit" class="space-y-5 mt-2">
        <AlertError v-if="errors.length > 0" :errors="errors" />

        <div class="space-y-2">
          <Label for="identifier" class="text-sm font-medium" style="font-family: var(--font-body); color: var(--smo-text-secondary);">
            {{ t({ ru: 'Email или телефон', kz: 'Email немесе телефон' }) }}
          </Label>
          <div class="relative">
            <component
              :is="inputIcon"
              class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2"
              style="color: var(--smo-text-muted)"
            />
            <Input
              id="identifier"
              v-model="identifier"
              :type="inputType === 'email' ? 'email' : 'text'"
              :placeholder="inputPlaceholder"
              class="pl-10 input-modern"
              required
              :disabled="processing"
            />
          </div>
        </div>

        <div class="space-y-2">
          <Label for="password" class="text-sm font-medium" style="font-family: var(--font-body); color: var(--smo-text-secondary);">
            {{ t({ ru: 'Пароль', kz: 'Құпия сөз' }) }}
          </Label>
          <Input
            id="password"
            v-model="password"
            type="password"
            :placeholder="t({ ru: 'Введите пароль', kz: 'Құпия сөзді енгізіңіз' })"
            class="input-modern"
            required
            :disabled="processing"
          />
        </div>

        <div class="flex items-center justify-between text-sm">
          <div class="flex items-center gap-2">
            <Checkbox id="remember" v-model:checked="remember" :disabled="processing" />
            <Label for="remember" class="font-normal cursor-pointer" style="font-family: var(--font-body); color: var(--smo-text-secondary);">
              {{ t({ ru: 'Запомнить меня', kz: 'Мені есте сақтау' }) }}
            </Label>
          </div>

          <a
            href="/forgot-password"
            class="font-medium hover:underline transition-colors"
            style="color: var(--smo-primary); font-family: var(--font-body);"
            @click.prevent="router.visit('/forgot-password')"
          >
            {{ t({ ru: 'Забыли пароль?', kz: 'Құпия сөзді ұмыттыңыз ба?' }) }}
          </a>
        </div>

        <div class="flex flex-col gap-4 pt-2">
          <Button type="submit" class="w-full btn-primary-modern" :disabled="processing">
            <span v-if="processing">{{ t({ ru: 'Пожалуйста, подождите...', kz: 'Күте тұрыңыз...' }) }}</span>
            <span v-else>{{ t({ ru: 'Продолжить', kz: 'Жалғастыру' }) }}</span>
          </Button>

          <div class="text-center text-sm" style="font-family: var(--font-body); color: var(--smo-text-secondary);">
            {{ t({ ru: 'Нет аккаунта?', kz: 'Тіркелгі жоқ па?' }) }}
            <button
              type="button"
              class="font-semibold hover:underline transition-colors ml-1"
              style="color: var(--smo-primary)"
              @click="handleRegisterClick"
              :disabled="processing"
            >
              {{ t({ ru: 'Регистрация', kz: 'Тіркелу' }) }}
            </button>
          </div>
        </div>
      </form>
    </DialogContent>
  </Dialog>
</template>
