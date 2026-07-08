# TravelBox ERP — User Roles & Permissions (V1)

## 1. Role Hierarchy

```
SUPER_ADMIN  ───  Full system access, all tenants, billing
     │
     ▼
ADMIN  ───  Tenant-wide access, user management, settings
     │
     ├── MANAGER  ───  All modules, reports, approvals
     │
     ├── ACCOUNTANT  ───  Accounting, payments, reports, read-only trips
     │
     ├── SALES_AGENT  ───  Create/edit trips & customers, payments, documents
     │
     ├── OPERATIONS  ───  View trips, manage services, update statuses
     │
     ├── CUSTOMER_SERVICE  ───  View trips, add notes, manage tasks
     │
     └── VIEWER  ───  Read-only access to assigned data
```

## 2. Permission Matrix

| Module | Action | SA | AD | MG | AC | SL | OP | CS | VW |
|--------|--------|:--:|:--:|:--:|:--:|:--:|:--:|:--:|:--:|
| **Dashboard** | View | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ |
| **Trips** | Create | ✓ | ✓ | ✓ | - | ✓ | - | - | - |
| | Read (all) | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ |
| | Read (own) | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ |
| | Update | ✓ | ✓ | ✓ | - | ✓ | ✓ | - | - |
| | Delete (soft) | ✓ | ✓ | ✓ | - | - | - | - | - |
| | Change Status | ✓ | ✓ | ✓ | - | ✓ | ✓ | - | - |
| | Assign | ✓ | ✓ | ✓ | - | - | ✓ | - | - |
| **Customers** | Create | ✓ | ✓ | ✓ | - | ✓ | ✓ | ✓ | - |
| | Read | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ |
| | Update | ✓ | ✓ | ✓ | - | ✓ | ✓ | ✓ | - |
| | Delete | ✓ | ✓ | ✓ | - | - | - | - | - |
| **Suppliers** | CRUD | ✓ | ✓ | ✓ | - | ✓ | ✓ | - | - |
| | Read | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ |
| **Services** | CRUD | ✓ | ✓ | ✓ | - | ✓ | ✓ | ✓ | - |
| (Flights, Hotels, etc.) | Read | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ |
| **Payments** | Create | ✓ | ✓ | ✓ | ✓ | ✓ | - | - | - |
| | Read | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ |
| | Approve | ✓ | ✓ | ✓ | ✓ | - | - | - | - |
| | Refund | ✓ | ✓ | ✓ | - | - | - | - | - |
| **Invoices** | Create | ✓ | ✓ | ✓ | ✓ | ✓ | - | - | - |
| | Read | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ |
| | Send | ✓ | ✓ | ✓ | ✓ | ✓ | - | - | - |
| | Cancel | ✓ | ✓ | ✓ | ✓ | - | - | - | - |
| **Accounting** | View Ledger | ✓ | ✓ | ✓ | ✓ | - | - | - | - |
| | Journal Entries | ✓ | ✓ | ✓ | ✓ | - | - | - | - |
| | Reports (P&L, BS) | ✓ | ✓ | ✓ | ✓ | - | - | - | - |
| **Documents** | Generate | ✓ | ✓ | ✓ | - | ✓ | ✓ | - | - |
| | Read | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ |
| | Send | ✓ | ✓ | ✓ | ✓ | ✓ | - | - | - |
| **Tasks** | CRUD | ✓ | ✓ | ✓ | - | ✓ | ✓ | ✓ | - |
| | Read | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ |
| **Reports** | Sales | ✓ | ✓ | ✓ | ✓ | ✓ | - | - | - |
| | Profit | ✓ | ✓ | ✓ | ✓ | - | - | - | - |
| | Cash Flow | ✓ | ✓ | ✓ | ✓ | - | - | - | - |
| | Outstanding | ✓ | ✓ | ✓ | ✓ | ✓ | - | - | - |
| | Performance | ✓ | ✓ | ✓ | - | ✓ | - | - | - |
| **Users** | CRUD | ✓ | ✓ | - | - | - | - | - | - |
| **Settings** | Update | ✓ | ✓ | - | - | - | - | - | - |
| | Read | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ |
| **Audit Logs** | View | ✓ | ✓ | ✓ | - | - | - | - | - |

## 3. Data Access Rules

| Role | Data Scope |
|------|-----------|
| SUPER_ADMIN | All tenants |
| ADMIN | Own tenant only |
| MANAGER | Own tenant only, all records |
| ACCOUNTANT | Own tenant only, all records (read-only for non-financial) |
| SALES_AGENT | Own tenant, own created trips + assigned trips |
| OPERATIONS | Own tenant, assigned trips + unassigned trips |
| CUSTOMER_SERVICE | Own tenant, assigned trips only |
| VIEWER | Own tenant, records explicitly shared |

## 4. Implementation: Guards & Decorators

```typescript
// Route-level role check
@Roles(UserRole.MANAGER, UserRole.ADMIN)
@Get('profit-report')
async getProfitReport() { ... }

// Granular permission check
@Permissions('trips', 'update-status')
@Patch(':id/status')
async changeStatus() { ... }

// Data scope filter (service layer)
class TripsService {
  async findAll(filter: TripFilterDto, user: JwtPayload) {
    if (user.role === UserRole.SALES_AGENT) {
      filter.createdById = user.id; // Auto-scope
    }
    // ...
  }
}
```
