import { type HTMLAttributes, forwardRef } from 'react';
import { cn } from '@/lib/utils';

const variants = {
  default:
    'bg-primary/10 text-primary border-transparent',
  secondary:
    'bg-secondary text-secondary-foreground border-transparent',
  destructive:
    'bg-destructive/10 text-destructive border-transparent',
  success:
    'bg-success/10 text-success border-transparent',
  warning:
    'bg-warning/10 text-warning-foreground border-transparent',
  outline: 'text-foreground',
} as const;

interface BadgeProps extends HTMLAttributes<HTMLDivElement> {
  variant?: keyof typeof variants;
}

const Badge = forwardRef<HTMLDivElement, BadgeProps>(
  ({ className, variant = 'default', ...props }, ref) => {
    return (
      <div
        ref={ref}
        className={cn(
          'inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold transition-colors',
          variants[variant],
          className
        )}
        {...props}
      />
    );
  }
);
Badge.displayName = 'Badge';

export { Badge, type BadgeProps, variants as badgeVariants };
