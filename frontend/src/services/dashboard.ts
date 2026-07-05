import api from '@/lib/api';
import type { DashboardStats, UpcomingTrip, RecentActivity } from '@/types';

export const dashboardService = {
  stats: () =>
    api.get<DashboardStats>('/api/dashboard/stats').then((r) => r.data),

  upcomingTrips: () =>
    api.get<UpcomingTrip[]>('/api/dashboard/upcoming-trips').then((r) => r.data),

  recentActivity: () =>
    api.get<RecentActivity>('/api/dashboard/recent-activity').then((r) => r.data),

  monthlyStats: () =>
    api
      .get<{ month: string; revenue: number; cost: number; profit: number }[]>(
        '/api/dashboard/monthly-stats'
      )
      .then((r) => r.data),
};
