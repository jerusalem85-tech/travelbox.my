import { Injectable } from '@nestjs/common';
import { PrismaService } from '../../core/database/prisma.service';

@Injectable()
export class DashboardService {
  constructor(private prisma: PrismaService) {}

  async getStats() {
    const [tripCount, activeTrips, customerCount, supplierCount, totalRevenue, totalCost, pendingTasks, unpaidInvoices] = await Promise.all([
      this.prisma.trip.count(),
      this.prisma.trip.count({ where: { status: 'CONFIRMED' } }),
      this.prisma.customer.count(),
      this.prisma.supplier.count(),
      this.prisma.trip.aggregate({ _sum: { totalSelling: true } }),
      this.prisma.trip.aggregate({ _sum: { totalCost: true } }),
      this.prisma.task.count({ where: { status: { notIn: ['COMPLETED', 'CANCELLED'] } } }),
      this.prisma.invoice.count({ where: { status: { not: 'PAID' } } }),
    ]);
    return {
      tripCount, activeTrips, customerCount, supplierCount,
      totalRevenue: totalRevenue._sum.totalSelling || 0,
      totalCost: totalCost._sum.totalCost || 0,
      profit: (totalRevenue._sum.totalSelling || 0) - (totalCost._sum.totalCost || 0),
      pendingTasks, unpaidInvoices,
    };
  }

  async getUpcomingTrips(limit = 5) {
    return this.prisma.trip.findMany({
      where: { startDate: { gte: new Date() }, status: { notIn: ['CANCELLED', 'COMPLETED'] } },
      orderBy: { startDate: 'asc' },
      take: limit,
      include: { customer: { select: { firstName: true, lastName: true } }, _count: { select: { passengers: true, services: true } } },
    });
  }

  async getRecentActivity(limit = 10) {
    const [trips, tasks, payments] = await Promise.all([
      this.prisma.trip.findMany({ orderBy: { updatedAt: 'desc' }, take: limit, select: { id: true, tripNumber: true, status: true, updatedAt: true, customer: { select: { firstName: true, lastName: true } } } }),
      this.prisma.task.findMany({ orderBy: { updatedAt: 'desc' }, take: limit, select: { id: true, title: true, status: true, updatedAt: true, trip: { select: { tripNumber: true } } } }),
      this.prisma.payment.findMany({ orderBy: { createdAt: 'desc' }, take: limit, select: { id: true, amount: true, direction: true, createdAt: true, trip: { select: { tripNumber: true } } } }),
    ]);
    return { trips, tasks, payments };
  }

  async getMonthlyStats(year: number) {
    const start = new Date(`${year}-01-01`);
    const end = new Date(`${year}-12-31`);
    const trips = await this.prisma.trip.findMany({ where: { createdAt: { gte: start, lte: end } }, select: { createdAt: true, totalSelling: true, totalCost: true, status: true } });
    const months = Array.from({ length: 12 }, (_, i) => ({ month: i + 1, tripCount: 0, revenue: 0, cost: 0 }));
    trips.forEach(t => { const m = new Date(t.createdAt).getMonth(); months[m].tripCount++; months[m].revenue += Number(t.totalSelling); months[m].cost += Number(t.totalCost); });
    return months;
  }
}
