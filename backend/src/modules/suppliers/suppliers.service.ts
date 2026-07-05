import { Injectable, NotFoundException } from '@nestjs/common';
import { PrismaService } from '../../core/database/prisma.service';

@Injectable()
export class SuppliersService {
  constructor(private prisma: PrismaService) {}

  async create(data: any) { return this.prisma.supplier.create({ data }); }

  async findAll(query: any) {
    const { page = 1, limit = 10, search, category } = query;
    const where: any = {};
    if (search) where.OR = [{ companyName: { contains: search, mode: 'insensitive' } }, { contactPerson: { contains: search, mode: 'insensitive' } }, { email: { contains: search, mode: 'insensitive' } }];
    if (category) where.category = category;
    const [data, total] = await Promise.all([this.prisma.supplier.findMany({ where, orderBy: { companyName: 'asc' }, skip: (page - 1) * limit, take: limit, include: { _count: { select: { services: true, contracts: true, payments: true } } } }), this.prisma.supplier.count({ where })]);
    return { data, total, page, limit, totalPages: Math.ceil(total / limit) };
  }

  async findById(id: string) {
    const supplier = await this.prisma.supplier.findUnique({ where: { id }, include: { contracts: true, documents: true, _count: { select: { services: true, payments: true } } } });
    if (!supplier) throw new NotFoundException('Supplier not found');
    return supplier;
  }

  async update(id: string, data: any) { return this.prisma.supplier.update({ where: { id }, data }); }
  async remove(id: string) { await this.prisma.supplier.delete({ where: { id } }); return { deleted: true }; }
}
