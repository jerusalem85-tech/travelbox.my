# TravelBox ERP — Implementation Plan (V1)

## 1. Phase Strategy

The implementation is divided into 4 phases, each delivering a working increment:

```
Phase 1: Foundation + Core (Weeks 1-3)
  → Schema, Auth, Trips, Customers, Services CRUD
  → Login works, can create/manage trips end-to-end

Phase 2: Financials (Weeks 4-6)
  → Payments, Accounting, Invoices, Suppliers
  → Money flows tracked, P&L visible per trip

Phase 3: Documents + Productivity (Weeks 7-8)
  → Document generator, Tasks, Notes, Timeline, Notifications
  → Full operational workspace

Phase 4: Analytics + Polish (Weeks 9-10)
  → Reports dashboard, performance metrics, export, optimizations
  → Production-ready ERP system
```

---

## 2. Phase 1: Foundation & Core (3 weeks)

### Week 1: Project Setup & Schema

| Day | Task | Deliverable |
|-----|------|-------------|
| 1-2 | Set up monorepo: pnpm workspaces, shared packages, tsconfig | Working monorepo with build |
| 3 | Migrate MySQL → PostgreSQL; design & write Prisma schema | Complete schema.prisma with all models |
| 4 | Create migrations, seed script (accounts, demo users) | Database ready with seed data |
| 5 | Set up Redis, BullMQ, Docker Compose | Local dev environment with all services |

### Week 2: Auth & Core Modules

| Day | Task | Deliverable |
|-----|------|-------------|
| 6 | CoreModule: PrismaService, redis, queue, audit interceptor | Infrastructure layer |
| 7 | AuthModule: JWT strategy, login/register/refresh, guards | Auth fully working |
| 8 | UsersModule: CRUD, role/permission guards | User management |
| 9 | CustomersModule + PassengersModule | Customer CRUD |
| 10 | SuppliersModule | Supplier CRUD |

### Week 3: Trips & Services

| Day | Task | Deliverable |
|-----|------|-------------|
| 11 | TripsModule: CRUD, reference generator, status machine | Trip workspace API |
| 12-13 | Services sub-modules: Flights, Hotels, Transfers, Visa, Insurance, Activities | All service APIs |
| 14 | Connect services to trips; implement Trip workspace endpoint | GET /trips/:id returns full workspace |
| 15 | Frontend setup: project structure, auth context, API client, layout shell | Login page, dashboard shell |

### Phase 1 Gate: ✅ Login works, can create customers & trips, add services, view trip workspace

---

## 3. Phase 2: Financials (3 weeks)

### Week 4: Payments

| Day | Task | Deliverable |
|-----|------|-------------|
| 16 | PaymentModel CRUD, direction (inflow/outflow) | Payment APIs |
| 17 | Trip payment summary, customer payment history | Payment views |
| 18 | Supplier payments, payment reconciliation | Supplier payment APIs |
| 19 | Frontend: payment forms, payment tables in Trip Workspace | Payment UI |
| 20 | Payment receipt PDF generation | Basic receipt |

### Week 5: Accounting Engine

| Day | Task | Deliverable |
|-----|------|-------------|
| 21 | Chart of Accounts seed, Account CRUD | Account management |
| 22 | Journal service: double-entry validation, auto-entries from payments | Core accounting logic |
| 23 | Auto-accounting: payment → journal entry bridge | Every payment posts to journal |
| 24 | Trial Balance, General Ledger queries | Accounting read APIs |
| 25 | Frontend: accounting dashboard, journal table | Accounting UI |

### Week 6: Invoices & Full Integration

| Day | Task | Deliverable |
|-----|------|-------------|
| 26 | InvoicesModule: create, send, track payments | Invoice CRUD |
| 27 | Invoice → Accounting integration (AR, Revenue recognition) | End-to-end financial flow |
| 28 | Frontend: invoice list, invoice form in Trip Workspace | Invoice UI |
| 29-30 | Profit & Loss per trip, balance per trip | Trip financial summary |

### Phase 2 Gate: ✅ Payments recordable, journal entries auto-generated, P&L visible per trip

---

## 4. Phase 3: Documents & Productivity (2 weeks)

### Week 7: Document Generator

| Day | Task | Deliverable |
|-----|------|-------------|
| 31 | Handlebars templates: quotation, invoice, receipt, voucher, itinerary | Template files |
| 32 | DocumentService: HTML → PDF via Puppeteer | PDF generation engine |
| 33 | DocumentController: generate, download, send (email) | Document APIs |
| 34 | Frontend: document preview, document list in Trip Workspace | Document UI |
| 35 | Template editor (company logo, colors, terms) | Template customization |

### Week 8: Productivity Features

| Day | Task | Deliverable |
|-----|------|-------------|
| 36 | NotesModule: CRUD, pin/unpin, author tracking | Internal notes |
| 37 | TasksModule: CRUD, assign, priority, status, due dates | Task management |
| 38 | TimelineModule: auto-logging all trip events | Activity timeline |
| 39 | NotificationsModule: in-app notifications, read/unread | Notification bell |
| 40 | Frontend: Notes, Tasks, Timeline, Notifications UI | Full workspace UI |

### Phase 3 Gate: ✅ PDF documents generated, notes & tasks functional, timeline tracking changes

---

## 5. Phase 4: Analytics & Polish (2 weeks)

### Week 9: Reports & Dashboard

| Day | Task | Deliverable |
|-----|------|-------------|
| 41 | Sales report: revenue by period, agent, service type | Sales analytics |
| 42 | Profit report: margin analysis, trend charts | Profit analytics |
| 43 | Cash flow: inflows/outflows by period | Cash flow report |
| 44 | Outstanding: AR aging, unpaid invoices | Collections view |
| 45 | Agent performance: trips closed, revenue generated | Performance metrics |

### Week 10: Frontend Complete & Polish

| Day | Task | Deliverable |
|-----|------|-------------|
| 46 | Dashboard page: stats, charts, widgets | Live dashboard |
| 47 | Reports frontend: interactive charts, filters, export | Report UI |
| 48 | Responsive design, mobile layout | Mobile-ready UI |
| 49 | Performance: caching, pagination, query optimization | Fast load times |
| 50 | Production build, Docker configuration, deployment | Production-ready |

### Phase 4 Gate: ✅ Full ERP operational, reports generated, production-deployed

---

## 6. File Creation Order (Technical)

### Backend (apps/api/)

```
Order 1:  prisma/schema.prisma                    # Database schema
Order 2:  src/common/**                           # Guards, decorators, pipes, interceptors
Order 3:  src/config/**                           # Configuration modules
Order 4:  src/core/**                             # Database, cache, queue, audit
Order 5:  src/modules/auth/**                     # Authentication
Order 6:  src/modules/users/**                    # Users
Order 7:  src/modules/customers/**                # Customers
Order 8:  src/modules/suppliers/**                # Suppliers
Order 9:  src/modules/trips/**                    # Trips (core)
Order 10: src/modules/trips/services/flights/**   # Services (flights, hotels, etc.)
Order 11: src/modules/payments/**                 # Payments
Order 12: src/modules/accounting/**               # Accounting
Order 13: src/modules/invoices/**                 # Invoices
Order 14: src/core/document/**                    # Document generator
Order 15: src/modules/documents/**                # Documents
Order 16: src/modules/tasks/**                    # Tasks
Order 17: src/modules/notes/**                    # Notes
Order 18: src/modules/notifications/**            # Notifications
Order 19: src/modules/reports/**                  # Reports
Order 20: src/modules/dashboard/**                # Dashboard
Order 21: src/modules/settings/**                 # Settings
```

### Frontend (apps/web/)

```
Order 1:  src/types/**                            # TypeScript types matching backend
Order 2:  src/lib/api.ts                          # API client with auth
Order 3:  src/lib/auth.ts                         # Auth context
Order 4:  src/app/(auth)/login/page.tsx           # Login page
Order 5:  src/components/ui/**                    # UI primitives (shadcn)
Order 6:  src/components/layout/**                # Sidebar, header, shell
Order 7:  src/app/(dashboard)/layout.tsx          # Dashboard shell
Order 8:  src/app/(dashboard)/dashboard/page.tsx  # Dashboard
Order 9:  src/app/(dashboard)/trips/**            # Trips pages
Order 10: src/components/trips/**                 # Trip workspace components
Order 11: src/app/(dashboard)/customers/**        # Customers pages
Order 12: src/app/(dashboard)/suppliers/**        # Suppliers pages
Order 13: src/app/(dashboard)/accounting/**       # Accounting pages
Order 14: src/app/(dashboard)/documents/**        # Documents pages
Order 15: src/app/(dashboard)/reports/**          # Reports pages
Order 16: src/app/(dashboard)/settings/**         # Settings pages
```

---

## 7. Risk Mitigation

| Risk | Impact | Mitigation |
|------|--------|------------|
| Scope creep on Trip Workspace | High | Strictly limit features per phase; "nice to have" goes to V2 |
| PostgreSQL migration breaks existing data | Medium | Backup MySQL, run migration tool, verify data integrity |
| PDF generation server load | Medium | Queue via BullMQ, cache generated PDFs, paginate large documents |
| Accounting errors (double-entry bugs) | High | Comprehensive unit tests for JournalService; manual reconciliation period |
| Performance with 1000+ trips | Medium | Pagination on all list endpoints; Redis caching for dashboard; DB indexing |
| Multi-tenant data leaks | Critical | TenantId filter in every query; middleware auto-injects tenant context |

---

## 8. V2 Candidates (Post-Launch)

- **Online Booking Portal** — Customer-facing booking site with payment gateway
- **Multi-Currency Engine** — Real-time FX rates, auto-conversion
- **Advanced Analytics** — ML-based demand forecasting, margin optimization
- **Mobile App** — React Native companion for field operations
- **API Public** — REST API for partner integrations
- **GDS Integration** — Amadeus, Sabre for real-time flight/hotel booking
- **WhatsApp Integration** — Send documents, booking confirmations via WhatsApp
- **Bulk Operations** — CSV import for customers, mass trip status updates
- **Custom Dashboards** — Drag-and-drop dashboard builder per user
- **Audit Trail Viewer** — Advanced search and replay of audit logs
