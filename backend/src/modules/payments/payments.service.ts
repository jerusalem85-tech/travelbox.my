import { Injectable, NotFoundException } from '@nestjs/common';
import { PrismaService } from '../../core/database/prisma.service';
import { CreatePaymentDto, UpdatePaymentDto } from './dto/create-payment.dto';

@Injectable()
export class PaymentsService {
  constructor(private prisma: PrismaService) {}

  async create(dto: CreatePaymentDto, userId: string) {
    const amountLocal = (dto.exchangeRate || 1) * dto.amount;

    const payment = await this.prisma.payment.create({
      data: {
        tripId: dto.tripId,
        direction: dto.direction,
        type: dto.customerId ? 'customer' : dto.supplierId ? 'supplier' : null,
        customerId: dto.customerId,
        supplierId: dto.supplierId,
        amount: dto.amount,
        currency: dto.currency || 'USD',
        exchangeRate: dto.exchangeRate || 1,
        amountLocal,
        method: dto.method,
        status: dto.status || 'PENDING',
        referenceNo: dto.referenceNo,
        description: dto.description,
        paymentDate: dto.paymentDate ? new Date(dto.paymentDate) : new Date(),
        dueDate: dto.dueDate ? new Date(dto.dueDate) : undefined,
        recordedById: userId,
        notes: dto.notes,
      },
      include: {
        trip: { select: { id: true, referenceNo: true } },
        customer: { select: { id: true, firstName: true, lastName: true } },
        supplier: { select: { id: true, name: true } },
      },
    });

    await this.createJournalEntries(payment, userId);
    await this.updateTripFinancials(payment.tripId);

    return payment;
  }

  async findAll(tripId?: string, customerId?: string, supplierId?: string) {
    return this.prisma.payment.findMany({
      where: {
        deletedAt: null,
        ...(tripId ? { tripId } : {}),
        ...(customerId ? { customerId } : {}),
        ...(supplierId ? { supplierId } : {}),
      },
      include: {
        trip: { select: { id: true, referenceNo: true } },
        customer: { select: { id: true, firstName: true, lastName: true } },
        supplier: { select: { id: true, name: true } },
        receivedBy: { select: { id: true, firstName: true, lastName: true } },
      },
      orderBy: { paymentDate: 'desc' },
    });
  }

  async findById(id: string) {
    const payment = await this.prisma.payment.findUnique({
      where: { id },
      include: {
        trip: { select: { id: true, referenceNo: true } },
        customer: { select: { id: true, firstName: true, lastName: true } },
        supplier: { select: { id: true, name: true } },
        receivedBy: { select: { id: true, firstName: true, lastName: true } },
        recordedBy: { select: { id: true, firstName: true, lastName: true } },
        journalEntries: { include: { account: true } },
      },
    });
    if (!payment || payment.deletedAt) throw new NotFoundException('Payment not found');
    return payment;
  }

  async update(id: string, dto: UpdatePaymentDto) {
    await this.findById(id);
    const updated = await this.prisma.payment.update({
      where: { id },
      data: {
        ...dto,
        paymentDate: dto.paymentDate ? new Date(dto.paymentDate) : undefined,
        dueDate: dto.dueDate ? new Date(dto.dueDate) : undefined,
        amountLocal: dto.amount && dto.exchangeRate
          ? dto.amount * dto.exchangeRate
          : undefined,
      },
    });

    if (dto.amount || dto.status) {
      await this.updateTripFinancials(updated.tripId);
    }
    return updated;
  }

  async remove(id: string) {
    const payment = await this.findById(id);
    await this.prisma.payment.update({
      where: { id },
      data: { deletedAt: new Date(), status: 'CANCELLED' },
    });
    // Note: when accounting module is built, reversal entries should be created
    await this.updateTripFinancials(payment.tripId);
    return { deleted: true };
  }

  async getTripBalance(tripId: string) {
    const payments = await this.prisma.payment.findMany({
      where: { tripId, deletedAt: null },
    });

    const totalInflow = payments
      .filter((p) => p.direction === 'INFLOW' && p.status !== 'CANCELLED')
      .reduce((sum, p) => sum + Number(p.amount), 0);

    const totalOutflow = payments
      .filter((p) => p.direction === 'OUTFLOW' && p.status !== 'CANCELLED')
      .reduce((sum, p) => sum + Number(p.amount), 0);

    return {
      totalReceived: totalInflow,
      totalPaid: totalOutflow,
      balance: totalInflow - totalOutflow,
    };
  }

  private async createJournalEntries(payment: any, userId: string) {
    // Find default cash account
    const cashAccount = await this.prisma.account.findFirst({
      where: { tenantId: 'default', code: '1000' },
    });
    if (!cashAccount) return; // Accounts not seeded yet

    if (payment.direction === 'INFLOW') {
      // Debit Cash, Credit Customer Deposits (or Accounts Receivable)
      const creditAccount = await this.prisma.account.findFirst({
        where: { tenantId: 'default', code: '2100' },
      });

      await this.prisma.accountingJournal.createMany({
        data: [
          {
            tripId: payment.tripId,
            paymentId: payment.id,
            customerId: payment.customerId,
            accountId: cashAccount.id,
            entryType: 'DEBIT',
            amount: payment.amountLocal,
            currency: payment.currency,
            description: payment.description || `Payment received - ${payment.trip?.referenceNo || ''}`,
            referenceNo: payment.referenceNo,
            createdById: userId,
          },
          {
            tripId: payment.tripId,
            paymentId: payment.id,
            customerId: payment.customerId,
            accountId: creditAccount?.id || cashAccount.id,
            entryType: 'CREDIT',
            amount: payment.amountLocal,
            currency: payment.currency,
            description: payment.description || `Payment received - ${payment.trip?.referenceNo || ''}`,
            referenceNo: payment.referenceNo,
            createdById: userId,
          },
        ],
      });
    } else {
      // Debit Cost of Sales (or Prepaid), Credit Cash
      const debitAccount = await this.prisma.account.findFirst({
        where: { tenantId: 'default', code: '5000' },
      });

      await this.prisma.accountingJournal.createMany({
        data: [
          {
            tripId: payment.tripId,
            paymentId: payment.id,
            supplierId: payment.supplierId,
            accountId: debitAccount?.id || cashAccount.id,
            entryType: 'DEBIT',
            amount: payment.amountLocal,
            currency: payment.currency,
            description: payment.description || `Payment to supplier - ${payment.trip?.referenceNo || ''}`,
            referenceNo: payment.referenceNo,
            createdById: userId,
          },
          {
            tripId: payment.tripId,
            paymentId: payment.id,
            supplierId: payment.supplierId,
            accountId: cashAccount.id,
            entryType: 'CREDIT',
            amount: payment.amountLocal,
            currency: payment.currency,
            description: payment.description || `Payment to supplier - ${payment.trip?.referenceNo || ''}`,
            referenceNo: payment.referenceNo,
            createdById: userId,
          },
        ],
      });
    }
  }

  private async updateTripFinancials(tripId: string) {
    const { totalReceived, totalPaid } = await this.getTripBalance(tripId);

    const trip = await this.prisma.trip.findUnique({
      where: { id: tripId },
      include: {
        flights: { select: { sellPrice: true, costPrice: true } },
        hotels: { select: { sellPrice: true, costPrice: true } },
        transfers: { select: { sellPrice: true, costPrice: true } },
        visas: { select: { sellPrice: true, costPrice: true } },
        insurances: { select: { sellPrice: true, premiumCost: true } },
        activities: { select: { sellPrice: true, costPrice: true } },
      },
    }) as any;

    if (!trip) return;

    const accumulate = (items: any[], costField: string) => ({
      revenue: items.reduce((s, i) => s + Number(i.sellPrice || 0), 0),
      cost: items.reduce((s, i) => s + Number(i[costField] || i.costPrice || 0), 0),
    });

    const flightsSum = accumulate(trip.flights, 'costPrice');
    const hotelsSum = accumulate(trip.hotels, 'costPrice');
    const transfersSum = accumulate(trip.transfers, 'costPrice');
    const visasSum = accumulate(trip.visas, 'costPrice');
    const insurancesSum = accumulate(trip.insurances, 'premiumCost');
    const activitiesSum = accumulate(trip.activities, 'costPrice');

    const revenue = flightsSum.revenue + hotelsSum.revenue + transfersSum.revenue
      + visasSum.revenue + insurancesSum.revenue + activitiesSum.revenue;
    const cost = flightsSum.cost + hotelsSum.cost + transfersSum.cost
      + visasSum.cost + insurancesSum.cost + activitiesSum.cost;
    const profit = revenue - cost;
    const margin = revenue > 0 ? (profit / revenue) * 100 : 0;

    await this.prisma.trip.update({
      where: { id: tripId },
      data: {
        totalRevenue: revenue,
        totalCost: cost,
        totalProfit: profit,
        profitMargin: margin,
      },
    });
  }
}
