import { Injectable, NotFoundException, ConflictException } from '@nestjs/common';
import { PrismaService } from '../../core/database/prisma.service';

@Injectable()
export class AdminService {
  constructor(private prisma: PrismaService) {}

  async createUser(data: any) {
    const existing = await this.prisma.user.findUnique({ where: { email: data.email } });
    if (existing) throw new ConflictException('Email already in use');
    const { bcrypt } = require('bcryptjs');
    const hashedPassword = await bcrypt.hash(data.password, 10);
    return this.prisma.user.create({ data: { ...data, password: hashedPassword }, select: { id: true, email: true, firstName: true, lastName: true, role: true, isActive: true, createdAt: true } });
  }

  async findAllUsers(query: any) {
    const { page = 1, limit = 20, role, isActive } = query;
    const where: any = {};
    if (role) where.role = role;
    if (isActive !== undefined) where.isActive = isActive === 'true';
    const [data, total] = await Promise.all([
      this.prisma.user.findMany({ where, orderBy: { createdAt: 'desc' }, skip: (page - 1) * limit, take: limit, select: { id: true, email: true, firstName: true, lastName: true, role: true, isActive: true, createdAt: true } }),
      this.prisma.user.count({ where }),
    ]);
    return { data, total, page, limit, totalPages: Math.ceil(total / limit) };
  }

  async findUserById(id: string) {
    const user = await this.prisma.user.findUnique({ where: { id }, select: { id: true, email: true, firstName: true, lastName: true, role: true, isActive: true, createdAt: true, updatedAt: true } });
    if (!user) throw new NotFoundException('User not found');
    return user;
  }

  async updateUser(id: string, data: any) {
    if (data.password) {
      const { bcrypt } = require('bcryptjs');
      data.password = await bcrypt.hash(data.password, 10);
    }
    return this.prisma.user.update({ where: { id }, data, select: { id: true, email: true, firstName: true, lastName: true, role: true, isActive: true, createdAt: true, updatedAt: true } });
  }

  async getAuditLogs(query: any) {
    const { page = 1, limit = 50, userId, action, entity } = query;
    const where: any = {};
    if (userId) where.userId = userId;
    if (action) where.action = action;
    if (entity) where.entity = entity;
    const [data, total] = await Promise.all([
      this.prisma.auditLog.findMany({ where, orderBy: { createdAt: 'desc' }, skip: (page - 1) * limit, take: limit, include: { user: { select: { firstName: true, lastName: true, email: true } } } }),
      this.prisma.auditLog.count({ where }),
    ]);
    return { data, total, page, limit, totalPages: Math.ceil(total / limit) };
  }

  async getSettings() {
    const settings = await this.prisma.setting.findMany();
    return settings.reduce((acc: any, s) => { acc[s.key] = s.value; return acc; }, {});
  }

  async updateSettings(data: Record<string, string>) {
    for (const [key, value] of Object.entries(data)) {
      await this.prisma.setting.upsert({ where: { key }, update: { value }, create: { key, value } });
    }
    return this.getSettings();
  }
}
