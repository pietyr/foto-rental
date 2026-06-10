<script setup lang="ts">
import { computed } from 'vue';
import { RouterLink, RouterView, useRoute, useRouter } from 'vue-router';
import { Camera, LogOut, Shield, User } from '@lucide/vue';
import { Button } from '@/components/ui/button';
import { Separator } from '@/components/ui/separator';
import { useAuthStore } from '@/stores/auth';

const auth = useAuthStore();
const route = useRoute();
const router = useRouter();

const navLinks = computed(() => {
  const links = [
    { to: '/equipment', label: 'Katalog' },
    { to: '/rentals', label: 'Moje wypożyczenia' },
  ];

  if (auth.isAdmin) {
    links.push({ to: '/admin', label: 'Panel admina' });
  }

  return links;
});

function isActive(path: string): boolean {
  return route.path === path || route.path.startsWith(`${path}/`);
}

async function handleLogout(): Promise<void> {
  await auth.logout();
  router.push({ name: 'login' });
}
</script>

<template>
  <div class="min-h-screen bg-background">
    <header class="border-b">
      <div class="mx-auto flex max-w-6xl items-center justify-between gap-4 px-4 py-4">
        <RouterLink
          to="/equipment"
          class="flex items-center gap-2 font-heading text-lg font-semibold"
        >
          <Camera class="size-5" />
          foto-rental
        </RouterLink>

        <nav v-if="auth.isAuthenticated" class="flex items-center gap-1">
          <RouterLink
            v-for="link in navLinks"
            :key="link.to"
            :to="link.to"
            class="rounded-md px-3 py-2 text-sm transition-colors hover:bg-muted"
            :class="isActive(link.to) ? 'bg-muted font-medium' : 'text-muted-foreground'"
          >
            {{ link.label }}
          </RouterLink>
        </nav>

        <div class="flex items-center gap-3">
          <template v-if="auth.isAuthenticated">
            <div class="hidden items-center gap-2 text-sm text-muted-foreground sm:flex">
              <Shield v-if="auth.isAdmin" class="size-4" />
              <User v-else class="size-4" />
              {{ auth.user?.name }}
            </div>
            <Button variant="outline" size="sm" @click="handleLogout">
              <LogOut class="size-4" />
              Wyloguj
            </Button>
          </template>
          <template v-else>
            <Button as-child variant="ghost" size="sm">
              <RouterLink to="/login">Logowanie</RouterLink>
            </Button>
            <Button as-child size="sm">
              <RouterLink to="/register">Rejestracja</RouterLink>
            </Button>
          </template>
        </div>
      </div>
    </header>

    <Separator />

    <main class="mx-auto max-w-6xl px-4 py-8">
      <RouterView />
    </main>
  </div>
</template>
