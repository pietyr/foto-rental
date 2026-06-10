<script setup lang="ts">
import { computed, onMounted, ref } from 'vue';
import { RouterLink } from 'vue-router';
import { Filter } from '@lucide/vue';
import { api } from '@/api/client';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Label } from '@/components/ui/label';
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select';
import { Skeleton } from '@/components/ui/skeleton';
import { equipmentStatusLabels, formatPrice } from '@/lib/labels';
import type { Category, Equipment } from '@/types';

const equipment = ref<Equipment[]>([]);
const categories = ref<Category[]>([]);
const loading = ref(true);
const categoryFilter = ref<string>('all');
const availableOnly = ref(false);

const filteredEquipment = computed(() => equipment.value);

async function loadData(): Promise<void> {
  loading.value = true;

  const params = new URLSearchParams();
  if (categoryFilter.value !== 'all') {
    params.set('category_id', categoryFilter.value);
  }
  if (availableOnly.value) {
    params.set('available_only', '1');
  }

  const query = params.toString();
  const [equipmentResponse, categoriesResponse] = await Promise.all([
    api.get<{ data: Equipment[] }>(`/equipment${query ? `?${query}` : ''}`),
    api.get<{ data: Category[] }>('/categories'),
  ]);

  equipment.value = equipmentResponse.data;
  categories.value = categoriesResponse.data;
  loading.value = false;
}

function statusVariant(status: Equipment['status']): 'default' | 'secondary' | 'destructive' {
  if (status === 'available') return 'default';
  if (status === 'rented') return 'secondary';
  return 'destructive';
}

onMounted(loadData);
</script>

<template>
  <div class="space-y-6">
    <div>
      <h1 class="font-heading text-2xl font-semibold">Katalog sprzętu</h1>
      <p class="text-muted-foreground">Przeglądaj dostępny sprzęt fotograficzny.</p>
    </div>

    <Card>
      <CardHeader class="pb-4">
        <CardTitle class="flex items-center gap-2 text-base">
          <Filter class="size-4" />
          Filtry
        </CardTitle>
      </CardHeader>
      <CardContent class="flex flex-wrap items-end gap-4">
        <div class="space-y-2">
          <Label>Kategoria</Label>
          <Select v-model="categoryFilter" @update:model-value="loadData">
            <SelectTrigger class="w-48">
              <SelectValue placeholder="Wszystkie" />
            </SelectTrigger>
            <SelectContent>
              <SelectItem value="all">Wszystkie</SelectItem>
              <SelectItem
                v-for="category in categories"
                :key="category.id"
                :value="String(category.id)"
              >
                {{ category.name }}
              </SelectItem>
            </SelectContent>
          </Select>
        </div>

        <Button
          :variant="availableOnly ? 'default' : 'outline'"
          @click="
            availableOnly = !availableOnly;
            loadData();
          "
        >
          Tylko dostępne
        </Button>
      </CardContent>
    </Card>

    <div v-if="loading" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
      <Skeleton v-for="n in 6" :key="n" class="h-48" />
    </div>

    <div
      v-else-if="filteredEquipment.length === 0"
      class="rounded-lg border border-dashed p-8 text-center text-muted-foreground"
    >
      Brak sprzętu spełniającego kryteria.
    </div>

    <div v-else class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
      <Card v-for="item in filteredEquipment" :key="item.id">
        <CardHeader>
          <div class="flex items-start justify-between gap-2">
            <CardTitle class="text-lg">{{ item.name }}</CardTitle>
            <Badge :variant="statusVariant(item.status)">
              {{ equipmentStatusLabels[item.status] }}
            </Badge>
          </div>
          <CardDescription>
            {{ item.category?.name }}
            <span v-if="item.brand"> · {{ item.brand }}</span>
            <span v-if="item.model"> {{ item.model }}</span>
          </CardDescription>
        </CardHeader>
        <CardContent class="space-y-4">
          <p v-if="item.description" class="line-clamp-2 text-sm text-muted-foreground">
            {{ item.description }}
          </p>
          <div class="flex items-center justify-between">
            <span class="font-medium">{{ formatPrice(item.price_per_day) }} / dzień</span>
            <Button as-child size="sm" variant="outline">
              <RouterLink :to="{ name: 'equipment-detail', params: { id: item.id } }"
                >Szczegóły</RouterLink
              >
            </Button>
          </div>
        </CardContent>
      </Card>
    </div>
  </div>
</template>
