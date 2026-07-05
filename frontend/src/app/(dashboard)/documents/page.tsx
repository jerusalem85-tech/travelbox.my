'use client';

import { Card } from '@/components/ui/card';
import { FileText, Plus } from 'lucide-react';
import { Button } from '@/components/ui/button';

export default function DocumentsPage() {
  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <div><h1 className="text-2xl font-bold">Documents</h1><p className="text-muted-foreground">Generate and manage documents</p></div>
        <Button><Plus className="h-4 w-4 mr-2" /> Generate Document</Button>
      </div>
      <Card className="p-12 flex flex-col items-center justify-center text-center">
        <FileText className="h-12 w-12 text-muted-foreground mb-4" />
        <h3 className="text-lg font-medium">Document Generation</h3>
        <p className="text-sm text-muted-foreground mt-1 max-w-md">
          Generate itineraries, vouchers, invoices, and other travel documents.
          PDF generation will be added in the next update.
        </p>
      </Card>
    </div>
  );
}
