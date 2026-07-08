import { Injectable, UnauthorizedException, ConflictException } from '@nestjs/common';
import { JwtService } from '@nestjs/jwt';
import * as bcrypt from 'bcryptjs';
import { PrismaService } from '../../core/database/prisma.service';
import { LoginDto } from './dto/login.dto';
import { RegisterDto } from './dto/register.dto';

@Injectable()
export class AuthService {
  constructor(
    private prisma: PrismaService,
    private jwt: JwtService,
  ) {}

  async login(dto: LoginDto) {
    const user = await this.prisma.user.findUnique({ where: { email: dto.email } });
    if (!user || !(await bcrypt.compare(dto.password, user.password))) {
      throw new UnauthorizedException('Invalid email or password');
    }
    if (!user.isActive) throw new UnauthorizedException('Account is disabled');

    await this.prisma.user.update({
      where: { id: user.id },
      data: { lastLoginAt: new Date() },
    });

    return this.generateTokens(user);
  }

  async register(dto: RegisterDto) {
    const existing = await this.prisma.user.findUnique({ where: { email: dto.email } });
    if (existing) throw new ConflictException('Email already registered');

    const hashedPassword = await bcrypt.hash(dto.password, 12);
    const user = await this.prisma.user.create({
      data: {
        email: dto.email,
        password: hashedPassword,
        firstName: dto.firstName,
        lastName: dto.lastName,
        phone: dto.phone,
        role: dto.role || 'SALES_AGENT',
        tenantId: dto.role === 'SUPER_ADMIN'
          ? (await this.ensureDefaultTenant()).id
          : (await this.ensureDefaultTenant()).id,
      },
    });

    return this.generateTokens(user);
  }

  async refresh(refreshToken: string) {
    const user = await this.prisma.user.findFirst({
      where: { refreshToken, isActive: true },
    });
    if (!user) throw new UnauthorizedException('Invalid refresh token');

    return this.generateTokens(user);
  }

  async logout(userId: string) {
    await this.prisma.user.update({
      where: { id: userId },
      data: { refreshToken: null },
    });
    return { message: 'Logged out successfully' };
  }

  private async generateTokens(user: any) {
    const payload = { sub: user.id, email: user.email, role: user.role, tenantId: user.tenantId };
    const refreshToken = this.jwt.sign(payload, { expiresIn: '7d' });

    await this.prisma.user.update({
      where: { id: user.id },
      data: { refreshToken },
    });

    return {
      accessToken: this.jwt.sign(payload),
      refreshToken,
      user: {
        id: user.id,
        email: user.email,
        firstName: user.firstName,
        lastName: user.lastName,
        role: user.role,
      },
    };
  }

  private async ensureDefaultTenant() {
    let tenant = await this.prisma.tenant.findFirst({ where: { slug: 'default' } });
    if (!tenant) {
      tenant = await this.prisma.tenant.create({
        data: {
          name: 'Default Company',
          slug: 'default',
          email: 'admin@travelbox.my',
        },
      });
    }
    return tenant;
  }
}
