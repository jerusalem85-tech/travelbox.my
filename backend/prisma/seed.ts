import { PrismaClient, UserRole, Currency, AccountCategory } from '@prisma/client';
import * as bcrypt from 'bcryptjs';

const prisma = new PrismaClient();

async function main() {
  console.log('Seeding database...');

  // 1. Create default tenant
  let tenant = await prisma.tenant.findUnique({ where: { slug: 'default' } });
  if (!tenant) {
    tenant = await prisma.tenant.create({
      data: {
        name: 'TravelBox Travel & Tourism',
        slug: 'default',
        email: 'info@travelbox.my',
        phone: '+1-555-0000',
        address: '123 Travel Street, City, Country',
        currency: 'USD',
        timezone: 'UTC',
      },
    });
    console.log('Default tenant created');
  }

  // 2. Create admin user
  const adminExists = await prisma.user.findUnique({ where: { email: 'admin@travelbox.my' } });
  if (!adminExists) {
    await prisma.user.create({
      data: {
        tenantId: tenant.id,
        email: 'admin@travelbox.my',
        password: await bcrypt.hash('admin123', 12),
        firstName: 'Admin',
        lastName: 'TravelBox',
        role: 'SUPER_ADMIN' as UserRole,
        isActive: true,
      },
    });
    console.log('Admin user created: admin@travelbox.my / admin123');
  }

  // 3. Create chart of accounts
  const accounts = [
    { code: '1000', name: 'Cash & Bank', category: 'ASSET' as AccountCategory },
    { code: '1100', name: 'Accounts Receivable', category: 'ASSET' as AccountCategory },
    { code: '1200', name: 'Prepaid Expenses', category: 'ASSET' as AccountCategory },
    { code: '2000', name: 'Accounts Payable', category: 'LIABILITY' as AccountCategory },
    { code: '2100', name: 'Customer Deposits', category: 'LIABILITY' as AccountCategory },
    { code: '2200', name: 'Unearned Revenue', category: 'LIABILITY' as AccountCategory },
    { code: '3000', name: 'Retained Earnings', category: 'EQUITY' as AccountCategory },
    { code: '4000', name: 'Trip Revenue - Flights', category: 'REVENUE' as AccountCategory },
    { code: '4100', name: 'Trip Revenue - Hotels', category: 'REVENUE' as AccountCategory },
    { code: '4200', name: 'Trip Revenue - Transfers', category: 'REVENUE' as AccountCategory },
    { code: '4300', name: 'Trip Revenue - Visa', category: 'REVENUE' as AccountCategory },
    { code: '4400', name: 'Trip Revenue - Insurance', category: 'REVENUE' as AccountCategory },
    { code: '4500', name: 'Trip Revenue - Activities', category: 'REVENUE' as AccountCategory },
    { code: '4600', name: 'Service Fees', category: 'REVENUE' as AccountCategory },
    { code: '5000', name: 'Cost of Sales - Flights', category: 'EXPENSE' as AccountCategory },
    { code: '5100', name: 'Cost of Sales - Hotels', category: 'EXPENSE' as AccountCategory },
    { code: '5200', name: 'Cost of Sales - Transfers', category: 'EXPENSE' as AccountCategory },
    { code: '5300', name: 'Cost of Sales - Visa', category: 'EXPENSE' as AccountCategory },
    { code: '5400', name: 'Cost of Sales - Insurance', category: 'EXPENSE' as AccountCategory },
    { code: '5500', name: 'Cost of Sales - Activities', category: 'EXPENSE' as AccountCategory },
    { code: '6000', name: 'Salaries & Wages', category: 'EXPENSE' as AccountCategory },
    { code: '6100', name: 'Rent & Utilities', category: 'EXPENSE' as AccountCategory },
    { code: '6200', name: 'Marketing & Advertising', category: 'EXPENSE' as AccountCategory },
    { code: '6300', name: 'Office Supplies', category: 'EXPENSE' as AccountCategory },
    { code: '6400', name: 'Professional Fees', category: 'EXPENSE' as AccountCategory },
    { code: '6500', name: 'Bank Charges', category: 'EXPENSE' as AccountCategory },
    { code: '7000', name: 'Other Income', category: 'REVENUE' as AccountCategory },
    { code: '7100', name: 'Other Expenses', category: 'EXPENSE' as AccountCategory },
  ];

  for (const account of accounts) {
    const exists = await prisma.account.findUnique({
      where: { tenantId_code: { tenantId: tenant.id, code: account.code } },
    });
    if (!exists) {
      await prisma.account.create({
        data: { ...account, tenantId: tenant.id },
      });
    }
  }
  console.log(`${accounts.length} accounts seeded`);

  console.log('Seeding complete!');
}

main()
  .catch((e) => {
    console.error('Seed error:', e);
    process.exit(1);
  })
  .finally(() => prisma.$disconnect());
