<script setup lang="ts">
import { reactive } from 'vue';
import { useRouter } from 'vue-router';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { useFormErrors } from '@/composables/useFormErrors';
import { useAuthStore } from '@/stores/auth';

const router = useRouter();
const auth = useAuthStore();
const { setErrors, clearErrors, fieldError } = useFormErrors();

const form = reactive({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
});

async function submit(): Promise<void> {
  clearErrors();

  if (form.password !== form.password_confirmation) {
    setErrors({ password_confirmation: ['Hasła nie są identyczne.'] });
    return;
  }

  const errors = await auth.register(
    form.name,
    form.email,
    form.password,
    form.password_confirmation,
  );

  if (Object.keys(errors).length > 0) {
    setErrors(errors);
    return;
  }

  router.push({ name: 'equipment' });
}
</script>

<template>
  <div class="mx-auto max-w-md">
    <Card>
      <CardHeader>
        <CardTitle>Rejestracja</CardTitle>
        <CardDescription>Utwórz konto, aby rezerwować sprzęt fotograficzny.</CardDescription>
      </CardHeader>
      <CardContent>
        <form class="space-y-4" @submit.prevent="submit">
          <div class="space-y-2">
            <Label for="name">Imię i nazwisko</Label>
            <Input id="name" v-model="form.name" autocomplete="name" required />
            <p v-if="fieldError('name')" class="text-sm text-destructive">
              {{ fieldError('name') }}
            </p>
          </div>

          <div class="space-y-2">
            <Label for="email">E-mail</Label>
            <Input id="email" v-model="form.email" type="email" autocomplete="email" required />
            <p v-if="fieldError('email')" class="text-sm text-destructive">
              {{ fieldError('email') }}
            </p>
          </div>

          <div class="space-y-2">
            <Label for="password">Hasło</Label>
            <Input
              id="password"
              v-model="form.password"
              type="password"
              autocomplete="new-password"
              required
            />
            <p v-if="fieldError('password')" class="text-sm text-destructive">
              {{ fieldError('password') }}
            </p>
          </div>

          <div class="space-y-2">
            <Label for="password_confirmation">Powtórz hasło</Label>
            <Input
              id="password_confirmation"
              v-model="form.password_confirmation"
              type="password"
              autocomplete="new-password"
              required
            />
            <p v-if="fieldError('password_confirmation')" class="text-sm text-destructive">
              {{ fieldError('password_confirmation') }}
            </p>
          </div>

          <Button type="submit" class="w-full" :disabled="auth.loading">Załóż konto</Button>
        </form>
      </CardContent>
    </Card>
  </div>
</template>
