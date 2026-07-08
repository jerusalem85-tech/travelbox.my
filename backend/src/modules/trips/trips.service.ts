import { Injectable, NotFoundException, BadRequestException } from '@nestjs/common';
import { PrismaService } from '../../core/database/prisma.service';
import { CreateTripDto, UpdateTripDto, ChangeStatusDto } from './dto/create-trip.dto';
import { TripStatus } from '@prisma/client';

const VALID_TRANSITIONS: Record<TripStatus, TripStatus[]> = {
  INQUIRY: ['QUOTATION', 'CANCELLED'],
  QUOTATION: ['PROVISIONAL', 'CANCELLED'],
  PROVISIONAL: ['CONFIRMED', 'CANCELLED'],
  CONFIRMED: ['IN_PROGRESS', 'CANCELLED'],
  IN_PROGRESS: ['COMPLETED', 'CANCELLED'],
  COMPLETED: ['CANCELLED'],
  CANCELLED: ['REFUNDED'],
  REFUNDED: [],
};

@Injectable()
export class TripsService {
  constructor(private prisma: PrismaService) {}

  async create(dto: CreateTripDto, userId: string) {
    const referenceNo = await this.generateReference();
    return this.prisma.trip.create({
      data: {
        ...dto,
        referenceNo,
        startDate: dto.startDate ? new Date(dto.startDate) : undefined,
        endDate: dto.endDate ? new Date(dto.endDate) : undefined,
        createdById: userId,
        tenantId: 'default',
      },
      include: {
        createdBy: { select: { id: true, firstName: true, lastName: true } },
        assignedTo: { select: { id: true, firstName: true, lastName: true } },
        customers: { include: { customer: true } },
        passengers: true,
      },
    });
  }

  async findAll() {
    return this.prisma.trip.findMany({
      where: { deletedAt: null },
      orderBy: { createdAt: 'desc' },
      include: {
        createdBy: { select: { id: true, firstName: true, lastName: true } },
        assignedTo: { select: { id: true, firstName: true, lastName: true } },
        customers: { include: { customer: { select: { id: true, firstName: true, lastName: true, email: true } } } },
        _count: { select: { flights: true, hotels: true, passengers: true, payments: true } },
      },
    });
  }

  async findById(id: string) {
    const trip = await this.prisma.trip.findUnique({
      where: { id },
      include: {
        createdBy: { select: { id: true, firstName: true, lastName: true, email: true } },
        assignedTo: { select: { id: true, firstName: true, lastName: true, email: true } },
        customers: { include: { customer: true } },
        passengers: true,
        flights: { include: { supplier: { select: { id: true, name: true } } } },
        hotels: { include: { supplier: { select: { id: true, name: true } } } },
        transfers: { include: { supplier: { select: { id: true, name: true } } } },
        visas: { include: { supplier: { select: { id: true, name: true } } } },
        insurances: { include: { supplier: { select: { id: true, name: true } } } },
        activities: { include: { supplier: { select: { id: true, name: true } } } },
        payments: true,
        invoices: true,
        documents: true,
        notes: { include: { author: { select: { id: true, firstName: true, lastName: true } } }, orderBy: { createdAt: 'desc' } },
        tasks: { include: { assignedTo: { select: { id: true, firstName: true, lastName: true } }, createdBy: { select: { id: true, firstName: true, lastName: true } } } },
        timelineEntries: { include: { user: { select: { id: true, firstName: true, lastName: true } } }, orderBy: { createdAt: 'desc' } },
        journalEntries: { include: { account: true } },
      },
    });
    if (!trip || trip.deletedAt) throw new NotFoundException('Trip not found');
    return trip;
  }

  async update(id: string, dto: UpdateTripDto) {
    await this.findById(id);
    return this.prisma.trip.update({
      where: { id },
      data: {
        ...dto,
        startDate: dto.startDate ? new Date(dto.startDate) : undefined,
        endDate: dto.endDate ? new Date(dto.endDate) : undefined,
      },
    });
  }

  async changeStatus(id: string, dto: ChangeStatusDto, userId: string) {
    const trip = await this.findById(id);
    const currentStatus = trip.status as TripStatus;
    const newStatus = dto.status;

    if (!VALID_TRANSITIONS[currentStatus]?.includes(newStatus)) {
      throw new BadRequestException(
        `Cannot transition from ${currentStatus} to ${newStatus}`,
      );
    }

    const updateData: any = { status: newStatus };
    if (newStatus === 'CONFIRMED') updateData.confirmedDate = new Date();
    if (newStatus === 'COMPLETED') updateData.completedDate = new Date();
    if (newStatus === 'CANCELLED') updateData.cancelledDate = new Date();

    const updated = await this.prisma.trip.update({ where: { id }, data: updateData });

    await this.prisma.timelineEntry.create({
      data: {
        tripId: id,
        action: 'status_change',
        description: `Status changed from ${currentStatus} to ${newStatus}`,
        userId,
      },
    });

    return updated;
  }

  async remove(id: string) {
    await this.findById(id);
    await this.prisma.trip.update({
      where: { id },
      data: { deletedAt: new Date(), isActive: false },
    });
    return { deleted: true };
  }

  async addCustomer(tripId: string, customerId: string, isPrimary = false, role = 'booker') {
    await this.findById(tripId);
    return this.prisma.tripCustomer.create({
      data: { tripId, customerId, isPrimary, role },
      include: { customer: true },
    });
  }

  async getProfitSummary(id: string) {
    const trip = await this.prisma.trip.findUnique({
      where: { id },
      select: {
        id: true,
        referenceNo: true,
        totalRevenue: true,
        totalCost: true,
        totalProfit: true,
        profitMargin: true,
      },
    });
    if (!trip) throw new NotFoundException('Trip not found');
    return trip;
  }

  private async generateReference(): Promise<string> {
    const year = new Date().getFullYear();
    const lastTrip = await this.prisma.trip.findFirst({
      where: { referenceNo: { startsWith: `TB-${year}-` } },
      orderBy: { referenceNo: 'desc' },
    });

    let sequence = 1;
    if (lastTrip) {
      const parts = lastTrip.referenceNo.split('-');
      sequence = parseInt(parts[2], 10) + 1;
    }

    return `TB-${year}-${String(sequence).padStart(4, '0')}`;
  }
}
