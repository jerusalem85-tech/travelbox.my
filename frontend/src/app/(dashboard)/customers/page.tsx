'use client';

import { useQuery } from '@tanstack/react-query';
import { Card } from '@/components/ui/card';
import { Users, Plus } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Search } from 'lucide-react';
import api from '@/lib/api';
import type { Customer } from '@/types';
import { useState } from 'react';

export default function CustomersPage() {
  const [search, setSearch] = useState('');
  const { data: customers } = useQuery({
    queryKey: ['customers', search],
    queryFn: () => api.get<{ data: Customer[] }>('/api/customers', { params: { search } }).then(r => r.data.data),
  });

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <div><h1 className="text-2xl font-bold">Customers</h1><p className="text-muted-foreground">Manage customers</p></div>
        <Button><Plus className="h-4 w-4 mr-2" /> Add Customer</Button>
      </div>
      <Card className="p-4">
        <div className="relative">
          <Search className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
          <Input placeholder="Search customers..." value={search} onChange={e => setSearch(e.target.value)} className="pl-9" />
        </div>
      </Card>
      <Card>
        <div className="overflow-x-auto">
          <table className="w-full">
            <thead>
              <tr className="border-b bg-muted/50">
                <th className="text-left text-xs font-medium text-muted-foreground uppercase tracking-wider px-4 py-3">Name</th>
                <th className="text-left text-xs font-medium text-muted-foreground uppercase tracking-wider px-4 py-3">Email</th>
                <th className="text-left text-xs font-medium text-muted-foreground uppercase tracking-wider px-4 py-3">Phone</th>
                <th className="text-left text-xs font-medium text-muted-foreground uppercase tracking-wider px-4 py-3">Type</th>
                <th className="text-right text-xs font-medium text-muted-foreground uppercase tracking-wider px-4 py-3">Trips</th>
              </tr>
            </thead>
            <tbody className="divide-y">
              {customers?.map(c => (
                <tr key={c.id} className="hover:bg-muted/50">
                  <td className="px-4 py-3 text-sm font-medium">{c.firstName} {c.lastName}</td>
                  <td className="px-4 py-3 text-sm text-muted-foreground">{c.email}</td>
                  <td className="px-4 py-3 text-sm text-muted-foreground">{c.phone}</td>
                  <td className="px-4 py-3"><span className="text-xs bg-muted px-2 py-0.5 rounded-full">{c.type}</span></td>
                  <td className="px-4 py-3 text-sm text-right">{c.totalTrips ?? 0}</td>
                </tr>
              ))}
              {(!customers || customers.length === 0) && (
                <tr><td colSpan={5} className="px-4 py-8 text-center text-sm text-muted-foreground">No customers found</td></tr>
              )}
            </tbody>
          </table>
        </div>
      </Card>
    </div>
  );
}
