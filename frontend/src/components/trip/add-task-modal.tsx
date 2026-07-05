'use client';

import { useState } from 'react';
import { useMutation, useQueryClient } from '@tanstack/react-query';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogFooter } from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { CheckSquare } from 'lucide-react';
import api from '@/lib/api';
import toast from 'react-hot-toast';

interface Props { tripId: string; open: boolean; onClose: () => void; }

export default function AddTaskModal({ tripId, open, onClose }: Props) {
  const queryClient = useQueryClient();
  const [form, setForm] = useState({ title: '', description: '', priority: 'MEDIUM', dueDate: '' });

  const mutation = useMutation({
    mutationFn: (data: any) => api.post('/api/tasks', data).then(r => r.data),
    onSuccess: () => { queryClient.invalidateQueries({ queryKey: ['trip', tripId] }); toast.success('Task added'); onClose(); setForm({ title: '', description: '', priority: 'MEDIUM', dueDate: '' }); },
    onError: (err: any) => toast.error(err?.response?.data?.message || 'Failed to create task'),
  });

  const update = (f: string, v: any) => setForm({ ...form, [f]: v });

  return (
    <Dialog open={open} onOpenChange={(o) => { if (!o) onClose(); }}>
      <DialogContent className="max-w-md">
        <DialogHeader><DialogTitle className="flex items-center gap-2"><CheckSquare className="h-5 w-5" /> Add Task</DialogTitle></DialogHeader>
        <div className="space-y-3 py-2">
          <div><label className="text-xs text-muted-foreground mb-1 block">Title *</label><Input value={form.title} onChange={e => update('title', e.target.value)} placeholder="e.g. Send visa documents" /></div>
          <div><label className="text-xs text-muted-foreground mb-1 block">Description</label><textarea className="flex w-full rounded-lg border border-input bg-background px-3 py-2 text-sm min-h-[80px]" value={form.description} onChange={e => update('description', e.target.value)} /></div>
          <div className="grid grid-cols-2 gap-3">
            <div><label className="text-xs text-muted-foreground mb-1 block">Priority</label><select className="flex h-10 w-full rounded-lg border border-input bg-background px-3 py-2 text-sm" value={form.priority} onChange={e => update('priority', e.target.value)}><option value="LOW">Low</option><option value="MEDIUM">Medium</option><option value="HIGH">High</option><option value="URGENT">Urgent</option></select></div>
            <div><label className="text-xs text-muted-foreground mb-1 block">Due Date</label><Input type="date" value={form.dueDate} onChange={e => update('dueDate', e.target.value)} /></div>
          </div>
        </div>
        <DialogFooter><Button variant="outline" onClick={onClose}>Cancel</Button><Button onClick={() => mutation.mutate({ ...form, tripId, status: 'PENDING' })} disabled={mutation.isPending || !form.title.trim()}>{mutation.isPending ? 'Adding...' : 'Add Task'}</Button></DialogFooter>
      </DialogContent>
    </Dialog>
  );
}
