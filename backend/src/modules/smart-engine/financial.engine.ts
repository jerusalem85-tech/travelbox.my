import { Injectable } from '@nestjs/common';
import { PrismaService } from '../../core/database/prisma.service';

export interface FinancialSummary {
  totalCost: number;
  totalSelling: number;
  totalProfit: number;
  totalCommission: number;
  margin: number;
  totalPaid: number;
  totalRemaining: number;
  supplierTotalPaid: number;
  supplierTotalRemaining: number;
  currency: string;
}

@Injectable()
export class FinancialEngine {
  constructor(private prisma: PrismaService) {}

  async calculate(tripId: string): Promise<FinancialSummary> {
    const services = await this.prisma.service.findMany({
      where: { tripId },
      include: { supplierPayments: true },
    });

    const payments = await this.prisma.payment.findMany({
      where: { tripId, direction: 'INCOMING' },
    });

    const supplierPayments = await this.prisma.supplierPayment.findMany({
      where: { service: { tripId } },
    });

    const totalCost = services.reduce((sum, s) => sum + (s.costPrice || 0), 0);
    const totalSelling = services.reduce((sum, s) => sum + (s.sellingPrice || 0), 0);
    const totalProfit = services.reduce((sum, s) => sum + (s.profit || 0), 0);
    const totalCommission = services.reduce((sum, s) => sum + (s.commissionAmount || 0), 0);
    const margin = totalSelling > 0 ? (totalProfit / totalSelling) * 100 : 0;

    const totalPaid = payments
      .filter(p => p.status === 'PAID' || p.status === 'PARTIAL')
      .reduce((sum, p) => sum + p.amount, 0);

    const supplierTotalPaid = supplierPayments
      .filter(p => p.status === 'PAID' || p.status === 'PARTIAL')
      .reduce((sum, p) => sum + p.amount, 0);

    return {
      totalCost,
      totalSelling,
      totalProfit,
      totalCommission,
      margin,
      totalPaid,
      totalRemaining: Math.max(0, totalSelling - totalPaid),
      supplierTotalPaid,
      supplierTotalRemaining: Math.max(0, totalCost - supplierTotalPaid),
      currency: 'USD',
    };
  }

  async updateTripFinancials(tripId: string): Promise<void> {
    const fin = await this.calculate(tripId);
    await this.prisma.trip.update({
      where: { id: tripId },
      data: {
        totalCost: fin.totalCost,
        totalSelling: fin.totalSelling,
        totalProfit: fin.totalProfit,
        totalCommission: fin.totalCommission,
        margin: fin.margin,
        totalPaid: fin.totalPaid,
        totalSupplierPaid: fin.supplierTotalPaid,
        totalSupplierBalance: fin.supplierTotalRemaining,
      },
    });
  }
}
