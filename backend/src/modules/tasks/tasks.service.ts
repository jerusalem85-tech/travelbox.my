import { Injectable, NotFoundException } from '@nestjs/common';
import { PrismaService } from '../../core/database/prisma.service';

@Injectable()
export class TasksService {
  constructor(private prisma: PrismaService) {}

  async create(data: any) { return this.prisma.task.create({ data, include: { trip: true, assignedUser: true } }); }

  async findAll(query: any) {
    const { page = 1, limit = 20, tripId, assigneeId, status, priority, dueDate } = query;
    const where: any = {};
    if (tripId) where.tripId = tripId;
    if (assigneeId) where.assigneeId = assigneeId;
    if (status) where.status = status;
    if (priority) where.priority = priority;
    if (dueDate) where.dueDate = { lte: new Date(dueDate) };
    const [data, total] = await Promise.all([
      this.prisma.task.findMany({ where, orderBy: [{ priority: 'desc' }, { dueDate: 'asc' }], skip: (page - 1) * limit, take: limit, include: { assignedUser: { select: { firstName: true, lastName: true } }, trip: { select: { tripNumber: true } } } }),
      this.prisma.task.count({ where }),
    ]);
    return { data, total, page, limit, totalPages: Math.ceil(total / limit) };
  }

  async findById(id: string) {
    const task = await this.prisma.task.findUnique({ where: { id }, include: { trip: true, assignedUser: true } });
    if (!task) throw new NotFoundException('Task not found');
    return task;
  }

  async update(id: string, data: any) { return this.prisma.task.update({ where: { id }, data, include: { assignedUser: true, trip: true } }); }
  async remove(id: string) { await this.prisma.task.delete({ where: { id } }); return { deleted: true }; }
}
