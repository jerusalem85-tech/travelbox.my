import api from '@/lib/api';
import type { LoginRequest, RegisterRequest, AuthResponse, User } from '@/types';

export const authService = {
  login: (data: LoginRequest) =>
    api.post<AuthResponse>('/api/auth/login', data).then((r) => r.data),

  register: (data: RegisterRequest) =>
    api.post<AuthResponse>('/api/auth/register', data).then((r) => r.data),

  refresh: (refreshToken: string) =>
    api
      .post<{ access_token: string; refresh_token: string }>('/api/auth/refresh', {
        refreshToken,
      })
      .then((r) => r.data),

  me: () => api.get<User>('/api/auth/me').then((r) => r.data),
};
