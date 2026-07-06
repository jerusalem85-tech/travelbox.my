import { Injectable } from '@nestjs/common';
import { PrismaService } from '../../core/database/prisma.service';

@Injectable()
export class SearchService {
  constructor(private prisma: PrismaService) {}

  async globalSearch(q: string) {
    const [trips, customers, suppliers, tasks, invoices] = await Promise.all([
      this.prisma.trip.findMany({ where: { OR: [{ tripNumber: { contains: q } }, { name: { contains: q } }] }, take: 5, select: { id: true, tripNumber: true, name: true, status: true } }),
      this.prisma.customer.findMany({ where: { OR: [{ firstName: { contains: q } }, { lastName: { contains: q } }, { email: { contains: q } }, { phone: { contains: q } }, { passportNumber: { contains: q } }] }, take: 5, select: { id: true, firstName: true, lastName: true, companyName: true } }),
      this.prisma.supplier.findMany({ where: { OR: [{ companyName: { contains: q } }, { contactPerson: { contains: q } }, { email: { contains: q } }] }, take: 5, select: { id: true, companyName: true, contactPerson: true } }),
      this.prisma.task.findMany({ where: { title: { contains: q } }, take: 5, select: { id: true, title: true, status: true, priority: true } }),
      this.prisma.invoice.findMany({ where: { invoiceNumber: { contains: q } }, take: 5, select: { id: true, invoiceNumber: true, status: true, total: true } }),
    ]);
    return { trips, customers, suppliers, tasks, invoices };
  }
}
