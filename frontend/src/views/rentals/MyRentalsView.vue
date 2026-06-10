<script setup lang="ts">
import { onMounted, ref } from 'vue';
import { api } from '@/api/client';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table';
import { Skeleton } from '@/components/ui/skeleton';
import { formatDate, rentalStatusLabels } from '@/lib/labels';
import type { Rental, RentalStatus } from '@/types';

const rentals = ref<Rental[]>([]);
const loading = ref(true);

function statusVariant(status: RentalStatus): 'default' | 'secondary' | 'outline' | 'destructive' {
  const map: Record<RentalStatus, 'default' | 'secondary' | 'outline' | 'destructive'> = {
    pending: 'outline',
    active: 'default',
    completed: 'secondary',
    cancelled: 'destructive',
  };
  return map[status];
}

async function loadRentals(): Promise<void> {
  loading.value = true;
  const response = await api.get<{ data: Rental[] }>('/rentals');
  rentals.value = response.data;
  loading.value = false;
}

onMounted(loadRentals);
</script>

<template>
  <div class="space-y-6">
    <div>
      <h1 class="font-heading text-2xl font-semibold">Moje wypożyczenia</h1>
      <p class="text-muted-foreground">Lista Twoich rezerwacji i ich statusów.</p>
    </div>

    <Card>
      <CardHeader>
        <CardTitle class="text-base">Historia</CardTitle>
      </CardHeader>
      <CardContent>
        <Skeleton v-if="loading" class="h-32" />

        <div v-else-if="rentals.length === 0" class="py-8 text-center text-muted-foreground">
          Nie masz jeszcze żadnych wypożyczeń.
        </div>

        <Table v-else>
          <TableHeader>
            <TableRow>
              <TableHead>Sprzęt</TableHead>
              <TableHead>Data rozpoczęcia</TableHead>
              <TableHead>Data zwrotu</TableHead>
              <TableHead>Status</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            <TableRow v-for="rental in rentals" :key="rental.id">
              <TableCell>{{ rental.equipment?.name }}</TableCell>
              <TableCell>{{ formatDate(rental.start_date) }}</TableCell>
              <TableCell>{{ formatDate(rental.end_date) }}</TableCell>
              <TableCell>
                <Badge :variant="statusVariant(rental.status)">
                  {{ rentalStatusLabels[rental.status] }}
                </Badge>
              </TableCell>
            </TableRow>
          </TableBody>
        </Table>
      </CardContent>
    </Card>
  </div>
</template>
