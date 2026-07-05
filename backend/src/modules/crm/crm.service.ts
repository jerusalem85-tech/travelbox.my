import { Injectable, NotFoundException } from '@nestjs/common';
import { PrismaService } from '../../core/database/prisma.service';

@Injectable()
export class CrmService {
  constructor(private prisma: PrismaService) {}

  async create(data: any) {
    const customer = await this.prisma.customer.create({ data });
    return customer;
  }

  async findAll(query: any) {
    const { page = 1, limit = 10, search, type, isVip } = query;
    const where: any = {};
    if (search) {
      where.OR = [
        { firstName: { contains: search, mode: 'insensitive' } },
        { lastName: { contains: search, mode: 'insensitive' } },
        { companyName: { contains: search, mode: 'insensitive' } },
        { email: { contains: search, mode: 'insensitive' } },
        { phone: { contains: search, mode: 'insensitive' } },
        { passportNumber: { contains: search, mode: 'insensitive' } },
      ];
    }
    if (type) where.type = type;
    if (isVip !== undefined) where.isVip = isVip === 'true';

    const [data, total] = await Promise.all([
      this.prisma.customer.findMany({ where, orderBy: { createdAt: 'desc' }, skip: (page - 1) * limit, take: limit, include: { _count: { select: { trips: true, payments: true } } } }),
      this.prisma.customer.count({ where }),
    ]);
    return { data, total, page, limit, totalPages: Math.ceil(total / limit) };
  }

  async findById(id: string) {
    const customer = await this.prisma.customer.findUnique({
      where: { id },
      include: { trips: { include: { _count: { select: { services: true, payments: true } } }, orderBy: { createdAt: 'desc' } }, payments: { orderBy: { createdAt: 'desc' } }, documents: true, loyalty: true },
    });
    if (!customer) throw new NotFoundException('Customer not found');
    return customer;
  }

  async update(id: string, data: any) { return this.prisma.customer.update({ where: { id }, data }); }
  async remove(id: string) { await this.prisma.customer.delete({ where: { id } }); return { deleted: true }; }
}
