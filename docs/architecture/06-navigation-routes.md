# TravelBox ERP — Navigation & Routes (V1)

## 1. Sidebar Navigation Structure

```
DASHBOARD                    /dashboard
├── Overview                 /dashboard
├── Calendar View            /dashboard/calendar
└── My Tasks                 /dashboard/tasks

TRIPS                        /trips
├── All Trips                /trips
├── Create Trip              /trips/new
└── Trip Workspace           /trips/[id]
    ├── General              /trips/[id]?tab=general
    ├── Customer             /trips/[id]?tab=customer
    ├── Passengers           /trips/[id]?tab=passengers
    ├── Flights              /trips/[id]?tab=flights
    ├── Hotels               /trips/[id]?tab=hotels
    ├── Transfers            /trips/[id]?tab=transfers
    ├── Visa                 /trips/[id]?tab=visa
    ├── Insurance            /trips/[id]?tab=insurance
    ├── Activities           /trips/[id]?tab=activities
    ├── Payments             /trips/[id]?tab=payments
    ├── Accounting           /trips/[id]?tab=accounting
    ├── Documents            /trips/[id]?tab=documents
    ├── Notes                /trips/[id]?tab=notes
    ├── Tasks                /trips/[id]?tab=tasks
    └── Timeline             /trips/[id]?tab=timeline

CUSTOMERS                    /customers
├── All Customers            /customers
├── Create Customer          /customers/new
└── Customer Profile         /customers/[id]
    ├── Trips                /customers/[id]?tab=trips
    ├── Passengers           /customers/[id]?tab=passengers
    ├── Payments             /customers/[id]?tab=payments
    └── Documents            /customers/[id]?tab=documents

SUPPLIERS                    /suppliers
├── All Suppliers            /suppliers
├── Create Supplier          /suppliers/new
└── Supplier Profile         /suppliers/[id]
    ├── Services             /suppliers/[id]?tab=services
    └── Payments             /suppliers/[id]?tab=payments

FINANCE                      /finance (or /accounting)
├── Dashboard                /accounting
├── Chart of Accounts        /accounting/chart-of-accounts
├── Journal Entries          /accounting/journal
├── General Ledger           /accounting/ledger
├── Trial Balance            /accounting/trial-balance
├── Profit & Loss            /accounting/profit-loss
├── Balance Sheet            /accounting/balance-sheet
└── Cash Flow                /accounting/cash-flow

REPORTS                      /reports
├── Sales Report             /reports/sales
├── Profit Report            /reports/profit
├── Cash Flow Report         /reports/cash-flow
├── Outstanding Report       /reports/outstanding
├── Agent Performance        /reports/performance
└── Custom Reports           /reports/custom

DOCUMENTS                    /documents
├── All Documents            /documents
├── Quotations               /documents?type=quotation
├── Invoices                 /documents?type=invoice
├── Receipts                 /documents?type=receipt
├── Vouchers                 /documents?type=voucher
├── Itineraries              /documents?type=itinerary
└── Templates                /documents/templates

TASKS                        /tasks
├── All Tasks                /tasks
├── My Tasks                 /tasks?assigned=me
└── Create Task              /tasks/new

SETTINGS                     /settings
├── General                  /settings/general
├── Company Profile          /settings/company
├── Users & Roles            /settings/users
├── Email Templates          /settings/email-templates
├── Document Templates       /settings/document-templates
├── Payment Methods          /settings/payment-methods
├── Currency & Tax           /settings/currency-tax
└── Notifications            /settings/notifications

ADMIN                        /admin (SUPER_ADMIN only)
├── Tenants                  /admin/tenants
├── System Logs              /admin/logs
└── System Settings          /admin/system
```

## 2. API Route Structure (NestJS)

```
/api
├── /auth
│   ├── POST   /login
│   ├── POST   /register
│   ├── POST   /refresh
│   ├── POST   /logout
│   └── GET    /me
├── /users
│   ├── GET    /
│   ├── GET    /:id
│   ├── POST   /
│   ├── PATCH  /:id
│   ├── DELETE /:id (soft)
│   └── PATCH  /:id/restore
├── /trips
│   ├── GET    /                    # List with filters
│   ├── POST   /                    # Create
│   ├── GET    /:id                 # Full workspace
│   ├── PATCH  /:id                 # Update
│   ├── DELETE /:id                 # Soft delete
│   ├── PATCH  /:id/status          # Change status
│   ├── GET    /:id/timeline        # Timeline entries
│   ├── GET    /:id/profit          # Profit summary
│   ├── GET    /:id/balance         # Payment balance
│   ├── POST   /:id/duplicate       # Clone trip
│   └── GET    /reference/:no       # Lookup by reference
├── /customers
│   ├── GET    /
│   ├── POST   /
│   ├── GET    /:id
│   ├── PATCH  /:id
│   ├── DELETE /:id
│   └── GET    /:id/trips
├── /passengers
│   ├── GET    /trip/:tripId
│   ├── POST   /
│   ├── PATCH  /:id
│   └── DELETE /:id
├── /services
│   ├── /flights
│   │   ├── GET    /trip/:tripId
│   │   ├── POST   /
│   │   ├── PATCH  /:id
│   │   └── DELETE /:id
│   ├── /hotels
│   │   ├── GET    /trip/:tripId
│   │   ├── POST   /
│   │   ├── PATCH  /:id
│   │   └── DELETE /:id
│   ├── /transfers (same pattern)
│   ├── /visa (same pattern)
│   ├── /insurance (same pattern)
│   └── /activities (same pattern)
├── /suppliers
│   ├── GET    /
│   ├── POST   /
│   ├── GET    /:id
│   ├── PATCH  /:id
│   └── DELETE /:id
├── /payments
│   ├── GET    /                    # All payments (with filters)
│   ├── POST   /                    # Record payment
│   ├── GET    /:id
│   ├── PATCH  /:id
│   ├── DELETE /:id
│   ├── GET    /trip/:tripId        # Trip payments
│   └── GET    /customer/:customerId
├── /invoices
│   ├── GET    /
│   ├── POST   /
│   ├── GET    /:id
│   ├── PATCH  /:id
│   ├── DELETE /:id
│   └── GET    /trip/:tripId
├── /accounting
│   ├── GET    /accounts            # Chart of accounts
│   ├── POST   /accounts
│   ├── PATCH  /accounts/:id
│   ├── GET    /journal             # Journal entries
│   ├── POST   /journal             # Manual entry
│   ├── GET    /ledger/:accountId   # Account ledger
│   ├── GET    /trial-balance
│   ├── GET    /profit-loss
│   ├── GET    /balance-sheet
│   ├── GET    /cash-flow
│   └── GET    /trip/:tripId/pnl    # Trip P&L
├── /documents
│   ├── GET    /
│   ├── POST   /generate            # Generate document
│   ├── GET    /:id
│   ├── GET    /:id/download        # Download PDF
│   ├── POST   /:id/send            # Email document
│   └── GET    /trip/:tripId
├── /tasks
│   ├── GET    /
│   ├── POST   /
│   ├── GET    /:id
│   ├── PATCH  /:id
│   └── DELETE /:id
├── /notifications
│   ├── GET    /
│   ├── PATCH  /:id/read
│   └── POST   /read-all
├── /reports
│   ├── GET    /sales
│   ├── GET    /profit
│   ├── GET    /cash-flow
│   ├── GET    /outstanding
│   └── GET    /performance
├── /dashboard
│   └── GET    /                    # Dashboard summary
└── /settings
    ├── GET    /
    ├── PATCH  /
    └── GET    /public             # Public company info
```

## 3. Trip Workspace Layout (Single Screen)

```
┌─────────────────────────────────────────────────────────────────┐
│ Header: Trip Reference | Status Badge | [Edit] [Duplicate] [More]│
├──────────┬──────────────────────────────────────────────────────┤
│  Side    │  Content Area                                        │
│  Nav     │                                                      │
│          │  Each tab loads component below the tabs             │
│  General │                                                      │
│  Customer│  ┌──────────────────────────────────────────────┐    │
│  Pax     │  │                                              │    │
│  Flights │  │  Tab-specific form / table / summary         │    │
│  Hotels  │  │                                              │    │
│  Transf. │  └──────────────────────────────────────────────┘    │
│  Visa    │                                                      │
│  Insur.  │  Profit Summary Card (sticky footer)                 │
│  Activ.  │  ┌────────────┬───────────┬──────────┬─────────┐   │
│─────────│  │ Revenue    │ Cost      │ Profit   │ Margin  │   │
│  Paymnt │  │ $12,500.00 │ $8,200.00 │$4,300.00 │  34.4%  │   │
│  Accnt. │  └────────────┴───────────┴──────────┴─────────┘   │
│  Docmnts│                                                      │
│  Notes  │                                                      │
│  Tasks  │                                                      │
│  Timeln │                                                      │
└─────────┴──────────────────────────────────────────────────────┘
```
