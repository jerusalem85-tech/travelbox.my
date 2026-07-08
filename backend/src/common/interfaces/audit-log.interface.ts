export interface AuditLogData {
  action: 'CREATE' | 'UPDATE' | 'DELETE' | 'VIEW' | 'LOGIN';
  entity: string;
  entityId: string;
  oldValue?: Record<string, unknown>;
  newValue?: Record<string, unknown>;
}
