# TravelBox ERP — UI Wireframe Descriptions (V1)

## 1. Login Page

```
┌────────────────────────────────────────┐
│                                        │
│           ┌────────────────┐           │
│           │   TRAVELBOX    │           │
│           │     LOGO       │           │
│           └────────────────┘           │
│                                        │
│       Sign in to your account          │
│                                        │
│   Email:    ┌────────────────────┐    │
│             │ admin@travelbox.my │    │
│             └────────────────────┘    │
│                                        │
│   Password: ┌────────────────────┐    │
│             │ ••••••••••••••••   │    │
│             └────────────────────┘    │
│                                        │
│   [  Remember me  ]  Forgot password? │
│                                        │
│   ┌────────────────────────────┐      │
│   │        Sign In             │      │
│   └────────────────────────────┘      │
│                                        │
└────────────────────────────────────────┘
```

Clean, centered card layout with company logo, email/password inputs with validation, "remember me" checkbox, and forgot password link. Error messages appear inline below inputs. Submit button shows loading spinner during API call.

---

## 2. Dashboard

```
┌─────────────────────────────────────────────────────────────┐
│ [Sidebar]  Dashboard                              [Avatar] │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  ┌─────────┐ ┌─────────┐ ┌─────────┐ ┌─────────┐          │
│  │ Active  │ │Monthly  │ │Pending  │ │Upcoming │          │
│  │ Trips   │ │Revenue  │ │Payments │ │Trips    │          │
│  │   24    │ │$45,200  │ │ $12,400 │ │   8     │          │
│  │ +12%    │ │ +8%     │ │  5 due  │ │ This wk │          │
│  └─────────┘ └─────────┘ └─────────┘ └─────────┘          │
│                                                             │
│  ┌─────────────────────────┐ ┌─────────────────────┐       │
│  │   Revenue Chart (30d)   │ │  Upcoming Trips     │       │
│  │   [Bar/Line Chart]      │ │  ┌────────────────┐ │       │
│  │                         │ │  │ TB-2026-0042   │ │       │
│  │                         │ │  │ John Smith     │ │       │
│  │                         │ │  │ Jul 12 - Jul 20│ │       │
│  │                         │ │  ├────────────────┤ │       │
│  │                         │ │  │ TB-2026-0043  │ │       │
│  │                         │ │  │ Sarah Johnson │ │       │
│  │                         │ │  │ Jul 15 - Jul 22│ │       │
│  │                         │ │  └────────────────┘ │       │
│  └─────────────────────────┘ └─────────────────────┘       │
│                                                             │
│  ┌──────────────────────────┐                               │
│  │  Recent Activity         │                               │
│  │  ┌────────────────────┐  │                               │
│  │  │ 12:30 - Payment $500│  │                               │
│  │  │ 11:15 - Trip conf.  │  │                               │
│  │  │ 10:00 - New cust.   │  │                               │
│  │  └────────────────────┘  │                               │
│  └──────────────────────────┘                               │
└─────────────────────────────────────────────────────────────┘
```

**Components:**
- Stats cards with trend indicators (up/down arrows, percentages)
- Revenue chart with month-to-date comparison
- Upcoming trips list with client names and dates
- Recent activity feed (timeline of today's actions)
- My Tasks widget (small tasks assigned to current user)

---

## 3. Trip List Page

```
┌─────────────────────────────────────────────────────────────┐
│ [Sidebar]  Trips                                [+ New]    │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  [Search...]  [Status ▼] [Agent ▼] [Date ▼]  [Filter]     │
│                                                             │
│  ┌───────────────────────────────────────────────────────┐ │
│  │ Ref No    │ Client      │ Date     │ Status   │Agent  │ │
│  ├───────────────────────────────────────────────────────┤ │
│  │ TB-2026-0042  │ John Smith  │ Jul 12 │ Confirmed │ Sam │ │
│  │ TB-2026-0043  │ Sarah J.    │ Jul 15 │ In Progress│ Sam │ │
│  │ TB-2026-0044  │ Ahmed Khan  │ Jul 20 │ Quotation  │ Ali │ │
│  │ TB-2026-0045  │ Maria G.    │ Jul 25 │ Inquiry    │ -   │ │
│  └───────────────────────────────────────────────────────┘ │
│                                                             │
│  Showing 1-20 of 156  ◀ 1 2 3 4 5 ▶                        │
│                                                             │
└─────────────────────────────────────────────────────────────┘
```

**Features:**
- Search by reference, client name, or destination
- Filter by status (multi-select), assigned agent, date range
- Sortable columns (click to sort asc/desc)
- Inline status badge with color coding
- Row click navigates to Trip Workspace
- Bulk actions (change status, assign agent) via checkbox selection
- Export to CSV/Excel button
- Empty state: illustration + "Create your first trip" CTA

---

## 4. Trip Workspace (The Core Screen)

```
┌───────────────────────────────────────────────────────────────┐
│ ◄ Trips   TB-2026-0042 [CONFIRMED]    [Edit] [Clone] [...]  │
│ John Smith · Jul 12-20, 2026 · Paris, France                 │
├──────────┬────────────────────────────────────────────────────┤
│          │  General  Customer  Pax  Flights  Hotels  ...      │
│  General │ ┌──────────────────────────────────────────────┐  │
│  Custome │ │                                              │  │
│  Passeng │ │  Trip Name:    [Summer Europe 2026         ]│  │
│  Flights │ │  Description:  [Family vacation to Paris...]│  │
│  Hotels  │ │  Start Date:   [07/12/2026]                 │  │
│  Transf. │ │  End Date:     [07/20/2026]                 │  │
│  Visa    │ │  Destination:  [Paris] [France]             │  │
│  Insur.  │ │  Status:       [CONFIRMED ▼]                │  │
│  Activit.│ │  Assigned To:  [Sam Wilson ▼]               │  │
│─────────│ │  Source:       [Referral ▼]                  │  │
│  Paymnt  │ │                                              │  │
│  Accntng │ │  Internal Notes:                             │  │
│  Docmts  │ │  [                                       ]  │  │
│  Notes   │ │  [                                       ]  │  │
│  Tasks   │ │                                              │  │
│  Timelin │ └──────────────────────────────────────────────┘  │
│          │                                                    │
│          │ Profit Summary              [Save Changes]         │
│          │ ┌──────────┬────────┬───────┬──────┐              │
│          │ │ Revenue  │ Cost   │Profit │Margin│              │
│          │ │$12,500.00│$8,200  │$4,300 │34.4% │              │
│          │ └──────────┴────────┴───────┴──────┘              │
└──────────┴────────────────────────────────────────────────────┘
```

**This is the most important screen in the entire application.** It replaces multiple separate pages with a single workspace.

**Left sidebar:** Vertical tab navigation with icons. Active tab highlighted.
**Right content:** Changes based on selected tab. Each tab has its own form/table/grid.
**Sticky footer:** Real-time profit summary that updates as services/payments are added.

**Tab summaries:**
- **General:** Trip metadata, dates, status, assignment, internal notes
- **Customer:** Search/add primary + additional customers, role selector
- **Passengers:** Table with fields (name, DOB, passport, nationality, type), bulk add from customer
- **Flights:** Add/table with airline, flight no, airports, dates, PNR, cost/sell prices
- **Hotels:** Add/table with hotel name, city, check-in/out, room type, board, cost/sell prices
- **Transfers:** Add/table with type, pickup/dropoff, date/time, vehicle, cost/sell
- **Visa:** Add/table with country, type, status, applicant, cost/sell
- **Insurance:** Add/table with provider, policy, dates, coverage, premium/sell
- **Activities:** Add/table with name, location, date, duration, cost/sell
- **Payments:** Customer payments table + Supplier payments table split view
- **Accounting:** Journal entries auto-generated from this trip
- **Documents:** Generated documents list + [Generate Quotation] [Generate Invoice] buttons
- **Notes:** Comment thread with author avatars, timestamps, pin support
- **Tasks:** Kanban or list view filtered to this trip
- **Timeline:** Chronological activity feed for the trip

---

## 5. Customer Profile

```
┌─────────────────────────────────────────────────────────────┐
│ ◄ Customers  John Smith                                     │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  ┌──────────────────────────────────────┐                   │
│  │  [Avatar]  John Smith                │                   │
│  │  john.smith@email.com  +1 555-0100   │                   │
│  │  New York, USA   Nationality: US     │                   │
│  │  Passport: AB123456  Exp: 2028-05-20 │                   │
│  │  15 Trips · $45,200 Total Revenue    │                   │
│  │                                      │                   │
│  │  [Edit] [New Trip] [New Passenger]   │                   │
│  └──────────────────────────────────────┘                   │
│                                                             │
│  Trips    Passengers   Payments   Documents                  │
│  ┌──────────────────────────────────────────────────────┐  │
│  │ Ref No     │ Trip    │ Date      │ Status  │Amount  │  │
│  ├──────────────────────────────────────────────────────┤  │
│  │ TB-2026-0042 │ Paris  │ Jul 12   │ Confirmed│$5,200 │  │
│  │ TB-2026-0038 │ London │ Jun 10   │ Completed│$3,800 │  │
│  │ TB-2026-0025 │ Dubai  │ Mar 15   │ Completed│$4,500 │  │
│  └──────────────────────────────────────────────────────┘  │
│                                                             │
└─────────────────────────────────────────────────────────────┘
```

**Sections:**
- Profile header with avatar, contact info, passport details, trip/revenue stats
- Quick actions: Edit, Create New Trip, Add Passenger
- Tabbed content: customer's trips, passengers on file, payment history, generated documents
- Each trip row clickable to navigate to Trip Workspace

---

## 6. Accounting Dashboard

```
┌─────────────────────────────────────────────────────────────┐
│ [Sidebar]  Accounting                                       │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  ┌────────────┐ ┌────────────┐ ┌────────────┐              │
│  │ Total      │ │ Total      │ │ Net        │              │
│  │ Revenue    │ │ Expenses   │ │ Income     │              │
│  │ $285,000   │ │ $192,000   │ │ $93,000    │              │
│  │ YTD        │ │ YTD        │ │ YTD        │              │
│  └────────────┘ └────────────┘ └────────────┘              │
│                                                             │
│  Quick Links: [Chart of Accounts] [Journal] [Ledger]       │
│  [Trial Balance] [P&L] [Balance Sheet] [Cash Flow]         │
│                                                             │
│  Recent Journal Entries                                     │
│  ┌──────────────────────────────────────────────────────┐  │
│  │ Date    │ Account   │ Type   │ Amount  │ Trip Ref   │  │
│  ├──────────────────────────────────────────────────────┤  │
│  │ Jul 12  │ Cash (1000)│ Debit  │ $2,000  │ TB-2026-42 │  │
│  │ Jul 12  │ Unearn Rev│ Credit │ $2,000  │ TB-2026-42 │  │
│  └──────────────────────────────────────────────────────┘  │
│                                                             │
└─────────────────────────────────────────────────────────────┘
```

**Layout:** Financial summary cards at top, quick navigation buttons to each accounting report, recent journal entries table with direct links to source documents.

---

## 7. Report: Profit & Loss

```
┌─────────────────────────────────────────────────────────────┐
│ [Sidebar]  Reports  >  Profit & Loss                        │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  Period: [July 2026 ▼]  Compare: [June 2026 ▼]  [Run]     │
│                                                             │
│  ┌──────────────────────────────────────────────────────┐  │
│  │ Revenue                            Current   Previous │  │
│  │ ─────────────────────────────────────────────────── │  │
│  │ Flight Revenue                    $45,200   $38,500 │  │
│  │ Hotel Revenue                     $32,100   $28,900 │  │
│  │ Transfer Revenue                  $8,400    $7,200  │  │
│  │ Visa Revenue                      $5,600    $4,800  │  │
│  │ Insurance Revenue                 $3,200    $2,900  │  │
│  │ Activity Revenue                  $6,500    $5,100  │  │
│  │ Service Fees                      $2,800    $2,400  │  │
│  │────────────────────────────────────────────────────│  │
│  │ Total Revenue                     $103,800  $89,800 │  │
│  │────────────────────────────────────────────────────│  │
│  │ Cost of Sales                                   │  │
│  │ Flight Costs                       -$28,400  -$24,500│  │
│  │ Hotel Costs                        -$21,300  -$19,100│  │
│  │ ...                                             │  │
│  │────────────────────────────────────────────────────│  │
│  │ Gross Profit                       $42,100   $35,200 │  │
│  │ Gross Margin                       40.5%     39.2%   │  │
│  │────────────────────────────────────────────────────│  │
│  │ Operating Expenses                              │  │
│  │ Salaries                           -$12,000  -$12,000│  │
│  │ Rent                               -$3,000   -$3,000 │  │
│  │ Marketing                          -$2,500   -$2,000 │  │
│  │────────────────────────────────────────────────────│  │
│  │ Net Profit                         $24,600   $18,200 │  │
│  └──────────────────────────────────────────────────────┘  │
│                                                             │
│  [Download PDF] [Export CSV] [Print]                        │
└─────────────────────────────────────────────────────────────┘
```

Report shows current period vs previous period side by side, with visual indicators for increases/decreases. Export options for PDF and CSV.

---

## 8. Document Generator

```
┌─────────────────────────────────────────────────────────────┐
│ [Sidebar]  Trip > Documents                                 │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  Generate Document: [Quotation ▼]  [Generate]              │
│                                                             │
│  Preview:                                                   │
│  ┌──────────────────────────────────────────────────────┐  │
│  │                                                      │  │
│  │              TRAVELBOX TRAVEL & TOURISM              │  │
│  │              123 Travel St, City, Country            │  │
│  │              Tel: +1 555-0000  |  VAT: XXX           │  │
│  │                                                      │  │
│  │              QUOTATION                               │  │
│  │              Ref: TB-Q-2026-0042                     │  │
│  │              Date: July 12, 2026                     │  │
│  │                                                      │  │
│  │  To: John Smith                                       │  │
│  │  Email: john@email.com                               │  │
│  │                                                      │  │
│  │  ┌──────────┬──────────┬──────┬────────┬─────────┐ │  │
│  │  │ Service  │ Details  │ Qty  │ Price  │ Total   │ │  │
│  │  ├──────────┼──────────┼──────┼────────┼─────────┤ │  │
│  │  │ Flight   │ NY→Paris  │   4 │ $1,200 │ $4,800  │ │  │
│  │  │ Hotel    │ Hilton 5n│   2 │ $1,500 │ $3,000  │ │  │
│  │  │ Transfer │ CDG→Htl  │   2 │   $80  │   $160  │ │  │
│  │  └──────────┴──────────┴──────┴────────┴─────────┘ │  │
│  │                                          │         │  │
│  │  Subtotal: $7,960  Tax: $0  Total: $7,960 │         │  │
│  │                                                      │  │
│  │  Terms: 50% deposit to confirm, balance due 14       │  │
│  │  days before departure.                               │  │
│  │                                                      │  │
│  └──────────────────────────────────────────────────────┘  │
│                                                             │
│  [Send to Customer] [Download PDF] [Edit Template]         │
│                                                             │
└─────────────────────────────────────────────────────────────┘
```

WYSIWYG-like preview showing the document with actual trip data. Template uses Handlebars with company logo, address, customer info, itemized services, totals, and payment terms. Different document types (quotation, invoice, receipt, voucher, itinerary) have distinct layouts.

---

## 9. Mobile Considerations

- Sidebar collapses to hamburger menu
- Trip Workspace tabs become a bottom tab bar on mobile
- Tables become card lists on small screens
- Date pickers use native mobile date inputs
- Profit summary collapses to a single row
- Floating action button for quick actions (add note, record payment)
