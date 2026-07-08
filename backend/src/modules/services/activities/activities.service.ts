import { Injectable, NotFoundException } from '@nestjs/common';
import { PrismaService } from '../../../core/database/prisma.service';
import { CreateActivityDto, UpdateActivityDto } from './dto/create-activity.dto';

@Injectable()
export class ActivitiesService {
  constructor(private prisma: PrismaService) {}

  async create(dto: CreateActivityDto) {
    return this.prisma.activity.create({
      data: { ...dto, date: new Date(dto.date), costPrice: dto.costPrice || 0, sellPrice: dto.sellPrice || 0 },
    });
  }

  async findByTrip(tripId: string) {
    return this.prisma.activity.findMany({ where: { tripId }, include: { supplier: { select: { id: true, name: true } } }, orderBy: { date: 'asc' } });
  }

  async findById(id: string) {
    const a = await this.prisma.activity.findUnique({ where: { id } });
    if (!a) throw new NotFoundException('Activity not found');
    return a;
  }

  async update(id: string, dto: UpdateActivityDto) {
    await this.findById(id);
    return this.prisma.activity.update({
      where: { id },
      data: { ...dto, date: dto.date ? new Date(dto.date) : undefined },
    });
  }

  async remove(id: string) {
    await this.findById(id);
    await this.prisma.activity.delete({ where: { id } });
    return { deleted: true };
  }
}
