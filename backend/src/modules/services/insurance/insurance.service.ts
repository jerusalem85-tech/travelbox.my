import { Injectable, NotFoundException } from '@nestjs/common';
import { PrismaService } from '../../../core/database/prisma.service';
import { CreateInsuranceDto, UpdateInsuranceDto } from './dto/create-insurance.dto';

@Injectable()
export class InsuranceService {
  constructor(private prisma: PrismaService) {}

  async create(dto: CreateInsuranceDto) {
    return this.prisma.insurance.create({
      data: { ...dto, startDate: new Date(dto.startDate), endDate: new Date(dto.endDate), premiumCost: dto.premiumCost || 0, sellPrice: dto.sellPrice || 0 },
    });
  }

  async findByTrip(tripId: string) {
    return this.prisma.insurance.findMany({ where: { tripId }, include: { supplier: { select: { id: true, name: true } } } });
  }

  async findById(id: string) {
    const i = await this.prisma.insurance.findUnique({ where: { id } });
    if (!i) throw new NotFoundException('Insurance not found');
    return i;
  }

  async update(id: string, dto: UpdateInsuranceDto) {
    await this.findById(id);
    return this.prisma.insurance.update({
      where: { id },
      data: { ...dto, startDate: dto.startDate ? new Date(dto.startDate) : undefined, endDate: dto.endDate ? new Date(dto.endDate) : undefined },
    });
  }

  async remove(id: string) {
    await this.findById(id);
    await this.prisma.insurance.delete({ where: { id } });
    return { deleted: true };
  }
}
