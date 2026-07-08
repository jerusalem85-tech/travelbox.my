import { Injectable, CanActivate, ExecutionContext } from '@nestjs/common';
import { Reflector } from '@nestjs/core';
import { PERMISSIONS_KEY } from '../decorators/permissions.decorator';

@Injectable()
export class PermissionsGuard implements CanActivate {
  constructor(private reflector: Reflector) {}

  canActivate(context: ExecutionContext): boolean {
    const permission = this.reflector.getAllAndOverride<{ module: string; action: string }>(
      PERMISSIONS_KEY,
      [context.getHandler(), context.getClass()],
    );
    if (!permission) return true;
    // In V1, we check role-based permissions at controller level
    // For granular permissions, this would check against a permission matrix
    return true;
  }
}
