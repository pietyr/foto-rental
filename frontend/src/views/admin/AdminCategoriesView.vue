<script setup lang="ts">
import { onMounted, reactive, ref } from 'vue';
import { Pencil, Plus, Trash2 } from '@lucide/vue';
import { api, ApiError } from '@/api/client';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table';
import { useFormErrors } from '@/composables/useFormErrors';
import type { Category } from '@/types';

const categories = ref<Category[]>([]);
const editingId = ref<number | null>(null);
const { setErrors, clearErrors, fieldError } = useFormErrors();

const form = reactive({
  name: '',
});

async function loadCategories(): Promise<void> {
  const response = await api.get<{ data: Category[] }>('/categories');
  categories.value = response.data;
}

function startCreate(): void {
  editingId.value = null;
  form.name = '';
  clearErrors();
}

function startEdit(category: Category): void {
  editingId.value = category.id;
  form.name = category.name;
  clearErrors();
}

async function submit(): Promise<void> {
  clearErrors();

  try {
    if (editingId.value) {
      await api.put(`/categories/${editingId.value}`, { name: form.name });
    } else {
      await api.post('/categories', { name: form.name });
    }

    form.name = '';
    editingId.value = null;
    await loadCategories();
  } catch (error) {
    if (error instanceof ApiError) {
      setErrors(error.errors);
    }
  }
}

async function remove(id: number): Promise<void> {
  if (!confirm('Usunąć kategorię?')) return;

  try {
    await api.delete(`/categories/${id}`);
    await loadCategories();
  } catch (error) {
    if (error instanceof ApiError) {
      alert(error.message);
    }
  }
}

onMounted(loadCategories);
</script>

<template>
  <div class="space-y-6">
    <div>
      <h1 class="font-heading text-2xl font-semibold">Kategorie</h1>
      <p class="text-muted-foreground">Grupy sprzętu w katalogu.</p>
    </div>

    <Card>
      <CardHeader>
        <CardTitle class="flex items-center gap-2 text-base">
          <Plus class="size-4" />
          {{ editingId ? 'Edycja kategorii' : 'Nowa kategoria' }}
        </CardTitle>
      </CardHeader>
      <CardContent>
        <form class="flex flex-wrap items-end gap-4" @submit.prevent="submit">
          <div class="min-w-64 flex-1 space-y-2">
            <Label for="name">Nazwa</Label>
            <Input id="name" v-model="form.name" required />
            <p v-if="fieldError('name')" class="text-sm text-destructive">
              {{ fieldError('name') }}
            </p>
          </div>
          <div class="flex gap-2">
            <Button type="submit">{{ editingId ? 'Zapisz' : 'Dodaj' }}</Button>
            <Button v-if="editingId" type="button" variant="outline" @click="startCreate"
              >Anuluj</Button
            >
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
              <TableHead class="w-32 text-right">Akcje</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            <TableRow v-for="category in categories" :key="category.id">
              <TableCell>{{ category.name }}</TableCell>
              <TableCell class="text-right">
                <div class="flex justify-end gap-1">
                  <Button size="icon" variant="ghost" @click="startEdit(category)">
                    <Pencil class="size-4" />
                  </Button>
                  <Button size="icon" variant="ghost" @click="remove(category.id)">
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
