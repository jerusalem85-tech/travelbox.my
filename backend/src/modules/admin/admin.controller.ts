import { Controller, Get, Post, Put, Body, Param, Query, UseGuards } from '@nestjs/common';
import { ApiTags, ApiBearerAuth, ApiOperation } from '@nestjs/swagger';
import { AuthGuard } from '@nestjs/passport';
import { RolesGuard } from '../../core/guards/roles.guard';
import { Roles } from '../../core/decorators/roles.decorator';
import { AdminService } from './admin.service';

@ApiTags('Admin')
@ApiBearerAuth()
@UseGuards(AuthGuard('jwt'), RolesGuard)
@Roles('ADMIN')
@Controller('admin')
export class AdminController {
  constructor(private service: AdminService) {}

  @Post('users') createUser(@Body() data: any) { return this.service.createUser(data); }
  @Get('users') findAllUsers(@Query() query: any) { return this.service.findAllUsers(query); }
  @Get('users/:id') findUserById(@Param('id') id: string) { return this.service.findUserById(id); }
  @Put('users/:id') updateUser(@Param('id') id: string, @Body() data: any) { return this.service.updateUser(id, data); }
  @Get('audit-logs') getAuditLogs(@Query() query: any) { return this.service.getAuditLogs(query); }
  @Get('settings') getSettings() { return this.service.getSettings(); }
  @Put('settings') updateSettings(@Body() data: Record<string, string>) { return this.service.updateSettings(data); }
}
