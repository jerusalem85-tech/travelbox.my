# TravelBox ERP — Database Schema (V1)

## 1. Schema Overview (Prisma)

### Enums

```prisma
enum UserRole {
  SUPER_ADMIN
  ADMIN
  MANAGER
  ACCOUNTANT
  SALES_AGENT
  OPERATIONS
  CUSTOMER_SERVICE
  VIEWER
}

enum TripStatus {
  INQUIRY
  QUOTATION
  PROVISIONAL
  CONFIRMED
  IN_PROGRESS
  COMPLETED
  CANCELLED
  REFUNDED
}

enum PaymentMethod {
  CASH
  BANK_TRANSFER
  CREDIT_CARD
  CHEQUE
  PAYPAL
  WALLET
}

enum PaymentDirection {
  INFLOW    // Customer -> Us
  OUTFLOW   // Us -> Supplier
}

enum PaymentStatus {
  PENDING
  PARTIAL
  PAID
  REFUNDED
  CANCELLED
}

enum DocumentType {
  QUOTATION
  PROPOSAL
  INVOICE
  RECEIPT
  VOUCHER
  ITINERARY
  TICKET
  CERTIFICATE
  CONTRACT
}

enum DocumentStatus {
  DRAFT
  FINAL
  SENT
  PAID
  CANCELLED
}

enum JournalEntryType {
  DEBIT
  CREDIT
}

enum AccountCategory {
  ASSET
  LIABILITY
  EQUITY
  REVENUE
  EXPENSE
  CONTRA_ASSET
  CONTRA_LIABILITY
  CONTRA_REVENUE
  CONTRA_EXPENSE
}

enum NotificationType {
  TASK_ASSIGNED
  PAYMENT_DUE
  PAYMENT_RECEIVED
  TRIP_UPDATED
  DOCUMENT_GENERATED
  BOOKING_CONFIRMED
  SYSTEM_ALERT
}

enum TaskPriority {
  LOW
  MEDIUM
  HIGH
  URGENT
}

enum TaskStatus {
  PENDING
  IN_PROGRESS
  COMPLETED
  CANCELLED
}

enum Currency {
  USD
  EUR
  GBP
  JPY
  AUD
  CAD
  CHF
  CNY
  INR
  AED
  SAR
  EGP
  TRY
  MXN
  BRL
  KRW
  SEK
  NOK
  DKK
  NZD
}

enum Gender {
  MALE
  FEMALE
  OTHER
}

enum PassengerType {
  ADULT
  CHILD
  INFANT
}
```

### Main Models

#### Tenants (Multi-Company Ready)

```prisma
model Tenant {
  id          String   @id @default(uuid())
  name        String
  slug        String   @unique
  logo        String?
  address     String?
  phone       String?
  email       String?
  website     String?
  taxId       String?
  currency    Currency @default(USD)
  dateFormat  String   @default("YYYY-MM-DD")
  timezone    String   @default("UTC")
  isActive    Boolean  @default(true)
  createdAt   DateTime @default(now())
  updatedAt   DateTime @updatedAt
  deletedAt   DateTime?

  users       User[]
  trips       Trip[]
  customers   Customer[]
  suppliers   Supplier[]
  accounts    Account[]
  settings    TenantSetting[]
  documents   Document[]

  @@map("tenants")
}
```

#### Users

```prisma
model User {
  id              String   @id @default(uuid())
  tenantId        String
  email           String   @unique
  password        String
  firstName       String
  lastName        String
  phone           String?
  avatar          String?
  role            UserRole @default(SALES_AGENT)
  isActive        Boolean  @default(true)
  lastLoginAt     DateTime?
  refreshToken    String?
  createdAt       DateTime @default(now())
  updatedAt       DateTime @updatedAt
  deletedAt       DateTime?

  tenant          Tenant   @relation(fields: [tenantId], references: [id])
  createdTrips    Trip[]   @relation("TripCreator")
  assignedTrips   Trip[]   @relation("TripAssignee")
  tasks           Task[]
  notifications   Notification[]
  auditLogs       AuditLog[]
  paymentReceived Payment[] @relation("PaymentReceivedBy")
  paymentRecorded Payment[] @relation("PaymentRecorder")

  @@index([tenantId])
  @@map("users")
}
```

#### Customers

```prisma
model Customer {
  id          String   @id @default(uuid())
  tenantId    String
  firstName   String
  lastName    String
  email       String?
  phone       String?
  phone2      String?
  address     String?
  city        String?
  country     String?
  nationality String?
  passportNo  String?
  passportExpiry DateTime?
  dob         DateTime?
  gender      Gender?
  isCompany   Boolean  @default(false)
  companyName String?
  taxId       String?
  notes       String?
  tags        String[] @default([])
  totalTrips  Int      @default(0)
  totalRevenue Decimal @default(0) @db.Decimal(12, 2)
  createdAt   DateTime @default(now())
  updatedAt   DateTime @updatedAt
  deletedAt   DateTime?

  tenant      Tenant     @relation(fields: [tenantId], references: [id])
  trips       TripCustomer[]
  passengers  Passenger[]
  payments    Payment[]
  journalEntries AccountingJournal[]

  @@index([tenantId])
  @@index([email])
  @@index([phone])
  @@map("customers")
}
```

#### Passengers

```prisma
model Passenger {
  id            String        @id @default(uuid())
  customerId    String?
  tripId        String
  firstName     String
  lastName      String
  middleName    String?
  dob           DateTime?
  gender        Gender?
  passportNo    String?
  passportExpiry DateTime?
  nationality   String?
  email         String?
  phone         String?
  passengerType PassengerType @default(ADULT)
  notes         String?
  createdAt     DateTime      @default(now())
  updatedAt     DateTime      @updatedAt

  customer      Customer?     @relation(fields: [customerId], references: [id])
  trip          Trip          @relation(fields: [tripId], references: [id])
  bookings      Booking[]

  @@index([tripId])
  @@index([customerId])
  @@map("passengers")
}
```

#### Trips (Core Aggregate)

```prisma
model Trip {
  id              String     @id @default(uuid())
  tenantId        String
  referenceNo     String     @unique  // e.g., TB-2026-0001
  name            String?             // Trip name / title
  description     String?
  status          TripStatus @default(INQUIRY)

  // Dates
  startDate       DateTime?
  endDate         DateTime?
  bookingDate     DateTime   @default(now())
  confirmedDate   DateTime?
  completedDate   DateTime?
  cancelledDate   DateTime?

  // Financial (computed via triggers)
  totalRevenue    Decimal    @default(0) @db.Decimal(12, 2)
  totalCost       Decimal    @default(0) @db.Decimal(12, 2)
  totalProfit     Decimal    @default(0) @db.Decimal(12, 2)
  profitMargin    Decimal    @default(0) @db.Decimal(5, 2)
  currency        Currency   @default(USD)

  // Team
  createdById     String
  assignedToId    String?

  // Metadata
  source          String?    // walk-in, referral, website, phone, email
  tags            String[]   @default([])
  internalNotes   String?
  isActive        Boolean    @default(true)
  createdAt       DateTime   @default(now())
  updatedAt       DateTime   @updatedAt
  deletedAt       DateTime?

  // Relations
  tenant          Tenant              @relation(fields: [tenantId], references: [id])
  createdBy       User                @relation("TripCreator", fields: [createdById], references: [id])
  assignedTo      User?               @relation("TripAssignee", fields: [assignedToId], references: [id])
  customers       TripCustomer[]
  passengers      Passenger[]
  flights         Flight[]
  hotels          Hotel[]
  transfers       Transfer[]
  visas           Visa[]
  insurances      Insurance[]
  activities      Activity[]
  payments        Payment[]
  invoices        Invoice[]
  documents       Document[]
  notes           Note[]
  tasks           Task[]
  timelineEntries TimelineEntry[]
  journalEntries  AccountingJournal[]

  @@index([tenantId])
  @@index([status])
  @@index([startDate])
  @@index([createdById])
  @@index([assignedToId])
  @@map("trips")
}
```

#### Trip-Customer Junction

```prisma
model TripCustomer {
  tripId      String
  customerId  String
  isPrimary   Boolean  @default(false)
  role        String?  @default("booker") // booker, traveler, payer
  createdAt   DateTime @default(now())

  trip        Trip     @relation(fields: [tripId], references: [id])
  customer    Customer @relation(fields: [customerId], references: [id])

  @@id([tripId, customerId])
  @@map("trip_customers")
}
```

#### Flight Services

```prisma
model Flight {
  id              String   @id @default(uuid())
  tripId          String
  airline         String
  flightNo        String
  departureAirport String
  arrivalAirport  String
  departureDate   DateTime
  arrivalDate     DateTime
  departureTerminal String?
  arrivalTerminal  String?
  bookingRef      String?  // PNR Code
  ticketNo        String?
  class           String?  // economy, business, first
  stops           Int      @default(0)
  layoverDuration String?
  baggageAllowance String?
  status          String   @default("confirmed") // confirmed, waitlisted, cancelled
  costPrice       Decimal  @default(0) @db.Decimal(12, 2)
  sellPrice       Decimal  @default(0) @db.Decimal(12, 2)
  supplierId      String?
  supplierRef     String?
  notes           String?
  createdAt       DateTime @default(now())
  updatedAt       DateTime @updatedAt

  trip            Trip     @relation(fields: [tripId], references: [id])
  supplier        Supplier? @relation(fields: [supplierId], references: [id])

  @@index([tripId])
  @@map("flights")
}
```

#### Hotel Services

```prisma
model Hotel {
  id              String   @id @default(uuid())
  tripId          String
  hotelName       String
  city            String
  country         String?
  checkIn         DateTime
  checkOut        DateTime
  roomType        String
  boardBasis      String?  // BB, HB, FB, AI, RO
  numberOfRooms   Int      @default(1)
  numberOfGuests  Int
  confirmationRef String?
  status          String   @default("requested")
  costPrice       Decimal  @default(0) @db.Decimal(12, 2)
  sellPrice       Decimal  @default(0) @db.Decimal(12, 2)
  supplierId      String?
  supplierRef     String?
  notes           String?
  createdAt       DateTime @default(now())
  updatedAt       DateTime @updatedAt

  trip            Trip     @relation(fields: [tripId], references: [id])
  supplier        Supplier? @relation(fields: [supplierId], references: [id])

  @@index([tripId])
  @@map("hotels")
}
```

#### Transfer Services

```prisma
model Transfer {
  id              String       @id @default(uuid())
  tripId          String
  type            String       // airport-hotel, hotel-airport, inter-hotel, excursion
  pickupLocation  String
  dropoffLocation String
  pickupDate      DateTime
  pickupTime      String?
  dropoffDate     DateTime?
  vehicleType     String       // sedan, minivan, bus, luxury
  passengers      Int
  flightNo        String?
  confirmationRef String?
  status          String       @default("requested")
  costPrice       Decimal      @default(0) @db.Decimal(12, 2)
  sellPrice       Decimal      @default(0) @db.Decimal(12, 2)
  supplierId      String?
  supplierRef     String?
  notes           String?
  createdAt       DateTime     @default(now())
  updatedAt       DateTime     @updatedAt

  trip            Trip         @relation(fields: [tripId], references: [id])
  supplier        Supplier?    @relation(fields: [supplierId], references: [id])

  @@index([tripId])
  @@map("transfers")
}
```

#### Visa Services

```prisma
model Visa {
  id              String   @id @default(uuid())
  tripId          String
  country         String
  visaType        String   // tourist, business, transit, eVisa
  applicantName   String
  passportNo      String
  nationality     String
  applicationDate DateTime?
  decisionDate    DateTime?
  validFrom       DateTime?
  validUntil      DateTime?
  entryType       String?  // single, double, multiple
  status          String   @default("not_started") // not_started, submitted, in_process, approved, rejected
  costPrice       Decimal  @default(0) @db.Decimal(12, 2)
  sellPrice       Decimal  @default(0) @db.Decimal(12, 2)
  supplierId      String?
  supplierRef     String?
  notes           String?
  createdAt       DateTime @default(now())
  updatedAt       DateTime @updatedAt

  trip            Trip     @relation(fields: [tripId], references: [id])
  supplier        Supplier? @relation(fields: [supplierId], references: [id])

  @@index([tripId])
  @@map("visas")
}
```

#### Insurance Services

```prisma
model Insurance {
  id              String   @id @default(uuid())
  tripId          String
  provider        String
  policyNo        String?
  type            String   // travel, medical, cancellation, baggage
  startDate       DateTime
  endDate         DateTime
  coverageAmount  Decimal  @db.Decimal(12, 2)
  premiumCost     Decimal  @default(0) @db.Decimal(12, 2)
  sellPrice       Decimal  @default(0) @db.Decimal(12, 2)
  status          String   @default("pending")
  supplierId      String?
  supplierRef     String?
  notes           String?
  createdAt       DateTime @default(now())
  updatedAt       DateTime @updatedAt

  trip            Trip     @relation(fields: [tripId], references: [id])
  supplier        Supplier? @relation(fields: [supplierId], references: [id])

  @@index([tripId])
  @@map("insurances")
}
```

#### Activity / Excursion Services

```prisma
model Activity {
  id              String   @id @default(uuid())
  tripId          String
  name            String
  description     String?
  location        String?
  date            DateTime
  startTime       String?
  duration        String?
  includes        String?  // what's included
  excludes        String?  // what's excluded
  bookingRef      String?
  status          String   @default("requested")
  costPrice       Decimal  @default(0) @db.Decimal(12, 2)
  sellPrice       Decimal  @default(0) @db.Decimal(12, 2)
  supplierId      String?
  supplierRef     String?
  notes           String?
  createdAt       DateTime @default(now())
  updatedAt       DateTime @updatedAt

  trip            Trip     @relation(fields: [tripId], references: [id])
  supplier        Supplier? @relation(fields: [supplierId], references: [id])

  @@index([tripId])
  @@map("activities")
}
```

#### Suppliers

```prisma
model Supplier {
  id          String   @id @default(uuid())
  tenantId    String
  name        String
  type        String   // airline, hotel, transfer, visa, insurance, activity, other
  contactName String?
  email       String?
  phone       String?
  phone2      String?
  address     String?
  city        String?
  country     String?
  taxId       String?
  paymentTerms String? // net15, net30, net45, advance
  commissionPct Decimal? @db.Decimal(5, 2)
  contractStart DateTime?
  contractEnd  DateTime?
  rating      Int?     @default(3)
  notes       String?
  tags        String[] @default([])
  isActive    Boolean  @default(true)
  createdAt   DateTime @default(now())
  updatedAt   DateTime @updatedAt
  deletedAt   DateTime?

  tenant      Tenant        @relation(fields: [tenantId], references: [id])
  flights     Flight[]
  hotels      Hotel[]
  transfers   Transfer[]
  visas       Visa[]
  insurances  Insurance[]
  activities  Activity[]
  payments    Payment[]

  @@index([tenantId])
  @@index([type])
  @@map("suppliers")
}
```

#### Payments

```prisma
model Payment {
  id              String          @id @default(uuid())
  tripId          String
  direction       PaymentDirection
  type            String?         // customer, supplier
  customerId      String?
  supplierId      String?
  amount          Decimal         @db.Decimal(12, 2)
  currency        Currency        @default(USD)
  exchangeRate    Decimal         @default(1) @db.Decimal(10, 4)
  amountLocal     Decimal         @db.Decimal(12, 2)
  method          PaymentMethod
  status          PaymentStatus   @default(PENDING)
  referenceNo     String?         // transaction reference
  description     String?
  paymentDate     DateTime        @default(now())
  dueDate         DateTime?
  receivedById    String?
  recordedById    String?
  notes           String?
  createdAt       DateTime        @default(now())
  updatedAt       DateTime        @updatedAt
  deletedAt       DateTime?

  trip            Trip            @relation(fields: [tripId], references: [id])
  customer        Customer?       @relation(fields: [customerId], references: [id])
  supplier        Supplier?       @relation(fields: [supplierId], references: [id])
  receivedBy      User?           @relation("PaymentReceivedBy", fields: [receivedById], references: [id])
  recordedBy      User?           @relation("PaymentRecorder", fields: [recordedById], references: [id])

  @@index([tripId])
  @@index([customerId])
  @@index([supplierId])
  @@index([direction])
  @@index([paymentDate])
  @@map("payments")
}
```

#### Accounting — Chart of Accounts

```prisma
model Account {
  id          String          @id @default(uuid())
  tenantId    String
  code        String          // e.g., "1000", "2000"
  name        String          // e.g., "Cash & Bank", "Accounts Receivable"
  description String?
  category    AccountCategory
  isActive    Boolean         @default(true)
  parentId    String?
  createdAt   DateTime        @default(now())
  updatedAt   DateTime        @updatedAt
  deletedAt   DateTime?

  tenant      Tenant          @relation(fields: [tenantId], references: [id])
  parent      Account?        @relation("AccountHierarchy", fields: [parentId], references: [id])
  children    Account[]       @relation("AccountHierarchy")
  journalEntries AccountingJournal[]

  @@unique([tenantId, code])
  @@index([tenantId])
  @@index([parentId])
  @@map("accounts")
}
```

#### Accounting — Journal Entries

```prisma
model AccountingJournal {
  id          String           @id @default(uuid())
  tripId      String?
  paymentId   String?
  customerId  String?
  supplierId  String?
  accountId   String
  entryType   JournalEntryType
  amount      Decimal          @db.Decimal(12, 2)
  currency    Currency         @default(USD)
  description String?
  entryDate   DateTime         @default(now())
  referenceNo String?          // Invoice No, Payment Ref
  createdById String
  createdAt   DateTime         @default(now())

  trip        Trip?            @relation(fields: [tripId], references: [id])
  payment     Payment?         @relation(fields: [paymentId], references: [id])
  customer    Customer?        @relation(fields: [customerId], references: [id])
  supplier    Supplier?        @relation(fields: [supplierId], references: [id])
  account     Account          @relation(fields: [accountId], references: [id])
  createdBy   User             @relation(fields: [createdById], references: [id])

  @@index([tripId])
  @@index([accountId])
  @@index([entryDate])
  @@index([createdById])
  @@map("accounting_journals")
}
```

#### Invoices

```prisma
model Invoice {
  id              String         @id @default(uuid())
  tripId          String
  invoiceNo       String         @unique
  documentType    DocumentType   @default(INVOICE)
  status          DocumentStatus @default(DRAFT)
  customerId      String?
  issueDate       DateTime       @default(now())
  dueDate         DateTime?
  paidDate        DateTime?
  subtotal        Decimal        @db.Decimal(12, 2)
  taxRate         Decimal        @default(0) @db.Decimal(5, 2)
  taxAmount       Decimal        @default(0) @db.Decimal(12, 2)
  discountPct     Decimal        @default(0) @db.Decimal(5, 2)
  discountAmount  Decimal        @default(0) @db.Decimal(12, 2)
  totalAmount     Decimal        @db.Decimal(12, 2)
  amountPaid      Decimal        @default(0) @db.Decimal(12, 2)
  balanceDue      Decimal        @db.Decimal(12, 2)
  currency        Currency       @default(USD)
  notes           String?
  terms           String?
  createdAt       DateTime       @default(now())
  updatedAt       DateTime       @updatedAt
  deletedAt       DateTime?

  trip            Trip           @relation(fields: [tripId], references: [id])
  customer        Customer?      @relation(fields: [customerId], references: [id])

  @@index([tripId])
  @@index([invoiceNo])
  @@index([status])
  @@map("invoices")
}
```

#### Documents (Generated)

```prisma
model Document {
  id            String       @id @default(uuid())
  tenantId      String
  tripId        String?
  documentType  DocumentType
  documentNo    String       @unique
  title         String
  status        DocumentStatus @default(DRAFT)
  filePath      String?      // Path to generated PDF
  fileSize      Int?         // File size in bytes
  mimeType      String?      @default("application/pdf")
  generatedById String?
  sentAt        DateTime?
  createdAt     DateTime     @default(now())
  updatedAt     DateTime     @updatedAt
  deletedAt     DateTime?

  tenant        Tenant       @relation(fields: [tenantId], references: [id])
  trip          Trip?        @relation(fields: [tripId], references: [id])
  generatedBy   User?        @relation(fields: [generatedById], references: [id])

  @@index([tenantId])
  @@index([tripId])
  @@index([documentType])
  @@map("documents")
}
```

#### Notes

```prisma
model Note {
  id        String   @id @default(uuid())
  tripId    String
  authorId  String
  content   String
  isPinned  Boolean  @default(false)
  createdAt DateTime @default(now())
  updatedAt DateTime @updatedAt

  trip      Trip     @relation(fields: [tripId], references: [id])
  author    User     @relation(fields: [authorId], references: [id])

  @@index([tripId])
  @@map("notes")
}
```

#### Tasks

```prisma
model Task {
  id          String       @id @default(uuid())
  tripId      String?
  title       String
  description String?
  priority    TaskPriority @default(MEDIUM)
  status      TaskStatus   @default(PENDING)
  assignedToId String?
  createdById String
  dueDate     DateTime?
  completedAt DateTime?
  createdAt   DateTime     @default(now())
  updatedAt   DateTime     @updatedAt

  trip        Trip?        @relation(fields: [tripId], references: [id])
  assignedTo  User?        @relation(fields: [assignedToId], references: [id])
  createdBy   User         @relation(fields: [createdById], references: [id])

  @@index([tripId])
  @@index([assignedToId])
  @@index([status])
  @@map("tasks")
}
```

#### Timeline

```prisma
model TimelineEntry {
  id          String   @id @default(uuid())
  tripId      String
  action      String   // status_change, payment, document, note, booking
  description String
  userId      String
  metadata    Json?    // Flexible payload for any action type
  createdAt   DateTime @default(now())

  trip        Trip     @relation(fields: [tripId], references: [id])
  user        User     @relation(fields: [userId], references: [id])

  @@index([tripId])
  @@index([createdAt])
  @@map("timeline_entries")
}
```

#### Notifications

```prisma
model Notification {
  id      String           @id @default(uuid())
  userId  String
  type    NotificationType
  title   String
  message String?
  data    Json?
  isRead  Boolean          @default(false)
  createdAt DateTime       @default(now())

  user    User             @relation(fields: [userId], references: [id])

  @@index([userId])
  @@index([isRead])
  @@map("notifications")
}
```

#### Audit Logs

```prisma
model AuditLog {
  id         String   @id @default(uuid())
  tenantId   String
  userId     String
  action     String   // CREATE, UPDATE, DELETE, VIEW, LOGIN
  entity     String   // trip, customer, payment, etc.
  entityId   String
  oldValue   Json?
  newValue   Json?
  ipAddress  String?
  userAgent  String?
  createdAt  DateTime @default(now())

  user       User     @relation(fields: [userId], references: [id])

  @@index([tenantId])
  @@index([userId])
  @@index([entity])
  @@index([entityId])
  @@index([createdAt])
  @@map("audit_logs")
}
```

#### Tenant Settings

```prisma
model TenantSetting {
  id        String @id @default(uuid())
  tenantId  String
  key       String
  value     Json
  createdAt DateTime @default(now())
  updatedAt DateTime @updatedAt

  tenant    Tenant @relation(fields: [tenantId], references: [id])

  @@unique([tenantId, key])
  @@map("tenant_settings")
}
```

---

## 2. ERD (Relationship Map)

```
TENANT 1──* USER
TENANT 1──* CUSTOMER
TENANT 1──* SUPPLIER
TENANT 1──* ACCOUNT
TENANT 1──* DOCUMENT

TRIP *──1 TENANT
TRIP *──1 USER (createdBy)
TRIP *──? USER (assignedTo)
TRIP 1──* PASSENGER
TRIP 1──* FLIGHT
TRIP 1──* HOTEL
TRIP 1──* TRANSFER
TRIP 1──* VISA
TRIP 1──* INSURANCE
TRIP 1──* ACTIVITY
TRIP 1──* PAYMENT
TRIP 1──* INVOICE
TRIP 1──* DOCUMENT
TRIP 1──* NOTE
TRIP 1──* TASK
TRIP 1──* TIMELINE_ENTRY
TRIP *──* CUSTOMER (via TRIP_CUSTOMER)

CUSTOMER 1──* PASSENGER
CUSTOMER 1──* PAYMENT
CUSTOMER *──* TRIP (via TRIP_CUSTOMER)

SUPPLIER 1──* FLIGHT
SUPPLIER 1──* HOTEL
SUPPLIER 1──* TRANSFER
SUPPLIER 1──* VISA
SUPPLIER 1──* INSURANCE
SUPPLIER 1──* ACTIVITY
SUPPLIER 1──* PAYMENT

ACCOUNT 1──* ACCOUNTING_JOURNAL
PAYMENT 1──? ACCOUNTING_JOURNAL
TRIP 1──* ACCOUNTING_JOURNAL
```

---

## 3. Indexing Strategy

| Table | Index | Type | Rationale |
|-------|-------|------|-----------|
| trips | (tenant_id, status) | Composite | Dashboard filtering by status |
| trips | (tenant_id, start_date) | Composite | Calendar view, date range queries |
| trips | reference_no | Unique | Lookup by reference |
| payments | (trip_id, direction) | Composite | Trip payment summary |
| payments | (customer_id) | Single | Customer payment history |
| payments | (payment_date) | Single | Cash flow reports |
| passengers | (trip_id) | Single | Trip passenger list |
| accounting_journals | (trip_id) | Single | Trip P&L calculation |
| accounting_journals | (entry_date, account_id) | Composite | Trial balance, ledger |
| audit_logs | (entity, entity_id) | Composite | Entity history |
| audit_logs | (created_at) | Single | Audit trail queries |

---

## 4. Chart of Accounts (Default Seed)

| Code | Name | Category |
|------|------|----------|
| 1000 | Cash & Bank | ASSET |
| 1100 | Accounts Receivable | ASSET |
| 1200 | Prepaid Expenses | ASSET |
| 1300 | Fixed Assets | ASSET |
| 2000 | Accounts Payable | LIABILITY |
| 2100 | Customer Deposits | LIABILITY |
| 2200 | Unearned Revenue | LIABILITY |
| 3000 | Retained Earnings | EQUITY |
| 3100 | Owner's Draw | EQUITY |
| 4000 | Trip Revenue — Flights | REVENUE |
| 4100 | Trip Revenue — Hotels | REVENUE |
| 4200 | Trip Revenue — Transfers | REVENUE |
| 4300 | Trip Revenue — Visa | REVENUE |
| 4400 | Trip Revenue — Insurance | REVENUE |
| 4500 | Trip Revenue — Activities | REVENUE |
| 4600 | Service Fees | REVENUE |
| 5000 | Cost of Sales — Flights | EXPENSE |
| 5100 | Cost of Sales — Hotels | EXPENSE |
| 5200 | Cost of Sales — Transfers | EXPENSE |
| 5300 | Cost of Sales — Visa | EXPENSE |
| 5400 | Cost of Sales — Insurance | EXPENSE |
| 5500 | Cost of Sales — Activities | EXPENSE |
| 6000 | Salaries & Wages | EXPENSE |
| 6100 | Rent & Utilities | EXPENSE |
| 6200 | Marketing & Advertising | EXPENSE |
| 6300 | Office Supplies | EXPENSE |
| 6400 | Professional Fees | EXPENSE |
| 6500 | Bank Charges | EXPENSE |
| 6600 | Taxes & Licenses | EXPENSE |
| 7000 | Other Income | REVENUE |
| 7100 | Other Expenses | EXPENSE |

---

## 5. Double-Entry Accounting Rules

### Every financial event creates automatic journal entries:

1. **Customer Payment (Deposit/Full)**
   - Debit: Cash & Bank (1000)
   - Credit: Customer Deposits (2100) or Unearned Revenue (2200)

2. **Invoice Issued**
   - Debit: Accounts Receivable (1100)
   - Credit: Trip Revenue — [Service Type] (4XXX)

3. **Payment on Invoice**
   - Debit: Cash & Bank (1000)
   - Credit: Accounts Receivable (1100)

4. **Supplier Payment (Cost)**
   - Debit: Cost of Sales — [Service Type] (5XXX)
   - Credit: Cash & Bank (1000) or Accounts Payable (2000)

5. **Revenue Recognition (at Trip Completion)**
   - Debit: Unearned Revenue (2200)
   - Credit: Trip Revenue — [Service Type] (4XXX)

### Profit Calculation (per Trip)

```
Total Revenue = Sum of all sell_prices (services) + service fees
Total Cost = Sum of all cost_prices (services)
Gross Profit = Total Revenue - Total Cost
Profit Margin = (Gross Profit / Total Revenue) × 100
```
