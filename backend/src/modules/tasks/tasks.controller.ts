import { Controller, Get, Post, Patch, Delete, Body, Param, Query } from '@nestjs/common';
import { TasksService } from './tasks.service';
import { CreateTaskDto, UpdateTaskDto } from './dto/create-task.dto';
import { CurrentUser } from '../../common/decorators/current-user.decorator';

@Controller('tasks')
export class TasksController {
  constructor(private tasks: TasksService) {}

  @Post()
  create(@Body() dto: CreateTaskDto, @CurrentUser('id') userId: string) {
    return this.tasks.create(dto, userId);
  }

  @Get()
  findAll(
    @Query('tripId') tripId?: string,
    @Query('status') status?: string,
    @Query('assignedToId') assignedToId?: string,
  ) {
    return this.tasks.findAll(tripId, status, assignedToId);
  }

  @Get(':id')
  findById(@Param('id') id: string) { return this.tasks.findById(id); }

  @Patch(':id')
  update(@Param('id') id: string, @Body() dto: UpdateTaskDto) {
    return this.tasks.update(id, dto);
  }

  @Delete(':id')
  remove(@Param('id') id: string) { return this.tasks.remove(id); }
}
