'use client';

import { useState } from 'react';
import { useRouter } from 'next/navigation';
import { useMutation } from '@tanstack/react-query';
import { Card } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Plane, ArrowLeft, ArrowRight, Check } from 'lucide-react';
import api from '@/lib/api';
import toast from 'react-hot-toast';

const steps = ['Trip Info', 'Customer', 'Dates', 'Review'];

export default function NewTripPage() {
  const router = useRouter();
  const [step, setStep] = useState(0);
  const [form, setForm] = useState({
    name: '', customerId: '', source: 'DIRECT', priority: 'MEDIUM',
    startDate: '', endDate: '', destination: '', currency: 'USD', notes: '',
  });

  const mutation = useMutation({
    mutationFn: (data: any) => api.post('/api/trips', data).then(r => r.data),
    onSuccess: (trip: any) => {
      toast.success('Trip created');
      router.push(`/trips/${trip.id}`);
    },
    onError: (err: any) => toast.error(err?.response?.data?.message || 'Failed to create trip'),
  });

  const update = (field: string, value: string) => setForm({ ...form, [field]: value });

  return (
    <div className="max-w-2xl mx-auto space-y-6">
      <div><h1 className="text-2xl font-bold">New Trip</h1><p className="text-muted-foreground">Create a new travel trip</p></div>

      <div className="flex gap-1">
        {steps.map((s, i) => (
          <div key={s} className={`flex-1 h-1.5 rounded-full ${i <= step ? 'bg-primary' : 'bg-muted'}`} />
        ))}
      </div>
      <p className="text-sm text-muted-foreground text-center">Step {step + 1} of {steps.length}: {steps[step]}</p>

      <Card className="p-6">
        {step === 0 && (
          <div className="space-y-4">
            <h2 className="text-lg font-semibold">Trip Information</h2>
            <div><label className="text-xs text-muted-foreground mb-1 block">Trip Name</label><Input value={form.name} onChange={e => update('name', e.target.value)} placeholder="e.g. Summer Vacation in Thailand" /></div>
            <div><label className="text-xs text-muted-foreground mb-1 block">Destination</label><Input value={form.destination} onChange={e => update('destination', e.target.value)} placeholder="e.g. Phuket, Thailand" /></div>
            <div className="grid grid-cols-2 gap-3">
              <div><label className="text-xs text-muted-foreground mb-1 block">Source</label><select className="flex h-10 w-full rounded-lg border border-input bg-background px-3 py-2 text-sm" value={form.source} onChange={e => update('source', e.target.value)}><option value="DIRECT">Direct</option><option value="AGENT">Agent</option><option value="ONLINE">Online</option><option value="REFERRAL">Referral</option></select></div>
              <div><label className="text-xs text-muted-foreground mb-1 block">Priority</label><select className="flex h-10 w-full rounded-lg border border-input bg-background px-3 py-2 text-sm" value={form.priority} onChange={e => update('priority', e.target.value)}><option value="LOW">Low</option><option value="MEDIUM">Medium</option><option value="HIGH">High</option><option value="URGENT">Urgent</option></select></div>
            </div>
          </div>
        )}

        {step === 1 && (
          <div className="space-y-4">
            <h2 className="text-lg font-semibold">Customer</h2>
            <div><label className="text-xs text-muted-foreground mb-1 block">Customer ID</label><Input value={form.customerId} onChange={e => update('customerId', e.target.value)} placeholder="Enter existing customer ID" /></div>
            <p className="text-xs text-muted-foreground">For now, enter a customer ID from the database. Customer search coming soon.</p>
            <div><label className="text-xs text-muted-foreground mb-1 block">Currency</label><select className="flex h-10 w-full rounded-lg border border-input bg-background px-3 py-2 text-sm" value={form.currency} onChange={e => update('currency', e.target.value)}><option value="USD">USD</option><option value="EUR">EUR</option><option value="GBP">GBP</option><option value="AED">AED</option></select></div>
          </div>
        )}

        {step === 2 && (
          <div className="space-y-4">
            <h2 className="text-lg font-semibold">Travel Dates</h2>
            <div className="grid grid-cols-2 gap-3">
              <div><label className="text-xs text-muted-foreground mb-1 block">Start Date</label><Input type="date" value={form.startDate} onChange={e => update('startDate', e.target.value)} /></div>
              <div><label className="text-xs text-muted-foreground mb-1 block">End Date</label><Input type="date" value={form.endDate} onChange={e => update('endDate', e.target.value)} /></div>
            </div>
            <div><label className="text-xs text-muted-foreground mb-1 block">Notes</label><textarea className="flex w-full rounded-lg border border-input bg-background px-3 py-2 text-sm min-h-[80px]" value={form.notes} onChange={e => update('notes', e.target.value)} placeholder="Internal notes about this trip..." /></div>
          </div>
        )}

        {step === 3 && (
          <div className="space-y-4">
            <h2 className="text-lg font-semibold">Review</h2>
            <div className="bg-muted/50 rounded-lg p-4 space-y-2 text-sm">
              <div className="flex justify-between"><span className="text-muted-foreground">Name</span><span className="font-medium">{form.name || '(not set)'}</span></div>
              <div className="flex justify-between"><span className="text-muted-foreground">Destination</span><span>{form.destination || '(not set)'}</span></div>
              <div className="flex justify-between"><span className="text-muted-foreground">Customer ID</span><span className="font-mono text-xs">{form.customerId || '(not set)'}</span></div>
              <div className="flex justify-between"><span className="text-muted-foreground">Dates</span><span>{form.startDate ? `${form.startDate} to ${form.endDate}` : '(not set)'}</span></div>
              <div className="flex justify-between"><span className="text-muted-foreground">Priority</span><span>{form.priority}</span></div>
              <div className="flex justify-between"><span className="text-muted-foreground">Source</span><span>{form.source}</span></div>
            </div>
          </div>
        )}

        <div className="flex justify-between mt-6 pt-4 border-t">
          <Button variant="outline" onClick={() => step > 0 ? setStep(s => s - 1) : router.back()} disabled={mutation.isPending}>
            <ArrowLeft className="h-4 w-4 mr-1" /> {step === 0 ? 'Cancel' : 'Back'}
          </Button>
          {step < 3 ? (
            <Button onClick={() => setStep(s => s + 1)}>
              Next <ArrowRight className="h-4 w-4 ml-1" />
            </Button>
          ) : (
            <Button onClick={() => mutation.mutate(form)} disabled={mutation.isPending}>
              {mutation.isPending ? 'Creating...' : 'Create Trip'} <Check className="h-4 w-4 ml-1" />
            </Button>
          )}
        </div>
      </Card>
    </div>
  );
}
