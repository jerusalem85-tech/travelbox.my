'use client';

import { useQuery } from '@tanstack/react-query';
import { Card } from '@/components/ui/card';
import { Building2, Plus } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Search } from 'lucide-react';
import api from '@/lib/api';
import type { Supplier } from '@/types';
import { useState } from 'react';

export default function SuppliersPage() {
  const [search, setSearch] = useState('');
  const { data: suppliers } = useQuery({
    queryKey: ['suppliers', search],
    queryFn: () => api.get<{ data: Supplier[] }>('/api/suppliers', { params: { search } }).then(r => r.data.data),
  });

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <div><h1 className="text-2xl font-bold">Suppliers</h1><p className="text-muted-foreground">Manage suppliers</p></div>
        <Button><Plus className="h-4 w-4 mr-2" /> Add Supplier</Button>
      </div>
      <Card className="p-4">
        <div className="relative">
          <Search className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
          <Input placeholder="Search suppliers..." value={search} onChange={e => setSearch(e.target.value)} className="pl-9" />
        </div>
      </Card>
      <Card>
        <div className="overflow-x-auto">
          <table className="w-full">
            <thead>
              <tr className="border-b bg-muted/50">
                <th className="text-left text-xs font-medium text-muted-foreground uppercase tracking-wider px-4 py-3">Company</th>
                <th className="text-left text-xs font-medium text-muted-foreground uppercase tracking-wider px-4 py-3">Contact</th>
                <th className="text-left text-xs font-medium text-muted-foreground uppercase tracking-wider px-4 py-3">Email</th>
                <th className="text-left text-xs font-medium text-muted-foreground uppercase tracking-wider px-4 py-3">Category</th>
                <th className="text-right text-xs font-medium text-muted-foreground uppercase tracking-wider px-4 py-3">Commission</th>
              </tr>
            </thead>
            <tbody className="divide-y">
              {suppliers?.map(s => (
                <tr key={s.id} className="hover:bg-muted/50">
                  <td className="px-4 py-3 text-sm font-medium">{s.companyName}</td>
                  <td className="px-4 py-3 text-sm text-muted-foreground">{s.contactPerson}</td>
                  <td className="px-4 py-3 text-sm text-muted-foreground">{s.email}</td>
                  <td className="px-4 py-3"><span className="text-xs bg-muted px-2 py-0.5 rounded-full">{s.category}</span></td>
                  <td className="px-4 py-3 text-sm text-right">{s.commissionRate ? `${s.commissionRate}%` : '-'}</td>
                </tr>
              ))}
              {(!suppliers || suppliers.length === 0) && (
                <tr><td colSpan={5} className="px-4 py-8 text-center text-sm text-muted-foreground">No suppliers found</td></tr>
              )}
            </tbody>
          </table>
        </div>
      </Card>
    </div>
  );
}
