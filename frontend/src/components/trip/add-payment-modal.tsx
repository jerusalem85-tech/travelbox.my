'use client';

import { useState } from 'react';
import { useMutation, useQueryClient } from '@tanstack/react-query';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogFooter } from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { DollarSign } from 'lucide-react';
import api from '@/lib/api';
import toast from 'react-hot-toast';

interface Props { tripId: string; open: boolean; onClose: () => void; }

export default function AddPaymentModal({ tripId, open, onClose }: Props) {
  const queryClient = useQueryClient();
  const [form, setForm] = useState({ amount: 100, direction: 'INCOMING', method: 'BANK_TRANSFER', dueDate: '', paidDate: '', reference: '', description: '', isInstallment: false });

  const mutation = useMutation({
    mutationFn: (data: any) => api.post('/api/payments', data).then(r => r.data),
    onSuccess: () => { queryClient.invalidateQueries({ queryKey: ['trip', tripId] }); queryClient.invalidateQueries({ queryKey: ['trip-financial', tripId] }); toast.success('Payment recorded'); onClose(); setForm({ amount: 100, direction: 'INCOMING', method: 'BANK_TRANSFER', dueDate: '', paidDate: '', reference: '', description: '', isInstallment: false }); },
    onError: (err: any) => toast.error(err?.response?.data?.message || 'Failed to record payment'),
  });

  const update = (f: string, v: any) => setForm({ ...form, [f]: v });

  return (
    <Dialog open={open} onOpenChange={(o) => { if (!o) onClose(); }}>
      <DialogContent className="max-w-md">
        <DialogHeader><DialogTitle className="flex items-center gap-2"><DollarSign className="h-5 w-5" /> Record Payment</DialogTitle></DialogHeader>
        <div className="space-y-3 py-2">
          <div className="grid grid-cols-2 gap-3">
            <div><label className="text-xs text-muted-foreground mb-1 block">Amount</label><Input type="number" step="0.01" value={form.amount} onChange={e => update('amount', parseFloat(e.target.value) || 0)} /></div>
            <div><label className="text-xs text-muted-foreground mb-1 block">Direction</label><select className="flex h-10 w-full rounded-lg border border-input bg-background px-3 py-2 text-sm" value={form.direction} onChange={e => update('direction', e.target.value)}><option value="INCOMING">Customer Payment</option><option value="OUTGOING">Supplier Payment</option></select></div>
            <div><label className="text-xs text-muted-foreground mb-1 block">Method</label><select className="flex h-10 w-full rounded-lg border border-input bg-background px-3 py-2 text-sm" value={form.method} onChange={e => update('method', e.target.value)}><option value="CASH">Cash</option><option value="BANK_TRANSFER">Bank Transfer</option><option value="CREDIT_CARD">Credit Card</option><option value="CHECK">Check</option></select></div>
            <div><label className="text-xs text-muted-foreground mb-1 block">Due Date</label><Input type="date" value={form.dueDate} onChange={e => update('dueDate', e.target.value)} /></div>
            <div><label className="text-xs text-muted-foreground mb-1 block">Paid Date</label><Input type="date" value={form.paidDate} onChange={e => update('paidDate', e.target.value)} /></div>
            <div className="col-span-2"><label className="text-xs text-muted-foreground mb-1 block">Reference</label><Input value={form.reference} onChange={e => update('reference', e.target.value)} placeholder="Invoice # or reference" /></div>
            <div className="col-span-2"><label className="text-xs text-muted-foreground mb-1 block">Description</label><textarea className="flex w-full rounded-lg border border-input bg-background px-3 py-2 text-sm min-h-[60px]" value={form.description} onChange={e => update('description', e.target.value)} /></div>
            <div className="col-span-2 flex items-center gap-2"><input type="checkbox" id="installment" checked={form.isInstallment} onChange={e => update('isInstallment', e.target.checked)} className="rounded border-gray-300" /><label htmlFor="installment" className="text-sm">This is an installment payment</label></div>
          </div>
        </div>
        <DialogFooter><Button variant="outline" onClick={onClose}>Cancel</Button><Button onClick={() => mutation.mutate({ ...form, tripId })} disabled={mutation.isPending}>{mutation.isPending ? 'Recording...' : 'Record Payment'}</Button></DialogFooter>
      </DialogContent>
    </Dialog>
  );
}
