import { Injectable, NestInterceptor, ExecutionContext, CallHandler } from '@nestjs/common';
import { Observable, tap } from 'rxjs';
import { PrismaService } from '../database/prisma.service';

@Injectable()
export class AuditLogInterceptor implements NestInterceptor {
  constructor(private prisma: PrismaService) {}

  intercept(context: ExecutionContext, next: CallHandler): Observable<any> {
    const request = context.switchToHttp().getRequest();
    const { method, url, user, ip } = request;
    const module = url.split('/')[2] || 'unknown';

    return next.handle().pipe(
      tap({
        next: () => {
          if (user && method !== 'GET') {
            this.prisma.auditLog.create({
              data: {
                userId: user.id,
                action: `${method} ${url}`,
                module,
                entityId: request.params?.id,
                ipAddress: ip,
              },
            }).catch(() => {});
          }
        },
        error: () => {},
      }),
    );
  }
}
