import type { EquipmentStatus, RentalStatus } from '@/types';

export const equipmentStatusLabels: Record<EquipmentStatus, string> = {
  available: 'Dostępny',
  rented: 'Wypożyczony',
  maintenance: 'Serwis',
};

export const rentalStatusLabels: Record<RentalStatus, string> = {
  pending: 'Oczekujące',
  active: 'Aktywne',
  completed: 'Zakończone',
  cancelled: 'Anulowane',
};

export function formatPrice(value: string | number): string {
  const amount = typeof value === 'string' ? parseFloat(value) : value;
  return new Intl.NumberFormat('pl-PL', {
    style: 'currency',
    currency: 'PLN',
  }).format(amount);
}

export function formatDate(value: string | null): string {
  if (!value) {
    return '-';
  }

  return new Intl.DateTimeFormat('pl-PL').format(new Date(value));
}
