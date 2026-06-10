<script setup lang="ts">
import { reactive } from 'vue';
import { useRouter } from 'vue-router';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Alert, AlertDescription } from '@/components/ui/alert';
import { useFormErrors } from '@/composables/useFormErrors';
import { useAuthStore } from '@/stores/auth';

const router = useRouter();
const auth = useAuthStore();
const { setErrors, clearErrors, fieldError, generalError } = useFormErrors();

const form = reactive({
  email: '',
  password: '',
});

async function submit(): Promise<void> {
  clearErrors();
  const errors = await auth.login(form.email, form.password);

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
        <CardTitle>Logowanie</CardTitle>
        <CardDescription>Zaloguj się, aby przeglądać i rezerwować sprzęt.</CardDescription>
      </CardHeader>
      <CardContent>
        <form class="space-y-4" @submit.prevent="submit">
          <Alert v-if="generalError" variant="destructive">
            <AlertDescription>{{ generalError }}</AlertDescription>
          </Alert>

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
              autocomplete="current-password"
              required
            />
            <p v-if="fieldError('password')" class="text-sm text-destructive">
              {{ fieldError('password') }}
            </p>
          </div>

          <Button type="submit" class="w-full" :disabled="auth.loading">Zaloguj się</Button>
        </form>
      </CardContent>
    </Card>
  </div>
</template>
