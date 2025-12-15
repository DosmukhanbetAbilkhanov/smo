<script setup lang="ts">
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import { Form } from '@inertiajs/vue3'
import { store } from '@/actions/App/Http/Controllers/Auth/LoginController'
import {
  Dialog,
  DialogContent,
  DialogHeader,
  DialogTitle,
  DialogDescription,
} from '@/components/ui/dialog'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Button } from '@/components/ui/button'
import { Checkbox } from '@/components/ui/checkbox'
import AlertError from '@/components/AlertError.vue'
import { LogIn, Mail, Phone } from 'lucide-vue-next'

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
  return 'Email or phone number'
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
        errorMessages.push('Failed to login. Please check your credentials.')
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
      <DialogHeader>
        <DialogTitle class="flex items-center gap-2">
          <LogIn class="h-5 w-5" />
          Login to Continue
        </DialogTitle>
        <DialogDescription>
          Please login to add items to your cart and make purchases.
        </DialogDescription>
      </DialogHeader>

      <form @submit.prevent="handleSubmit" class="space-y-4">
        <AlertError v-if="errors.length > 0" :errors="errors" />

        <div class="space-y-2">
          <Label for="identifier">Email or Phone Number</Label>
          <div class="relative">
            <component
              :is="inputIcon"
              class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground"
            />
            <Input
              id="identifier"
              v-model="identifier"
              :type="inputType === 'email' ? 'email' : 'text'"
              :placeholder="inputPlaceholder"
              class="pl-10"
              required
              :disabled="processing"
            />
          </div>
        </div>

        <div class="space-y-2">
          <Label for="password">Password</Label>
          <Input
            id="password"
            v-model="password"
            type="password"
            placeholder="Enter your password"
            required
            :disabled="processing"
          />
        </div>

        <div class="flex items-center justify-between">
          <div class="flex items-center gap-2">
            <Checkbox id="remember" v-model:checked="remember" :disabled="processing" />
            <Label for="remember" class="text-sm font-normal cursor-pointer">
              Remember me
            </Label>
          </div>

          <a
            href="/forgot-password"
            class="text-sm text-primary hover:underline"
            @click.prevent="router.visit('/forgot-password')"
          >
            Forgot password?
          </a>
        </div>

        <div class="flex flex-col gap-3">
          <Button type="submit" class="w-full" :disabled="processing">
            <span v-if="processing">Logging in...</span>
            <span v-else>Login</span>
          </Button>

          <div class="text-center text-sm text-muted-foreground">
            Don't have an account?
            <button
              type="button"
              class="text-primary hover:underline font-medium"
              @click="handleRegisterClick"
              :disabled="processing"
            >
              Register here
            </button>
          </div>
        </div>
      </form>
    </DialogContent>
  </Dialog>
</template>
