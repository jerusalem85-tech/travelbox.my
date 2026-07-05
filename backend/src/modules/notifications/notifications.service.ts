import { Injectable, NotFoundException } from '@nestjs/common';
import { PrismaService } from '../../core/database/prisma.service';

@Injectable()
export class NotificationsService {
  constructor(private prisma: PrismaService) {}

  async create(data: any) {
    return this.prisma.notification.create({
      data: { ...data, readAt: data.read ? new Date() : undefined },
      include: { user: { select: { firstName: true, lastName: true } } },
    });
  }

  async findAll(query: any) {
    const { page = 1, limit = 20, userId, type, read } = query;
    const where: any = {};
    if (userId) where.userId = userId;
    if (type) where.type = type;
    if (read !== undefined) where.readAt = read === 'true' ? { not: null } : null;
    const [data, total] = await Promise.all([
      this.prisma.notification.findMany({ where, orderBy: { createdAt: 'desc' }, skip: (page - 1) * limit, take: limit }),
      this.prisma.notification.count({ where }),
    ]);
    return { data, total, page, limit, totalPages: Math.ceil(total / limit) };
  }

  async findById(id: string) {
    const notif = await this.prisma.notification.findUnique({ where: { id } });
    if (!notif) throw new NotFoundException('Notification not found');
    return notif;
  }

  async markRead(id: string) { return this.prisma.notification.update({ where: { id }, data: { readAt: new Date() } }); }
  async remove(id: string) { await this.prisma.notification.delete({ where: { id } }); return { deleted: true }; }
}
