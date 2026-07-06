import { PrismaClient } from '@prisma/client';
import * as bcrypt from 'bcryptjs';

const prisma = new PrismaClient();

async function main() {
  console.log('🌱 Seeding TravelBox ERP database...');

  // Clean existing data
  const tablenames = await prisma.$queryRaw<Array<{ tablename: string }>>`SELECT tablename FROM pg_tables WHERE schemaname='public'`;
  for (const { tablename } of tablenames) {
    if (tablename !== '_prisma_migrations') {
      await prisma.$executeRawUnsafe(`TRUNCATE TABLE "public"."${tablename}" CASCADE;`);
    }
  }

  // === USERS ===
  const password = await bcrypt.hash('admin123', 10);
  const users = await Promise.all([
    prisma.user.create({ data: { email: 'admin@travelbox.com', password, firstName: 'Admin', lastName: 'User', role: 'OWNER', isActive: true } }),
    prisma.user.create({ data: { email: 'manager@travelbox.com', password, firstName: 'Sarah', lastName: 'Johnson', role: 'MANAGER', isActive: true } }),
    prisma.user.create({ data: { email: 'sales@travelbox.com', password, firstName: 'Ahmed', lastName: 'Hassan', role: 'SALES', isActive: true } }),
    prisma.user.create({ data: { email: 'ops@travelbox.com', password, firstName: 'Maria', lastName: 'Garcia', role: 'OPERATIONS', isActive: true } }),
    prisma.user.create({ data: { email: 'accounting@travelbox.com', password, firstName: 'John', lastName: 'Smith', role: 'ACCOUNTING', isActive: true } }),
    prisma.user.create({ data: { email: 'visa@travelbox.com', password, firstName: 'Fatima', lastName: 'Ali', role: 'VISA_OFFICER', isActive: true } }),
  ]);
  console.log(`  ✅ ${users.length} users created`);

  // === COUNTRIES ===
  const countries = await Promise.all([
    prisma.country.create({ data: { name: 'United Arab Emirates', code: 'AE', phoneCode: '+971', currency: 'AED' } }),
    prisma.country.create({ data: { name: 'United States', code: 'US', phoneCode: '+1', currency: 'USD' } }),
    prisma.country.create({ data: { name: 'United Kingdom', code: 'GB', phoneCode: '+44', currency: 'GBP' } }),
    prisma.country.create({ data: { name: 'Egypt', code: 'EG', phoneCode: '+20', currency: 'EGP' } }),
    prisma.country.create({ data: { name: 'Saudi Arabia', code: 'SA', phoneCode: '+966', currency: 'SAR' } }),
    prisma.country.create({ data: { name: 'Turkey', code: 'TR', phoneCode: '+90', currency: 'TRY' } }),
    prisma.country.create({ data: { name: 'Thailand', code: 'TH', phoneCode: '+66', currency: 'THB' } }),
    prisma.country.create({ data: { name: 'France', code: 'FR', phoneCode: '+33', currency: 'EUR' } }),
    prisma.country.create({ data: { name: 'Italy', code: 'IT', phoneCode: '+39', currency: 'EUR' } }),
    prisma.country.create({ data: { name: 'Maldives', code: 'MV', phoneCode: '+960', currency: 'MVR' } }),
  ]);
  console.log(`  ✅ ${countries.length} countries created`);

  // === CITIES ===
  const dubai = await prisma.city.create({ data: { name: 'Dubai', code: 'DXB', countryId: countries[0].id } });
  const abuDhabi = await prisma.city.create({ data: { name: 'Abu Dhabi', code: 'AUH', countryId: countries[0].id } });
  const newYork = await prisma.city.create({ data: { name: 'New York', code: 'NYC', countryId: countries[1].id } });
  const london = await prisma.city.create({ data: { name: 'London', code: 'LON', countryId: countries[2].id } });
  const cairo = await prisma.city.create({ data: { name: 'Cairo', code: 'CAI', countryId: countries[3].id } });
  const riyadh = await prisma.city.create({ data: { name: 'Riyadh', code: 'RUH', countryId: countries[4].id } });
  const istanbul = await prisma.city.create({ data: { name: 'Istanbul', code: 'IST', countryId: countries[5].id } });
  const bangkok = await prisma.city.create({ data: { name: 'Bangkok', code: 'BKK', countryId: countries[6].id } });
  const paris = await prisma.city.create({ data: { name: 'Paris', code: 'PAR', countryId: countries[7].id } });
  const male = await prisma.city.create({ data: { name: 'Malé', code: 'MLE', countryId: countries[9].id } });

  // === AIRLINES ===
  const airlines = await Promise.all([
    prisma.airline.create({ data: { name: 'Emirates', code: 'EK', country: 'UAE', alliance: 'none' } }),
    prisma.airline.create({ data: { name: 'Etihad Airways', code: 'EY', country: 'UAE', alliance: 'none' } }),
    prisma.airline.create({ data: { name: 'Qatar Airways', code: 'QR', country: 'Qatar', alliance: 'oneworld' } }),
    prisma.airline.create({ data: { name: 'Turkish Airlines', code: 'TK', country: 'Turkey', alliance: 'Star Alliance' } }),
    prisma.airline.create({ data: { name: 'British Airways', code: 'BA', country: 'UK', alliance: 'oneworld' } }),
    prisma.airline.create({ data: { name: 'EgyptAir', code: 'MS', country: 'Egypt', alliance: 'Star Alliance' } }),
    prisma.airline.create({ data: { name: 'Flydubai', code: 'FZ', country: 'UAE' } }),
    prisma.airline.create({ data: { name: 'Air Arabia', code: 'G9', country: 'UAE' } }),
  ]);

  // === CURRENCIES ===
  const currencies = await Promise.all([
    prisma.currency.create({ data: { code: 'USD', name: 'US Dollar', symbol: '$', rate: 1 } }),
    prisma.currency.create({ data: { code: 'AED', name: 'UAE Dirham', symbol: 'د.إ', rate: 3.67 } }),
    prisma.currency.create({ data: { code: 'EUR', name: 'Euro', symbol: '€', rate: 0.92 } }),
    prisma.currency.create({ data: { code: 'GBP', name: 'British Pound', symbol: '£', rate: 0.79 } }),
    prisma.currency.create({ data: { code: 'EGP', name: 'Egyptian Pound', symbol: 'E£', rate: 30.9 } }),
    prisma.currency.create({ data: { code: 'SAR', name: 'Saudi Riyal', symbol: '﷼', rate: 3.75 } }),
    prisma.currency.create({ data: { code: 'TRY', name: 'Turkish Lira', symbol: '₺', rate: 30.5 } }),
  ]);

  // === CUSTOMERS ===
  const customers = await Promise.all([
    prisma.customer.create({ data: { type: 'INDIVIDUAL', firstName: 'Mohammed', lastName: 'Al-Rashid', email: 'm.alrashid@email.com', phone: '+971501234567', whatsapp: '+971501234567', passportNumber: 'A12345678', nationality: 'AE', isVip: true } }),
    prisma.customer.create({ data: { type: 'INDIVIDUAL', firstName: 'Emma', lastName: 'Thompson', email: 'emma.t@email.com', phone: '+447890123456', passportNumber: 'B98765432', nationality: 'GB' } }),
    prisma.customer.create({ data: { type: 'COMPANY', companyName: 'GulfTech Solutions', email: 'travel@gulftech.com', phone: '+97142223344', isVip: true } }),
    prisma.customer.create({ data: { type: 'INDIVIDUAL', firstName: 'Olga', lastName: 'Petrova', email: 'olga.p@email.com', phone: '+74951234567', passportNumber: 'C45678901', nationality: 'RU' } }),
    prisma.customer.create({ data: { type: 'INDIVIDUAL', firstName: 'James', lastName: 'Wilson', email: 'james.w@email.com', phone: '+12125551234', passportNumber: 'D78901234', nationality: 'US' } }),
    prisma.customer.create({ data: { type: 'COMPANY', companyName: 'Al-Futtaim Travel', email: 'corporate@alfuttaim.ae', phone: '+97143335566' } }),
  ]);

  // === SUPPLIERS ===
  const suppliers = await Promise.all([
    prisma.supplier.create({ data: { category: 'AIRLINE', companyName: 'Emirates Airline', contactPerson: 'Ahmed Al-Maktoum', email: 'corporate@emirates.com', phone: '+97142221111', commissionRate: 3, paymentTerms: 'NET30' } }),
    prisma.supplier.create({ data: { category: 'HOTEL', companyName: 'Jumeirah Group', contactPerson: 'Hassan Al-Abbas', email: 'reservations@jumeirah.com', phone: '+97143666666', commissionRate: 10, paymentTerms: 'NET15' } }),
    prisma.supplier.create({ data: { category: 'HOTEL', companyName: 'Marriott International', contactPerson: 'Sarah Connor', email: 'sales@marriott.com', phone: '+97144447777', commissionRate: 8, paymentTerms: 'NET30' } }),
    prisma.supplier.create({ data: { category: 'TRANSFER', companyName: 'Careem', contactPerson: 'Omar Khan', email: 'business@careem.com', phone: '+971800227336', commissionRate: 5, paymentTerms: 'NET7' } }),
    prisma.supplier.create({ data: { category: 'TOUR', companyName: 'Rayna Tours', contactPerson: 'Vijay Singh', email: 'bookings@raynatours.com', phone: '+97144223344', commissionRate: 15, paymentTerms: 'NET15' } }),
    prisma.supplier.create({ data: { category: 'VISA', companyName: 'VFS Global', contactPerson: 'Raj Patel', email: 'corporate@vfsglobal.com', phone: '+971600522225', commissionRate: 5, paymentTerms: 'PREPAID' } }),
    prisma.supplier.create({ data: { category: 'INSURANCE', companyName: 'AXA Insurance', contactPerson: 'Lisa Chen', email: 'travel@axa.com', phone: '+97143338888', commissionRate: 20, paymentTerms: 'NET30' } }),
    prisma.supplier.create({ data: { category: 'CAR_RENTAL', companyName: 'Hertz UAE', contactPerson: 'Michael Brown', email: 'reservations@hertz.ae', phone: '+971800462212', commissionRate: 7, paymentTerms: 'NET15' } }),
  ]);

  // === SETTINGS ===
  await prisma.setting.createMany({
    data: [
      { key: 'company_name', value: 'TravelBox ERP', category: 'general' },
      { key: 'company_email', value: 'info@travelbox.com', category: 'general' },
      { key: 'company_phone', value: '+97140001111', category: 'general' },
      { key: 'default_currency', value: 'USD', category: 'finance' },
      { key: 'default_markup_percentage', value: '15', category: 'pricing' },
      { key: 'passport_validity_months', value: '6', category: 'travel' },
      { key: 'minimum_connection_minutes', value: '60', category: 'travel' },
      { key: 'tax_rate', value: '5', category: 'finance' },
      { key: 'invoice_terms', value: 'Payment due within 30 days', category: 'finance' },
    ],
  });

  // === TRIPS ===
  const now = new Date();
  const trips = await Promise.all([
    prisma.trip.create({
      data: {
        tripNumber: 'TB-24-00001', name: 'Dubai Luxury Experience', status: 'CONFIRMED', customerId: customers[0].id,
        assignedToId: users[2].id, createdById: users[2].id, source: 'DIRECT', priority: 'HIGH',
        startDate: new Date(now.getTime() + 14 * 86400000), endDate: new Date(now.getTime() + 21 * 86400000),
        duration: 7, destination: 'Dubai, UAE', currency: 'USD', totalCost: 8500, totalSelling: 12500, totalProfit: 4000,
        totalCommission: 0, margin: 32, notes: 'VIP client - Premium package', tags: '["VIP","LUXURY","FAMILY"]',
      },
    }),
    prisma.trip.create({
      data: {
        tripNumber: 'TB-24-00002', name: 'London Business Trip', status: 'BOOKED', customerId: customers[1].id,
        assignedToId: users[2].id, createdById: users[3].id, source: 'REFERRAL', priority: 'MEDIUM',
        startDate: new Date(now.getTime() + 30 * 86400000), endDate: new Date(now.getTime() + 35 * 86400000),
        duration: 5, destination: 'London, UK', currency: 'GBP', totalCost: 3200, totalSelling: 4800, totalProfit: 1600,
        totalCommission: 0, margin: 33.33, notes: 'Corporate client - Conference attendance',
      },
    }),
    prisma.trip.create({
      data: {
        tripNumber: 'TB-24-00003', name: 'Maldives Honeymoon', status: 'LEAD', customerId: customers[3].id,
        assignedToId: users[2].id, createdById: users[2].id, source: 'DIRECT', priority: 'HIGH',
        startDate: new Date(now.getTime() + 45 * 86400000), endDate: new Date(now.getTime() + 52 * 86400000),
        duration: 7, destination: 'Maldives', currency: 'USD', totalCost: 5500, totalSelling: 8200, totalProfit: 2700,
        margin: 32.93, notes: 'Honeymoon package - All inclusive resort',
      },
    }),
    prisma.trip.create({
      data: {
        tripNumber: 'TB-24-00004', name: 'Turkey Group Tour', status: 'CONFIRMED', customerId: customers[2].id,
        assignedToId: users[3].id, createdById: users[3].id, source: 'AGENT', priority: 'MEDIUM',
        startDate: new Date(now.getTime() + 60 * 86400000), endDate: new Date(now.getTime() + 67 * 86400000),
        duration: 7, destination: 'Istanbul, Turkey', currency: 'USD', totalCost: 18000, totalSelling: 28000, totalProfit: 10000,
        totalCommission: 0, margin: 35.71, notes: 'Corporate group of 12 pax',
      },
    }),
    prisma.trip.create({
      data: {
        tripNumber: 'TB-24-00005', name: 'Egypt Heritage Tour', status: 'COMPLETED', customerId: customers[4].id,
        assignedToId: users[2].id, createdById: users[2].id, source: 'DIRECT', priority: 'LOW',
        startDate: new Date(now.getTime() - 45 * 86400000), endDate: new Date(now.getTime() - 38 * 86400000),
        duration: 7, destination: 'Cairo, Egypt', currency: 'USD', totalCost: 2200, totalSelling: 3500, totalProfit: 1300,
        margin: 37.14,
      },
    }),
  ]);

  // === PASSENGERS ===
  const passengers = await Promise.all([
    prisma.passenger.create({ data: { tripId: trips[0].id, firstName: 'Mohammed', lastName: 'Al-Rashid', passportNumber: 'A12345678', passportExpiry: new Date('2027-06-15'), nationality: 'AE', isLeadPassenger: true } }),
    prisma.passenger.create({ data: { tripId: trips[0].id, firstName: 'Fatima', lastName: 'Al-Rashid', passportNumber: 'A87654321', passportExpiry: new Date('2026-11-20'), nationality: 'AE', mealPreference: 'HALAL' } }),
    prisma.passenger.create({ data: { tripId: trips[0].id, firstName: 'Omar', lastName: 'Al-Rashid', passportNumber: 'A24681357', passportExpiry: new Date('2028-03-10'), nationality: 'AE' } }),
    prisma.passenger.create({ data: { tripId: trips[1].id, firstName: 'Emma', lastName: 'Thompson', passportNumber: 'B98765432', passportExpiry: new Date('2025-08-22'), nationality: 'GB', isLeadPassenger: true } }),
    prisma.passenger.create({ data: { tripId: trips[2].id, firstName: 'Olga', lastName: 'Petrova', passportNumber: 'C45678901', passportExpiry: new Date('2026-05-30'), nationality: 'RU', isLeadPassenger: true } }),
    prisma.passenger.create({ data: { tripId: trips[2].id, firstName: 'Dmitry', lastName: 'Petrov', passportNumber: 'C98765432', passportExpiry: new Date('2027-01-15'), nationality: 'RU' } }),
    prisma.passenger.create({ data: { tripId: trips[3].id, firstName: 'Khalid', lastName: 'Al-Mansour', passportNumber: 'D11223344', passportExpiry: new Date('2026-09-01'), nationality: 'AE', isLeadPassenger: true } }),
    prisma.passenger.create({ data: { tripId: trips[3].id, firstName: 'Saad', lastName: 'Al-Mansour', passportNumber: 'D55667788', passportExpiry: new Date('2025-12-10'), nationality: 'AE' } }),
    prisma.passenger.create({ data: { tripId: trips[4].id, firstName: 'James', lastName: 'Wilson', passportNumber: 'D78901234', passportExpiry: new Date('2026-07-18'), nationality: 'US', isLeadPassenger: true } }),
  ]);

  // === SERVICES + FLIGHTS ===
  const servicesData = [
    { tripId: trips[0].id, type: 'FLIGHT' as const, supplierId: suppliers[0].id, description: 'EK 501 DXB-LHR', status: 'CONFIRMED' as const, costPrice: 1800, sellingPrice: 2800, commissionRate: 3, profit: 1000, sortOrder: 1 },
    { tripId: trips[0].id, type: 'HOTEL' as const, supplierId: suppliers[1].id, description: 'Burj Al Arab - 7 nights', status: 'CONFIRMED' as const, costPrice: 4200, sellingPrice: 6200, commissionRate: 10, profit: 2000, sortOrder: 2 },
    { tripId: trips[0].id, type: 'TRANSFER' as const, supplierId: suppliers[3].id, description: 'Airport-Hotel-Airport', status: 'CONFIRMED' as const, costPrice: 300, sellingPrice: 500, commissionRate: 5, profit: 200, sortOrder: 3 },
    { tripId: trips[1].id, type: 'FLIGHT' as const, supplierId: suppliers[0].id, description: 'EK 1 DXB-LHR', status: 'CONFIRMED' as const, costPrice: 1200, sellingPrice: 2000, commissionRate: 3, profit: 800, sortOrder: 1 },
    { tripId: trips[1].id, type: 'HOTEL' as const, supplierId: suppliers[2].id, description: 'Marriott Canary Wharf', status: 'CONFIRMED' as const, costPrice: 1500, sellingPrice: 2200, commissionRate: 8, profit: 700, sortOrder: 2 },
    { tripId: trips[2].id, type: 'FLIGHT' as const, supplierId: suppliers[0].id, description: 'EK 504 DXB-MLE', status: 'PENDING' as const, costPrice: 1400, sellingPrice: 2200, commissionRate: 3, profit: 800, sortOrder: 1 },
    { tripId: trips[2].id, type: 'HOTEL' as const, supplierId: suppliers[2].id, description: 'W Maldives - 7 nights', status: 'PENDING' as const, costPrice: 3500, sellingPrice: 5200, commissionRate: 8, profit: 1700, sortOrder: 2 },
    { tripId: trips[2].id, type: 'TRANSFER' as const, supplierId: suppliers[3].id, description: 'Speedboat transfer', status: 'PENDING' as const, costPrice: 400, sellingPrice: 600, commissionRate: 5, profit: 200, sortOrder: 3 },
    { tripId: trips[3].id, type: 'FLIGHT' as const, supplierId: suppliers[0].id, description: 'EK 121 DXB-IST (x12 pax)', status: 'REQUESTED' as const, costPrice: 7200, sellingPrice: 12000, commissionRate: 3, profit: 4800, sortOrder: 1 },
    { tripId: trips[3].id, type: 'HOTEL' as const, supplierId: suppliers[1].id, description: 'JW Marriott Istanbul (6 rooms)', status: 'REQUESTED' as const, costPrice: 7200, sellingPrice: 10800, commissionRate: 10, profit: 3600, sortOrder: 2 },
    { tripId: trips[3].id, type: 'TOUR' as const, supplierId: suppliers[4].id, description: 'Istanbul City Tour (x12 pax)', status: 'REQUESTED' as const, costPrice: 1200, sellingPrice: 2400, commissionRate: 15, profit: 1200, sortOrder: 3 },
    { tripId: trips[3].id, type: 'TRANSFER' as const, supplierId: suppliers[3].id, description: 'Group transfers', status: 'REQUESTED' as const, costPrice: 600, sellingPrice: 1200, commissionRate: 5, profit: 600, sortOrder: 4 },
    { tripId: trips[4].id, type: 'FLIGHT' as const, supplierId: suppliers[5].id, description: 'MS 901 JFK-CAI', status: 'COMPLETED' as const, costPrice: 800, sellingPrice: 1400, commissionRate: 3, profit: 600, sortOrder: 1 },
    { tripId: trips[4].id, type: 'HOTEL' as const, supplierId: suppliers[2].id, description: 'Mena House Cairo', status: 'COMPLETED' as const, costPrice: 900, sellingPrice: 1400, commissionRate: 8, profit: 500, sortOrder: 2 },
    { tripId: trips[4].id, type: 'TOUR' as const, supplierId: suppliers[4].id, description: 'Pyramids & Museum Tour', status: 'COMPLETED' as const, costPrice: 300, sellingPrice: 500, commissionRate: 15, profit: 200, sortOrder: 3 },
  ];

  const services = await Promise.all(
    servicesData.map(s => prisma.service.create({
      data: s,
      include: { flight: true, hotel: true, transfer: true, tour: true },
    }))
  );

  // === FLIGHT DETAILS ===
  await prisma.flight.createMany({
    data: [
      { serviceId: services[0].id, supplierId: suppliers[0].id, flightType: 'ROUND_TRIP', airline: 'Emirates', flightNumber: 'EK 501', cabinClass: 'BUSINESS', departureAirport: 'DXB', arrivalAirport: 'LHR', departureTime: new Date(now.getTime() + 14 * 86400000 + 9 * 3600000), arrivalTime: new Date(now.getTime() + 14 * 86400000 + 14 * 3600000), pnr: 'ABC123', ticketNumber: '176-1234567890', totalAmount: 2800, baggageAllowance: '40kg', seats: '3A,3B,3C' },
      { serviceId: services[3].id, supplierId: suppliers[0].id, flightType: 'ROUND_TRIP', airline: 'Emirates', flightNumber: 'EK 1', cabinClass: 'ECONOMY', departureAirport: 'DXB', arrivalAirport: 'LHR', departureTime: new Date(now.getTime() + 30 * 86400000 + 14 * 3600000), arrivalTime: new Date(now.getTime() + 30 * 86400000 + 19 * 3600000), pnr: 'DEF456', ticketNumber: '176-9876543210', totalAmount: 2000, baggageAllowance: '30kg', seats: '15A' },
      { serviceId: services[5].id, supplierId: suppliers[0].id, flightType: 'ROUND_TRIP', airline: 'Emirates', flightNumber: 'EK 504', cabinClass: 'ECONOMY', departureAirport: 'DXB', arrivalAirport: 'MLE', departureTime: new Date(now.getTime() + 45 * 86400000 + 10 * 3600000), arrivalTime: new Date(now.getTime() + 45 * 86400000 + 14 * 3600000), pnr: 'GHI789', totalAmount: 2200, baggageAllowance: '30kg' },
    ],
  });

  // === HOTEL DETAILS ===
  await prisma.hotelBooking.createMany({
    data: [
      { serviceId: services[1].id, supplierId: suppliers[1].id, hotelName: 'Burj Al Arab', roomType: 'Suite', boardType: 'BREAKFAST', checkIn: new Date(now.getTime() + 14 * 86400000), checkOut: new Date(now.getTime() + 21 * 86400000), rooms: 2, adults: 3, confirmationNumber: 'HTL-123456' },
      { serviceId: services[4].id, supplierId: suppliers[2].id, hotelName: 'Marriott Canary Wharf', roomType: 'Deluxe', boardType: 'BREAKFAST', checkIn: new Date(now.getTime() + 30 * 86400000), checkOut: new Date(now.getTime() + 35 * 86400000), rooms: 1, adults: 1, confirmationNumber: 'MAR-789012' },
      { serviceId: services[6].id, supplierId: suppliers[2].id, hotelName: 'W Maldives', roomType: 'Overwater Villa', boardType: 'ALL_INCLUSIVE', checkIn: new Date(now.getTime() + 45 * 86400000), checkOut: new Date(now.getTime() + 52 * 86400000), rooms: 1, adults: 2, confirmationNumber: 'W-345678' },
    ],
  });

  // === TRANSFER DETAILS ===
  await prisma.transfer.createMany({
    data: [
      { serviceId: services[2].id, supplierId: suppliers[3].id, transferType: 'PRIVATE', pickupLocation: 'DXB Airport', dropoffLocation: 'Burj Al Arab', vehicleType: 'Mercedes S-Class', passengers: 3 },
      { serviceId: services[7].id, supplierId: suppliers[3].id, transferType: 'SHARED', pickupLocation: 'MLE Airport', dropoffLocation: 'W Maldives Jetty', vehicleType: 'Speedboat', passengers: 2 },
    ],
  });

  // === TOUR DETAILS ===
  await prisma.tour.createMany({
    data: [
      { serviceId: services[10].id, supplierId: suppliers[4].id, tourName: 'Istanbul City Tour', meetingPoint: 'Hotel Lobby', duration: '8 hours', guideName: 'Mehmet', language: 'English & Arabic', includes: 'Lunch, Guide, Entry fees' },
      { serviceId: services[14].id, supplierId: suppliers[4].id, tourName: 'Pyramids & Egyptian Museum', meetingPoint: 'Hotel Lobby', duration: '6 hours', guideName: 'Mahmoud', language: 'English', includes: 'Lunch, Guide, Entry fees, Transport' },
    ],
  });

  // === TASKS ===
  await prisma.task.createMany({
    data: [
      { tripId: trips[0].id, assignedTo: users[3].id, title: 'Confirm hotel early check-in', status: 'PENDING', priority: 'HIGH', dueDate: new Date(now.getTime() + 10 * 86400000), category: 'SUPPLIER_FOLLOWUP' },
      { tripId: trips[0].id, assignedTo: users[5].id, title: 'Verify all passports valid', status: 'PENDING', priority: 'URGENT', dueDate: new Date(now.getTime() + 7 * 86400000), category: 'VISA_FOLLOWUP' },
      { tripId: trips[1].id, assignedTo: users[3].id, title: 'Send visa invitation letter', status: 'IN_PROGRESS', priority: 'HIGH', dueDate: new Date(now.getTime() + 25 * 86400000), category: 'VISA_FOLLOWUP' },
      { tripId: trips[2].id, assignedTo: users[2].id, title: 'Collect honeymoon preferences', status: 'PENDING', priority: 'MEDIUM', dueDate: new Date(now.getTime() + 40 * 86400000), category: 'CUSTOMER_FOLLOWUP' },
      { tripId: trips[3].id, assignedTo: users[3].id, title: 'Confirm group booking with hotel', status: 'IN_PROGRESS', priority: 'HIGH', dueDate: new Date(now.getTime() + 55 * 86400000), category: 'SUPPLIER_FOLLOWUP' },
      { title: 'Update supplier contracts', assignedTo: users[1].id, status: 'PENDING', priority: 'MEDIUM', category: 'GENERAL' },
    ],
  });

  // === PAYMENTS ===
  await prisma.payment.createMany({
    data: [
      { paymentNumber: 'PAY-2024-00001', tripId: trips[0].id, customerId: customers[0].id, direction: 'INCOMING', method: 'BANK_TRANSFER', amount: 12500, status: 'PAID', paidDate: new Date(now.getTime() - 7 * 86400000), description: 'Full payment - Dubai Luxury Experience' },
      { paymentNumber: 'PAY-2024-00002', tripId: trips[1].id, customerId: customers[1].id, direction: 'INCOMING', method: 'CREDIT_CARD', amount: 2000, status: 'PARTIAL', description: 'Deposit - London Business Trip', isInstallment: true, installmentNo: 1 },
      { paymentNumber: 'PAY-2024-00003', tripId: trips[2].id, customerId: customers[3].id, direction: 'INCOMING', method: 'BANK_TRANSFER', amount: 3000, status: 'PARTIAL', description: 'Deposit - Maldives Honeymoon' },
      { paymentNumber: 'PAY-2024-00004', tripId: trips[4].id, customerId: customers[4].id, direction: 'INCOMING', method: 'CREDIT_CARD', amount: 3500, status: 'PAID', paidDate: new Date(now.getTime() - 50 * 86400000), description: 'Full payment - Egypt Heritage Tour' },
      { paymentNumber: 'PAY-2024-00005', customerId: customers[0].id, direction: 'INCOMING', method: 'CASH', amount: 5000, status: 'PENDING', description: 'Retainer - Future bookings' },
    ],
  });

  // === INVOICES ===
  await prisma.invoice.createMany({
    data: [
      { invoiceNumber: 'INV-2024-00001', tripId: trips[0].id, customerId: customers[0].id, subtotal: 11904.76, taxAmount: 595.24, discount: 0, total: 12500, paidAmount: 12500, status: 'PAID', dueDate: new Date(now.getTime() - 14 * 86400000), notes: 'Paid in full' },
      { invoiceNumber: 'INV-2024-00002', tripId: trips[1].id, customerId: customers[1].id, subtotal: 4571.43, taxAmount: 228.57, discount: 0, total: 4800, paidAmount: 2000, status: 'PARTIAL', dueDate: new Date(now.getTime() + 25 * 86400000), notes: 'Deposit received' },
      { invoiceNumber: 'INV-2024-00003', tripId: trips[4].id, customerId: customers[4].id, subtotal: 3333.33, taxAmount: 166.67, discount: 0, total: 3500, paidAmount: 3500, status: 'PAID', dueDate: new Date(now.getTime() - 60 * 86400000), notes: 'Fully paid' },
    ],
  });

  // === TIMELINE ===
  await prisma.timelineEntry.createMany({
    data: [
      { tripId: trips[0].id, date: new Date(now.getTime() + 14 * 86400000), time: '09:00', title: 'Departure', subtitle: 'EK 501 DXB → LHR', type: 'DEPARTURE', sortOrder: 1 },
      { tripId: trips[0].id, date: new Date(now.getTime() + 14 * 86400000), time: '15:00', title: 'Check-in', subtitle: 'Burj Al Arab', type: 'HOTEL', sortOrder: 2 },
      { tripId: trips[0].id, date: new Date(now.getTime() + 15 * 86400000), title: 'Free Day', subtitle: 'Leisure & Spa', type: 'FREE_DAY', sortOrder: 3 },
      { tripId: trips[0].id, date: new Date(now.getTime() + 21 * 86400000), time: '12:00', title: 'Check-out', subtitle: 'Burj Al Arab', type: 'HOTEL', sortOrder: 4 },
      { tripId: trips[0].id, date: new Date(now.getTime() + 21 * 86400000), time: '15:00', title: 'Return Flight', subtitle: 'EK 502 LHR → DXB', type: 'ARRIVAL', sortOrder: 5 },
      { tripId: trips[1].id, date: new Date(now.getTime() + 30 * 86400000), time: '14:00', title: 'Departure', subtitle: 'EK 1 DXB → LHR', type: 'DEPARTURE', sortOrder: 1 },
      { tripId: trips[1].id, date: new Date(now.getTime() + 30 * 86400000), time: '20:00', title: 'Check-in', subtitle: 'Marriott Canary Wharf', type: 'HOTEL', sortOrder: 2 },
      { tripId: trips[1].id, date: new Date(now.getTime() + 35 * 86400000), time: '11:00', title: 'Check-out', subtitle: 'Marriott Canary Wharf', type: 'HOTEL', sortOrder: 3 },
      { tripId: trips[1].id, date: new Date(now.getTime() + 35 * 86400000), time: '16:00', title: 'Return Flight', subtitle: 'EK 2 LHR → DXB', type: 'ARRIVAL', sortOrder: 4 },
      { tripId: trips[2].id, date: new Date(now.getTime() + 45 * 86400000), time: '10:00', title: 'Departure', subtitle: 'EK 504 DXB → MLE', type: 'DEPARTURE', sortOrder: 1 },
      { tripId: trips[2].id, date: new Date(now.getTime() + 45 * 86400000), time: '15:00', title: 'Check-in', subtitle: 'W Maldives', type: 'HOTEL', sortOrder: 2 },
      { tripId: trips[2].id, date: new Date(now.getTime() + 52 * 86400000), time: '12:00', title: 'Check-out', subtitle: 'W Maldives', type: 'HOTEL', sortOrder: 3 },
      { tripId: trips[2].id, date: new Date(now.getTime() + 52 * 86400000), time: '16:00', title: 'Return Flight', subtitle: 'EK 505 MLE → DXB', type: 'ARRIVAL', sortOrder: 4 },
      { tripId: trips[3].id, date: new Date(now.getTime() + 60 * 86400000), time: '08:00', title: 'Departure', subtitle: 'EK 121 DXB → IST (12 pax)', type: 'DEPARTURE', sortOrder: 1 },
      { tripId: trips[3].id, date: new Date(now.getTime() + 60 * 86400000), time: '15:00', title: 'Check-in', subtitle: 'JW Marriott Istanbul', type: 'HOTEL', sortOrder: 2 },
      { tripId: trips[3].id, date: new Date(now.getTime() + 61 * 86400000), time: '09:00', title: 'Istanbul City Tour', subtitle: 'Full day guided tour', type: 'TOUR', sortOrder: 3 },
      { tripId: trips[3].id, date: new Date(now.getTime() + 67 * 86400000), time: '12:00', title: 'Check-out', subtitle: 'JW Marriott Istanbul', type: 'HOTEL', sortOrder: 4 },
      { tripId: trips[3].id, date: new Date(now.getTime() + 67 * 86400000), time: '16:00', title: 'Return Flight', subtitle: 'EK 122 IST → DXB', type: 'ARRIVAL', sortOrder: 5 },
      { tripId: trips[4].id, date: new Date(now.getTime() - 45 * 86400000), title: 'Departure', subtitle: 'MS 901 JFK → CAI', type: 'DEPARTURE', sortOrder: 1 },
      { tripId: trips[4].id, date: new Date(now.getTime() - 44 * 86400000), title: 'Pyramids Tour', subtitle: 'Guided tour of Giza Pyramids', type: 'TOUR', sortOrder: 2 },
      { tripId: trips[4].id, date: new Date(now.getTime() - 38 * 86400000), title: 'Return Flight', subtitle: 'MS 902 CAI → JFK', type: 'ARRIVAL', sortOrder: 3 },
    ],
  });

  // === DOCUMENTS ===
  await prisma.document.createMany({
    data: [
      { name: 'Passport - Mohammed Al-Rashid', category: 'PASSPORT', filePath: '/docs/passports/A12345678.pdf', tripId: trips[0].id, uploadedById: users[2].id },
      { name: 'Visa - UK Tourist Visa', category: 'VISA', filePath: '/docs/visas/UK-Emma-T.pdf', tripId: trips[1].id, uploadedById: users[5].id },
      { name: 'Hotel Voucher - Burj Al Arab', category: 'VOUCHER', filePath: '/docs/vouchers/Burj-001.pdf', tripId: trips[0].id, uploadedById: users[3].id },
      { name: 'Invoice - Dubai Luxury', category: 'INVOICE', filePath: '/docs/invoices/INV-2024-00001.pdf', tripId: trips[0].id, uploadedById: users[4].id },
      { name: 'Travel Insurance Policy', category: 'INSURANCE', filePath: '/docs/insurance/TRV-456.pdf', tripId: trips[2].id, uploadedById: users[4].id },
    ],
  });

  // === NOTIFICATIONS ===
  await prisma.notification.createMany({
    data: [
      { userId: users[3].id, type: 'PASSPORT_EXPIRY', title: 'Passport Expiring Soon', message: 'Emma Thompson\'s passport expires in 5 months', relatedId: trips[1].id, relatedType: 'trip' },
      { userId: users[3].id, type: 'TASK_ASSIGNED', title: 'Hotel Confirmation Needed', message: 'Confirm group booking with Marriott Istanbul', relatedId: trips[3].id, relatedType: 'trip' },
      { userId: users[2].id, type: 'PAYMENT_DUE', title: 'Payment Due', message: 'Outstanding balance of $2,800 for London trip', relatedId: trips[1].id, relatedType: 'trip' },
      { userId: users[1].id, type: 'GENERAL', title: 'Monthly Report Ready', message: 'November sales report is ready for review' },
    ],
  });

  // === AUDIT LOGS ===
  await prisma.auditLog.createMany({
    data: [
      { userId: users[2].id, action: 'CREATE', module: 'trips', entityId: trips[0].id, entityType: 'trip', newValues: { tripNumber: 'TB-24-00001', customer: 'Mohammed Al-Rashid' } },
      { userId: users[4].id, action: 'CREATE', module: 'payments', entityId: trips[0].id, entityType: 'payment', newValues: { amount: 12500, status: 'PAID' } },
    ],
  });

  // === EXCHANGE RATES ===
  await prisma.exchangeRate.createMany({
    data: [
      { from: 'USD', to: 'AED', rate: 3.67, date: new Date() },
      { from: 'USD', to: 'EUR', rate: 0.92, date: new Date() },
      { from: 'USD', to: 'GBP', rate: 0.79, date: new Date() },
      { from: 'USD', to: 'EGP', rate: 30.9, date: new Date() },
      { from: 'USD', to: 'SAR', rate: 3.75, date: new Date() },
    ],
  });

  console.log('\n🎉 Seeding complete!');
  console.log('\n📋 Login Credentials:');
  console.log('   Admin:      admin@travelbox.com / admin123');
  console.log('   Manager:    manager@travelbox.com / admin123');
  console.log('   Sales:      sales@travelbox.com / admin123');
  console.log('   Operations: ops@travelbox.com / admin123');
  console.log('   Accounting: accounting@travelbox.com / admin123');
  console.log('   Visa:       visa@travelbox.com / admin123');
}

main()
  .catch((e) => { console.error(e); process.exit(1); })
  .finally(async () => { await prisma.$disconnect(); });
