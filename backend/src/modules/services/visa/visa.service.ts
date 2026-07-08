import { Injectable, NotFoundException } from '@nestjs/common';
import { PrismaService } from '../../../core/database/prisma.service';
import { CreateVisaDto, UpdateVisaDto } from './dto/create-visa.dto';

@Injectable()
export class VisaService {
  constructor(private prisma: PrismaService) {}

  async create(dto: CreateVisaDto) {
    return this.prisma.visa.create({
      data: { ...dto, applicationDate: dto.applicationDate ? new Date(dto.applicationDate) : undefined, decisionDate: dto.decisionDate ? new Date(dto.decisionDate) : undefined, validFrom: dto.validFrom ? new Date(dto.validFrom) : undefined, validUntil: dto.validUntil ? new Date(dto.validUntil) : undefined, costPrice: dto.costPrice || 0, sellPrice: dto.sellPrice || 0 },
    });
  }

  async findByTrip(tripId: string) {
    return this.prisma.visa.findMany({ where: { tripId }, include: { supplier: { select: { id: true, name: true } } } });
  }

  async findById(id: string) {
    const v = await this.prisma.visa.findUnique({ where: { id } });
    if (!v) throw new NotFoundException('Visa not found');
    return v;
  }

  async update(id: string, dto: UpdateVisaDto) {
    await this.findById(id);
    return this.prisma.visa.update({
      where: { id },
      data: { ...dto, applicationDate: dto.applicationDate ? new Date(dto.applicationDate) : undefined, decisionDate: dto.decisionDate ? new Date(dto.decisionDate) : undefined, validFrom: dto.validFrom ? new Date(dto.validFrom) : undefined, validUntil: dto.validUntil ? new Date(dto.validUntil) : undefined },
    });
  }

  async remove(id: string) {
    await this.findById(id);
    await this.prisma.visa.delete({ where: { id } });
    return { deleted: true };
  }
}
