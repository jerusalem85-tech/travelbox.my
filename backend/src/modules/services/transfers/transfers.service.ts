import { Injectable, NotFoundException } from '@nestjs/common';
import { PrismaService } from '../../../core/database/prisma.service';
import { CreateTransferDto, UpdateTransferDto } from './dto/create-transfer.dto';

@Injectable()
export class TransfersService {
  constructor(private prisma: PrismaService) {}

  async create(dto: CreateTransferDto) {
    return this.prisma.transfer.create({
      data: { ...dto, pickupDate: new Date(dto.pickupDate), dropoffDate: dto.dropoffDate ? new Date(dto.dropoffDate) : undefined, costPrice: dto.costPrice || 0, sellPrice: dto.sellPrice || 0 },
    });
  }

  async findByTrip(tripId: string) {
    return this.prisma.transfer.findMany({ where: { tripId }, include: { supplier: { select: { id: true, name: true } } }, orderBy: { pickupDate: 'asc' } });
  }

  async findById(id: string) {
    const t = await this.prisma.transfer.findUnique({ where: { id } });
    if (!t) throw new NotFoundException('Transfer not found');
    return t;
  }

  async update(id: string, dto: UpdateTransferDto) {
    await this.findById(id);
    return this.prisma.transfer.update({
      where: { id },
      data: { ...dto, pickupDate: dto.pickupDate ? new Date(dto.pickupDate) : undefined, dropoffDate: dto.dropoffDate ? new Date(dto.dropoffDate) : undefined },
    });
  }

  async remove(id: string) {
    await this.findById(id);
    await this.prisma.transfer.delete({ where: { id } });
    return { deleted: true };
  }
}
