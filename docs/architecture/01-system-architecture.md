# TravelBox ERP — System Architecture (V1)

## 1. Architectural Philosophy

### Trip-Centric Design

The **Trip** is the root aggregate. Every service sold, every cost incurred, every document generated, and every payment collected belongs to a Trip. No module exists in isolation — Flights, Hotels, Transfers, Visa, Insurance, Activities, Payments, and Accounting are all child entities of a Trip.

```
                    ┌─────────────────────┐
                    │       TRIP          │
                    │  (Aggregate Root)   │
                    └──────────┬──────────┘
          ┌──────────┬──────────┼──────────┬──────────┬──────────┐
          ▼          ▼          ▼          ▼          ▼          ▼
      Flights     Hotels    Transfers    Visa     Insurance  Activities
          │          │          │          │          │          │
          └──────────┴──────────┼──────────┴──────────┴──────────┘
                               ▼
                          Payments
                          (Customer + Supplier)
                               ▼
                          Accounting
                          (Auto-generated
                           journal entries)
```

### Clean Architecture Layers

```
┌─────────────────────────────────────────────────────────────┐
│                   PRESENTATION LAYER                         │
│  Next.js App Router  │  shadcn/ui Components  │  Pages      │
├─────────────────────────────────────────────────────────────┤
│                   APPLICATION LAYER                          │
│  NestJS Controllers  │  DTOs  │  Guards  │  Interceptors   │
├─────────────────────────────────────────────────────────────┤
│                   DOMAIN LAYER                               │
│  Services  │  Domain Logic  │  Validators  │  Calculators  │
├─────────────────────────────────────────────────────────────┤
│                   INFRASTRUCTURE LAYER                       │
│  Prisma Service  │  Redis Cache  │  BullMQ  │  File Storage │
│  PDF Generator  │  Email Service  │  Audit Logger          │
├─────────────────────────────────────────────────────────────┤
│                   DATABASE LAYER                             │
│  PostgreSQL  │  Prisma ORM  │  Migrations  │  Seeds        │
└─────────────────────────────────────────────────────────────┘
```

---

## 2. Technology Stack

| Layer | Technology | Version | Justification |
|-------|-----------|---------|---------------|
| Frontend Framework | Next.js | 16.2 LTS | App Router, server components, static export |
| UI Library | React | 19.x | Latest stable |
| Styling | TailwindCSS | 4.x | Utility-first, fast iteration |
| Component Library | shadcn/ui | Latest | Customizable, unopinionated |
| Backend Framework | NestJS | 10.x | Modular, decorator-based, DI |
| ORM | Prisma | 5.x | Type-safe, migrations, powerful relations |
| Database | PostgreSQL | 16 | JSON support, advanced indexing, reliability |
| Cache | Redis | 7 | BullMQ backend, session cache, rate limiting |
| Queue | BullMQ | Latest | Background jobs (PDF gen, email, reports) |
| Auth | JWT + Passport | Latest | Stateless, API-friendly |
| Validation | class-validator | 0.14 | Decorator-based, DTO validation |
| PDF | Puppeteer + Handlebars | Latest | HTML-to-PDF with custom templates |
| Container | Docker | Latest | Development consistency |
| Testing | Jest + Playwright | Latest | Unit + E2E |

### Migration from MySQL to PostgreSQL

The existing project uses MySQL via Prisma. V1 will migrate to PostgreSQL for:
- Native UUID type
- JSON/JSONB for flexible service metadata
- Advanced indexing (partial, covering)
- Better analytical queries for reports
- Enum type support

---

## 3. System Principles

### SOLID in Practice

- **Single Responsibility**: Each service has one reason to change. `TripService` handles trip CRUD; `TripProfitCalculator` computes profit.
- **Open/Closed**: Modules extend via NestJS module imports, never by modification of existing modules.
- **Liskov Substitution**: Service interfaces are contracts. Implementations are swappable.
- **Interface Segregation**: Controllers depend on small, focused service interfaces.
- **Dependency Inversion**: High-level modules (services) depend on abstractions (PrismaService interface), not concretions.

### Key Rules

1. **No cross-module direct DB access**. Modules communicate only through service methods.
2. **Every mutation creates an audit log entry**. Who did what, when, and the previous state.
3. **Every financial transaction creates a journal entry automatically**. No manual accounting.
4. **Soft delete on all primary entities**. Records are never permanently deleted.
5. **UUIDs for all primary keys**. No auto-increment IDs in the API.
6. **Timestamps on all tables**. `createdAt`, `updatedAt` — and where relevant, `deletedAt`.
7. **Validation at every boundary**. DTOs validate input; services validate business rules; database enforces constraints.

---

## 4. Deployment Architecture

```
                         ┌──────────────┐
                         │   Cloudflare  │
                         │   (DNS + CDN) │
                         └──────┬───────┘
                                │
                    ┌───────────┴───────────┐
                    │   Hostinger Nginx     │
                    │  (Reverse Proxy)      │
                    └───────────┬───────────┘
                                │
              ┌─────────────────┼─────────────────┐
              ▼                 ▼                  ▼
     ┌──────────────┐  ┌──────────────┐  ┌──────────────┐
     │  Static Files │  │  Node.js App │  │   MySQL DB   │
     │  public_html/ │  │  (NestJS)    │  │  (Existing)  │
     │  (Frontend)   │  │  :3001       │  │  :3306       │
     └──────────────┘  └──────────────┘  └──────────────┘
                              │
                    ┌─────────┴─────────┐
                    │   Redis + BullMQ  │
                    │  (Background)     │
                    └───────────────────┘
```

- Frontend: Static export served from `public_html/`
- Backend: NestJS running as Node.js app under Passenger
- Database: MySQL initially, migrate to PostgreSQL when feasible
- Redis/BullMQ: For async jobs (PDF generation, reports, notifications)

---

## 5. Security Architecture

### Authentication Flow

```
Login → JWT (access + refresh) → Bearer token on all requests
       → Token expiry: 1 day (access), 7 days (refresh)
       → Refresh flow: POST /api/auth/refresh
```

### Authorization

- Role-Based Access Control (RBAC) with 8 roles
- Permission matrix at the module/action level
- `RolesGuard` + `@Roles()` decorator for controller-level access
- `PermissionsGuard` + `@Permissions()` decorator for granular access

### Data Protection

- Passwords: bcrypt with 12 rounds
- JWT secrets: 512-bit random, rotated quarterly
- API rate limiting via `@nestjs/throttler`
- Input sanitization via class-validator whitelist
- SQL injection prevention via Prisma parameterized queries

---

## 6. Background Job Architecture

```
┌──────────┐    ┌──────────┐    ┌──────────┐
│  Controller │───▶  Service  │───▶  Queue   │
└──────────┘    └──────────┘    └─────┬────┘
                                      │
                          ┌───────────┴───────────┐
                          │    BullMQ (Redis)      │
                          └───────────┬───────────┘
                                      │
              ┌───────────────────────┼───────────────────────┐
              ▼                       ▼                       ▼
      ┌──────────────┐      ┌──────────────┐      ┌──────────────┐
      │ PDF Generator │      │ Email Sender  │      │ Report Builder│
      │  Worker       │      │  Worker       │      │  Worker       │
      └──────────────┘      └──────────────┘      └──────────────┘
```

Jobs:
- `generateDocument` — PDF generation for invoices, vouchers, etc.
- `sendEmail` — Email notifications (booking confirmations, payment reminders)
- `buildReport` — Async report generation for large datasets
- `syncFinancials` — Recalculate accounting entries on data changes
- `cacheWarm` — Pre-warm dashboard cache
