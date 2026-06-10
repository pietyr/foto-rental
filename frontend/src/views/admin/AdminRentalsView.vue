<script setup lang="ts">
import { onMounted, reactive, ref } from 'vue';
import { Plus } from '@lucide/vue';
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
import { useFormErrors } from '@/composables/useFormErrors';
import { formatDate, rentalStatusLabels } from '@/lib/labels';
import type { Equipment, Rental, RentalStatus, User } from '@/types';

const rentals = ref<Rental[]>([]);
const users = ref<User[]>([]);
const equipment = ref<Equipment[]>([]);
const editingId = ref<number | null>(null);
const editStatus = ref<RentalStatus>('pending');
const editEndDate = ref('');
const showForm = ref(false);
const { setErrors, clearErrors, fieldError } = useFormErrors();

const form = reactive({
  user_id: '',
  equipment_id: '',
  start_date: '',
  end_date: '',
  status: 'active' as RentalStatus,
});

async function loadRentals(): Promise<void> {
  const response = await api.get<{ data: Rental[] }>('/rentals?all=1');
  rentals.value = response.data;
}

async function loadFormData(): Promise<void> {
  const [usersResponse, equipmentResponse] = await Promise.all([
    api.get<{ data: User[] }>('/users'),
    api.get<{ data: Equipment[] }>('/equipment?available_only=1'),
  ]);
  users.value = usersResponse.data;
  equipment.value = equipmentResponse.data;
}

function resetForm(): void {
  form.user_id = '';
  form.equipment_id = '';
  form.start_date = '';
  form.end_date = '';
  form.status = 'active';
  showForm.value = false;
  clearErrors();
}

function startCreate(): void {
  resetForm();
  showForm.value = true;
  loadFormData();
}

function startEdit(rental: Rental): void {
  editingId.value = rental.id;
  editStatus.value = rental.status;
  editEndDate.value = rental.end_date ?? '';
}

function cancelEdit(): void {
  editingId.value = null;
}

async function createRental(): Promise<void> {
  clearErrors();

  try {
    await api.post('/rentals/manage', {
      user_id: Number(form.user_id),
      equipment_id: Number(form.equipment_id),
      start_date: form.start_date,
      end_date: form.end_date || null,
      status: form.status,
    });
    resetForm();
    await loadRentals();
  } catch (error) {
    if (error instanceof ApiError) {
      setErrors(error.errors);
    }
  }
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
    <div class="flex items-center justify-between gap-4">
      <div>
        <h1 class="font-heading text-2xl font-semibold">Wypożyczenia</h1>
        <p class="text-muted-foreground">Zarządzanie rezerwacjami i wydawaniem sprzętu.</p>
      </div>
      <Button @click="startCreate">
        <Plus class="size-4" />
        Nowe wypożyczenie
      </Button>
    </div>

    <Card v-if="showForm">
      <CardHeader>
        <CardTitle class="text-base">Nowe wypożyczenie</CardTitle>
      </CardHeader>
      <CardContent>
        <form class="grid gap-4 sm:grid-cols-2" @submit.prevent="createRental">
          <div class="space-y-2">
            <Label>Użytkownik</Label>
            <Select v-model="form.user_id">
              <SelectTrigger>
                <SelectValue placeholder="Wybierz użytkownika" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem v-for="user in users" :key="user.id" :value="String(user.id)">
                  {{ user.name }} ({{ user.email }})
                </SelectItem>
              </SelectContent>
            </Select>
            <p v-if="fieldError('user_id')" class="text-sm text-destructive">
              {{ fieldError('user_id') }}
            </p>
          </div>

          <div class="space-y-2">
            <Label>Sprzęt</Label>
            <Select v-model="form.equipment_id">
              <SelectTrigger>
                <SelectValue placeholder="Wybierz sprzęt" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem v-for="item in equipment" :key="item.id" :value="String(item.id)">
                  {{ item.name }}
                </SelectItem>
              </SelectContent>
            </Select>
            <p v-if="fieldError('equipment_id')" class="text-sm text-destructive">
              {{ fieldError('equipment_id') }}
            </p>
          </div>

          <div class="space-y-2">
            <Label for="start_date">Data rozpoczęcia</Label>
            <Input id="start_date" v-model="form.start_date" type="date" required />
            <p v-if="fieldError('start_date')" class="text-sm text-destructive">
              {{ fieldError('start_date') }}
            </p>
          </div>

          <div class="space-y-2">
            <Label for="end_date">Data zwrotu</Label>
            <Input id="end_date" v-model="form.end_date" type="date" />
            <p v-if="fieldError('end_date')" class="text-sm text-destructive">
              {{ fieldError('end_date') }}
            </p>
          </div>

          <div class="space-y-2">
            <Label>Status</Label>
            <Select v-model="form.status">
              <SelectTrigger>
                <SelectValue />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="pending">Oczekujące</SelectItem>
                <SelectItem value="active">Aktywne</SelectItem>
              </SelectContent>
            </Select>
            <p v-if="fieldError('status')" class="text-sm text-destructive">
              {{ fieldError('status') }}
            </p>
          </div>

          <div class="flex gap-2 sm:col-span-2">
            <Button type="submit">Utwórz</Button>
            <Button type="button" variant="outline" @click="resetForm">Anuluj</Button>
          </div>
        </form>
      </CardContent>
    </Card>

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
                <Button v-else size="sm" variant="outline" @click="startEdit(rental)">Edytuj</Button>
              </TableCell>
            </TableRow>
          </TableBody>
        </Table>
      </CardContent>
    </Card>
  </div>
</template>
