import { Injectable } from '@nestjs/common';
import { PrismaService } from '../../core/database/prisma.service';

@Injectable()
export class DashboardService {
  constructor(private prisma: PrismaService) {}

  async getStats() {
    const [totalUsers, activeUsers, totalClients, totalTrips, totalBookings, totalInvoices, totalExpenses, revenueCollected, pendingAmount, recentUsers] = await Promise.all([
      this.prisma.user.count(),
      this.prisma.user.count({ where: { isActive: true } }),
      this.prisma.client.count(),
      this.prisma.trip.count(),
      this.prisma.booking.count(),
      this.prisma.invoice.count(),
      this.prisma.expense.count(),
      this.prisma.invoice.aggregate({ where: { status: 'PAID' }, _sum: { amount: true } }),
      this.prisma.invoice.aggregate({ where: { status: { in: ['PENDING', 'OVERDUE'] } }, _sum: { amount: true } }),
      this.prisma.user.findMany({ take: 5, orderBy: { createdAt: 'desc' }, select: { id: true, firstName: true, lastName: true, email: true, role: true, createdAt: true } }),
    ]);
    return {
      totalUsers, activeUsers,
      totalClients, totalTrips, totalBookings,
      totalInvoices, totalExpenses,
      revenueCollected: revenueCollected._sum.amount || 0,
      pendingAmount: pendingAmount._sum.amount || 0,
      recentUsers,
    };
  }
}
