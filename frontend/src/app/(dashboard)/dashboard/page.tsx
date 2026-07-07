'use client';

import { useEffect, useState } from 'react';
import { useRouter } from 'next/navigation';
import { Card, CardTitle } from '@/components/ui/card';
import { api } from '@/lib/api';

interface DashData {
  totalUsers: number;
  activeUsers: number;
  recentUsers: { id: string; firstName: string; lastName: string; email: string; role: string; createdAt: string }[];
}

export default function DashboardPage() {
  const router = useRouter();
  const [data, setData] = useState<DashData | null>(null);

  useEffect(() => {
    if (!localStorage.getItem('token')) return void router.push('/login');
    api.get('/dashboard').then(setData).catch(() => router.push('/login'));
  }, [router]);

  if (!data) return <p className="text-gray-500">Loading...</p>;

  return (
    <div className="space-y-6">
      <h1 className="text-2xl font-bold">Dashboard</h1>
      <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
        <Card>
          <CardTitle>Total Users</CardTitle>
          <p className="text-3xl font-bold mt-2">{data.totalUsers}</p>
        </Card>
        <Card>
          <CardTitle>Active Users</CardTitle>
          <p className="text-3xl font-bold mt-2 text-green-600">{data.activeUsers}</p>
        </Card>
      </div>
      <Card>
        <CardTitle>Recent Users</CardTitle>
        <div className="mt-4 overflow-x-auto">
          <table className="w-full text-sm">
            <thead>
              <tr className="border-b text-left">
                <th className="pb-2 font-medium">Name</th>
                <th className="pb-2 font-medium">Email</th>
                <th className="pb-2 font-medium">Role</th>
              </tr>
            </thead>
            <tbody>
              {data.recentUsers.map((u) => (
                <tr key={u.id} className="border-b last:border-0">
                  <td className="py-2">{u.firstName} {u.lastName}</td>
                  <td className="py-2 text-gray-600">{u.email}</td>
                  <td className="py-2"><span className="px-2 py-0.5 rounded bg-gray-100 text-xs">{u.role}</span></td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </Card>
    </div>
  );
}
