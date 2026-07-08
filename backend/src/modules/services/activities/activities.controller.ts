import { Controller, Get, Post, Patch, Delete, Body, Param } from '@nestjs/common';
import { ActivitiesService } from './activities.service';
import { CreateActivityDto, UpdateActivityDto } from './dto/create-activity.dto';

@Controller('services/activities')
export class ActivitiesController {
  constructor(private activities: ActivitiesService) {}

  @Post()
  create(@Body() dto: CreateActivityDto) { return this.activities.create(dto); }

  @Get('trip/:tripId')
  findByTrip(@Param('tripId') tripId: string) { return this.activities.findByTrip(tripId); }

  @Get(':id')
  findById(@Param('id') id: string) { return this.activities.findById(id); }

  @Patch(':id')
  update(@Param('id') id: string, @Body() dto: UpdateActivityDto) { return this.activities.update(id, dto); }

  @Delete(':id')
  remove(@Param('id') id: string) { return this.activities.remove(id); }
}
