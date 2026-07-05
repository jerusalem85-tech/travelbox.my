import { Injectable, UnauthorizedException, ConflictException, Logger } from '@nestjs/common';
import { JwtService } from '@nestjs/jwt';
import * as bcrypt from 'bcryptjs';
import { PrismaService } from '../../core/database/prisma.service';
import type { JwtPayload } from '../../shared/interfaces';

@Injectable()
export class AuthService {
  private readonly logger = new Logger(AuthService.name);

  constructor(
    private prisma: PrismaService,
    private jwtService: JwtService,
  ) {}

  async login(email: string, password: string) {
    const user = await this.prisma.user.findUnique({ where: { email } });
    if (!user || !user.isActive) throw new UnauthorizedException('Invalid credentials');

    const valid = await bcrypt.compare(password, user.password);
    if (!valid) throw new UnauthorizedException('Invalid credentials');

    await this.prisma.user.update({ where: { id: user.id }, data: { lastLoginAt: new Date() } });

    const payload: JwtPayload = { sub: user.id, email: user.email, role: user.role };
    const refreshToken = this.jwtService.sign(payload, {
      secret: process.env.JWT_REFRESH_SECRET || 'travelbox-refresh-secret',
      expiresIn: process.env.JWT_REFRESH_EXPIRATION || '7d',
    });

    await this.prisma.user.update({ where: { id: user.id }, data: { refreshToken } });

    return {
      access_token: this.jwtService.sign(payload),
      refresh_token: refreshToken,
      token_type: 'Bearer',
      expires_in: 900,
      user: { id: user.id, email: user.email, role: user.role, firstName: user.firstName, lastName: user.lastName },
    };
  }

  async refresh(refreshToken: string) {
    try {
      const payload = this.jwtService.verify<JwtPayload>(refreshToken, {
        secret: process.env.JWT_REFRESH_SECRET || 'travelbox-refresh-secret',
      });
      const user = await this.prisma.user.findUnique({ where: { id: payload.sub } });
      if (!user || !user.isActive || user.refreshToken !== refreshToken) {
        throw new UnauthorizedException('Invalid refresh token');
      }
      const newPayload: JwtPayload = { sub: user.id, email: user.email, role: user.role };
      const newRefreshToken = this.jwtService.sign(newPayload, {
        secret: process.env.JWT_REFRESH_SECRET || 'travelbox-refresh-secret',
        expiresIn: process.env.JWT_REFRESH_EXPIRATION || '7d',
      });
      await this.prisma.user.update({ where: { id: user.id }, data: { refreshToken: newRefreshToken } });
      return {
        access_token: this.jwtService.sign(newPayload),
        refresh_token: newRefreshToken,
        token_type: 'Bearer',
        expires_in: 900,
      };
    } catch {
      throw new UnauthorizedException('Invalid refresh token');
    }
  }

  async register(data: { email: string; password: string; firstName: string; lastName: string; role?: string }) {
    const exists = await this.prisma.user.findUnique({ where: { email: data.email } });
    if (exists) throw new ConflictException('Email already registered');

    const hashedPassword = await bcrypt.hash(data.password, 12);
    const user = await this.prisma.user.create({
      data: {
        email: data.email,
        password: hashedPassword,
        firstName: data.firstName,
        lastName: data.lastName,
        role: (data.role as any) || 'SALES',
      },
    });

    return { id: user.id, email: user.email, role: user.role, firstName: user.firstName, lastName: user.lastName };
  }

  async me(userId: string) {
    const user = await this.prisma.user.findUnique({
      where: { id: userId },
      select: { id: true, email: true, firstName: true, lastName: true, phone: true, avatar: true, role: true, isActive: true, lastLoginAt: true, createdAt: true },
    });
    return user;
  }
}
