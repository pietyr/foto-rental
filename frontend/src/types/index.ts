export type UserRole = 'admin' | 'user';

export interface User {
  id: number;
  name: string;
  email: string;
  role: UserRole;
}

export interface Category {
  id: number;
  name: string;
}

export type EquipmentStatus = 'available' | 'rented' | 'maintenance';

export interface Equipment {
  id: number;
  category_id: number;
  name: string;
  description: string | null;
  brand: string | null;
  model: string | null;
  price_per_day: string;
  status: EquipmentStatus;
  category?: Category;
}

export type RentalStatus = 'pending' | 'active' | 'completed' | 'cancelled';

export interface Rental {
  id: number;
  user_id: number;
  equipment_id: number;
  start_date: string;
  end_date: string | null;
  status: RentalStatus;
  equipment?: Equipment;
  user?: User;
}

export interface ValidationErrors {
  message?: string;
  errors?: Record<string, string[]>;
}
