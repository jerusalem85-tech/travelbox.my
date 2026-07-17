# TravelBox ERP – Technical Architecture Document v1.0

> **Stack:** Laravel 13 · Livewire 3 · Volt · MySQL (MariaDB) · TailwindCSS 3 · Vite  
> **Server:** Hostinger – PHP 8.3 shared hosting  
> **Status:** Active development — Version 1

---

## 1. System Philosophy

The **Trip** is the atomic unit of the system. Every customer interaction, every service booked, every financial transaction, every document generated belongs to a Trip. No module exists in isolation — Flights, Hotels, Payments, Accounting are all **extensions of the Trip workspace**.

### Design Tenets

- **Trip-centric**: Every operation can be performed without leaving the Trip workspace.
- **Automatic accounting**: Every financial event generates journal entries. No double-entry.
- **Conversational communication**: Email and WhatsApp are first-class citizens, logged per trip.
- **Document-first**: All confirmations, invoices, and vouchers are stored as Documents with a clear type taxonomy.
- **Audit by default**: Every change is tracked via timeline events.
- **Soft delete everywhere**: No data is ever permanently lost via the UI.

---

## 2. Module Dependency Map

```
                     ┌─────────────────────────────┐
                     │         DASHBOARD           │
                     │  KPIs · Charts · Feed       │
                     └─────────────┬───────────────┘
                                   │
              ┌────────────────────┼────────────────────┐
              │                    │                    │
     ┌────────▼────────┐  ┌───────▼────────┐  ┌───────▼────────┐
     │   CUSTOMERS     │  │     TRIPS      │  │   SUPPLIERS    │
     │  · Profile      │  │  (Heart of ERP)│  │  · Profile     │
     │  · Family       │  │  · Services    │  │  · Contacts    │
     │  · Contacts     │  │  · Finance     │  │  · Bookings    │
     │  · History      │  │  · Documents   │  │  · Balance     │
     └────────┬────────┘  │  · Comm       │  └───────┬────────┘
              │           └───────┬────────┘          │
              └──────────┬───────┘                    │
                         │                            │
              ┌──────────▼────────────────────────────▼──────────┐
              │                  PASSENGERS                       │
              │  · Personal Info · Passport · Preferences · FFP  │
              └────────────────────────┬─────────────────────────┘
                                       │
        ┌──────────┬──────────┬────────┼────────┬──────────┬──────────┐
        ▼          ▼          ▼        ▼        ▼          ▼          ▼
    ┌──────┐ ┌──────┐ ┌────────┐ ┌───┐ ┌────────┐ ┌────────┐ ┌────────┐
    │FLIGHT│ │HOTEL │ │TRANSFER│ │VISA│ │INSUR-  │ │ACTIVITY│ │EXPENSE │
    │      │ │      │ │        │ │    │ │ANCE    │ │        │ │        │
    └──┬───┘ └──┬───┘ └───┬────┘ └──┬─┘ └───┬────┘ └───┬────┘ └───┬────┘
       │        │         │         │       │          │          │
       └────────┴─────────┴─────────┴───────┴──────────┴──────────┘
                                    │
                         ┌──────────▼──────────┐
                         │   ACCOUNTING        │
                         │  · Chart of Accounts │
                         │  · Journal Entries   │
                         │  · Auto-posting      │
                         └──────────┬──────────┘
                                    │
              ┌─────────────────────┼────────────────────┐
              │                     │                    │
     ┌────────▼────────┐  ┌────────▼────────┐  ┌────────▼────────┐
     │   INVOICES      │  │    PAYMENTS     │  │   REPORTS       │
     │  · Items        │  │  · Customer     │  │  · Sales        │
     │  · PDF Export   │  │  · Supplier     │  │  · Profit       │
     └─────────────────┘  │  · Refunds      │  │  · Cash Flow    │
                          └─────────────────┘  │  · Outstanding  │
                                               └─────────────────┘
```

### Dependency Rules

1. **Trips** depend on Customers and Suppliers (FK references only)
2. **Passengers** depend on Trips (and optionally Customers)
3. **Services** (Flight, Hotel, Transfer, Visa, Insurance, Activity) depend on Trips + optional Supplier
4. **Documents** depend on Trips (or optionally Customers/Suppliers)
5. **Invoices** depend on Trips + Customers
6. **Payments** depend on Trips (with polymorphic payer: Customer or Supplier)
7. **Journal Entries** depend on Trips (with polymorphic reference to the source transaction)
8. **Reports** are read-only aggregations across all modules

---

## 3. Database Schema — Entity Relationship

### 3.1 Core Entities

```
TRIPS
│
├── trip_id              UUID PK
├── trip_number          VARCHAR(20) UNIQUE
├── customer_id          UUID FK → CUSTOMERS
├── status               ENUM(enquiry, confirmed, in_progress, completed, cancelled)
├── type                 ENUM(package, custom)
├── name                 VARCHAR(255)
├── destination          VARCHAR(255)
├── start_date           DATE
├── end_date             DATE
├── total_selling_price  DECIMAL(12,2)
├── total_cost_price     DECIMAL(12,2)
├── currency             ENUM(USD, ILS, JOD, EUR)
├── notes                TEXT
├── internal_notes       TEXT
├── latitude             DECIMAL(10,7) NULL
├── longitude            DECIMAL(10,7) NULL
├── created_by           UUID FK → USERS
├── created_at           TIMESTAMP
├── updated_at           TIMESTAMP
└── deleted_at           TIMESTAMP NULL

CUSTOMERS
│
├── customer_id          UUID PK
├── customer_code        VARCHAR(20) UNIQUE
├── type                 ENUM(individual, corporate)
├── first_name           VARCHAR(255)
├── last_name            VARCHAR(255)
├── company_name         VARCHAR(255) NULL
├── email                VARCHAR(255)
├── phone                VARCHAR(50)
├── mobile               VARCHAR(50)
├── address              TEXT
├── city                 VARCHAR(255)
├── country              VARCHAR(255)
├── nationality          VARCHAR(255)
├── passport_number      VARCHAR(50) NULL
├── passport_expiry      DATE NULL
├── date_of_birth        DATE NULL
├── preferred_currency   VARCHAR(3)
├── credit_limit         DECIMAL(12,2)
├── current_balance      DECIMAL(12,2)
├── is_active            BOOLEAN
├── created_by           UUID FK → USERS
└── (timestamps + soft delete)

SUPPLIERS
│
├── supplier_id          UUID PK
├── supplier_code        VARCHAR(20) UNIQUE
├── type                 ENUM(airline, hotel, transfer, visa_office, insurance, tour_operator)
├── company_name         VARCHAR(255)
├── contact_person       VARCHAR(255)
├── email                VARCHAR(255)
├── phone                VARCHAR(50)
├── mobile               VARCHAR(50)
├── address              TEXT
├── city                 VARCHAR(255)
├── country              VARCHAR(255)
├── preferred_currency   VARCHAR(3)
├── payment_terms        TEXT
├── contract_notes       TEXT
├── current_balance      DECIMAL(12,2)
├── is_active            BOOLEAN
├── created_by           UUID FK → USERS
└── (timestamps + soft delete)

PASSENGERS
│
├── passenger_id         UUID PK
├── trip_id              UUID FK → TRIPS
├── customer_id          UUID FK → CUSTOMERS NULL
├── first_name           VARCHAR(255)
├── last_name            VARCHAR(255)
├── date_of_birth        DATE NULL
├── nationality          VARCHAR(255)
├── passport_number      VARCHAR(50)
├── passport_expiry      DATE NULL
├── passport_issue_date  DATE NULL
├── passport_issue_place VARCHAR(255) NULL
├── meal_preference      VARCHAR(50) NULL
├── seat_preference      VARCHAR(50) NULL
├── ffp_number           VARCHAR(50) NULL
├── ffp_airline          VARCHAR(255) NULL
├── special_requests     TEXT NULL
└── (timestamps + soft delete)
```

### 3.2 Service Entities (all follow same pattern)

```
FLIGHT_SEGMENTS        HOTEL_BOOKINGS        TRANSFER_BOOKINGS
├── segment_id (PK)    ├── booking_id (PK)   ├── booking_id (PK)
├── trip_id (FK)       ├── trip_id (FK)      ├── trip_id (FK)
├── supplier_id (FK)   ├── supplier_id (FK)  ├── supplier_id (FK)
├── type               ├── hotel_name        ├── type (arrival/departure/...)
├── airline            ├── city              ├── pickup_location
├── flight_number      ├── check_in (DATE)   ├── dropoff_location
├── departure_airport  ├── check_out (DATE)  ├── pickup_datetime
├── arrival_airport    ├── room_type         ├── vehicle_type
├── departure_datetime ├── meal_plan         ├── number_of_passengers
├── arrival_datetime   ├── number_of_rooms   ├── booking_reference
├── booking_reference  ├── booking_reference ├── status
├── ticket_number      ├── status            ├── cost_price
├── class              ├── cost_price        ├── selling_price
├── status             ├── selling_price     ├── currency
├── cost_price         ├── currency          └── notes
├── selling_price      └── notes
├── currency
└── notes

VISA_APPLICATIONS      INSURANCE_POLICIES    ACTIVITIES
├── visa_id (PK)       ├── policy_id (PK)    ├── activity_id (PK)
├── trip_id (FK)       ├── trip_id (FK)      ├── trip_id (FK)
├── passenger_id (FK)  ├── passenger_id (FK) ├── supplier_id (FK)
├── supplier_id (FK)   ├── supplier_id (FK)  ├── name
├── country            ├── policy_number     ├── type
├── visa_type          ├── type              ├── location
├── application_date   ├── coverage_details  ├── date (DATE)
├── expected_delivery  ├── start_date (DATE) ├── time (TIME)
├── actual_delivery    ├── end_date (DATE)   ├── duration
├── status             ├── status            ├── number_of_participants
├── cost_price         ├── cost_price        ├── booking_reference
├── selling_price      ├── selling_price     ├── status
├── currency           ├── currency          ├── cost_price
└── notes              └── notes             ├── selling_price
                                              ├── currency
                                              └── notes
```

### 3.3 Financial Entities

```
INVOICES               INVOICE_ITEMS          PAYMENTS
├── invoice_id (PK)    ├── item_id (PK)       ├── payment_id (PK)
├── invoice_number     ├── invoice_id (FK)    ├── payment_number
├── trip_id (FK)       ├── description        ├── trip_id (FK)
├── customer_id (FK)   ├── quantity           ├── type (received/made)
├── type (invoice/     ├── unit_price         ├── category
│   receipt/credit)    ├── total              ├── payment_method
├── issue_date (DATE)  ├── service_type       ├── amount
├── due_date (DATE)    ├── service_id (uuid)  ├── currency
├── subtotal           └── (morphTo)          ├── exchange_rate
├── tax                                         ├── payment_date (DATE)
├── total                                       ├── reference
├── status (draft/                               ├── description
│   sent/paid/                                 ├── payer_type (morphTo)
│   overdue/                                   ├── payer_id (uuid)
│   cancelled)                                 ├── invoice_id (FK) NULL
├── notes                                       ├── receipt_number
└── (timestamps + soft delete)                  ├── status
                                                ├── created_by (FK)
                                                └── (timestamps)

JOURNAL_ENTRIES        JOURNAL_ENTRY_ITEMS    CHART_OF_ACCOUNTS
├── entry_id (PK)      ├── item_id (PK)       ├── account_id (PK)
├── entry_number       ├── entry_id (FK)      ├── code VARCHAR(20) UNIQUE
├── trip_id (FK)       ├── account_id (FK)    ├── name VARCHAR(255)
├── date (DATE)        ├── debit DECIMAL(12,2)├── type ENUM(asset,
├── description        ├── credit DECIMAL(    │   liability, equity,
├── type (manual/      │   12,2)              │   income, expense)
│   auto)              └── description        ├── parent_id (self FK)
├── reference_type                           ├── is_active BOOLEAN
│   (morphTo)                                └── (timestamps)
├── reference_id
├── created_by (FK)
└── (timestamps)
```

### 3.4 Communication & Task Entities

```
EMAIL_LOGS             WHATSAPP_LOGS          TASKS
├── log_id (PK)        ├── log_id (PK)        ├── task_id (PK)
├── trip_id (FK)       ├── trip_id (FK)       ├── trip_id (FK)
├── customer_id (FK)   ├── customer_id (FK)   ├── title
├── to (email)         ├── to (phone)         ├── description
├── subject            ├── message            ├── assigned_to (FK→Users)
├── body (TEXT)        ├── type               ├── due_date (DATE)
├── type               ├── status             ├── priority (low/medium/
├── status (sent/      ├── green_api_msg_id   │   high/urgent)
│   failed)            ├── error_message      ├── status (pending/
├── error_message      ├── sent_by (FK→Users) │   in_progress/
├── sent_by (FK→Users) └── (timestamps)       │   completed/cancelled)
└── (timestamps)                              ├── completed_at (DATE)
                                               ├── created_by (FK→Users)
TRIP_TIMELINE_EVENTS    TRIP_NOTES             └── (timestamps)
├── event_id (PK)      ├── note_id (PK)
├── trip_id (FK)       ├── trip_id (FK)
├── type               ├── note_type
├── description        ├── content (TEXT)
├── user_id (FK)       ├── created_by (FK)
├── metadata (JSON)    └── (timestamps)
└── (timestamps)
```

### 3.5 Document Entities

```
DOCUMENTS              DOCUMENT_TEMPLATES
├── document_id (PK)   ├── template_id (PK)
├── trip_id (FK) NULL  ├── name VARCHAR(255)
├── customer_id FK NULL├── type VARCHAR(50)
├── supplier_id FK NULL├── content (TEXT - Blade)
├── type (itinerary/   └── is_default BOOLEAN
│   invoice/receipt/
│   voucher/booking/
│   quotation/visa_letter/
│   insurance_cert/
│   custom)
├── document_number
├── title
├── file_path
├── mime_type
├── size (bytes)
└── generated_at (DATE)
```

### 3.6 System Entities

```
USERS                 SETTINGS              NOTIFICATIONS
├── id (bigint PK)    ├── key (string PK)   ├── id (UUID PK)
├── name              ├── value (TEXT)      ├── type
├── email             └── (timestamps)      ├── message
├── password                                ├── url TEXT NULL
├── roles (via                               ├── icon VARCHAR(50)
│   Spatie Permission)                       ├── read_at TIMESTAMP NULL
└── (timestamps)                             ├── created_at
```

---

## 4. Complete Navigation Structure

```
DASHBOARD           /dashboard
│
├── TRIPS           /trips
│   ├── All Trips   /trips
│   ├── Create      /trips/create
│   ├── Calendar    /trips/calendar
│   ├── Pipeline    /trips/pipeline
│   ├── Map         /trips/map
│   └── [Trip ID]   /trips/{trip}
│
├── CONTACTS
│   ├── Customers   /customers
│   │   ├── All     /customers
│   │   ├── Create  /customers/create
│   │   └── [ID]    /customers/{customer}
│   └── Suppliers   /suppliers
│       ├── All     /suppliers
│       ├── Create  /suppliers/create
│       └── [ID]    /suppliers/{supplier}
│
├── FINANCE
│   ├── Invoices    /invoices
│   │   ├── All     /invoices
│   │   ├── Create  /invoices/create
│   │   └── [ID]    /invoices/{invoice}
│   ├── Payments    /payments
│   │   ├── All     /payments
│   │   └── Create  /payments/create
│   └── Accounting  /accounting
│       └── COA     /accounting/chart-of-accounts
│
├── REPORTS
│   ├── Sales       /reports/sales
│   ├── Profit      /reports/profit
│   └── [More...]   (to be added)
│
├── SETTINGS        /settings
└── PROFILE         /profile
```

---

## 5. User Roles & Permissions Matrix

| Permission | super_admin | admin | manager | sales | operations | accountant | viewer |
|---|---|---|---|---|---|---|---|
| trips.* (CRUD) | ✓ | ✓ | ✓ | ✓ | ✓ edit only | ✗ | ✓ read |
| customers.* | ✓ | ✓ | ✓ | ✓ | ✓ read | ✗ | ✓ read |
| suppliers.* | ✓ | ✓ | ✓ | ✗ | ✓ read | ✗ | ✓ read |
| passengers.* | ✓ | ✓ | ✓ | ✓ | ✓ | ✗ | ✓ read |
| invoices.* | ✓ | ✓ | ✓ | ✓ read | ✓ read | ✓ | ✓ read |
| payments.* | ✓ | ✓ | ✗ | ✗ | ✗ | ✓ | ✓ read |
| accounting.* | ✓ | ✓ | ✓ view | ✗ | ✗ | ✓ | ✓ read |
| reports.* | ✓ | ✓ | ✓ | ✓ sales | ✗ | ✓ | ✓ read |
| documents.* | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ read |
| settings.* | ✓ | ✗ | ✗ | ✗ | ✗ | ✗ | ✗ |
| users.* | ✓ | ✗ | ✗ | ✗ | ✗ | ✗ | ✗ |
| roles.* | ✓ | ✗ | ✗ | ✗ | ✗ | ✗ | ✗ |

---

## 6. Trip Workspace — Full Screen Layout

The Trip Show page (`/trips/{trip}`) is the **primary workspace**. It should display:

```
┌─────────────────────────────────────────────────────────┐
│  Trip #T-0001  │  [Edit] [PDF Itinerary] [Share]       │
│  Istanbul, Turkey  │  Jul 10 – Jul 17, 2026            │
│  Customer: Ahmed Hassan  │  Status: Confirmed          │
├─────────────────────────────────────────────────────────┤
│  Profit Summary: ▲ +$450.00 (22.5% margin)             │
├─────────────────────────────────────────────────────────┤
│  ┌─TABS──────────────────────────────────────────────┐  │
│  │ General │ Passengers │ Services │ Documents │     │  │
│  │ Finance │ Notes/Tasks │ Timeline │ Comm           │  │
│  └───────────────────────────────────────────────────┘  │
│                                                         │
│  [Tab Content — full-width, no scroll on page]          │
│                                                         │
│  Each tab is a Livewire component loaded lazily.        │
└─────────────────────────────────────────────────────────┘
```

### Tabs Summary

| Tab | Contents | Component |
|---|---|---|
| General | Trip info, customer, dates, status, notes | inline in trip-show |
| Passengers | Passenger list + add/edit modal | `PassengerList` |
| Services | Accordion of flights, hotels, transfers, visa, insurance, activities + add/edit modals | `ServiceForm` |
| Documents | Uploaded files, generated PDFs | `TripFiles` |
| Finance | Invoices, payments, expenses, profit breakdown | Trip-show inline |
| Notes/Tasks | Internal notes + task list | `TripNotes` + `TripTasks` |
| Timeline | Chronological event log | Trip-show inline |
| Comm | Email log + WhatsApp log | `TripEmails` + `TripWhatsApp` |

---

## 7. Accounting — Automatic Journal Entry Rules

Every financial transaction must auto-generate a Journal Entry with two Journal Entry Items (double-entry).

### Transaction → Journal Entry Map

| Source Action | Debit Account | Credit Account | Description |
|---|---|---|---|
| Customer Payment (received) | Cash/Bank | Accounts Receivable (Customer) | "Payment received from {Customer}" |
| Supplier Payment (made) | Accounts Payable (Supplier) | Cash/Bank | "Payment made to {Supplier}" |
| Invoice Issued | Accounts Receivable (Customer) | Sales Revenue | "Invoice #{num} to {Customer}" |
| Invoice Payment | Cash/Bank | Accounts Receivable (Customer) | "Payment for Invoice #{num}" |
| Service Booked (cost) | Cost of Sales (by type) | Accounts Payable (Supplier) | "{Flight TK1234} from {Supplier}" |
| Expense Recorded | Expense Account (by category) | Cash/Bank | "Expense: {description}" |
| Refund Issued | Accounts Receivable | Cash/Bank | "Refund to {Customer}" |
| Credit Note | Sales Revenue (contra) | Accounts Receivable | "Credit Note #{num}" |

---

## 8. Document Generator — Template System

The Document Generator uses Laravel DomPDF with Blade templates.

### Template Types

| Type | Blade View | Purpose |
|---|---|---|
| `itinerary` | `pdfs/itinerary.blade.php` | ✅ EXISTS — Travel Itinerary |
| `quotation` | `pdfs/quotation.blade.php` | ❌ MISSING — Sales Quotation |
| `invoice` | `pdfs/invoice.blade.php` | ❌ MISSING — Invoice PDF |
| `receipt` | `pdfs/receipt.blade.php` | ❌ MISSING — Payment Receipt |
| `hotel_voucher` | `pdfs/hotel-voucher.blade.php` | ❌ MISSING — Hotel Voucher |
| `service_voucher` | `pdfs/service-voucher.blade.php` | ❌ MISSING — Service Voucher |
| `payment_voucher` | `pdfs/payment-voucher.blade.php` | ❌ MISSING — Payment Voucher |
| `visa_letter` | `pdfs/visa-letter.blade.php` | ❌ MISSING — Visa Support Letter |
| `insurance_cert` | `pdfs/insurance-cert.blade.php` | ❌ MISSING — Insurance Certificate |
| `booking_confirmation` | `pdfs/booking-confirmation.blade.php` | ❌ MISSING — Booking Confirmation |

### Template Variables (passed to all templates)

```php
[
    'company'    => Setting::get('company_name', 'TravelBox'),
    'logo'       => Setting::get('company_logo'),
    'address'    => Setting::get('company_address'),
    'phone'      => Setting::get('company_phone'),
    'email'      => Setting::get('company_email'),
    'trip'       => $trip,                  // Trip model with relations
    'customer'   => $trip->customer,
    'passengers' => $trip->passengers,
    'services'   => $trip->flightSegments,  // + hotels, transfers, etc.
    'document'   => $document,              // Document model record
    'generated_at' => now(),
]
```

---

## 9. Reports — Specification

| Report | Source Data | Key Metrics | Status |
|---|---|---|---|
| Sales Report | Invoices, Payments | Revenue by month/quarter/year, by customer, by type | ✅ EXISTS |
| Profit Report | Trips (selling - cost) | Gross profit by trip, by month, margin % | ✅ EXISTS |
| Customer Report | Trips, Payments, Invoices | Total spent, trip count, outstanding balance | ❌ MISSING |
| Supplier Report | Bookings, Payments | Total booked, paid, outstanding per supplier | ❌ MISSING |
| Trip Performance | All trips | Avg revenue, avg margin, by destination, by type | ❌ MISSING |
| Cash Flow | Payments (in/out) | Net cash flow by period | ❌ MISSING |
| Outstanding Balances | Customers, Suppliers, Invoices | Aging report (30/60/90+ days) | ❌ MISSING |
| Destination Report | Trips grouped by destination | Popularity, revenue, margin per destination | ❌ MISSING |

---

## 10. Implementation Status — Version 1

### ✅ Completed

| Area | Details |
|---|---|
| Authentication | Login, register, password reset, email verify, profile |
| Role-Based Access | 7 roles, 31 permissions via Spatie |
| Trip CRUD | Full lifecycle with pipeline, calendar, map views |
| Customer Management | CRUD, contacts, family members, trip history |
| Supplier Management | CRUD, contacts, categorized by type |
| Passenger Management | CRUD, passport OCR scan, benefits/preferences |
| Service Management | Flight, Hotel, Transfer, Visa, Insurance, Activity — add/edit/delete within trip |
| Financial Core | Chart of Accounts, Journal Entries (double-entry), Invoices, Payments |
| Communication | Email sending + log, WhatsApp via Green API + log |
| Notification System | Database notifications for key events |
| Document Management | File uploads per trip/customer/supplier |
| PDF Generation | Travel Itinerary PDF (dompdf) |
| Reports | Sales Report, Profit Report |
| Dashboard | KPIs, charts, revenue overview, status counts |
| Global Search | Search across trips, customers, suppliers |
| Trip Timeline | Auto-logged events for all trip actions |
| Tasks | Per-trip task management with assignee, priority, due date |
| Notes | Per-trip internal notes with type |
| Trip Expenses | Per-trip expense tracking |
| Trip Benefits | Per-trip add-on benefits |
| Settings | Company info, number prefixes, defaults |
| OCR Services | Passport OCR (MRZ parsing), Flight document OCR |
| Maps | Trip map view with Leaflet/OSM, geolocated markers |
| Calendar | Monthly trip calendar grid |

### 🔴 High Priority — Missing

| Item | Effort | Reason |
|---|---|---|
| **Auto-Accounting Integration** | Medium | Payments/invoices/expenses should auto-create Journal Entries |
| **Document Generator (9 more PDFs)** | Medium | Quotation, Invoice PDF, Receipt, Hotel/Service/Payment Voucher, Visa Letter, Insurance Cert, Booking Confirmation |
| **Customer Report** | Small | Aggregated view of customer spend, trips, balance |
| **Supplier Report** | Small | Aggregated view of supplier bookings, payments, balance |
| **Profit Summary in Trip Workspace** | Small | Revenue - Cost = Profit card with margin % |
| **Cash Flow Report** | Medium | Payments grouped by type, net cash by period |
| **Outstanding Balances** | Medium | Aging report for both customers and suppliers |

### 🟡 Medium Priority

| Item | Effort | Reason |
|---|---|---|
| **Trip Performance Report** | Medium | Revenue, margin by destination/type/month |
| **Destination Report** | Small | Popularity metrics per destination |
| **Dashboard KPIs** | Small | Today's sales, monthly sales/profit, outstanding balances, upcoming trips, recent bookings |

### 🔵 Future / Version 2

| Item | Reason |
|---|---|
| Multi-company / SaaS | Out of scope for V1 |
| Customer online portal | Out of scope for V1 |
| API for external integrations | Out of scope for V1 |
| Marketplace / supplier portal | Out of scope for V1 |
| Exchange rate auto-updates | Nice-to-have |
| Recurring invoices | Nice-to-have |

---

## 11. Technology Decisions

| Concern | Decision | Rationale |
|---|---|---|
| Frontend framework | Livewire 3 + Alpine.js | No JS build step; server-rendered; fast for internal tools |
| CSS | TailwindCSS 3 + Vite | Utility-first; rapid prototyping; tree-shaking via Vite |
| PDF | barryvdh/laravel-dompdf | Mature; Blade templates; no external service |
| Email | Laravel Mail (SMTP) | Reliable; wide driver support |
| WhatsApp | Green API | Works on shared hosting (outbound HTTP only) |
| OCR | smalot/pdfparser + OCR.space API | PDFs parsed locally; images use cloud API |
| Maps | Leaflet + OpenStreetMap | Free; no API key; no usage limits |
| Auth | Laravel Breeze + Volt | Default stack; works with Spatie Permission |
| Roles | Spatie laravel-permission | Industry standard; cached; flexible |
| Payments | Custom (no gateway) | Internal ERP; manual payment recording |
| Accounting | Custom double-entry | Integrated; no external accounting software needed |

---

## 12. Folder Structure (Current — Optimized)

```
app/
├── Http/
│   ├── Controllers/
│   │   └── ItineraryController.php    # PDF generation controller
│   └── Livewire/                      # ALL Livewire components
│       ├── AdminDashboard.php
│       ├── GlobalSearch.php
│       ├── NotificationBell.php
│       ├── Trips/                     # 13 trip-related components
│       ├── Customers/                 # 3 customer components
│       ├── Suppliers/                 # 3 supplier components
│       ├── Invoices/                  # 3 invoice components
│       ├── Payments/                  # 2 payment components
│       └── Reports/                   # 2 report components
├── Livewire/                          # Volt-powered pages
├── Mail/
│   ├── TripDetailMail.php
│   └── CustomEmailMail.php
├── Models/                            # 30 Eloquent models
├── Notifications/
│   └── AppNotification.php
├── Services/
│   ├── AirportService.php             # Airport database
│   ├── EmailService.php               # Email sending logic
│   ├── FlightOcrService.php           # Flight doc OCR
│   ├── FlightParserService.php         # Flight text parser
│   ├── ItineraryService.php           # PDF generation
│   ├── NotificationService.php        # Notification creation
│   ├── PassportOcrService.php         # Passport MRZ OCR
│   └── WhatsAppService.php            # Green API WhatsApp
├── Traits/
│   └── HasUuid.php
└── View/Components/
    ├── AppLayout.php
    └── GuestLayout.php

config/
├── dompdf.php                         # PDF config
├── permission.php                     # Spatie config
└── services.php                       # Green API + OCR keys

database/
├── migrations/                        # 36 migration files
└── seeders/
    ├── RoleAndPermissionSeeder.php    # 7 roles, 31 perms
    ├── ChartOfAccountSeeder.php       # 27 accounts
    ├── DefaultSettingsSeeder.php      # 11 settings
    ├── SupplierSeeder.php             # 10 suppliers
    ├── CustomerSeeder.php             # 10 customers
    ├── TripSeeder.php                 # 10 trips
    └── DatabaseSeeder.php

resources/views/
├── layouts/app.blade.php              # Main layout with sidebar
├── livewire/                          # 33 Livewire view files
├── pdfs/itinerary.blade.php           # PDF template
├── emails/                            # Email templates
└── components/                        # Reusable Blade components
```

---

## 13. Key Architectural Rules

### 13.1 UUID Primary Keys
Every business entity uses UUID v4 as primary key. No auto-increment IDs are exposed to users. The `HasUuid` trait auto-generates on `creating`.

### 13.2 Soft Deletes
All business entities use `SoftDeletes`. Data is never permanently removed through the UI.

### 13.3 Audit Timeline
Every meaningful action on a Trip (create, edit, delete service, add passenger, record payment, etc.) must call `$trip->logTimeline()`.

### 13.4 Notification Events
Actions that affect other users (task assigned, payment received, status change) must create a Notification via `NotificationService`.

### 13.5 No Duplicate Business Logic
Business logic lives in:
- **Models** — relationships, computed attributes, simple queries
- **Services** — complex operations, external API calls, OCR, PDF generation
- **Livewire Components** — UI state, validation, user interaction orchestration
Controllers are stubs — all logic is in Livewire.

### 13.6 Service Data Ownership
- `FlightSegment`, `HotelBooking`, etc. belong to a Trip AND optionally a Supplier.
- Deleting a Trip cascades to all its services (via migration foreign key `ON DELETE CASCADE`).

---

## 14. Next Steps — Implementation Order

### Phase 1a: Complete Core (1-2 days)
1. Auto-accounting: wire up payments → journal entries
2. Profit summary card in Trip workspace
3. Outstanding balances on customer/supplier show pages

### Phase 1b: Document Generator (2-3 days)
4. Create 9 missing PDF templates (quotation, invoice, receipt, hotel voucher, service voucher, payment voucher, visa letter, insurance cert, booking confirmation)
5. Build a unified `DocumentGenerator` service

### Phase 1c: Reports (1-2 days)
6. Customer Report
7. Supplier Report
8. Cash Flow Report
9. Outstanding Aging Report

### Phase 1d: Dashboard Polish (1 day)
10. Add today's sales, monthly KPIs, upcoming trips cards to dashboard
