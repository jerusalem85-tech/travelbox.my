import { create } from 'zustand';
import type { User } from '@/types';
import { authService } from '@/services/auth';

interface AuthState {
  user: User | null;
  token: string | null;
  isLoading: boolean;
  login: (email: string, password: string) => Promise<void>;
  logout: () => void;
  fetchUser: () => Promise<void>;
}

export const useAuthStore = create<AuthState>((set) => ({
  user: null,
  token:
    typeof window !== 'undefined' ? localStorage.getItem('accessToken') : null,
  isLoading: false,

  login: async (email, password) => {
    const data = await authService.login({ email, password });
    localStorage.setItem('accessToken', data.access_token);
    localStorage.setItem('refreshToken', data.refresh_token);
    set({ user: data.user, token: data.access_token });
  },

  logout: () => {
    localStorage.removeItem('accessToken');
    localStorage.removeItem('refreshToken');
    set({ user: null, token: null });
    window.location.href = '/login';
  },

  fetchUser: async () => {
    try {
      set({ isLoading: true });
      const user = await authService.me();
      set({ user, isLoading: false });
    } catch {
      set({ isLoading: false });
    }
  },
}));
