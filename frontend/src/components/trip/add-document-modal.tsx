'use client';

import { useState } from 'react';
import { useMutation, useQueryClient } from '@tanstack/react-query';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogFooter } from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { FileText } from 'lucide-react';
import api from '@/lib/api';
import toast from 'react-hot-toast';

interface Props { tripId: string; open: boolean; onClose: () => void; }

export default function AddDocumentModal({ tripId, open, onClose }: Props) {
  const queryClient = useQueryClient();
  const [form, setForm] = useState({ name: '', type: 'OTHER', notes: '' });

  const mutation = useMutation({
    mutationFn: (data: any) => api.post('/api/documents', data).then(r => r.data),
    onSuccess: () => { queryClient.invalidateQueries({ queryKey: ['trip', tripId] }); toast.success('Document added'); onClose(); setForm({ name: '', type: 'OTHER', notes: '' }); },
    onError: (err: any) => toast.error(err?.response?.data?.message || 'Failed to add document'),
  });

  const update = (f: string, v: any) => setForm({ ...form, [f]: v });

  return (
    <Dialog open={open} onOpenChange={(o) => { if (!o) onClose(); }}>
      <DialogContent className="max-w-md">
        <DialogHeader><DialogTitle className="flex items-center gap-2"><FileText className="h-5 w-5" /> Add Document</DialogTitle></DialogHeader>
        <div className="space-y-3 py-2">
          <div><label className="text-xs text-muted-foreground mb-1 block">Document Name *</label><Input value={form.name} onChange={e => update('name', e.target.value)} placeholder="e.g. Invoice, Itinerary" /></div>
          <div className="grid grid-cols-2 gap-3">
            <div><label className="text-xs text-muted-foreground mb-1 block">Type</label><select className="flex h-10 w-full rounded-lg border border-input bg-background px-3 py-2 text-sm" value={form.type} onChange={e => update('type', e.target.value)}><option value="INVOICE">Invoice</option><option value="VOUCHER">Voucher</option><option value="ITINERARY">Itinerary</option><option value="CONTRACT">Contract</option><option value="TICKET">Ticket</option><option value="VISA">Visa</option><option value="OTHER">Other</option></select></div>
            <div><label className="text-xs text-muted-foreground mb-1 block">Status</label><select className="flex h-10 w-full rounded-lg border border-input bg-background px-3 py-2 text-sm" defaultValue="DRAFT"><option value="DRAFT">Draft</option><option value="FINAL">Final</option><option value="SENT">Sent</option></select></div>
          </div>
          <div><label className="text-xs text-muted-foreground mb-1 block">Notes</label><textarea className="flex w-full rounded-lg border border-input bg-background px-3 py-2 text-sm min-h-[60px]" value={form.notes} onChange={e => update('notes', e.target.value)} /></div>
        </div>
        <DialogFooter><Button variant="outline" onClick={onClose}>Cancel</Button><Button onClick={() => mutation.mutate({ ...form, tripId })} disabled={mutation.isPending || !form.name.trim()}>{mutation.isPending ? 'Adding...' : 'Add Document'}</Button></DialogFooter>
      </DialogContent>
    </Dialog>
  );
}
