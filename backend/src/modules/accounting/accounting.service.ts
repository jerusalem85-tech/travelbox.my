import { Injectable, NotFoundException, BadRequestException } from '@nestjs/common';
import { PrismaService } from '../../core/database/prisma.service';
import { CreateJournalEntryDto } from './dto/create-journal-entry.dto';

@Injectable()
export class AccountingService {
  constructor(private prisma: PrismaService) {}

  async getChartOfAccounts() {
    return this.prisma.account.findMany({
      where: { deletedAt: null, tenantId: 'default' },
      include: { children: true },
      orderBy: { code: 'asc' },
    });
  }

  async createAccount(data: { code: string; name: string; category: any; parentId?: string }) {
    const existing = await this.prisma.account.findUnique({
      where: { tenantId_code: { tenantId: 'default', code: data.code } },
    });
    if (existing) throw new BadRequestException(`Account ${data.code} already exists`);
    return this.prisma.account.create({
      data: { ...data, tenantId: 'default' },
    });
  }

  async getJournalEntries(tripId?: string, accountId?: string, from?: string, to?: string) {
    return this.prisma.accountingJournal.findMany({
      where: {
        ...(tripId ? { tripId } : {}),
        ...(accountId ? { accountId } : {}),
        ...(from || to ? {
          entryDate: {
            ...(from ? { gte: new Date(from) } : {}),
            ...(to ? { lte: new Date(to) } : {}),
          },
        } : {}),
      },
      include: {
        account: { select: { id: true, code: true, name: true } },
        trip: { select: { id: true, referenceNo: true } },
        createdBy: { select: { id: true, firstName: true, lastName: true } },
      },
      orderBy: { entryDate: 'desc' },
      take: 500,
    });
  }

  async getTrialBalance() {
    const entries = await this.prisma.accountingJournal.groupBy({
      by: ['accountId'],
      _sum: { amount: true },
      where: { entryType: 'DEBIT' },
    });

    const creditEntries = await this.prisma.accountingJournal.groupBy({
      by: ['accountId'],
      _sum: { amount: true },
      where: { entryType: 'CREDIT' },
    });

    const accounts = await this.prisma.account.findMany({
      where: { deletedAt: null, tenantId: 'default' },
    });

    const creditMap = new Map(creditEntries.map((e) => [e.accountId, Number(e._sum.amount)]));

    return accounts.map((account) => {
      const debitTotal = Number(entries.find((e) => e.accountId === account.id)?._sum.amount || 0);
      const creditTotal = creditMap.get(account.id) || 0;
      const balance = debitTotal - creditTotal;

      return {
        code: account.code,
        name: account.name,
        category: account.category,
        debit: debitTotal,
        credit: creditTotal,
        balance: account.category === 'LIABILITY' || account.category === 'EQUITY' || account.category === 'REVENUE'
          ? -balance : balance,
      };
    });
  }

  async getProfitAndLoss(from?: string, to?: string) {
    const where: any = {};
    if (from || to) {
      where.entryDate = {};
      if (from) where.entryDate.gte = new Date(from);
      if (to) where.entryDate.lte = new Date(to);
    }

    const revenueAccounts = await this.prisma.account.findMany({
      where: { deletedAt: null, tenantId: 'default', category: { in: ['REVENUE', 'CONTRA_REVENUE'] } },
    });

    const expenseAccounts = await this.prisma.account.findMany({
      where: { deletedAt: null, tenantId: 'default', category: { in: ['EXPENSE', 'CONTRA_EXPENSE'] } },
    });

    const revenueEntries = await this.prisma.accountingJournal.groupBy({
      by: ['accountId'],
      _sum: { amount: true },
      where: { ...where, accountId: { in: revenueAccounts.map((a) => a.id) }, entryType: 'CREDIT' },
    });

    const expenseEntries = await this.prisma.accountingJournal.groupBy({
      by: ['accountId'],
      _sum: { amount: true },
      where: { ...where, accountId: { in: expenseAccounts.map((a) => a.id) }, entryType: 'DEBIT' },
    });

    const revenueMap = new Map(revenueEntries.map((e) => [e.accountId, Number(e._sum.amount)]));
    const expenseMap = new Map(expenseEntries.map((e) => [e.accountId, Number(e._sum.amount)]));

    const revenues = revenueAccounts.map((a) => ({
      code: a.code, name: a.name, amount: revenueMap.get(a.id) || 0,
    }));
    const expenses = expenseAccounts.map((a) => ({
      code: a.code, name: a.name, amount: expenseMap.get(a.id) || 0,
    }));

    const totalRevenue = revenues.reduce((s, r) => s + r.amount, 0);
    const totalExpenses = expenses.reduce((s, e) => s + e.amount, 0);

    return {
      revenues,
      totalRevenue,
      expenses,
      totalExpenses,
      netProfit: totalRevenue - totalExpenses,
      grossMargin: totalRevenue > 0 ? ((totalRevenue - totalExpenses) / totalRevenue) * 100 : 0,
    };
  }

  async getTripProfitAndLoss(tripId: string) {
    const entries = await this.prisma.accountingJournal.findMany({
      where: { tripId },
      include: { account: true },
    });

    const revenue = entries
      .filter((e) => e.entryType === 'CREDIT' && ['REVENUE', 'CONTRA_REVENUE'].includes(e.account.category))
      .reduce((s, e) => s + Number(e.amount), 0);

    const expenses = entries
      .filter((e) => e.entryType === 'DEBIT' && ['EXPENSE', 'CONTRA_EXPENSE'].includes(e.account.category))
      .reduce((s, e) => s + Number(e.amount), 0);

    return {
      tripId,
      totalRevenue: revenue,
      totalCost: expenses,
      netProfit: revenue - expenses,
    };
  }

  // Manual journal entry (must have balancing debit/credit)
  async createManualEntry(dto: CreateJournalEntryDto, userId: string) {
    const account = await this.prisma.account.findUnique({ where: { id: dto.accountId } });
    if (!account) throw new NotFoundException('Account not found');

    return this.prisma.accountingJournal.create({
      data: {
        tripId: dto.tripId,
        customerId: dto.customerId,
        supplierId: dto.supplierId,
        accountId: dto.accountId,
        entryType: dto.entryType,
        amount: dto.amount,
        currency: dto.currency || 'USD',
        description: dto.description,
        entryDate: dto.entryDate ? new Date(dto.entryDate) : new Date(),
        referenceNo: dto.referenceNo,
        createdById: userId,
      },
      include: { account: true },
    });
  }

  async createBalancedEntry(
    entries: { accountId: string; entryType: 'DEBIT' | 'CREDIT'; amount: number; description?: string }[],
    userId: string,
    tripId?: string,
  ) {
    const totalDebit = entries.filter((e) => e.entryType === 'DEBIT').reduce((s, e) => s + e.amount, 0);
    const totalCredit = entries.filter((e) => e.entryType === 'CREDIT').reduce((s, e) => s + e.amount, 0);

    if (totalDebit !== totalCredit) {
      throw new BadRequestException(`Unbalanced entry: Debit ${totalDebit} ≠ Credit ${totalCredit}`);
    }

    return this.prisma.accountingJournal.createMany({
      data: entries.map((e) => ({
        tripId,
        accountId: e.accountId,
        entryType: e.entryType as any,
        amount: e.amount,
        description: e.description,
        createdById: userId,
      })),
    });
  }
}
