import { Injectable, NotFoundException } from '@nestjs/common';
import { PrismaService } from '../../core/database/prisma.service';

@Injectable()
export class AccountingService {
  constructor(private prisma: PrismaService) {}

  async createInvoice(data: any) {
    const seq = await this.prisma.invoice.count();
    const invoiceNumber = `INV-${new Date().getFullYear()}-${String(seq + 1).padStart(5, '0')}`;
    return this.prisma.invoice.create({ data: { ...data, invoiceNumber }, include: { trip: true, customer: true } });
  }

  async findAllInvoices(query: any) {
    const { page = 1, limit = 10, tripId, customerId, status } = query;
    const where: any = {};
    if (tripId) where.tripId = tripId;
    if (customerId) where.customerId = customerId;
    if (status) where.status = status;
    const [data, total] = await Promise.all([
      this.prisma.invoice.findMany({ where, orderBy: { createdAt: 'desc' }, skip: (page - 1) * limit, take: limit, include: { customer: { select: { firstName: true, lastName: true, companyName: true } }, trip: { select: { tripNumber: true } } } }),
      this.prisma.invoice.count({ where }),
    ]);
    return { data, total, page, limit, totalPages: Math.ceil(total / limit) };
  }

  async findInvoiceById(id: string) {
    const invoice = await this.prisma.invoice.findUnique({ where: { id }, include: { trip: { include: { services: { include: { flight: true, hotel: true, transfer: true, tour: true, visa: true, insurance: true, carRental: true } } } }, customer: true } });
    if (!invoice) throw new NotFoundException('Invoice not found');
    return invoice;
  }

  async updateInvoice(id: string, data: any) { return this.prisma.invoice.update({ where: { id }, data }); }

  async createCreditNote(data: any) {
    const seq = await this.prisma.creditNote.count();
    const creditNoteNumber = `CN-${new Date().getFullYear()}-${String(seq + 1).padStart(5, '0')}`;
    return this.prisma.creditNote.create({ data: { ...data, creditNoteNumber }, include: { invoice: true } });
  }

  async createExpense(data: any) { return this.prisma.expense.create({ data, include: { trip: true } }); }

  async findAllExpenses(query: any) {
    const { page = 1, limit = 10, tripId, category } = query;
    const where: any = {};
    if (tripId) where.tripId = tripId;
    if (category) where.category = category;
    const [data, total] = await Promise.all([
      this.prisma.expense.findMany({ where, orderBy: { date: 'desc' }, skip: (page - 1) * limit, take: limit, include: { trip: { select: { tripNumber: true } } } }),
      this.prisma.expense.count({ where }),
    ]);
    return { data, total, page, limit, totalPages: Math.ceil(total / limit) };
  }

  async getTrialBalance(startDate?: string, endDate?: string) {
    const dateFilter: any = {};
    if (startDate) dateFilter.gte = new Date(startDate);
    if (endDate) dateFilter.lte = new Date(endDate);
    const where = Object.keys(dateFilter).length ? { createdAt: dateFilter } : undefined;
    const [invoices, expenses, payments] = await Promise.all([
      this.prisma.invoice.findMany({ where, select: { total: true, status: true, createdAt: true } }),
      this.prisma.expense.findMany({ where, select: { amount: true, date: true } }),
      this.prisma.payment.findMany({ where, select: { amount: true, direction: true, createdAt: true } }),
    ]);
    const totalRevenue = invoices.reduce((s, i) => s + Number(i.total), 0);
    const totalExpenses = expenses.reduce((s, e) => s + Number(e.amount), 0);
    const totalIncoming = payments.filter(p => p.direction === 'INCOMING').reduce((s, p) => s + Number(p.amount), 0);
    const totalOutgoing = payments.filter(p => p.direction === 'OUTGOING').reduce((s, p) => s + Number(p.amount), 0);
    return { totalRevenue, totalExpenses, totalIncoming, totalOutgoing, netProfit: totalRevenue - totalExpenses, invoiceCount: invoices.length, expenseCount: expenses.length };
  }
}
