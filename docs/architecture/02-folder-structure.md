# TravelBox ERP — Folder Structure (V1)

## 1. Monorepo Root

```
travelbox.my/
├── apps/
│   ├── web/                          # Next.js frontend
│   └── api/                          # NestJS backend
├── packages/
│   ├── shared/                       # Shared types, enums, utilities
│   │   ├── src/
│   │   │   ├── types/
│   │   │   ├── enums/
│   │   │   ├── interfaces/
│   │   │   ├── constants/
│   │   │   └── utils/
│   │   ├── package.json
│   │   └── tsconfig.json
│   └── ui/                           # Shared UI components
│       └── src/
│           ├── components/
│           ├── hooks/
│           └── styles/
├── docker/
│   ├── Dockerfile.api
│   ├── Dockerfile.web
│   └── docker-compose.yml
├── docs/
│   └── architecture/
├── scripts/
│   ├── seed.ts
│   └── migrate.ts
├── .github/
│   └── workflows/
├── turbo.json
├── package.json                      # Workspace root
└── pnpm-workspace.yaml
```

---

## 2. Backend — NestJS (`apps/api/`)

```
apps/api/
├── prisma/
│   ├── schema.prisma
│   ├── migrations/
│   └── seed.ts
├── src/
│   ├── main.ts
│   ├── app.module.ts
│   ├── common/
│   │   ├── decorators/
│   │   │   ├── current-user.decorator.ts
│   │   │   ├── roles.decorator.ts
│   │   │   ├── permissions.decorator.ts
│   │   │   └── public.decorator.ts
│   │   ├── guards/
│   │   │   ├── jwt-auth.guard.ts
│   │   │   ├── roles.guard.ts
│   │   │   └── permissions.guard.ts
│   │   ├── interceptors/
│   │   │   ├── audit-log.interceptor.ts
│   │   │   ├── transform.interceptor.ts
│   │   │   └── logging.interceptor.ts
│   │   ├── filters/
│   │   │   └── http-exception.filter.ts
│   │   ├── pipes/
│   │   │   └── validation.pipe.ts
│   │   ├── middleware/
│   │   │   └── tenant.middleware.ts
│   │   ├── dto/
│   │   │   ├── pagination.dto.ts
│   │   │   └── date-range.dto.ts
│   │   ├── interfaces/
│   │   │   ├── audit-log.interface.ts
│   │   │   └── soft-delete.interface.ts
│   │   └── helpers/
│   │       ├── date.helper.ts
│   │       └── number.helper.ts
│   ├── config/
│   │   ├── app.config.ts
│   │   ├── database.config.ts
│   │   ├── jwt.config.ts
│   │   ├── redis.config.ts
│   │   └── queue.config.ts
│   ├── core/
│   │   ├── database/
│   │   │   ├── database.module.ts
│   │   │   └── prisma.service.ts
│   │   ├── cache/
│   │   │   ├── cache.module.ts
│   │   │   └── redis.service.ts
│   │   ├── queue/
│   │   │   ├── queue.module.ts
│   │   │   └── bull.service.ts
│   │   ├── audit/
│   │   │   ├── audit.module.ts
│   │   │   ├── audit.service.ts
│   │   │   └── audit-log.entity.ts
│   │   ├── document/
│   │   │   ├── document.module.ts
│   │   │   ├── document.service.ts
│   │   │   ├── document.generator.ts
│   │   │   └── templates/
│   │   │       ├── quotation.hbs
│   │   │       ├── invoice.hbs
│   │   │       ├── receipt.hbs
│   │   │       ├── itinerary.hbs
│   │   │       ├── voucher.hbs
│   │   │       └── certificate.hbs
│   │   └── storage/
│   │       ├── storage.module.ts
│   │       └── storage.service.ts
│   ├── modules/
│   │   ├── auth/
│   │   │   ├── auth.module.ts
│   │   │   ├── auth.controller.ts
│   │   │   ├── auth.service.ts
│   │   │   ├── strategies/
│   │   │   │   ├── jwt.strategy.ts
│   │   │   │   └── jwt-refresh.strategy.ts
│   │   │   └── dto/
│   │   │       ├── login.dto.ts
│   │   │       ├── register.dto.ts
│   │   │       └── refresh-token.dto.ts
│   │   ├── users/
│   │   │   ├── users.module.ts
│   │   │   ├── users.controller.ts
│   │   │   ├── users.service.ts
│   │   │   └── dto/
│   │   │       ├── create-user.dto.ts
│   │   │       ├── update-user.dto.ts
│   │   │       └── user-filter.dto.ts
│   │   ├── trips/                     # Trip Workspace (core module)
│   │   │   ├── trips.module.ts
│   │   │   ├── trips.controller.ts
│   │   │   ├── trips.service.ts
│   │   │   ├── trips-workspace.controller.ts
│   │   │   └── dto/
│   │   │       ├── create-trip.dto.ts
│   │   │       ├── update-trip.dto.ts
│   │   │       └── trip-filter.dto.ts
│   │   ├── customers/
│   │   │   ├── customers.module.ts
│   │   │   ├── customers.controller.ts
│   │   │   ├── customers.service.ts
│   │   │   └── dto/
│   │   │       ├── create-customer.dto.ts
│   │   │       ├── update-customer.dto.ts
│   │   │       └── customer-filter.dto.ts
│   │   ├── passengers/
│   │   │   ├── passengers.module.ts
│   │   │   ├── passengers.controller.ts
│   │   │   ├── passengers.service.ts
│   │   │   └── dto/
│   │   │       ├── create-passenger.dto.ts
│   │   │       └── update-passenger.dto.ts
│   │   ├── services/                 # Trip Services (Flights, Hotels, etc.)
│   │   │   ├── services.module.ts
│   │   │   ├── flights/
│   │   │   │   ├── flights.controller.ts
│   │   │   │   ├── flights.service.ts
│   │   │   │   └── dto/
│   │   │   │       ├── create-flight.dto.ts
│   │   │   │       └── update-flight.dto.ts
│   │   │   ├── hotels/
│   │   │   │   ├── hotels.controller.ts
│   │   │   │   ├── hotels.service.ts
│   │   │   │   └── dto/
│   │   │   │       ├── create-hotel.dto.ts
│   │   │   │       └── update-hotel.dto.ts
│   │   │   ├── transfers/
│   │   │   │   ├── transfers.controller.ts
│   │   │   │   ├── transfers.service.ts
│   │   │   │   └── dto/
│   │   │   │       ├── create-transfer.dto.ts
│   │   │   │       └── update-transfer.dto.ts
│   │   │   ├── visa/
│   │   │   │   ├── visa.controller.ts
│   │   │   │   ├── visa.service.ts
│   │   │   │   └── dto/
│   │   │   │       ├── create-visa.dto.ts
│   │   │   │       └── update-visa.dto.ts
│   │   │   ├── insurance/
│   │   │   │   ├── insurance.controller.ts
│   │   │   │   ├── insurance.service.ts
│   │   │   │   └── dto/
│   │   │   │       ├── create-insurance.dto.ts
│   │   │   │       └── update-insurance.dto.ts
│   │   │   └── activities/
│   │   │       ├── activities.controller.ts
│   │   │       ├── activities.service.ts
│   │   │       └── dto/
│   │   │           ├── create-activity.dto.ts
│   │   │           └── update-activity.dto.ts
│   │   ├── payments/
│   │   │   ├── payments.module.ts
│   │   │   ├── payments.controller.ts
│   │   │   ├── payments.service.ts
│   │   │   └── dto/
│   │   │       ├── create-customer-payment.dto.ts
│   │   │       ├── create-supplier-payment.dto.ts
│   │   │       └── payment-filter.dto.ts
│   │   ├── accounting/
│   │   │   ├── accounting.module.ts
│   │   │   ├── accounting.controller.ts
│   │   │   ├── accounting.service.ts
│   │   │   ├── journal.service.ts
│   │   │   └── dto/
│   │   │       ├── journal-entry.dto.ts
│   │   │       └── account-filter.dto.ts
│   │   ├── suppliers/
│   │   │   ├── suppliers.module.ts
│   │   │   ├── suppliers.controller.ts
│   │   │   ├── suppliers.service.ts
│   │   │   └── dto/
│   │   │       ├── create-supplier.dto.ts
│   │   │       ├── update-supplier.dto.ts
│   │   │       └── supplier-filter.dto.ts
│   │   ├── reports/
│   │   │   ├── reports.module.ts
│   │   │   ├── reports.controller.ts
│   │   │   └── services/
│   │   │       ├── sales-report.service.ts
│   │   │       ├── profit-report.service.ts
│   │   │       ├── cash-flow.service.ts
│   │   │       ├── outstanding.service.ts
│   │   │       └── performance.service.ts
│   │   ├── dashboard/
│   │   │   ├── dashboard.module.ts
│   │   │   ├── dashboard.controller.ts
│   │   │   └── dashboard.service.ts
│   │   ├── documents/
│   │   │   ├── documents.module.ts
│   │   │   ├── documents.controller.ts
│   │   │   ├── documents.service.ts
│   │   │   └── dto/
│   │   │       ├── generate-document.dto.ts
│   │   │       └── document-template.dto.ts
│   │   ├── tasks/
│   │   │   ├── tasks.module.ts
│   │   │   ├── tasks.controller.ts
│   │   │   ├── tasks.service.ts
│   │   │   └── dto/
│   │   │       ├── create-task.dto.ts
│   │   │       └── update-task.dto.ts
│   │   ├── notifications/
│   │   │   ├── notifications.module.ts
│   │   │   ├── notifications.controller.ts
│   │   │   ├── notifications.service.ts
│   │   │   └── dto/
│   │   │       └── notification.dto.ts
│   │   └── settings/
│   │       ├── settings.module.ts
│   │       ├── settings.controller.ts
│   │       ├── settings.service.ts
│   │       └── dto/
│   │           └── update-settings.dto.ts
│   └── shared/
│       ├── shared.module.ts
│       └── services/
│           ├── email.service.ts
│           └── sms.service.ts
├── test/
│   ├── unit/
│   └── e2e/
├── package.json
├── nest-cli.json
├── tsconfig.json
└── tsconfig.build.json
```

---

## 3. Frontend — Next.js (`apps/web/`)

```
apps/web/
├── public/
│   ├── favicon.ico
│   ├── logo.svg
│   └── fonts/
├── src/
│   ├── app/
│   │   ├── layout.tsx                 # Root layout (providers, theme)
│   │   ├── page.tsx                   # Redirect to /dashboard
│   │   ├── loading.tsx
│   │   ├── not-found.tsx
│   │   ├── error.tsx
│   │   ├── globals.css
│   │   ├── (auth)/
│   │   │   └── login/
│   │   │       └── page.tsx
│   │   └── (dashboard)/
│   │       ├── layout.tsx             # Dashboard shell (sidebar + header)
│   │       ├── dashboard/
│   │       │   └── page.tsx
│   │       ├── trips/
│   │       │   ├── page.tsx           # Trip list
│   │       │   ├── new/
│   │       │   │   └── page.tsx
│   │       │   └── [id]/
│   │       │       └── page.tsx       # Trip Workspace (single screen)
│   │       ├── customers/
│   │       │   ├── page.tsx
│   │       │   └── [id]/
│   │       │       └── page.tsx
│   │       ├── passengers/
│   │       │   └── page.tsx
│   │       ├── suppliers/
│   │       │   ├── page.tsx
│   │       │   └── [id]/
│   │       │       └── page.tsx
│   │       ├── accounting/
│   │       │   ├── page.tsx
│   │       │   ├── ledger/
│   │       │   │   └── page.tsx
│   │       │   └── reports/
│   │       │       └── page.tsx
│   │       ├── reports/
│   │       │   ├── page.tsx
│   │       │   ├── sales/
│   │       │   │   └── page.tsx
│   │       │   ├── profit/
│   │       │   │   └── page.tsx
│   │       │   ├── cash-flow/
│   │       │   │   └── page.tsx
│   │       │   └── outstanding/
│   │       │       └── page.tsx
│   │       ├── documents/
│   │       │   ├── page.tsx
│   │       │   └── templates/
│   │       │       └── page.tsx
│   │       ├── settings/
│   │       │   └── page.tsx
│   │       └── admin/
│   │           └── users/
│   │               └── page.tsx
│   ├── components/
│   │   ├── ui/                        # shadcn/ui components
│   │   │   ├── button.tsx
│   │   │   ├── card.tsx
│   │   │   ├── input.tsx
│   │   │   ├── select.tsx
│   │   │   ├── table.tsx
│   │   │   ├── dialog.tsx
│   │   │   ├── sheet.tsx
│   │   │   ├── badge.tsx
│   │   │   ├── tabs.tsx
│   │   │   ├── dropdown-menu.tsx
│   │   │   ├── form.tsx
│   │   │   ├── toast.tsx
│   │   │   ├── calendar.tsx
│   │   │   ├── date-picker.tsx
│   │   │   ├── search-input.tsx
│   │   │   ├── data-table.tsx
│   │   │   ├── confirmation-dialog.tsx
│   │   │   └── empty-state.tsx
│   │   ├── layout/
│   │   │   ├── sidebar.tsx
│   │   │   ├── header.tsx
│   │   │   ├── breadcrumb.tsx
│   │   │   └── page-container.tsx
│   │   ├── trips/
│   │   │   ├── trip-workspace.tsx     # Main trip workspace component
│   │   │   ├── trip-general.tsx
│   │   │   ├── trip-flights.tsx
│   │   │   ├── trip-hotels.tsx
│   │   │   ├── trip-transfers.tsx
│   │   │   ├── trip-visa.tsx
│   │   │   ├── trip-insurance.tsx
│   │   │   ├── trip-activities.tsx
│   │   │   ├── trip-customer.tsx
│   │   │   ├── trip-passengers.tsx
│   │   │   ├── trip-payments.tsx
│   │   │   ├── trip-supplier-payments.tsx
│   │   │   ├── trip-documents.tsx
│   │   │   ├── trip-notes.tsx
│   │   │   ├── trip-tasks.tsx
│   │   │   ├── trip-timeline.tsx
│   │   │   ├── trip-profit-summary.tsx
│   │   │   └── trip-sidebar-nav.tsx
│   │   ├── customers/
│   │   │   ├── customer-card.tsx
│   │   │   ├── customer-form.tsx
│   │   │   ├── customer-history.tsx
│   │   │   └── passenger-list.tsx
│   │   ├── suppliers/
│   │   │   ├── supplier-card.tsx
│   │   │   └── supplier-form.tsx
│   │   ├── payments/
│   │   │   ├── payment-form.tsx
│   │   │   └── payment-list.tsx
│   │   ├── accounting/
│   │   │   ├── journal-table.tsx
│   │   │   └── account-balance.tsx
│   │   ├── documents/
│   │   │   ├── document-preview.tsx
│   │   │   └── document-generator.tsx
│   │   ├── dashboard/
│   │   │   ├── stats-card.tsx
│   │   │   ├── upcoming-trips.tsx
│   │   │   ├── recent-bookings.tsx
│   │   │   ├── task-list.tsx
│   │   │   └── notifications-panel.tsx
│   │   ├── reports/
│   │   │   ├── report-chart.tsx
│   │   │   └── report-table.tsx
│   │   └── shared/
│   │       ├── loading-spinner.tsx
│   │       ├── error-state.tsx
│   │       ├── confirm-dialog.tsx
│   │       ├── status-badge.tsx
│   │       ├── currency-input.tsx
│   │       ├── phone-input.tsx
│   │       ├── file-upload.tsx
│   │       └── search-combobox.tsx
│   ├── hooks/
│   │   ├── use-auth.ts
│   │   ├── use-trip.ts
│   │   ├── use-customers.ts
│   │   ├── use-pagination.ts
│   │   ├── use-debounce.ts
│   │   ├── use-local-storage.ts
│   │   └── use-media-query.ts
│   ├── lib/
│   │   ├── api.ts                     # Axios/fetch client with auth
│   │   ├── auth.ts                    # Auth context/provider
│   │   ├── utils.ts                   # cn(), formatCurrency(), etc.
│   │   ├── validators.ts              # Form validation rules
│   │   └── constants.ts
│   ├── stores/                        # Zustand stores
│   │   ├── auth-store.ts
│   │   ├── trip-store.ts
│   │   └── ui-store.ts
│   └── types/
│       ├── trip.ts
│       ├── customer.ts
│       ├── passenger.ts
│       ├── supplier.ts
│       ├── payment.ts
│       ├── accounting.ts
│       ├── document.ts
│       ├── user.ts
│       └── api.ts
├── package.json
├── next.config.ts
├── tsconfig.json
├── tailwind.config.ts
└── postcss.config.mjs
```
