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
import { formatDate } from '@/lib/labels';
import type { User } from '@/types';

interface UserRow extends User {
  created_at: string;
}

const users = ref<UserRow[]>([]);

async function loadUsers(): Promise<void> {
  const response = await api.get<{ data: UserRow[] }>('/users');
  users.value = response.data;
}

onMounted(loadUsers);
</script>

<template>
  <div class="space-y-6">
    <div>
      <h1 class="font-heading text-2xl font-semibold">Użytkownicy</h1>
      <p class="text-muted-foreground">Konta zarejestrowane w systemie.</p>
    </div>

    <Card>
      <CardHeader>
        <CardTitle class="text-base">Lista użytkowników</CardTitle>
      </CardHeader>
      <CardContent>
        <Table>
          <TableHeader>
            <TableRow>
              <TableHead>Imię i nazwisko</TableHead>
              <TableHead>E-mail</TableHead>
              <TableHead>Rola</TableHead>
              <TableHead>Data rejestracji</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            <TableRow v-for="user in users" :key="user.id">
              <TableCell>{{ user.name }}</TableCell>
              <TableCell>{{ user.email }}</TableCell>
              <TableCell>
                <Badge :variant="user.role === 'admin' ? 'default' : 'secondary'">
                  {{ user.role === 'admin' ? 'Administrator' : 'Użytkownik' }}
                </Badge>
              </TableCell>
              <TableCell>{{ formatDate(user.created_at) }}</TableCell>
            </TableRow>
          </TableBody>
        </Table>
      </CardContent>
    </Card>
  </div>
</template>
