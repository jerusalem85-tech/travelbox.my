import { Injectable, NotFoundException, Logger } from '@nestjs/common';
import { PrismaService } from '../../core/database/prisma.service';
import { SmartEngineService } from '../smart-engine/smart-engine.service';
import { CreateTripDto, UpdateTripDto, QueryTripDto, CreateServiceDto, UpdateServiceDto } from './trip.dto';

@Injectable()
export class TripsService {
  private readonly logger = new Logger(TripsService.name);

  constructor(
    private prisma: PrismaService,
    private smartEngine: SmartEngineService,
  ) {}

  async create(dto: CreateTripDto, userId: string) {
    const tripNumber = await this.generateTripNumber();
    const trip = await this.prisma.trip.create({
      data: {
        tripNumber,
        name: dto.name,
        customerId: dto.customerId,
        assignedToId: dto.assignedToId,
        createdById: userId,
        source: dto.source,
        startDate: dto.startDate ? new Date(dto.startDate) : null,
        endDate: dto.endDate ? new Date(dto.endDate) : null,
        destination: dto.destination,
        currency: dto.currency || 'USD',
        notes: dto.notes,
        tags: dto.tags ? (typeof dto.tags === 'string' ? dto.tags : JSON.stringify(dto.tags)) : '[]',
      },
      include: { customer: true, assignedTo: true, createdBy: true },
    });

    this.updateDuration(trip.id, trip.startDate, trip.endDate);
    return trip;
  }

  async findAll(query: QueryTripDto) {
    const { page = 1, limit = 10, search, status, destination, startDate, endDate, customerId, sortBy = 'createdAt', sortOrder = 'desc' } = query;
    const where: any = {};

    if (search) {
      where.OR = [
        { tripNumber: { contains: search } },
        { destination: { contains: search } },
        { customer: { OR: [
          { firstName: { contains: search } },
          { lastName: { contains: search } },
          { companyName: { contains: search } },
        ]}},
      ];
    }
    if (status) where.status = status;
    if (destination) where.destination = { contains: destination };
    if (startDate) where.startDate = { gte: new Date(startDate) };
    if (endDate) where.endDate = { lte: new Date(endDate) };
    if (customerId) where.customerId = customerId;

    const [data, total] = await Promise.all([
      this.prisma.trip.findMany({
        where,
        include: { customer: true, assignedTo: true, passengers: true, services: true, _count: { select: { tasks: true, documents: true, payments: true } } },
        orderBy: { [sortBy]: sortOrder },
        skip: (page - 1) * limit,
        take: limit,
      }),
      this.prisma.trip.count({ where }),
    ]);

    return { data, total, page, limit, totalPages: Math.ceil(total / limit) };
  }

  async findById(id: string) {
    const trip = await this.prisma.trip.findUnique({
      where: { id },
      include: {
        customer: true,
        assignedTo: true,
        createdBy: true,
        passengers: { orderBy: { createdAt: 'asc' } },
        services: {
          include: {
            supplier: true,
            flight: { include: { passengers: true } },
            hotel: { include: { guests: true } },
            transfer: true,
            tour: true,
            visa: { include: { passengers: true } },
            insurance: true,
            carRental: true,
          },
          orderBy: { sortOrder: 'asc' },
        },
        payments: true,
        documents: true,
        tasks: { orderBy: { createdAt: 'desc' } },
        timeline: { orderBy: { date: 'asc' } },
      },
    });
    if (!trip) throw new NotFoundException('Trip not found');
    return trip;
  }

  async update(id: string, dto: UpdateTripDto) {
    const trip = await this.prisma.trip.findUnique({ where: { id } });
    if (!trip) throw new NotFoundException('Trip not found');

    const data: any = {};
    if (dto.name !== undefined) data.name = dto.name;
    if (dto.status !== undefined) data.status = dto.status;
    if (dto.customerId !== undefined) data.customerId = dto.customerId;
    if (dto.assignedToId !== undefined) data.assignedToId = dto.assignedToId;
    if (dto.source !== undefined) data.source = dto.source;
    if (dto.destination !== undefined) data.destination = dto.destination;
    if (dto.currency !== undefined) data.currency = dto.currency;
    if (dto.notes !== undefined) data.notes = dto.notes;
    if (dto.internalNotes !== undefined) data.internalNotes = dto.internalNotes;
    if (dto.tags !== undefined) data.tags = typeof dto.tags === 'string' ? dto.tags : JSON.stringify(dto.tags);
    if (dto.startDate !== undefined) data.startDate = dto.startDate ? new Date(dto.startDate) : null;
    if (dto.endDate !== undefined) data.endDate = dto.endDate ? new Date(dto.endDate) : null;

    const updated = await this.prisma.trip.update({ where: { id }, data, include: { customer: true, assignedTo: true } });

    if (dto.startDate || dto.endDate) {
      this.updateDuration(id, updated.startDate, updated.endDate);
    }

    await this.recalculateFinances(id);
    return updated;
  }

  async remove(id: string) {
    const trip = await this.prisma.trip.findUnique({ where: { id } });
    if (!trip) throw new NotFoundException('Trip not found');
    await this.prisma.trip.delete({ where: { id } });
    return { deleted: true };
  }

  async updateDuration(tripId: string, startDate: Date | null, endDate: Date | null) {
    if (startDate && endDate) {
      const duration = Math.ceil((new Date(endDate).getTime() - new Date(startDate).getTime()) / (1000 * 3600 * 24)) + 1;
      await this.prisma.trip.update({ where: { id: tripId }, data: { duration: Math.max(1, duration) } });
    }
  }

  async recalculateFinances(tripId: string) {
    const services = await this.prisma.service.findMany({ where: { tripId } });
    const totalCost = services.reduce((sum, s) => sum + s.costPrice, 0);
    const totalSelling = services.reduce((sum, s) => sum + s.sellingPrice, 0);
    const totalProfit = services.reduce((sum, s) => sum + s.profit, 0);
    const totalCommission = services.reduce((sum, s) => sum + s.commissionAmount, 0);
    const margin = totalSelling > 0 ? (totalProfit / totalSelling) * 100 : 0;

    await this.prisma.trip.update({
      where: { id: tripId },
      data: { totalCost, totalSelling, totalProfit, totalCommission, margin },
    });
  }

  async updateTimeline(tripId: string) {
    const services = await this.prisma.service.findMany({
      where: { tripId },
      include: { flight: true, hotel: true, transfer: true, tour: true },
      orderBy: { sortOrder: 'asc' },
    });

    await this.prisma.timelineEntry.deleteMany({ where: { tripId } });

    const entries: any[] = [];
    for (const service of services) {
      if (service.flight) {
        if (service.flight.departureTime) {
          entries.push({ tripId, date: service.flight.departureTime, title: `Flight: ${service.flight.flightNumber || ''}`, subtitle: `${service.flight.departureAirport || ''} → ${service.flight.arrivalAirport || ''}`, icon: 'flight', type: 'FLIGHT', serviceId: service.id, sortOrder: service.sortOrder });
        }
      } else if (service.hotel) {
        if (service.hotel.checkIn) {
          entries.push({ tripId, date: service.hotel.checkIn, title: `Check-in: ${service.hotel.hotelName || 'Hotel'}`, subtitle: `${service.hotel.roomType || ''} - ${service.hotel.boardType || ''}`, icon: 'hotel', type: 'HOTEL', serviceId: service.id, sortOrder: service.sortOrder });
        }
      } else if (service.transfer) {
        if (service.transfer.pickupTime) {
          entries.push({ tripId, date: service.transfer.pickupTime, title: `Transfer: ${service.transfer.pickupLocation || ''} → ${service.transfer.dropoffLocation || ''}`, subtitle: service.transfer.vehicleType || '', icon: 'transfer', type: 'TRANSFER', serviceId: service.id, sortOrder: service.sortOrder });
        }
      } else if (service.tour) {
        if (service.tour.startTime) {
          entries.push({ tripId, date: service.tour.startTime, title: `Tour: ${service.tour.tourName || ''}`, subtitle: service.tour.duration || '', icon: 'tour', type: 'TOUR', serviceId: service.id, sortOrder: service.sortOrder });
        }
      } else if (service.startDate) {
        entries.push({ tripId, date: service.startDate, title: service.description || service.type, subtitle: '', icon: service.type.toLowerCase(), type: service.type, serviceId: service.id, sortOrder: service.sortOrder });
      }
    }

    if (entries.length > 0) {
      await this.prisma.timelineEntry.createMany({ data: entries });
    }
  }

  private async generateTripNumber(): Promise<string> {
    const year = new Date().getFullYear().toString().slice(-2);
    const count = await this.prisma.trip.count();
    const seq = (count + 1).toString().padStart(5, '0');
    const tripNumber = `TB-${year}-${seq}`;

    const exists = await this.prisma.trip.findUnique({ where: { tripNumber } });
    if (exists) return this.generateTripNumber();
    return tripNumber;
  }

  // --- Service Management ---

  async findServicesByTrip(tripId: string) {
    return this.prisma.service.findMany({
      where: { tripId },
      include: {
        supplier: true,
        flight: true,
        hotel: { include: { guests: true } },
        transfer: true,
        tour: true,
        cruise: true,
        visa: true,
        insurance: true,
        carRental: true,
        train: true,
      },
      orderBy: { sortOrder: 'asc' },
    });
  }

  async createService(tripId: string, dto: CreateServiceDto) {
    const count = await this.prisma.service.count({ where: { tripId } });

    const service = await this.prisma.service.create({
      data: {
        tripId,
        type: dto.type as any,
        sortOrder: dto.sortOrder ?? count,
        supplierId: dto.supplierId,
        description: dto.description,
        startDate: dto.startDate ? new Date(dto.startDate) : null,
        endDate: dto.endDate ? new Date(dto.endDate) : null,
        costPrice: dto.costPrice ?? 0,
        sellingPrice: dto.sellingPrice ?? 0,
        profit: (dto.sellingPrice ?? 0) - (dto.costPrice ?? 0),
        currency: dto.currency ?? 'USD',
        status: (dto.status ?? 'PENDING') as any,
        notes: dto.notes,
        isOptional: dto.isOptional ?? false,
      },
    });

    await this.recalculateFinances(tripId);
    return this.prisma.service.findUnique({
      where: { id: service.id },
      include: { supplier: true },
    });
  }

  async updateService(serviceId: string, dto: UpdateServiceDto) {
    const data: any = {};
    if (dto.supplierId !== undefined) data.supplierId = dto.supplierId;
    if (dto.description !== undefined) data.description = dto.description;
    if (dto.type !== undefined) data.type = dto.type as any;
    if (dto.startDate !== undefined) data.startDate = dto.startDate ? new Date(dto.startDate) : null;
    if (dto.endDate !== undefined) data.endDate = dto.endDate ? new Date(dto.endDate) : null;
    if (dto.costPrice !== undefined) data.costPrice = dto.costPrice;
    if (dto.sellingPrice !== undefined) data.sellingPrice = dto.sellingPrice;
    if (dto.profit !== undefined) data.profit = dto.profit;
    if (dto.currency !== undefined) data.currency = dto.currency;
    if (dto.status !== undefined) data.status = dto.status as any;
    if (dto.notes !== undefined) data.notes = dto.notes;
    if (dto.isOptional !== undefined) data.isOptional = dto.isOptional;
    if (dto.sortOrder !== undefined) data.sortOrder = dto.sortOrder;

    if (dto.costPrice !== undefined || dto.sellingPrice !== undefined) {
      const current = await this.prisma.service.findUnique({ where: { id: serviceId } });
      if (current) {
        data.profit = (dto.sellingPrice ?? current.sellingPrice) - (dto.costPrice ?? current.costPrice);
      }
    }

    const service = await this.prisma.service.update({
      where: { id: serviceId },
      data,
    });

    await this.recalculateFinances(service.tripId);
    return service;
  }

  async removeService(tripId: string, serviceId: string) {
    await this.prisma.service.delete({ where: { id: serviceId } });
    await this.recalculateFinances(tripId);
    return { deleted: true };
  }

  async reorderServices(tripId: string, serviceIds: string[]) {
    const updates = serviceIds.map((id, index) =>
      this.prisma.service.update({
        where: { id },
        data: { sortOrder: index, previousId: index > 0 ? serviceIds[index - 1] : null, nextId: index < serviceIds.length - 1 ? serviceIds[index + 1] : null },
      }),
    );
    await this.prisma.$transaction(updates);
    return this.findServicesByTrip(tripId);
  }
}
