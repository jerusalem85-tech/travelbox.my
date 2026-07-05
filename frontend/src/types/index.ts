export interface User {
  id: string;
  email: string;
  firstName: string;
  lastName: string;
  role: string;
  phone?: string;
  avatar?: string;
  isActive?: boolean;
  createdAt?: string;
  updatedAt?: string;
}

export interface Trip {
  id: string;
  tripNumber: string;
  name?: string;
  status: string;
  customerId?: string;
  customer?: Customer;
  primaryContact?: string;
  assignedToId?: string;
  createdById?: string;
  source?: string;
  priority?: string;
  color?: string;
  tags?: string[];
  agency?: string;
  startDate?: string;
  endDate?: string;
  duration?: number;
  destination?: string;
  destinationCities?: string[];
  destinationCountries?: string[];
  currency?: string;
  totalCost: number;
  totalSelling: number;
  totalProfit: number;
  totalCommission?: number;
  totalPaid?: number;
  totalSupplierPaid?: number;
  totalSupplierBalance?: number;
  margin?: number;
  paymentStatus?: string;
  notes?: string;
  internalNotes?: string;
  services?: Service[];
  tasks?: Task[];
  documents?: any[];
  payments?: Payment[];
  passengers?: Passenger[];
  createdAt?: string;
  updatedAt?: string;
}

export interface Passenger {
  id: string;
  tripId: string;
  firstName: string;
  lastName: string;
  dateOfBirth?: string;
  gender?: string;
  nationality?: string;
  passportNumber?: string;
  passportExpiry?: string;
  isLeadPassenger?: boolean;
  createdAt?: string;
  updatedAt?: string;
}

export interface Service {
  id: string;
  tripId: string;
  type: string;
  sortOrder?: number;
  previousId?: string;
  nextId?: string;
  locationId?: string;
  previousCityId?: string;
  nextCityId?: string;
  supplierId?: string;
  supplierReference?: string;
  description?: string;
  startDate?: string;
  endDate?: string;
  startTime?: string;
  endTime?: string;
  departureAt?: string;
  arrivalAt?: string;
  checkinAt?: string;
  checkoutAt?: string;
  costPrice: number;
  sellingPrice: number;
  profit: number;
  commissionAmount?: number;
  commissionRate?: number;
  paidAmount?: number;
  remainingAmount?: number;
  currency?: string;
  exchangeRate?: number;
  taxAmount?: number;
  status: string;
  isOptional?: boolean;
  confirmationNumber?: string;
  bookingReference?: string;
  voucherNumber?: string;
  notes?: string;
  flight?: any;
  hotel?: any;
  transfer?: any;
  tour?: any;
  cruise?: any;
  visa?: any;
  insurance?: any;
  carRental?: any;
  train?: any;
}

export interface Customer {
  id: string;
  type: string;
  firstName?: string;
  lastName?: string;
  companyName?: string;
  email?: string;
  phone?: string;
  whatsapp?: string;
  passportNumber?: string;
  passportExpiry?: string;
  nationality?: string;
  dateOfBirth?: string;
  gender?: string;
  isVip?: boolean;
  totalTrips?: number;
  totalSpent?: number;
  outstandingBalance?: number;
}

export interface Supplier {
  id: string;
  category: string;
  companyName: string;
  contactPerson?: string;
  email?: string;
  phone?: string;
  commissionRate?: number;
  paymentTerms?: string;
  isActive?: boolean;
}

export interface Payment {
  id: string;
  paymentNumber: string;
  tripId?: string;
  customerId?: string;
  direction: string;
  method: string;
  amount: number;
  currency?: string;
  status: string;
  dueDate?: string;
  paidDate?: string;
  reference?: string;
  description?: string;
  isInstallment?: boolean;
}

export interface Task {
  id: string;
  tripId?: string;
  assignedTo?: string;
  title: string;
  description?: string;
  status: string;
  priority: string;
  dueDate?: string;
  completedAt?: string;
  category?: string;
}

export interface DashboardStats {
  tripCount: number;
  activeTrips: number;
  customerCount: number;
  supplierCount: number;
  totalRevenue: number;
  totalCost?: number;
  profit: number;
  pendingTasks?: number;
  unpaidInvoices?: number;
}

export interface UpcomingTrip {
  id: string;
  tripNumber: string;
  name?: string;
  status: string;
  startDate?: string;
  destination?: string;
  customer?: { firstName?: string; lastName?: string };
  _count?: { passengers?: number; services?: number };
}

export interface RecentActivity {
  trips: Array<{ id: string; tripNumber: string; status: string; updatedAt: string; customer?: { firstName?: string; lastName?: string } }>;
  tasks: Array<{ id: string; title: string; status: string; updatedAt: string; trip?: { tripNumber?: string } }>;
  payments: Array<{ id: string; amount: number; direction: string; createdAt: string; trip?: { tripNumber?: string } }>;
}

export interface PaginatedResponse<T> {
  data: T[];
  total: number;
  page: number;
  limit: number;
  totalPages: number;
}

export interface LoginRequest {
  email: string;
  password: string;
}

export interface RegisterRequest {
  email: string;
  password: string;
  firstName: string;
  lastName: string;
  role?: string;
}

export interface AuthResponse {
  user: User;
  access_token: string;
  refresh_token: string;
  token_type: string;
  expires_in: number;
}
