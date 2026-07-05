import * as React from 'react';

interface DialogProps { open: boolean; onOpenChange: (open: boolean) => void; children: React.ReactNode; }

export function Dialog({ open, onOpenChange, children }: DialogProps) {
  if (!open) return null;
  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center">
      <div className="fixed inset-0 bg-black/50" onClick={() => onOpenChange(false)} />
      <div className="relative bg-background rounded-lg shadow-lg z-10 max-w-lg w-full mx-4 border">
        {children}
      </div>
    </div>
  );
}

export function DialogContent({ className = '', children, ...props }: any) {
  return <div className={`p-6 ${className}`} {...props}>{children}</div>;
}

export function DialogHeader({ children }: { children: React.ReactNode }) {
  return <div className="mb-4">{children}</div>;
}

export function DialogTitle({ children, className = '' }: { children: React.ReactNode; className?: string }) {
  return <h2 className={`text-lg font-semibold ${className}`}>{children}</h2>;
}

export function DialogFooter({ children, className = '' }: { children: React.ReactNode; className?: string }) {
  return <div className={`flex items-center justify-end pt-4 border-t mt-4 ${className}`}>{children}</div>;
}
