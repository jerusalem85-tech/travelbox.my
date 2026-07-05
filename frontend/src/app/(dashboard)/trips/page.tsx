'use client';

import { useState } from 'react';
import { useQuery } from '@tanstack/react-query';
import { tripService } from '@/services/trips';
import { Card } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Badge } from '@/components/ui/badge';
import { Search, ChevronLeft, ChevronRight, Plus, ExternalLink } from 'lucide-react';
import { format } from 'date-fns';
import { useRouter } from 'next/navigation';

const statusColors: Record<string, 'default' | 'success' | 'warning' | 'destructive'> = {
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

const statuses = ['', 'LEAD', 'QUOTATION', 'NEGOTIATION', 'CONFIRMED', 'DEPOSIT_PAID', 'BOOKED', 'TICKETED', 'DOCUMENTS_SENT', 'TRAVELING', 'COMPLETED', 'CANCELLED', 'ARCHIVED'];

export default function TripsPage() {
  const router = useRouter();
  const [page, setPage] = useState(1);
  const [search, setSearch] = useState('');
  const [statusFilter, setStatusFilter] = useState<string>('');
  const limit = 10;

  const { data, isLoading } = useQuery({
    queryKey: ['trips', page, search, statusFilter],
    queryFn: () =>
      tripService.list({
        page,
        limit,
        search: search || undefined,
        status: statusFilter || undefined,
      }),
  });

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold">Trips</h1>
          <p className="text-muted-foreground">Manage all travel trips</p>
        </div>
        <Button onClick={() => router.push('/trips/new')}>
          <Plus className="h-4 w-4 mr-2" />
          New Trip
        </Button>
      </div>

      <Card className="p-4">
        <div className="flex flex-col sm:flex-row gap-4">
          <div className="flex-1 relative">
            <Search className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
            <Input
              placeholder="Search trips..."
              value={search}
              onChange={(e) => { setSearch(e.target.value); setPage(1); }}
              className="pl-9"
            />
          </div>
          <div className="flex gap-2 flex-wrap">
            {statuses.map((status) => (
              <button
                key={status}
                onClick={() => { setStatusFilter(status); setPage(1); }}
                className={`px-3 py-1.5 rounded-lg text-sm font-medium transition-colors ${
                  statusFilter === status
                    ? 'bg-primary text-primary-foreground'
                    : 'bg-muted text-muted-foreground hover:bg-muted/80'
                }`}
              >
                {status || 'All'}
              </button>
            ))}
          </div>
        </div>
      </Card>

      <Card>
        <div className="overflow-x-auto">
          <table className="w-full">
            <thead>
              <tr className="border-b bg-muted/50">
                <th className="text-left text-xs font-medium text-muted-foreground uppercase tracking-wider px-4 py-3">Trip #</th>
                <th className="text-left text-xs font-medium text-muted-foreground uppercase tracking-wider px-4 py-3">Name</th>
                <th className="text-left text-xs font-medium text-muted-foreground uppercase tracking-wider px-4 py-3">Destination</th>
                <th className="text-left text-xs font-medium text-muted-foreground uppercase tracking-wider px-4 py-3">Dates</th>
                <th className="text-left text-xs font-medium text-muted-foreground uppercase tracking-wider px-4 py-3">Status</th>
                <th className="text-right text-xs font-medium text-muted-foreground uppercase tracking-wider px-4 py-3">Revenue</th>
                <th className="text-right text-xs font-medium text-muted-foreground uppercase tracking-wider px-4 py-3">Profit</th>
              </tr>
            </thead>
            <tbody className="divide-y">
              {isLoading ? (
                [...Array(5)].map((_, i) => (
                  <tr key={i}>
                    {[...Array(7)].map((_, j) => (
                      <td key={j} className="px-4 py-3"><div className="h-5 bg-muted rounded animate-pulse" /></td>
                    ))}
                  </tr>
                ))
              ) : data && data.data.length > 0 ? (
                data.data.map((trip) => (
                  <tr key={trip.id} className="hover:bg-muted/50 transition-colors cursor-pointer" onClick={() => router.push(`/trips/${trip.id}`)}>
                    <td className="px-4 py-3 text-sm font-mono">{trip.tripNumber}</td>
                    <td className="px-4 py-3 text-sm font-medium">{trip.name}</td>
                    <td className="px-4 py-3 text-sm text-muted-foreground">{trip.destination}</td>
                    <td className="px-4 py-3 text-sm text-muted-foreground whitespace-nowrap">
                      {trip.startDate ? format(new Date(trip.startDate), 'MMM dd') : '—'} - {trip.endDate ? format(new Date(trip.endDate), 'MMM dd, yyyy') : '—'}
                    </td>
                    <td className="px-4 py-3">
                      <Badge variant={statusColors[trip.status] || 'default'}>{trip.status}</Badge>
                    </td>
                    <td className="px-4 py-3 text-sm text-right">${(trip.totalSelling || 0).toLocaleString()}</td>
                    <td className="px-4 py-3 text-sm text-right font-medium">${(trip.totalProfit || 0).toLocaleString()}</td>
                  </tr>
                ))
              ) : (
                <tr>
                  <td colSpan={7} className="px-4 py-8 text-center text-sm text-muted-foreground">No trips found</td>
                </tr>
              )}
            </tbody>
          </table>
        </div>

        {data && data.totalPages > 1 && (
          <div className="flex items-center justify-between px-4 py-3 border-t">
            <p className="text-sm text-muted-foreground">Page {data.page} of {data.totalPages} ({data.total} total)</p>
            <div className="flex items-center gap-2">
              <Button variant="outline" size="sm" disabled={page <= 1} onClick={() => setPage((p) => Math.max(1, p - 1))}>
                <ChevronLeft className="h-4 w-4" />
              </Button>
              <Button variant="outline" size="sm" disabled={page >= data.totalPages} onClick={() => setPage((p) => p + 1)}>
                <ChevronRight className="h-4 w-4" />
              </Button>
            </div>
          </div>
        )}
      </Card>
    </div>
  );
}
