'use client';

import { useQuery } from '@tanstack/react-query';
import { Card } from '@/components/ui/card';
import { ClipboardList, Plus } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Badge } from '@/components/ui/badge';
import { Search } from 'lucide-react';
import api from '@/lib/api';
import type { Task } from '@/types';
import { useState } from 'react';
import { format } from 'date-fns';

const statusVariant: Record<string, 'success' | 'warning' | 'default' | 'destructive'> = {
  COMPLETED: 'success', IN_PROGRESS: 'warning', PENDING: 'default', CANCELLED: 'destructive',
};
const priorityVariant: Record<string, 'destructive' | 'warning' | 'default'> = {
  HIGH: 'destructive', MEDIUM: 'warning', LOW: 'default',
};

export default function TasksPage() {
  const [search, setSearch] = useState('');
  const { data: tasks } = useQuery({
    queryKey: ['tasks', search],
    queryFn: () => api.get<{ data: Task[] }>('/api/tasks', { params: { search } }).then(r => r.data.data),
  });

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <div><h1 className="text-2xl font-bold">Tasks</h1><p className="text-muted-foreground">Manage tasks and to-dos</p></div>
        <Button><Plus className="h-4 w-4 mr-2" /> New Task</Button>
      </div>
      <Card className="p-4">
        <div className="relative">
          <Search className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
          <Input placeholder="Search tasks..." value={search} onChange={e => setSearch(e.target.value)} className="pl-9" />
        </div>
      </Card>
      <Card>
        <div className="overflow-x-auto">
          <table className="w-full">
            <thead>
              <tr className="border-b bg-muted/50">
                <th className="text-left text-xs font-medium text-muted-foreground uppercase tracking-wider px-4 py-3">Title</th>
                <th className="text-left text-xs font-medium text-muted-foreground uppercase tracking-wider px-4 py-3">Status</th>
                <th className="text-left text-xs font-medium text-muted-foreground uppercase tracking-wider px-4 py-3">Priority</th>
                <th className="text-left text-xs font-medium text-muted-foreground uppercase tracking-wider px-4 py-3">Due Date</th>
                <th className="text-left text-xs font-medium text-muted-foreground uppercase tracking-wider px-4 py-3">Category</th>
              </tr>
            </thead>
            <tbody className="divide-y">
              {tasks?.map(t => (
                <tr key={t.id} className="hover:bg-muted/50">
                  <td className="px-4 py-3 text-sm font-medium">{t.title}</td>
                  <td className="px-4 py-3"><Badge variant={statusVariant[t.status] || 'default'}>{t.status}</Badge></td>
                  <td className="px-4 py-3"><Badge variant={priorityVariant[t.priority] || 'default'}>{t.priority}</Badge></td>
                  <td className="px-4 py-3 text-sm text-muted-foreground">{t.dueDate ? format(new Date(t.dueDate), 'MMM dd, yyyy') : '-'}</td>
                  <td className="px-4 py-3 text-sm text-muted-foreground">{t.category || '-'}</td>
                </tr>
              ))}
              {(!tasks || tasks.length === 0) && (
                <tr><td colSpan={5} className="px-4 py-8 text-center text-sm text-muted-foreground">No tasks found</td></tr>
              )}
            </tbody>
          </table>
        </div>
      </Card>
    </div>
  );
}
