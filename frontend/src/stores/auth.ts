import { ref, computed } from 'vue';
import { defineStore } from 'pinia';
import { api, setToken, ApiError } from '@/api/client';
import type { User } from '@/types';

interface AuthResponse {
  user: User;
  token: string;
}

interface UserResponse {
  user: User;
}

export const useAuthStore = defineStore('auth', () => {
  const user = ref<User | null>(null);
  const loading = ref(false);

  const isAuthenticated = computed(() => user.value !== null);
  const isAdmin = computed(() => user.value?.role === 'admin');

  async function fetchUser(): Promise<void> {
    try {
      const response = await api.get<UserResponse>('/user');
      user.value = response.user;
    } catch {
      user.value = null;
      setToken(null);
    }
  }

  async function login(email: string, password: string): Promise<Record<string, string[]>> {
    loading.value = true;
    try {
      const response = await api.post<AuthResponse>('/login', { email, password });
      setToken(response.token);
      user.value = response.user;
      return {};
    } catch (error) {
      if (error instanceof ApiError) {
        return error.errors;
      }
      throw error;
    } finally {
      loading.value = false;
    }
  }

  async function register(
    name: string,
    email: string,
    password: string,
    password_confirmation: string,
  ): Promise<Record<string, string[]>> {
    loading.value = true;
    try {
      const response = await api.post<AuthResponse>('/register', {
        name,
        email,
        password,
        password_confirmation,
      });
      setToken(response.token);
      user.value = response.user;
      return {};
    } catch (error) {
      if (error instanceof ApiError) {
        return error.errors;
      }
      throw error;
    } finally {
      loading.value = false;
    }
  }

  async function logout(): Promise<void> {
    try {
      await api.post('/logout');
    } finally {
      user.value = null;
      setToken(null);
    }
  }

  return {
    user,
    loading,
    isAuthenticated,
    isAdmin,
    fetchUser,
    login,
    register,
    logout,
  };
});
