'use client';

import { Card } from '@/components/ui/card';
import { BarChart3 } from 'lucide-react';

export default function ReportsPage() {
  return (
    <div className="space-y-6">
      <div><h1 className="text-2xl font-bold">Reports</h1><p className="text-muted-foreground">View business reports and analytics</p></div>
      <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
        {['Revenue Report', 'Profit & Loss', 'Customer Report', 'Supplier Report', 'Trip Report', 'Tax Summary'].map((r) => (
          <Card key={r} className="p-4 hover:bg-muted/50 cursor-pointer transition-colors">
            <div className="flex items-center gap-3">
              <BarChart3 className="h-5 w-5 text-muted-foreground" />
              <div><p className="text-sm font-medium">{r}</p><p className="text-xs text-muted-foreground">View report</p></div>
            </div>
          </Card>
        ))}
      </div>
    </div>
  );
}
