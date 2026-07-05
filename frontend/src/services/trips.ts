import api from '@/lib/api';
import type { Trip, PaginatedResponse } from '@/types';

export interface TripFilters {
  page?: number;
  limit?: number;
  search?: string;
  status?: string;
  sortBy?: string;
  sortOrder?: 'asc' | 'desc';
}

export const tripService = {
  list: (filters?: TripFilters) =>
    api
      .get<PaginatedResponse<Trip>>('/api/trips', { params: filters })
      .then((r) => r.data),

  getById: (id: string) =>
    api.get<Trip>(`/api/trips/${id}`).then((r) => r.data),

  create: (data: Partial<Trip>) =>
    api.post<Trip>('/api/trips', data).then((r) => r.data),

  update: (id: string, data: Partial<Trip>) =>
    api.put<Trip>(`/api/trips/${id}`, data).then((r) => r.data),

  delete: (id: string) =>
    api.delete(`/api/trips/${id}`).then((r) => r.data),
};
