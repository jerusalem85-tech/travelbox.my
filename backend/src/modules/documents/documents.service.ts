import { Injectable, NotFoundException } from '@nestjs/common';
import { PrismaService } from '../../core/database/prisma.service';

@Injectable()
export class DocumentsService {
  constructor(private prisma: PrismaService) {}

  async create(data: any) { return this.prisma.document.create({ data, include: { trip: true, customer: true, supplier: true } }); }

  async findAll(query: any) {
    const { page = 1, limit = 10, tripId, customerId, category } = query;
    const where: any = {};
    if (tripId) where.tripId = tripId;
    if (customerId) where.customerId = customerId;
    if (category) where.category = category;
    const [data, total] = await Promise.all([
      this.prisma.document.findMany({ where, orderBy: { createdAt: 'desc' }, skip: (page - 1) * limit, take: limit, include: { uploadedBy: { select: { firstName: true, lastName: true } } } }),
      this.prisma.document.count({ where }),
    ]);
    return { data, total, page, limit, totalPages: Math.ceil(total / limit) };
  }

  async findById(id: string) {
    const doc = await this.prisma.document.findUnique({ where: { id }, include: { trip: true, customer: true, supplier: true, uploadedBy: { select: { firstName: true, lastName: true } } } });
    if (!doc) throw new NotFoundException('Document not found');
    return doc;
  }

  async update(id: string, data: any) { return this.prisma.document.update({ where: { id }, data }); }
  async remove(id: string) { await this.prisma.document.delete({ where: { id } }); return { deleted: true }; }
}
