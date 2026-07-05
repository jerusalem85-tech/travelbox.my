import { Injectable } from '@nestjs/common';
import { PrismaService } from '../../core/database/prisma.service';

@Injectable()
export class ReportsService {
  constructor(private prisma: PrismaService) {}

  async getSalesReport(startDate: string, endDate: string) {
    const start = new Date(startDate);
    const end = new Date(endDate);
    const trips = await this.prisma.trip.findMany({ where: { createdAt: { gte: start, lte: end } }, include: { payments: true, services: true } });
    const totalRevenue = trips.reduce((s, t) => s + Number(t.totalSelling), 0);
    const totalCost = trips.reduce((s, t) => s + Number(t.totalCost), 0);
    const totalProfit = trips.reduce((s, t) => s + Number(t.totalProfit), 0);
    const tripCount = trips.length;
    return { totalRevenue, totalCost, totalProfit, tripCount, averageProfit: tripCount ? totalProfit / tripCount : 0, margin: totalRevenue ? (totalProfit / totalRevenue) * 100 : 0 };
  }

  async getServiceBreakdown(startDate: string, endDate: string) {
    const start = new Date(startDate);
    const end = new Date(endDate);
    const services = await this.prisma.service.groupBy({ by: ['type'], where: { trip: { createdAt: { gte: start, lte: end } } }, _count: { id: true }, _sum: { sellingPrice: true, costPrice: true } });
    return services.map(s => ({ type: s.type, count: s._count.id, totalSelling: s._sum.sellingPrice, totalCost: s._sum.costPrice }));
  }

  async getMonthlyRevenue(year: number) {
    const start = new Date(`${year}-01-01`);
    const end = new Date(`${year}-12-31`);
    const payments = await this.prisma.payment.findMany({ where: { direction: 'INCOMING', createdAt: { gte: start, lte: end } }, select: { amount: true, createdAt: true } });
    const months = Array.from({ length: 12 }, (_, i) => ({ month: i + 1, revenue: 0 }));
    payments.forEach(p => { const m = new Date(p.createdAt).getMonth(); months[m].revenue += Number(p.amount); });
    return months;
  }

  async getTopCustomers(limit = 10) {
    const customers = await this.prisma.customer.findMany({
      orderBy: { trips: { _count: 'desc' } },
      take: limit,
      include: { _count: { select: { trips: true } }, trips: { select: { totalSelling: true } } },
    });
    return customers.map(c => ({ id: c.id, name: `${c.firstName} ${c.lastName}`, company: c.companyName, tripCount: c._count.trips, totalSpent: c.trips.reduce((s, t) => s + Number(t.totalSelling), 0) }));
  }

  async getSupplierPerformance(startDate: string, endDate: string) {
    const start = new Date(startDate);
    const end = new Date(endDate);
    const suppliers = await this.prisma.supplier.findMany({
      include: {
        services: { where: { trip: { createdAt: { gte: start, lte: end } } }, select: { costPrice: true, sellingPrice: true, status: true } },
        _count: { select: { services: { where: { trip: { createdAt: { gte: start, lte: end } } } } } },
      },
    });
    return suppliers.map(s => ({
      id: s.id, name: s.companyName, serviceCount: s._count.services,
      totalCost: s.services.reduce((sum, sv) => sum + Number(sv.costPrice), 0),
      totalSelling: s.services.reduce((sum, sv) => sum + Number(sv.sellingPrice), 0),
    }));
  }
}
