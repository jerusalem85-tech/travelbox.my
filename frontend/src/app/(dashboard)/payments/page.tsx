'use client';

import { useQuery } from '@tanstack/react-query';
import { Card } from '@/components/ui/card';
import { CreditCard, Plus } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Badge } from '@/components/ui/badge';
import { Search } from 'lucide-react';
import api from '@/lib/api';
import type { Payment } from '@/types';
import { useState } from 'react';
import { format } from 'date-fns';

const statusVariant: Record<string, 'success' | 'warning' | 'default' | 'destructive'> = {
  PAID: 'success', PENDING: 'warning', OVERDUE: 'destructive', CANCELLED: 'destructive',
};

export default function PaymentsPage() {
  const [search, setSearch] = useState('');
  const { data: payments } = useQuery({
    queryKey: ['payments', search],
    queryFn: () => api.get<{ data: Payment[] }>('/api/payments', { params: { search } }).then(r => r.data.data),
  });

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <div><h1 className="text-2xl font-bold">Payments</h1><p className="text-muted-foreground">Manage payments and invoices</p></div>
        <Button><Plus className="h-4 w-4 mr-2" /> Record Payment</Button>
      </div>
      <Card className="p-4">
        <div className="relative">
          <Search className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
          <Input placeholder="Search payments..." value={search} onChange={e => setSearch(e.target.value)} className="pl-9" />
        </div>
      </Card>
      <Card>
        <div className="overflow-x-auto">
          <table className="w-full">
            <thead>
              <tr className="border-b bg-muted/50">
                <th className="text-left text-xs font-medium text-muted-foreground uppercase tracking-wider px-4 py-3">#</th>
                <th className="text-left text-xs font-medium text-muted-foreground uppercase tracking-wider px-4 py-3">Description</th>
                <th className="text-left text-xs font-medium text-muted-foreground uppercase tracking-wider px-4 py-3">Direction</th>
                <th className="text-right text-xs font-medium text-muted-foreground uppercase tracking-wider px-4 py-3">Amount</th>
                <th className="text-left text-xs font-medium text-muted-foreground uppercase tracking-wider px-4 py-3">Due Date</th>
                <th className="text-left text-xs font-medium text-muted-foreground uppercase tracking-wider px-4 py-3">Status</th>
              </tr>
            </thead>
            <tbody className="divide-y">
              {payments?.map(p => (
                <tr key={p.id} className="hover:bg-muted/50">
                  <td className="px-4 py-3 text-sm font-mono">{p.paymentNumber}</td>
                  <td className="px-4 py-3 text-sm text-muted-foreground">{p.description}</td>
                  <td className="px-4 py-3 text-sm">{p.direction === 'INCOMING' ? 'Incoming' : 'Outgoing'}</td>
                  <td className="px-4 py-3 text-sm text-right font-medium">${p.amount.toLocaleString()}</td>
                  <td className="px-4 py-3 text-sm text-muted-foreground">{p.dueDate ? format(new Date(p.dueDate), 'MMM dd, yyyy') : '-'}</td>
                  <td className="px-4 py-3"><Badge variant={statusVariant[p.status] || 'default'}>{p.status}</Badge></td>
                </tr>
              ))}
              {(!payments || payments.length === 0) && (
                <tr><td colSpan={6} className="px-4 py-8 text-center text-sm text-muted-foreground">No payments found</td></tr>
              )}
            </tbody>
          </table>
        </div>
      </Card>
    </div>
  );
}
