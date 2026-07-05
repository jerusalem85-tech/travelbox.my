import { Injectable, NotFoundException } from '@nestjs/common';
import { PrismaService } from '../../core/database/prisma.service';

@Injectable()
export class PaymentsService {
  constructor(private prisma: PrismaService) {}

  async create(data: any) {
    const payment = await this.prisma.payment.create({ data, include: { trip: true, customer: true } });
    if (data.direction === 'INCOMING' && data.tripId) {
      await this.updateTripPaymentStatus(data.tripId);
    }
    return payment;
  }

  async findAll(query: any) {
    const { page = 1, limit = 10, tripId, customerId, direction, status, method } = query;
    const where: any = {};
    if (tripId) where.tripId = tripId;
    if (customerId) where.customerId = customerId;
    if (direction) where.direction = direction;
    if (status) where.status = status;
    if (method) where.method = method;
    const [data, total] = await Promise.all([
      this.prisma.payment.findMany({ where, orderBy: { createdAt: 'desc' }, skip: (page - 1) * limit, take: limit, include: { trip: { select: { tripNumber: true } }, customer: { select: { firstName: true, lastName: true } } } }),
      this.prisma.payment.count({ where }),
    ]);
    return { data, total, page, limit, totalPages: Math.ceil(total / limit) };
  }

  async findById(id: string) {
    const payment = await this.prisma.payment.findUnique({ where: { id }, include: { trip: true, customer: true } });
    if (!payment) throw new NotFoundException('Payment not found');
    return payment;
  }

  async update(id: string, data: any) {
    const payment = await this.prisma.payment.update({ where: { id }, data, include: { trip: true, customer: true } });
    if (payment.tripId) await this.updateTripPaymentStatus(payment.tripId);
    return payment;
  }

  async remove(id: string) {
    const payment = await this.prisma.payment.findUnique({ where: { id } });
    if (payment?.tripId) await this.updateTripPaymentStatus(payment.tripId);
    await this.prisma.payment.delete({ where: { id } });
    return { deleted: true };
  }

  private async updateTripPaymentStatus(tripId: string) {
    const payments = await this.prisma.payment.findMany({ where: { tripId, direction: 'INCOMING' } });
    const totalPaid = payments.reduce((s, p) => s + Number(p.amount), 0);
    const trip = await this.prisma.trip.findUnique({ where: { id: tripId } });
    if (!trip) return;
    const totalCost = Number(trip.totalCost);
    const status = totalPaid >= totalCost ? 'PAID' : totalPaid > 0 ? 'PARTIAL' : 'UNPAID';
    await this.prisma.trip.update({ where: { id: tripId }, data: { paymentStatus: status as any } });
  }
}
