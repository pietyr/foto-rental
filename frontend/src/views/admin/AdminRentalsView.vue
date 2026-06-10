<script setup lang="ts">
import { onMounted, ref } from 'vue';
import { api, ApiError } from '@/api/client';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
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
import { formatDate, rentalStatusLabels } from '@/lib/labels';
import type { Rental, RentalStatus } from '@/types';

const rentals = ref<Rental[]>([]);
const editingId = ref<number | null>(null);
const editStatus = ref<RentalStatus>('pending');
const editEndDate = ref('');

async function loadRentals(): Promise<void> {
  const response = await api.get<{ data: Rental[] }>('/rentals');
  rentals.value = response.data;
}

function startEdit(rental: Rental): void {
  editingId.value = rental.id;
  editStatus.value = rental.status;
  editEndDate.value = rental.end_date ?? '';
}

function cancelEdit(): void {
  editingId.value = null;
}

async function save(rentalId: number): Promise<void> {
  try {
    await api.put(`/rentals/${rentalId}`, {
      status: editStatus.value,
      end_date: editEndDate.value || null,
    });
    editingId.value = null;
    await loadRentals();
  } catch (error) {
    if (error instanceof ApiError) {
      alert(error.message);
    }
  }
}

onMounted(loadRentals);
</script>

<template>
  <div class="space-y-6">
    <div>
      <h1 class="font-heading text-2xl font-semibold">Wypożyczenia</h1>
      <p class="text-muted-foreground">Potwierdzanie i zamykanie rezerwacji.</p>
    </div>

    <Card>
      <CardHeader>
        <CardTitle class="text-base">Lista wypożyczeń</CardTitle>
      </CardHeader>
      <CardContent>
        <Table>
          <TableHeader>
            <TableRow>
              <TableHead>Użytkownik</TableHead>
              <TableHead>Sprzęt</TableHead>
              <TableHead>Od</TableHead>
              <TableHead>Do</TableHead>
              <TableHead>Status</TableHead>
              <TableHead class="w-48 text-right">Akcje</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            <TableRow v-for="rental in rentals" :key="rental.id">
              <TableCell>{{ rental.user?.name }}</TableCell>
              <TableCell>{{ rental.equipment?.name }}</TableCell>
              <TableCell>{{ formatDate(rental.start_date) }}</TableCell>
              <TableCell>
                <template v-if="editingId === rental.id">
                  <Input v-model="editEndDate" type="date" class="w-40" />
                </template>
                <template v-else>
                  {{ formatDate(rental.end_date) }}
                </template>
              </TableCell>
              <TableCell>
                <template v-if="editingId === rental.id">
                  <Select v-model="editStatus">
                    <SelectTrigger class="w-36">
                      <SelectValue />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem value="pending">Oczekujące</SelectItem>
                      <SelectItem value="active">Aktywne</SelectItem>
                      <SelectItem value="completed">Zakończone</SelectItem>
                      <SelectItem value="cancelled">Anulowane</SelectItem>
                    </SelectContent>
                  </Select>
                </template>
                <template v-else>
                  <Badge variant="outline">{{ rentalStatusLabels[rental.status] }}</Badge>
                </template>
              </TableCell>
              <TableCell class="text-right">
                <div v-if="editingId === rental.id" class="flex justify-end gap-2">
                  <Button size="sm" @click="save(rental.id)">Zapisz</Button>
                  <Button size="sm" variant="outline" @click="cancelEdit">Anuluj</Button>
                </div>
                <Button v-else size="sm" variant="outline" @click="startEdit(rental)"
                  >Edytuj</Button
                >
              </TableCell>
            </TableRow>
          </TableBody>
        </Table>
      </CardContent>
    </Card>
  </div>
</template>
