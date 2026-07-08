# TravelBox ERP — Module Dependency Map (V1)

## 1. Module Dependency Graph

```
                    ┌──────────────┐
                    │     CORE     │
                    │  (Database,  │
                    │   Cache,     │
                    │   Queue,     │
                    │   Audit)     │
                    └──────┬───────┘
                           │
              ┌────────────┼────────────┐
              ▼            ▼            ▼
       ┌──────────┐ ┌──────────┐ ┌──────────┐
       │   Auth   │ │  Users   │ │Settings  │
       └────┬─────┘ └────┬─────┘ └──────────┘
            │            │
            ▼            │
       ┌──────────┐      │
       │Customers │      │
       └────┬─────┘      │
            │            │
            ▼            ▼
       ┌──────────────────────┐
       │        TRIPS         │  ←── Core Aggregate Root
       │  (Trips Module)      │
       └──┬───┬───┬───┬───┬──┘
          │   │   │   │   │
    ┌─────┘   │   │   │   └──────┐
    ▼         ▼   ▼   ▼          ▼
┌────────┐ ┌──────────────┐ ┌────────┐
│Services│ │   Payments   │ │Tasks   │
│(Flight,│ └──────┬───────┘ │Notes   │
│ Hotel, │        │         │Timeline│
│Transfer│        ▼         └────────┘
│ Visa,  │  ┌──────────────┐
│Insuran.│  │  Accounting  │
│Activit.)│  │  (Journal)   │
└────────┘  └──────┬───────┘
                   │
    ┌──────────────┼──────────────┐
    ▼              ▼              ▼
┌──────────┐ ┌──────────┐ ┌──────────┐
│Suppliers  │ │Invoices  │ │Documents │
└──────────┘ └──────────┘ └────┬─────┘
                               │
                         ┌─────▼─────┐
                         │  Reports  │
                         │  (Sales,  │
                         │   Profit, │
                         │   Cash    │
                         │   Flow,   │
                         │   Out-    │
                         │   standing│
                         │   Perform)│
                         └───────────┘
```

## 2. Module Import Map (NestJS)

| Module | Imports From | Exports | Description |
|--------|-------------|---------|-------------|
| CoreModule | - | DatabaseModule, CacheModule, QueueModule, AuditModule | Shared infrastructure |
| AuthModule | CoreModule, UsersModule | AuthService | JWT auth, login, refresh |
| UsersModule | CoreModule | UsersService | User CRUD, admin |
| CustomersModule | CoreModule | CustomersService | Customer + Passenger CRUD |
| TripsModule | CoreModule, CustomersModule, UsersModule | TripsService | Trip CRUD, workspace |
| ServicesModule | CoreModule, TripsModule, SuppliersModule | - | All service types |
| SuppliersModule | CoreModule | SuppliersService | Supplier CRUD |
| PaymentsModule | CoreModule, TripsModule, CustomersModule, SuppliersModule | PaymentsService | Payment CRUD |
| InvoicesModule | CoreModule, TripsModule, CustomersModule | InvoicesService | Invoice CRUD |
| AccountingModule | CoreModule, TripsModule, PaymentsModule | AccountingService | Journal, ledger, P&L |
| DocumentsModule | CoreModule, TripsModule | DocumentsService | PDF generation |
| TasksModule | CoreModule, TripsModule | TasksService | Task CRUD |
| NotificationsModule | CoreModule | NotificationsService | In-app notifications |
| ReportsModule | CoreModule, TripsModule, AccountingModule | - | Report generation |
| DashboardModule | CoreModule, TripsModule, TasksModule, PaymentsModule | DashboardService | Dashboard data |
| SettingsModule | CoreModule | SettingsService | Tenant configuration |

## 3. Service Layer Design

### Domain Services (Business Logic)

```
TripsService
├── createTrip()        → Validates dates, generates reference, creates aggregate
├── updateTrip()        → Validates status transitions
├── changeStatus()      → Enforces state machine rules
├── getWorkspace()      → Returns full trip with all relations
├── getProfitSummary()  → Computes revenue, cost, margin per service
├── getTimeline()       → Returns chronological activity
└── addCustomer()       → Associates customer with role

PaymentsService
├── recordCustomerPayment() → Creates payment + auto-generates journal entry
├── recordSupplierPayment() → Creates payment + auto-generates journal entry
├── getTripPaymentSummary() → Returns total received, total paid, balance
└── reconcilePayment()     → Matches payment to invoice

AccountingService
├── createJournalEntry()   → Double-entry validation
├── getTrialBalance()      → All accounts with debit/credit totals
├── getProfitAndLoss()     → Revenue - Expense by date range
├── getBalanceSheet()      → Assets = Liabilities + Equity
├── getTripProfit()        → Single trip P&L
└── getCashFlowStatement() → Cash in/out by period

DocumentsService
├── generateQuotation()    → PDF from trip data
├── generateInvoice()      → PDF from invoice data
├── generateReceipt()      → PDF from payment data
├── generateItinerary()    → PDF timeline of all services
├── generateVoucher()      → PDF service voucher
└── sendDocument()         → Email PDF to customer
```

### State Machine: Trip Status Transitions

```
INQUIRY ──→ QUOTATION ──→ PROVISIONAL ──→ CONFIRMED ──→ IN_PROGRESS ──→ COMPLETED
   │            │              │               │                              │
   └───→ CANCELLED ───────────┴───────────────┴──────→ CANCELLED ←────────────┘
                                                         │
                                                         ▼
                                                     REFUNDED

Valid transitions enforced by TripsService:
- INQUIRY → QUOTATION: Quotation generated
- QUOTATION → PROVISIONAL: Customer interested, partial payment
- PROVISIONAL → CONFIRMED: Deposit received, services booked
- CONFIRMED → IN_PROGRESS: Trip start date reached
- IN_PROGRESS → COMPLETED: Trip end date reached
- Any → CANCELLED: With reason required
- CANCELLED → REFUNDED: Full refund processed
```
