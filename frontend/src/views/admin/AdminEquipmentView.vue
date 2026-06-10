<script setup lang="ts">
import { onMounted, reactive, ref } from 'vue';
import { Pencil, Plus, Trash2 } from '@lucide/vue';
import { api, ApiError } from '@/api/client';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select';
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table';
import { Textarea } from '@/components/ui/textarea';
import { useFormErrors } from '@/composables/useFormErrors';
import { equipmentStatusLabels, formatPrice } from '@/lib/labels';
import type { Category, Equipment, EquipmentStatus } from '@/types';

const equipment = ref<Equipment[]>([]);
const categories = ref<Category[]>([]);
const editingId = ref<number | null>(null);
const showForm = ref(false);
const { setErrors, clearErrors, fieldError } = useFormErrors();

const form = reactive({
  category_id: '',
  name: '',
  description: '',
  brand: '',
  model: '',
  price_per_day: '',
  status: 'available' as EquipmentStatus,
});

async function loadData(): Promise<void> {
  const [equipmentResponse, categoriesResponse] = await Promise.all([
    api.get<{ data: Equipment[] }>('/equipment'),
    api.get<{ data: Category[] }>('/categories'),
  ]);
  equipment.value = equipmentResponse.data;
  categories.value = categoriesResponse.data;
}

function resetForm(): void {
  editingId.value = null;
  showForm.value = false;
  form.category_id = '';
  form.name = '';
  form.description = '';
  form.brand = '';
  form.model = '';
  form.price_per_day = '';
  form.status = 'available';
  clearErrors();
}

function startCreate(): void {
  resetForm();
  showForm.value = true;
}

function startEdit(item: Equipment): void {
  editingId.value = item.id;
  showForm.value = true;
  form.category_id = String(item.category_id);
  form.name = item.name;
  form.description = item.description ?? '';
  form.brand = item.brand ?? '';
  form.model = item.model ?? '';
  form.price_per_day = item.price_per_day;
  form.status = item.status;
  clearErrors();
}

async function submit(): Promise<void> {
  clearErrors();

  const payload = {
    category_id: Number(form.category_id),
    name: form.name,
    description: form.description || null,
    brand: form.brand || null,
    model: form.model || null,
    price_per_day: Number(form.price_per_day),
    status: form.status,
  };

  try {
    if (editingId.value) {
      await api.put(`/equipment/${editingId.value}`, payload);
    } else {
      await api.post('/equipment', payload);
    }

    resetForm();
    await loadData();
  } catch (error) {
    if (error instanceof ApiError) {
      setErrors(error.errors);
    }
  }
}

async function remove(id: number): Promise<void> {
  if (!confirm('Usunąć sprzęt?')) return;

  try {
    await api.delete(`/equipment/${id}`);
    await loadData();
  } catch (error) {
    if (error instanceof ApiError) {
      alert(error.message);
    }
  }
}

onMounted(loadData);
</script>

<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between gap-4">
      <div>
        <h1 class="font-heading text-2xl font-semibold">Sprzęt</h1>
        <p class="text-muted-foreground">Pozycje dostępne w katalogu.</p>
      </div>
      <Button @click="startCreate">
        <Plus class="size-4" />
        Dodaj sprzęt
      </Button>
    </div>

    <Card v-if="showForm">
      <CardHeader>
        <CardTitle class="text-base">{{ editingId ? 'Edycja sprzętu' : 'Nowy sprzęt' }}</CardTitle>
      </CardHeader>
      <CardContent>
        <form class="grid gap-4 sm:grid-cols-2" @submit.prevent="submit">
          <div class="space-y-2">
            <Label>Kategoria</Label>
            <Select v-model="form.category_id">
              <SelectTrigger>
                <SelectValue placeholder="Wybierz kategorię" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem
                  v-for="category in categories"
                  :key="category.id"
                  :value="String(category.id)"
                >
                  {{ category.name }}
                </SelectItem>
              </SelectContent>
            </Select>
            <p v-if="fieldError('category_id')" class="text-sm text-destructive">
              {{ fieldError('category_id') }}
            </p>
          </div>

          <div class="space-y-2">
            <Label for="name">Nazwa</Label>
            <Input id="name" v-model="form.name" required />
            <p v-if="fieldError('name')" class="text-sm text-destructive">
              {{ fieldError('name') }}
            </p>
          </div>

          <div class="space-y-2 sm:col-span-2">
            <Label for="description">Opis</Label>
            <Textarea id="description" v-model="form.description" rows="3" />
          </div>

          <div class="space-y-2">
            <Label for="brand">Marka</Label>
            <Input id="brand" v-model="form.brand" />
          </div>

          <div class="space-y-2">
            <Label for="model">Model</Label>
            <Input id="model" v-model="form.model" />
          </div>

          <div class="space-y-2">
            <Label for="price">Cena za dzień (PLN)</Label>
            <Input
              id="price"
              v-model="form.price_per_day"
              type="number"
              min="0"
              step="0.01"
              required
            />
            <p v-if="fieldError('price_per_day')" class="text-sm text-destructive">
              {{ fieldError('price_per_day') }}
            </p>
          </div>

          <div class="space-y-2">
            <Label>Status</Label>
            <Select v-model="form.status">
              <SelectTrigger>
                <SelectValue />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="available">Dostępny</SelectItem>
                <SelectItem value="rented">Wypożyczony</SelectItem>
                <SelectItem value="maintenance">Serwis</SelectItem>
              </SelectContent>
            </Select>
          </div>

          <div class="flex gap-2 sm:col-span-2">
            <Button type="submit">{{ editingId ? 'Zapisz' : 'Dodaj' }}</Button>
            <Button type="button" variant="outline" @click="resetForm">Anuluj</Button>
          </div>
        </form>
      </CardContent>
    </Card>

    <Card>
      <CardContent class="pt-6">
        <Table>
          <TableHeader>
            <TableRow>
              <TableHead>Nazwa</TableHead>
              <TableHead>Kategoria</TableHead>
              <TableHead>Cena</TableHead>
              <TableHead>Status</TableHead>
              <TableHead class="w-32 text-right">Akcje</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            <TableRow v-for="item in equipment" :key="item.id">
              <TableCell>{{ item.name }}</TableCell>
              <TableCell>{{ item.category?.name }}</TableCell>
              <TableCell>{{ formatPrice(item.price_per_day) }}</TableCell>
              <TableCell>
                <Badge variant="outline">{{ equipmentStatusLabels[item.status] }}</Badge>
              </TableCell>
              <TableCell class="text-right">
                <div class="flex justify-end gap-1">
                  <Button size="icon" variant="ghost" @click="startEdit(item)">
                    <Pencil class="size-4" />
                  </Button>
                  <Button size="icon" variant="ghost" @click="remove(item.id)">
                    <Trash2 class="size-4" />
                  </Button>
                </div>
              </TableCell>
            </TableRow>
          </TableBody>
        </Table>
      </CardContent>
    </Card>
  </div>
</template>
