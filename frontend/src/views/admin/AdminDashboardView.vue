<script setup lang="ts">
import { RouterLink } from 'vue-router';
import { Boxes, FolderTree, Users, ClipboardList } from '@lucide/vue';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';

const sections = [
  {
    to: '/admin/categories',
    title: 'Kategorie',
    description: 'Zarządzaj kategoriami sprzętu.',
    icon: FolderTree,
  },
  {
    to: '/admin/equipment',
    title: 'Sprzęt',
    description: 'Dodawaj i edytuj pozycje w katalogu.',
    icon: Boxes,
  },
  {
    to: '/admin/rentals',
    title: 'Wypożyczenia',
    description: 'Zmieniaj statusy rezerwacji.',
    icon: ClipboardList,
  },
  {
    to: '/admin/users',
    title: 'Użytkownicy',
    description: 'Lista zarejestrowanych kont.',
    icon: Users,
  },
];
</script>

<template>
  <div class="space-y-6">
    <div>
      <h1 class="font-heading text-2xl font-semibold">Panel administratora</h1>
      <p class="text-muted-foreground">Zarządzanie katalogiem i wypożyczeniami.</p>
    </div>

    <div class="grid gap-4 sm:grid-cols-2">
      <RouterLink v-for="section in sections" :key="section.to" :to="section.to">
        <Card class="h-full transition-colors hover:bg-muted/50">
          <CardHeader>
            <CardTitle class="flex items-center gap-2 text-lg">
              <component :is="section.icon" class="size-5" />
              {{ section.title }}
            </CardTitle>
            <CardDescription>{{ section.description }}</CardDescription>
          </CardHeader>
          <CardContent />
        </Card>
      </RouterLink>
    </div>
  </div>
</template>
