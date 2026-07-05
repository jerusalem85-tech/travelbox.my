'use client';

import { useQuery } from '@tanstack/react-query';
import { Card } from '@/components/ui/card';
import { Shield, Users, BarChart3, Settings, Activity } from 'lucide-react';
import api from '@/lib/api';

export default function AdminPage() {
  const { data: stats } = useQuery({
    queryKey: ['admin-stats'],
    queryFn: () => api.get('/api/admin/stats').then(r => r.data),
  });

  return (
    <div className="space-y-6">
      <div><h1 className="text-2xl font-bold">Admin</h1><p className="text-muted-foreground">System administration</p></div>
      <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <Card className="p-4"><div className="flex items-center gap-3"><Users className="h-5 w-5 text-blue-500" /><div><p className="text-2xl font-bold">{stats?.userCount ?? 0}</p><p className="text-xs text-muted-foreground">Users</p></div></div></Card>
        <Card className="p-4"><div className="flex items-center gap-3"><Activity className="h-5 w-5 text-green-500" /><div><p className="text-2xl font-bold">{stats?.activeUsers ?? 0}</p><p className="text-xs text-muted-foreground">Active</p></div></div></Card>
        <Card className="p-4"><div className="flex items-center gap-3"><BarChart3 className="h-5 w-5 text-purple-500" /><div><p className="text-2xl font-bold">{stats?.tripCount ?? 0}</p><p className="text-xs text-muted-foreground">Total Trips</p></div></div></Card>
        <Card className="p-4"><div className="flex items-center gap-3"><Settings className="h-5 w-5 text-orange-500" /><div><p className="text-2xl font-bold">{stats?.systemHealth ?? 'OK'}</p><p className="text-xs text-muted-foreground">System</p></div></div></Card>
      </div>
    </div>
  );
}
