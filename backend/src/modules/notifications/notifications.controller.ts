import { Controller, Get, Post, Put, Delete, Body, Param, Query, UseGuards } from '@nestjs/common';
import { ApiTags, ApiBearerAuth, ApiOperation } from '@nestjs/swagger';
import { AuthGuard } from '@nestjs/passport';
import { NotificationsService } from './notifications.service';

@ApiTags('Notifications')
@ApiBearerAuth()
@UseGuards(AuthGuard('jwt'))
@Controller('notifications')
export class NotificationsController {
  constructor(private service: NotificationsService) {}

  @Post() create(@Body() data: any) { return this.service.create(data); }
  @Get() findAll(@Query() query: any) { return this.service.findAll(query); }
  @Get(':id') findById(@Param('id') id: string) { return this.service.findById(id); }
  @Put(':id/read') markRead(@Param('id') id: string) { return this.service.markRead(id); }
  @Delete(':id') remove(@Param('id') id: string) { return this.service.remove(id); }
}
