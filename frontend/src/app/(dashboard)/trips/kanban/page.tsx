'use client';

import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import { useRouter } from 'next/navigation';
import { Card } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { tripService } from '@/services/trips';
import { Plane, Plus, ArrowLeft, ArrowRight } from 'lucide-react';
import { format } from 'date-fns';
import toast from 'react-hot-toast';
import api from '@/lib/api';

const stages = ['LEAD', 'QUOTATION', 'NEGOTIATION', 'CONFIRMED', 'DEPOSIT_PAID', 'BOOKED', 'TICKETED', 'DOCUMENTS_SENT', 'TRAVELING', 'COMPLETED', 'CANCELLED', 'ARCHIVED'];

const stageColors: Record<string, string> = {
  LEAD: 'bg-amber-50 border-amber-200',
  QUOTATION: 'bg-orange-50 border-orange-200',
  NEGOTIATION: 'bg-yellow-50 border-yellow-200',
  CONFIRMED: 'bg-green-50 border-green-200',
  DEPOSIT_PAID: 'bg-emerald-50 border-emerald-200',
  BOOKED: 'bg-teal-50 border-teal-200',
  TICKETED: 'bg-cyan-50 border-cyan-200',
  DOCUMENTS_SENT: 'bg-blue-50 border-blue-200',
  TRAVELING: 'bg-indigo-50 border-indigo-200',
  COMPLETED: 'bg-purple-50 border-purple-200',
  CANCELLED: 'bg-red-50 border-red-200',
  ARCHIVED: 'bg-gray-50 border-gray-200',
};

function StageColumn({ stage, trips, onMove }: { stage: string; trips: any[]; onMove: (id: string, to: string) => void }) {
  return (
    <div className="shrink-0 w-72">
      <div className={`rounded-t-lg px-3 py-2 border ${stageColors[stage] || 'bg-muted'}`}>
        <div className="flex items-center justify-between">
          <span className="font-medium text-sm">{stage.replace(/_/g, ' ')}</span>
          <Badge variant="outline" className="text-xs">{trips.length}</Badge>
        </div>
      </div>
      <div className="space-y-2 p-2 min-h-[200px] border-x border-b rounded-b-lg bg-muted/20">
        {trips.length === 0 ? (
          <p className="text-xs text-muted-foreground text-center py-8">No trips</p>
        ) : (
          trips.map((trip) => (
            <Card key={trip.id} className="p-3 cursor-pointer hover:shadow-md transition-shadow" onClick={() => window.location.href = `/trips/${trip.id}`}>
              <p className="font-medium text-sm truncate">{trip.name || trip.tripNumber}</p>
              <p className="text-xs text-muted-foreground mt-1">{trip.destination || 'No destination'}</p>
              <div className="flex items-center justify-between mt-2">
                <span className="text-xs text-muted-foreground">{trip.startDate ? format(new Date(trip.startDate), 'MMM dd') : ''}</span>
                <span className="text-xs font-mono">{trip.customer?.firstName?.[0] || '?'}</span>
              </div>
              <div className="flex gap-1 mt-2 pt-2 border-t">
                {stages[Math.max(0, stages.indexOf(stage) - 1)] && (
                  <Button variant="ghost" size="icon" className="h-6 w-6" onClick={(e) => { e.stopPropagation(); onMove(trip.id, stages[stages.indexOf(stage) - 1]); }}>
                    <ArrowLeft className="h-3 w-3" />
                  </Button>
                )}
                {stages[Math.min(stages.length - 1, stages.indexOf(stage) + 1)] && (
                  <Button variant="ghost" size="icon" className="h-6 w-6 ml-auto" onClick={(e) => { e.stopPropagation(); onMove(trip.id, stages[stages.indexOf(stage) + 1]); }}>
                    <ArrowRight className="h-3 w-3" />
                  </Button>
                )}
              </div>
            </Card>
          ))
        )}
      </div>
    </div>
  );
}

export default function KanbanPage() {
  const router = useRouter();
  const queryClient = useQueryClient();

  const { data: trips, isLoading } = useQuery({
    queryKey: ['trips', 'kanban'],
    queryFn: () => tripService.list({ limit: 200 }),
  });

  const mutation = useMutation({
    mutationFn: ({ id, toStage }: { id: string; toStage: string }) => api.post(`/api/smart-engine/workflow/${id}/transition`, { toStage }).then(r => r.data),
    onSuccess: () => { queryClient.invalidateQueries({ queryKey: ['trips', 'kanban'] }); toast.success('Stage updated'); },
    onError: (err: any) => toast.error(err?.response?.data?.message || 'Failed to update stage'),
  });

  const grouped = stages.reduce((acc, stage) => ({ ...acc, [stage]: (trips?.data || []).filter((t: any) => t.status === stage) }), {} as Record<string, any[]>);

  return (
    <div className="space-y-4">
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold">Kanban Board</h1>
          <p className="text-muted-foreground">Drag and drop trips between workflow stages</p>
        </div>
        <Button onClick={() => router.push('/trips/new')}><Plus className="h-4 w-4 mr-1" /> New Trip</Button>
      </div>

      {isLoading ? (
        <div className="flex gap-4 overflow-x-auto pb-4">
          {[...Array(5)].map((_, i) => <div key={i} className="shrink-0 w-72 h-96 bg-muted rounded animate-pulse" />)}
        </div>
      ) : (
        <div className="flex gap-4 overflow-x-auto pb-4">
          {stages.map((stage) => (
            <StageColumn key={stage} stage={stage} trips={grouped[stage] || []} onMove={(id, toStage) => mutation.mutate({ id, toStage })} />
          ))}
        </div>
      )}
    </div>
  );
}
