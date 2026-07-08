import { Injectable, NotFoundException } from '@nestjs/common';
import { PrismaService } from '../../core/database/prisma.service';
import { CreateTaskDto, UpdateTaskDto } from './dto/create-task.dto';
import { TaskStatus } from '@prisma/client';

@Injectable()
export class TasksService {
  constructor(private prisma: PrismaService) {}

  async create(dto: CreateTaskDto, userId: string) {
    return this.prisma.task.create({
      data: {
        tripId: dto.tripId,
        title: dto.title,
        description: dto.description,
        priority: dto.priority || 'MEDIUM',
        assignedToId: dto.assignedToId,
        createdById: userId,
        dueDate: dto.dueDate ? new Date(dto.dueDate) : undefined,
      },
      include: {
        assignedTo: { select: { id: true, firstName: true, lastName: true } },
        createdBy: { select: { id: true, firstName: true, lastName: true } },
      },
    });
  }

  async findAll(tripId?: string, status?: string, assignedToId?: string) {
    return this.prisma.task.findMany({
      where: {
        ...(tripId ? { tripId } : {}),
        ...(status ? { status: status as TaskStatus } : {}),
        ...(assignedToId ? { assignedToId } : {}),
      },
      include: {
        trip: { select: { id: true, referenceNo: true, name: true } },
        assignedTo: { select: { id: true, firstName: true, lastName: true } },
        createdBy: { select: { id: true, firstName: true, lastName: true } },
      },
      orderBy: [{ priority: 'desc' }, { dueDate: 'asc' }],
    });
  }

  async findById(id: string) {
    const task = await this.prisma.task.findUnique({
      where: { id },
      include: {
        trip: { select: { id: true, referenceNo: true, name: true } },
        assignedTo: { select: { id: true, firstName: true, lastName: true } },
        createdBy: { select: { id: true, firstName: true, lastName: true } },
      },
    });
    if (!task) throw new NotFoundException('Task not found');
    return task;
  }

  async update(id: string, dto: UpdateTaskDto) {
    await this.findById(id);
    const data: any = { ...dto };
    if (dto.dueDate) data.dueDate = new Date(dto.dueDate);
    if (dto.status === 'COMPLETED') data.completedAt = new Date();
    return this.prisma.task.update({ where: { id }, data });
  }

  async remove(id: string) {
    await this.findById(id);
    await this.prisma.task.delete({ where: { id } });
    return { deleted: true };
  }
}
