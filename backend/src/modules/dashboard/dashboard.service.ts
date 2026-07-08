import { Injectable } from '@nestjs/common';
import { PrismaService } from '../../core/database/prisma.service';

@Injectable()
export class DashboardService {
  constructor(private prisma: PrismaService) {}

  async getSummary() {
    const [
      totalTrips,
      activeTrips,
      totalCustomers,
      totalRevenue,
      totalCost,
      recentTrips,
      upcomingTrips,
      pendingPayments,
    ] = await Promise.all([
      this.prisma.trip.count({ where: { deletedAt: null } }),
      this.prisma.trip.count({ where: { deletedAt: null, status: { in: ['CONFIRMED', 'IN_PROGRESS'] } } }),
      this.prisma.customer.count({ where: { deletedAt: null } }),
      this.prisma.trip.aggregate({ _sum: { totalRevenue: true }, where: { deletedAt: null } }),
      this.prisma.trip.aggregate({ _sum: { totalCost: true }, where: { deletedAt: null } }),
      this.prisma.trip.findMany({
        where: { deletedAt: null },
        orderBy: { createdAt: 'desc' },
        take: 5,
        include: {
          createdBy: { select: { id: true, firstName: true, lastName: true } },
          customers: { include: { customer: { select: { id: true, firstName: true, lastName: true } } } },
        },
      }),
      this.prisma.trip.findMany({
        where: { deletedAt: null, startDate: { gte: new Date() }, status: { notIn: ['CANCELLED', 'COMPLETED'] } },
        orderBy: { startDate: 'asc' },
        take: 5,
        include: {
          customers: { include: { customer: { select: { id: true, firstName: true, lastName: true } } } },
        },
      }),
      this.prisma.payment.aggregate({
        _sum: { amount: true },
        where: { status: { in: ['PENDING', 'PARTIAL'] } },
      }),
    ]);

    return {
      stats: {
        totalTrips,
        activeTrips,
        totalCustomers,
        totalRevenue: totalRevenue._sum.totalRevenue || 0,
        totalCost: totalCost._sum.totalCost || 0,
        totalProfit: Number(totalRevenue._sum.totalRevenue || 0) - Number(totalCost._sum.totalCost || 0),
        pendingPayments: pendingPayments._sum.amount || 0,
      },
      recentTrips,
      upcomingTrips,
    };
  }
}
