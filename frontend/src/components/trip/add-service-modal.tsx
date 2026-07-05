'use client';

import { useState } from 'react';
import { useMutation, useQueryClient } from '@tanstack/react-query';
import { Dialog as Modal } from '@radix-ui/react-dialog';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Plane, Hotel, Car, MapPin, Ship, Train, Shield, FileText, X } from 'lucide-react';
import api from '@/lib/api';
import toast from 'react-hot-toast';

const serviceTypes = [
  { value: 'FLIGHT', label: 'Flight', icon: Plane, color: 'text-blue-500 bg-blue-50 border-blue-200' },
  { value: 'HOTEL', label: 'Hotel', icon: Hotel, color: 'text-purple-500 bg-purple-50 border-purple-200' },
  { value: 'TRANSFER', label: 'Transfer', icon: Car, color: 'text-orange-500 bg-orange-50 border-orange-200' },
  { value: 'TOUR', label: 'Tour', icon: MapPin, color: 'text-cyan-500 bg-cyan-50 border-cyan-200' },
  { value: 'CRUISE', label: 'Cruise', icon: Ship, color: 'text-teal-500 bg-teal-50 border-teal-200' },
  { value: 'TRAIN', label: 'Train', icon: Train, color: 'text-amber-500 bg-amber-50 border-amber-200' },
  { value: 'VISA', label: 'Visa', icon: FileText, color: 'text-red-500 bg-red-50 border-red-200' },
  { value: 'INSURANCE', label: 'Insurance', icon: Shield, color: 'text-emerald-500 bg-emerald-50 border-emerald-200' },
];

interface Props {
  tripId: string;
  open: boolean;
  onClose: () => void;
}

export default function AddServiceModal({ tripId, open, onClose }: Props) {
  const [step, setStep] = useState<'type' | 'form'>('type');
  const [type, setType] = useState('');
  const [form, setForm] = useState<any>({});
  const queryClient = useQueryClient();

  const mutation = useMutation({
    mutationFn: (data: any) => api.post(`/api/trips/${tripId}/services`, data).then(r => r.data),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['trip', tripId] });
      queryClient.invalidateQueries({ queryKey: ['trip-timeline', tripId] });
      toast.success('Service added');
      onClose();
      setStep('type');
      setType('');
      setForm({});
    },
    onError: (err: any) => toast.error(err?.response?.data?.message || 'Failed to add service'),
  });

  const handleSubmit = () => {
    mutation.mutate({ type, ...form, costPrice: parseFloat(form.costPrice) || 0, sellingPrice: parseFloat(form.sellingPrice) || 0 });
  };

  if (!open) return null;

  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/50" onClick={onClose}>
      <div className="bg-background rounded-xl shadow-lg w-full max-w-lg max-h-[90vh] overflow-y-auto" onClick={e => e.stopPropagation()}>
        <div className="flex items-center justify-between p-4 border-b">
          <h2 className="text-lg font-semibold">{step === 'type' ? 'Add Service' : `Add ${type}`}</h2>
          <button onClick={onClose} className="p-1 rounded-md hover:bg-muted"><X className="h-4 w-4" /></button>
        </div>

        {step === 'type' ? (
          <div className="p-4 grid grid-cols-2 gap-3">
            {serviceTypes.map((st) => {
              const Icon = st.icon;
              return (
                <button key={st.value} onClick={() => { setType(st.value); setStep('form'); }} className={`flex items-center gap-3 p-3 rounded-lg border hover:bg-muted/50 transition-colors text-left ${st.color.split(' ')[0]}`}>
                  <div className={`h-8 w-8 rounded-full flex items-center justify-center border ${st.color}`}><Icon className="h-4 w-4" /></div>
                  <span className="text-sm font-medium">{st.label}</span>
                </button>
              );
            })}
          </div>
        ) : (
          <div className="p-4 space-y-4">
            <div className="grid grid-cols-2 gap-3">
              <div><label className="text-xs text-muted-foreground mb-1 block">Description</label><Input value={form.description || ''} onChange={e => setForm({ ...form, description: e.target.value })} placeholder="e.g. EK 501 DXB-LHR" /></div>
              <div><label className="text-xs text-muted-foreground mb-1 block">Supplier ID</label><Input value={form.supplierId || ''} onChange={e => setForm({ ...form, supplierId: e.target.value })} placeholder="Supplier ID (optional)" /></div>
              <div><label className="text-xs text-muted-foreground mb-1 block">Start Date</label><Input type="date" value={form.startDate || ''} onChange={e => setForm({ ...form, startDate: e.target.value })} /></div>
              <div><label className="text-xs text-muted-foreground mb-1 block">End Date</label><Input type="date" value={form.endDate || ''} onChange={e => setForm({ ...form, endDate: e.target.value })} /></div>
              <div><label className="text-xs text-muted-foreground mb-1 block">Cost Price ($)</label><Input type="number" value={form.costPrice || ''} onChange={e => setForm({ ...form, costPrice: e.target.value })} placeholder="0.00" /></div>
              <div><label className="text-xs text-muted-foreground mb-1 block">Selling Price ($)</label><Input type="number" value={form.sellingPrice || ''} onChange={e => setForm({ ...form, sellingPrice: e.target.value })} placeholder="0.00" /></div>
              <div><label className="text-xs text-muted-foreground mb-1 block">Status</label>
                <select className="flex h-10 w-full rounded-lg border border-input bg-background px-3 py-2 text-sm" value={form.status || 'PENDING'} onChange={e => setForm({ ...form, status: e.target.value })}>
                  <option value="PENDING">Pending</option><option value="CONFIRMED">Confirmed</option><option value="CANCELLED">Cancelled</option>
                </select>
              </div>
              <div><label className="text-xs text-muted-foreground mb-1 block">Currency</label><Input value={form.currency || 'USD'} onChange={e => setForm({ ...form, currency: e.target.value })} /></div>
            </div>
            <div><label className="text-xs text-muted-foreground mb-1 block">Notes</label><textarea className="flex w-full rounded-lg border border-input bg-background px-3 py-2 text-sm min-h-[60px]" value={form.notes || ''} onChange={e => setForm({ ...form, notes: e.target.value })} /></div>
            <div className="flex gap-2 justify-end pt-2 border-t">
              <Button variant="outline" onClick={() => setStep('type')}>Back</Button>
              <Button onClick={handleSubmit} disabled={mutation.isPending}>{mutation.isPending ? 'Adding...' : 'Add Service'}</Button>
            </div>
          </div>
        )}
      </div>
    </div>
  );
}
