'use client';

import { useQuery } from '@tanstack/react-query';
import { dashboardService } from '@/services/dashboard';
import { Card } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import {
  Plane,
  Users,
  Building2,
  DollarSign,
  TrendingUp,
  Activity,
  PieChart,
  BarChart3,
} from 'lucide-react';
import { format } from 'date-fns';
import {
  BarChart, Bar, XAxis, YAxis, Tooltip, ResponsiveContainer, PieChart as RePieChart, Pie, Cell,
} from 'recharts';

const statusVariant: Record<string, 'success' | 'warning' | 'default' | 'destructive'> = {
  CONFIRMED: 'success',
  BOOKED: 'success',
  DEPOSIT_PAID: 'success',
  TICKETED: 'success',
  TRAVELING: 'success',
  COMPLETED: 'default',
  LEAD: 'warning',
  QUOTATION: 'warning',
  NEGOTIATION: 'warning',
  DOCUMENTS_SENT: 'warning',
  CANCELLED: 'destructive',
  ARCHIVED: 'destructive',
};

export default function DashboardPage() {
  const { data: stats, isLoading: statsLoading } = useQuery({
    queryKey: ['dashboard-stats'],
    queryFn: dashboardService.stats,
  });

  const { data: upcomingTrips, isLoading: tripsLoading } = useQuery({
    queryKey: ['upcoming-trips'],
    queryFn: dashboardService.upcomingTrips,
  });

  const { data: recentActivity, isLoading: activityLoading } = useQuery({
    queryKey: ['recent-activity'],
    queryFn: dashboardService.recentActivity,
  });

  const { data: monthlyStats, isLoading: monthlyLoading } = useQuery({
    queryKey: ['monthly-stats'],
    queryFn: dashboardService.monthlyStats,
  });

  const otherCount = Math.max((stats?.tripCount ?? 0) - (stats?.activeTrips ?? 0), 0);
  const statusData = [
    { name: 'Active', value: stats?.activeTrips ?? 0, color: '#22c55e' },
    { name: 'Other', value: otherCount, color: '#6366f1' },
  ];

  const statCards = [
    { label: 'Total Trips', value: stats?.tripCount ?? 0, icon: Plane, color: 'text-blue-600' },
    { label: 'Active Trips', value: stats?.activeTrips ?? 0, icon: Activity, color: 'text-green-600' },
    { label: 'Customers', value: stats?.customerCount ?? 0, icon: Users, color: 'text-purple-600' },
    { label: 'Suppliers', value: stats?.supplierCount ?? 0, icon: Building2, color: 'text-orange-600' },
    { label: 'Revenue', value: stats ? `$${stats.totalRevenue.toLocaleString()}` : '$0', icon: DollarSign, color: 'text-emerald-600' },
    { label: 'Profit', value: stats ? `$${stats.profit.toLocaleString()}` : '$0', icon: TrendingUp, color: 'text-cyan-600' },
  ];

  const activities = recentActivity
    ? [
        ...(recentActivity.trips || []).map((t: any) => ({ id: t.id, desc: `Trip ${t.tripNumber} updated to ${t.status}`, time: t.updatedAt, user: t.customer ? `${t.customer.firstName || ''} ${t.customer.lastName || ''}`.trim() : '' })),
        ...(recentActivity.tasks || []).map((t: any) => ({ id: t.id, desc: `Task: ${t.title}`, time: t.updatedAt, user: t.trip?.tripNumber || '' })),
        ...(recentActivity.payments || []).map((p: any) => ({ id: p.id, desc: `Payment $${p.amount} ${p.direction}`, time: p.createdAt, user: p.trip?.tripNumber || '' })),
      ].sort((a, b) => new Date(b.time).getTime() - new Date(a.time).getTime()).slice(0, 10)
    : [];

  return (
    <div className="space-y-6">
      <div>
        <h1 className="text-2xl font-bold">Dashboard</h1>
        <p className="text-muted-foreground">Overview of your travel business</p>
      </div>

      <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">
        {statCards.map((stat) => {
          const Icon = stat.icon;
          return (
            <Card key={stat.label} className="p-4">
              <div className="flex items-center justify-between mb-2">
                <p className="text-sm text-muted-foreground">{stat.label}</p>
                <Icon className={`h-5 w-5 ${stat.color}`} />
              </div>
              <p className="text-2xl font-bold">
                {statsLoading ? (
                  <span className="inline-block w-16 h-6 bg-muted rounded animate-pulse" />
                ) : (
                  stat.value
                )}
              </p>
            </Card>
          );
        })}
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <Card className="p-4">
          <div className="flex items-center gap-2 mb-4">
            <BarChart3 className="h-5 w-5 text-muted-foreground" />
            <h2 className="text-lg font-semibold">Revenue & Profit</h2>
          </div>
          {monthlyLoading ? (
            <div className="h-48 bg-muted rounded animate-pulse" />
          ) : monthlyStats && monthlyStats.length > 0 ? (
            <ResponsiveContainer width="100%" height={200}>
              <BarChart data={monthlyStats}>
                <XAxis dataKey="month" tick={{ fontSize: 11 }} />
                <YAxis tick={{ fontSize: 11 }} />
                <Tooltip />
                <Bar dataKey="revenue" fill="#22c55e" name="Revenue" radius={[3, 3, 0, 0]} />
                <Bar dataKey="profit" fill="#6366f1" name="Profit" radius={[3, 3, 0, 0]} />
              </BarChart>
            </ResponsiveContainer>
          ) : (
            <p className="text-sm text-muted-foreground h-48 flex items-center justify-center">No revenue data yet</p>
          )}
        </Card>

        <Card className="p-4">
          <div className="flex items-center gap-2 mb-4">
            <PieChart className="h-5 w-5 text-muted-foreground" />
            <h2 className="text-lg font-semibold">Trips by Status</h2>
          </div>
          {statsLoading ? (
            <div className="h-48 bg-muted rounded animate-pulse" />
          ) : (
            <div className="flex items-center justify-center h-48 gap-6">
              <ResponsiveContainer width={160} height={160}>
                <RePieChart>
                  <Pie data={statusData} cx="50%" cy="50%" innerRadius={35} outerRadius={65} dataKey="value" paddingAngle={2}>
                    {statusData.map((entry, i) => <Cell key={i} fill={entry.color} />)}
                  </Pie>
                </RePieChart>
              </ResponsiveContainer>
              <div className="space-y-2">
                {statusData.map(s => (
                  <div key={s.name} className="flex items-center gap-2 text-sm">
                    <span className="h-3 w-3 rounded-full shrink-0" style={{ backgroundColor: s.color }} />
                    <span className="text-muted-foreground">{s.name}</span>
                    <span className="font-medium ml-auto">{s.value}</span>
                  </div>
                ))}
              </div>
            </div>
          )}
        </Card>
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <Card className="p-4">
          <h2 className="text-lg font-semibold mb-4">Upcoming Trips</h2>
          {tripsLoading ? (
            <div className="space-y-3">{[...Array(4)].map((_, i) => <div key={i} className="h-12 bg-muted rounded animate-pulse" />)}</div>
          ) : upcomingTrips && upcomingTrips.length > 0 ? (
            <div className="space-y-3">
              {upcomingTrips.slice(0, 5).map((trip) => (
                <div key={trip.id} className="flex items-center justify-between py-2 border-b last:border-0">
                  <div className="flex-1 min-w-0">
                    <p className="text-sm font-medium truncate">{trip.name || trip.tripNumber}</p>
                    <p className="text-xs text-muted-foreground">
                      {trip.destination} &middot;{' '}
                      {trip.startDate ? format(new Date(trip.startDate), 'MMM dd, yyyy') : 'TBD'}
                    </p>
                  </div>
                  <div className="flex items-center gap-2 ml-4 shrink-0">
                    <span className="text-xs text-muted-foreground">{trip._count?.passengers ?? 0} pax</span>
                    <Badge variant={statusVariant[trip.status] || 'default'}>{trip.status}</Badge>
                  </div>
                </div>
              ))}
            </div>
          ) : (
            <p className="text-sm text-muted-foreground">No upcoming trips</p>
          )}
        </Card>

        <Card className="p-4">
          <h2 className="text-lg font-semibold mb-4">Recent Activity</h2>
          {activityLoading ? (
            <div className="space-y-3">{[...Array(4)].map((_, i) => <div key={i} className="h-12 bg-muted rounded animate-pulse" />)}</div>
          ) : activities.length > 0 ? (
            <div className="space-y-3">
              {activities.slice(0, 8).map((a) => (
                <div key={a.id} className="flex items-start gap-3 py-2 border-b last:border-0">
                  <div className="h-2 w-2 mt-2 rounded-full bg-primary shrink-0" />
                  <div className="flex-1 min-w-0">
                    <p className="text-sm">{a.desc}</p>
                    <p className="text-xs text-muted-foreground">{a.user} &middot; {format(new Date(a.time), 'MMM dd, HH:mm')}</p>
                  </div>
                </div>
              ))}
            </div>
          ) : (
            <p className="text-sm text-muted-foreground">No recent activity</p>
          )}
        </Card>
      </div>
    </div>
  );
}
