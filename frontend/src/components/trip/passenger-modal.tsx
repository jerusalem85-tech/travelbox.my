'use client';

import { useState } from 'react';
import { useMutation, useQueryClient } from '@tanstack/react-query';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogFooter } from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Users } from 'lucide-react';
import api from '@/lib/api';
import toast from 'react-hot-toast';

interface Props { tripId: string; open: boolean; onClose: () => void; }

export default function PassengerModal({ tripId, open, onClose }: Props) {
  const queryClient = useQueryClient();
  const [form, setForm] = useState({ firstName: '', lastName: '', dateOfBirth: '', gender: '', nationality: '', passportNumber: '', passportExpiry: '' });

  const mutation = useMutation({
    mutationFn: (data: any) => api.post('/api/passengers', data).then(r => r.data),
    onSuccess: () => { queryClient.invalidateQueries({ queryKey: ['trip', tripId] }); toast.success('Passenger added'); onClose(); setForm({ firstName: '', lastName: '', dateOfBirth: '', gender: '', nationality: '', passportNumber: '', passportExpiry: '' }); },
    onError: (err: any) => toast.error(err?.response?.data?.message || 'Failed to add passenger'),
  });

  const update = (f: string, v: any) => setForm({ ...form, [f]: v });

  return (
    <Dialog open={open} onOpenChange={(o) => { if (!o) onClose(); }}>
      <DialogContent className="max-w-md">
        <DialogHeader><DialogTitle className="flex items-center gap-2"><Users className="h-5 w-5" /> Add Passenger</DialogTitle></DialogHeader>
        <div className="space-y-3 py-2">
          <div className="grid grid-cols-2 gap-3">
            <div><label className="text-xs text-muted-foreground mb-1 block">First Name *</label><Input value={form.firstName} onChange={e => update('firstName', e.target.value)} /></div>
            <div><label className="text-xs text-muted-foreground mb-1 block">Last Name *</label><Input value={form.lastName} onChange={e => update('lastName', e.target.value)} /></div>
            <div><label className="text-xs text-muted-foreground mb-1 block">Date of Birth</label><Input type="date" value={form.dateOfBirth} onChange={e => update('dateOfBirth', e.target.value)} /></div>
            <div><label className="text-xs text-muted-foreground mb-1 block">Gender</label><select className="flex h-10 w-full rounded-lg border border-input bg-background px-3 py-2 text-sm" value={form.gender} onChange={e => update('gender', e.target.value)}><option value="">Select</option><option value="MALE">Male</option><option value="FEMALE">Female</option><option value="OTHER">Other</option></select></div>
            <div><label className="text-xs text-muted-foreground mb-1 block">Nationality</label><Input value={form.nationality} onChange={e => update('nationality', e.target.value)} /></div>
            <div><label className="text-xs text-muted-foreground mb-1 block">Passport #</label><Input value={form.passportNumber} onChange={e => update('passportNumber', e.target.value)} /></div>
            <div className="col-span-2"><label className="text-xs text-muted-foreground mb-1 block">Passport Expiry</label><Input type="date" value={form.passportExpiry} onChange={e => update('passportExpiry', e.target.value)} /></div>
          </div>
        </div>
        <DialogFooter><Button variant="outline" onClick={onClose}>Cancel</Button><Button onClick={() => mutation.mutate({ ...form, tripId })} disabled={mutation.isPending || !form.firstName.trim() || !form.lastName.trim()}>{mutation.isPending ? 'Adding...' : 'Add Passenger'}</Button></DialogFooter>
      </DialogContent>
    </Dialog>
  );
}
