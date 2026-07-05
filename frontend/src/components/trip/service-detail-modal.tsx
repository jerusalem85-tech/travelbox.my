'use client';

import { useState, useEffect } from 'react';
import { useMutation, useQueryClient } from '@tanstack/react-query';
import {
  Dialog, DialogContent, DialogHeader, DialogTitle, DialogFooter,
} from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import {
  Plane, Hotel, Car, MapPin, Ship, Train, Shield, FileText, HelpCircle, Trash2,
} from 'lucide-react';
import api from '@/lib/api';
import toast from 'react-hot-toast';

const typeIcons: Record<string, any> = {
  FLIGHT: Plane, HOTEL: Hotel, TRANSFER: Car, TOUR: MapPin,
  CRUISE: Ship, TRAIN: Train, VISA: Shield, INSURANCE: FileText,
  CAR_RENTAL: Car,
};

const typeDefaults: Record<string, { hasTime?: boolean; hasCheckInOut?: boolean; label: string }> = {
  FLIGHT: { hasTime: true, label: 'Flight' },
  HOTEL: { hasCheckInOut: true, label: 'Hotel' },
  TRANSFER: { hasTime: true, label: 'Transfer' },
  TOUR: { hasTime: true, label: 'Tour' },
  CRUISE: { hasTime: true, label: 'Cruise' },
  TRAIN: { hasTime: true, label: 'Train' },
  VISA: { label: 'Visa' },
  INSURANCE: { label: 'Insurance' },
  CAR_RENTAL: { hasCheckInOut: true, label: 'Car Rental' },
};

export default function ServiceDetailModal({
  tripId, service, open, onClose,
}: {
  tripId: string; service: any; open: boolean; onClose: () => void;
}) {
  const queryClient = useQueryClient();
  const [form, setForm] = useState<any>({});

  useEffect(() => {
    if (service) {
      setForm({
        type: service.type || 'OTHER',
        description: service.description || '',
        supplierId: service.supplierId || '',
        supplierReference: service.supplierReference || '',
        startDate: service.startDate ? service.startDate.slice(0, 10) : '',
        endDate: service.endDate ? service.endDate.slice(0, 10) : '',
        departureAt: service.departureAt ? service.departureAt.slice(0, 16) : '',
        arrivalAt: service.arrivalAt ? service.arrivalAt.slice(0, 16) : '',
        checkinAt: service.checkinAt ? service.checkinAt.slice(0, 10) : '',
        checkoutAt: service.checkoutAt ? service.checkoutAt.slice(0, 10) : '',
        costPrice: service.costPrice ?? 0,
        sellingPrice: service.sellingPrice ?? 0,
        currency: service.currency || 'USD',
        status: service.status || 'CONFIRMED',
        isOptional: service.isOptional ?? false,
        notes: service.notes || '',
        confirmationNumber: service.confirmationNumber || '',
        bookingReference: service.bookingReference || '',
      });
    }
  }, [service]);

  const updateMutation = useMutation({
    mutationFn: (data: any) => api.put(`/api/trips/${tripId}/services/${service.id}`, data).then(r => r.data),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['trip', tripId] });
      toast.success('Service updated');
      onClose();
    },
    onError: (err: any) => toast.error(err?.response?.data?.message || 'Update failed'),
  });

  const deleteMutation = useMutation({
    mutationFn: () => api.delete(`/api/trips/${tripId}/services/${service.id}`).then(r => r.data),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['trip', tripId] });
      toast.success('Service deleted');
      onClose();
    },
    onError: (err: any) => toast.error(err?.response?.data?.message || 'Delete failed'),
  });

  const update = (field: string, value: any) => setForm({ ...form, [field]: value });

  if (!service) return null;

  const Icon = typeIcons[form.type as keyof typeof typeIcons] || HelpCircle;
  const config = typeDefaults[form.type as keyof typeof typeDefaults] || { label: form.type };

  const handleSave = () => {
    const payload: any = {};
    const fields = ['type', 'description', 'supplierId', 'supplierReference', 'startDate', 'endDate',
      'departureAt', 'arrivalAt', 'checkinAt', 'checkoutAt', 'costPrice', 'sellingPrice',
      'currency', 'status', 'isOptional', 'notes', 'confirmationNumber', 'bookingReference'];
    for (const f of fields) {
      if (form[f] !== (service as any)[f]) payload[f] = form[f];
    }
    if (Object.keys(payload).length === 0) { onClose(); return; }
    updateMutation.mutate(payload);
  };

  return (
    <Dialog open={open} onOpenChange={(o) => { if (!o) onClose(); }}>
      <DialogContent className="max-w-lg max-h-[90vh] overflow-y-auto">
        <DialogHeader>
          <DialogTitle className="flex items-center gap-2">
            <Icon className="h-5 w-5" />
            Edit {config.label}
          </DialogTitle>
        </DialogHeader>

        <div className="space-y-3 py-2">
          <div className="grid grid-cols-2 gap-3">
            <div className="col-span-2">
              <label className="text-xs text-muted-foreground mb-1 block">Type</label>
              <select className="flex h-10 w-full rounded-lg border border-input bg-background px-3 py-2 text-sm" value={form.type} onChange={e => update('type', e.target.value)}>
                {Object.keys(typeDefaults).map(t => <option key={t} value={t}>{typeDefaults[t as keyof typeof typeDefaults].label}</option>)}
                <option value="OTHER">Other</option>
              </select>
            </div>

            <div className="col-span-2">
              <label className="text-xs text-muted-foreground mb-1 block">Description</label>
              <textarea className="flex w-full rounded-lg border border-input bg-background px-3 py-2 text-sm min-h-[60px]" value={form.description} onChange={e => update('description', e.target.value)} />
            </div>

            <div>
              <label className="text-xs text-muted-foreground mb-1 block">Start Date</label>
              <Input type="date" value={form.startDate} onChange={e => update('startDate', e.target.value)} />
            </div>
            <div>
              <label className="text-xs text-muted-foreground mb-1 block">End Date</label>
              <Input type="date" value={form.endDate} onChange={e => update('endDate', e.target.value)} />
            </div>

            {config.hasTime && (
              <>
                <div>
                  <label className="text-xs text-muted-foreground mb-1 block">Departure</label>
                  <Input type="datetime-local" value={form.departureAt} onChange={e => update('departureAt', e.target.value)} />
                </div>
                <div>
                  <label className="text-xs text-muted-foreground mb-1 block">Arrival</label>
                  <Input type="datetime-local" value={form.arrivalAt} onChange={e => update('arrivalAt', e.target.value)} />
                </div>
              </>
            )}

            {config.hasCheckInOut && (
              <>
                <div>
                  <label className="text-xs text-muted-foreground mb-1 block">Check-in</label>
                  <Input type="date" value={form.checkinAt} onChange={e => update('checkinAt', e.target.value)} />
                </div>
                <div>
                  <label className="text-xs text-muted-foreground mb-1 block">Check-out</label>
                  <Input type="date" value={form.checkoutAt} onChange={e => update('checkoutAt', e.target.value)} />
                </div>
              </>
            )}

            <div>
              <label className="text-xs text-muted-foreground mb-1 block">Cost Price</label>
              <Input type="number" step="0.01" value={form.costPrice} onChange={e => update('costPrice', parseFloat(e.target.value) || 0)} />
            </div>
            <div>
              <label className="text-xs text-muted-foreground mb-1 block">Selling Price</label>
              <Input type="number" step="0.01" value={form.sellingPrice} onChange={e => update('sellingPrice', parseFloat(e.target.value) || 0)} />
            </div>
            <div>
              <label className="text-xs text-muted-foreground mb-1 block">Profit</label>
              <Input type="number" step="0.01" value={(form.sellingPrice - form.costPrice).toFixed(2)} disabled className="bg-muted" />
            </div>

            <div>
              <label className="text-xs text-muted-foreground mb-1 block">Currency</label>
              <select className="flex h-10 w-full rounded-lg border border-input bg-background px-3 py-2 text-sm" value={form.currency} onChange={e => update('currency', e.target.value)}>
                <option value="USD">USD</option><option value="EUR">EUR</option>
                <option value="GBP">GBP</option><option value="AED">AED</option>
                <option value="EGP">EGP</option>
              </select>
            </div>

            <div>
              <label className="text-xs text-muted-foreground mb-1 block">Status</label>
              <select className="flex h-10 w-full rounded-lg border border-input bg-background px-3 py-2 text-sm" value={form.status} onChange={e => update('status', e.target.value)}>
                <option value="PROVISIONAL">Provisional</option>
                <option value="CONFIRMED">Confirmed</option>
                <option value="CANCELLED">Cancelled</option>
                <option value="PENDING">Pending</option>
              </select>
            </div>

            <div>
              <label className="text-xs text-muted-foreground mb-1 block">Booking Ref</label>
              <Input value={form.bookingReference} onChange={e => update('bookingReference', e.target.value)} />
            </div>
            <div>
              <label className="text-xs text-muted-foreground mb-1 block">Confirmation #</label>
              <Input value={form.confirmationNumber} onChange={e => update('confirmationNumber', e.target.value)} />
            </div>

            <div className="col-span-2">
              <label className="text-xs text-muted-foreground mb-1 block">Notes</label>
              <textarea className="flex w-full rounded-lg border border-input bg-background px-3 py-2 text-sm min-h-[60px]" value={form.notes} onChange={e => update('notes', e.target.value)} />
            </div>

            <div className="col-span-2 flex items-center gap-2">
              <input type="checkbox" id="isOptional" checked={form.isOptional} onChange={e => update('isOptional', e.target.checked)} className="rounded border-gray-300" />
              <label htmlFor="isOptional" className="text-sm">Optional service (not included in base package)</label>
            </div>
          </div>
        </div>

        <DialogFooter className="gap-2">
          <Button variant="destructive" size="sm" onClick={() => { if (confirm('Delete this service?')) deleteMutation.mutate(); }} disabled={deleteMutation.isPending}>
            <Trash2 className="h-4 w-4 mr-1" /> {deleteMutation.isPending ? 'Deleting...' : 'Delete'}
          </Button>
          <Button variant="outline" onClick={onClose}>Cancel</Button>
          <Button onClick={handleSave} disabled={updateMutation.isPending}>
            {updateMutation.isPending ? 'Saving...' : 'Save'}
          </Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>
  );
}
