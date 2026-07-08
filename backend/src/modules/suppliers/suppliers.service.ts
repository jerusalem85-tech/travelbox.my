import { Injectable, NotFoundException } from '@nestjs/common';
import { PrismaService } from '../../core/database/prisma.service';
import { CreateSupplierDto, UpdateSupplierDto } from './dto/create-supplier.dto';

@Injectable()
export class SuppliersService {
  constructor(private prisma: PrismaService) {}

  async create(dto: CreateSupplierDto) {
    return this.prisma.supplier.create({
      data: {
        ...dto,
        contractStart: dto.contractStart ? new Date(dto.contractStart) : undefined,
        contractEnd: dto.contractEnd ? new Date(dto.contractEnd) : undefined,
        tenantId: 'default',
      },
    });
  }

  async findAll(type?: string) {
    return this.prisma.supplier.findMany({
      where: {
        deletedAt: null,
        ...(type ? { type } : {}),
      },
      orderBy: { name: 'asc' },
    });
  }

  async findById(id: string) {
    const supplier = await this.prisma.supplier.findUnique({
      where: { id },
      include: { flights: true, hotels: true, transfers: true },
    });
    if (!supplier || supplier.deletedAt) throw new NotFoundException('Supplier not found');
    return supplier;
  }

  async update(id: string, dto: UpdateSupplierDto) {
    await this.findById(id);
    return this.prisma.supplier.update({
      where: { id },
      data: {
        ...dto,
        contractStart: dto.contractStart ? new Date(dto.contractStart) : undefined,
        contractEnd: dto.contractEnd ? new Date(dto.contractEnd) : undefined,
      },
    });
  }

  async remove(id: string) {
    await this.findById(id);
    await this.prisma.supplier.update({
      where: { id },
      data: { deletedAt: new Date(), isActive: false },
    });
    return { deleted: true };
  }
}
