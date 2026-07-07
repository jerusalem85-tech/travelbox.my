import { Injectable, NotFoundException } from '@nestjs/common';
import { PrismaService } from '../../core/database/prisma.service';
import { CreateBookingDto } from './dto/create-booking.dto';
import { UpdateBookingDto } from './dto/update-booking.dto';

@Injectable()
export class BookingsService {
  constructor(private prisma: PrismaService) {}

  async create(dto: CreateBookingDto, userId: string) {
    return this.prisma.booking.create({
      data: { ...dto, createdById: userId } as any,
      include: { client: true, trip: true, createdBy: true },
    });
  }

  async findAll() {
    return this.prisma.booking.findMany({
      orderBy: { createdAt: 'desc' },
      include: { client: true, trip: true, createdBy: { select: { id: true, firstName: true, lastName: true } } },
    });
  }

  async findOne(id: string) {
    const booking = await this.prisma.booking.findUnique({
      where: { id },
      include: { client: true, trip: true, createdBy: { select: { id: true, firstName: true, lastName: true } } },
    });
    if (!booking) throw new NotFoundException('Booking not found');
    return booking;
  }

  async update(id: string, dto: UpdateBookingDto) {
    await this.findOne(id);
    return this.prisma.booking.update({
      where: { id },
      data: dto as any,
      include: { client: true, trip: true },
    });
  }

  async remove(id: string) {
    await this.findOne(id);
    await this.prisma.booking.delete({ where: { id } });
    return { deleted: true };
  }
}
