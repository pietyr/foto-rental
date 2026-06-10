<script setup lang="ts">
import { onMounted, reactive, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { ArrowLeft, Calendar } from '@lucide/vue';
import { api } from '@/api/client';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Alert, AlertDescription } from '@/components/ui/alert';
import { Skeleton } from '@/components/ui/skeleton';
import { useFormErrors } from '@/composables/useFormErrors';
import { equipmentStatusLabels, formatPrice } from '@/lib/labels';
import type { Equipment } from '@/types';
import { ApiError } from '@/api/client';

const route = useRoute();
const router = useRouter();
const { setErrors, clearErrors, fieldError } = useFormErrors();

const equipment = ref<Equipment | null>(null);
const loading = ref(true);
const submitting = ref(false);
const successMessage = ref('');

const form = reactive({
  start_date: '',
  end_date: '',
});

async function loadEquipment(): Promise<void> {
  loading.value = true;
  const response = await api.get<{ data: Equipment }>(`/equipment/${route.params.id}`);
  equipment.value = response.data;
  loading.value = false;
}

async function reserve(): Promise<void> {
  if (!equipment.value) return;

  clearErrors();
  successMessage.value = '';
  submitting.value = true;

  try {
    await api.post('/rentals', {
      equipment_id: equipment.value.id,
      start_date: form.start_date,
      end_date: form.end_date || null,
    });
    successMessage.value =
      'Rezerwacja została złożona. Oczekuje na potwierdzenie przez administratora.';
    form.start_date = '';
    form.end_date = '';
  } catch (error) {
    if (error instanceof ApiError) {
      setErrors(error.errors);
    }
  } finally {
    submitting.value = false;
  }
}

onMounted(loadEquipment);
</script>

<template>
  <div class="space-y-6">
    <Button variant="ghost" size="sm" @click="router.back()">
      <ArrowLeft class="size-4" />
      Wróć
    </Button>

    <Skeleton v-if="loading" class="h-64" />

    <template v-else-if="equipment">
      <div class="grid gap-6 lg:grid-cols-2">
        <Card>
          <CardHeader>
            <div class="flex items-start justify-between gap-2">
              <CardTitle class="text-2xl">{{ equipment.name }}</CardTitle>
              <Badge>{{ equipmentStatusLabels[equipment.status] }}</Badge>
            </div>
            <CardDescription>
              {{ equipment.category?.name }}
              <span v-if="equipment.brand"> · {{ equipment.brand }}</span>
              <span v-if="equipment.model"> {{ equipment.model }}</span>
            </CardDescription>
          </CardHeader>
          <CardContent class="space-y-4">
            <p v-if="equipment.description" class="text-muted-foreground">
              {{ equipment.description }}
            </p>
            <p class="text-xl font-semibold">{{ formatPrice(equipment.price_per_day) }} / dzień</p>
          </CardContent>
        </Card>

        <Card v-if="equipment.status === 'available'">
          <CardHeader>
            <CardTitle class="flex items-center gap-2 text-lg">
              <Calendar class="size-4" />
              Rezerwacja
            </CardTitle>
            <CardDescription>Wybierz daty wypożyczenia.</CardDescription>
          </CardHeader>
          <CardContent>
            <form class="space-y-4" @submit.prevent="reserve">
              <Alert v-if="successMessage">
                <AlertDescription>{{ successMessage }}</AlertDescription>
              </Alert>

              <div class="space-y-2">
                <Label for="start_date">Data rozpoczęcia</Label>
                <Input id="start_date" v-model="form.start_date" type="date" required />
                <p v-if="fieldError('start_date')" class="text-sm text-destructive">
                  {{ fieldError('start_date') }}
                </p>
                <p v-if="fieldError('equipment_id')" class="text-sm text-destructive">
                  {{ fieldError('equipment_id') }}
                </p>
              </div>

              <div class="space-y-2">
                <Label for="end_date">Planowana data zwrotu (opcjonalnie)</Label>
                <Input id="end_date" v-model="form.end_date" type="date" />
                <p v-if="fieldError('end_date')" class="text-sm text-destructive">
                  {{ fieldError('end_date') }}
                </p>
              </div>

              <Button type="submit" class="w-full" :disabled="submitting">Zarezerwuj</Button>
            </form>
          </CardContent>
        </Card>

        <Card v-else>
          <CardContent class="py-8 text-center text-muted-foreground">
            Ten sprzęt nie jest obecnie dostępny do rezerwacji.
          </CardContent>
        </Card>
      </div>
    </template>
  </div>
</template>
