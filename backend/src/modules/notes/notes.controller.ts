import { Controller, Get, Post, Patch, Delete, Body, Param } from '@nestjs/common';
import { NotesService } from './notes.service';
import { CreateNoteDto, UpdateNoteDto } from './dto/create-note.dto';
import { CurrentUser } from '../../common/decorators/current-user.decorator';

@Controller('notes')
export class NotesController {
  constructor(private notes: NotesService) {}

  @Post()
  create(@Body() dto: CreateNoteDto, @CurrentUser('id') userId: string) {
    return this.notes.create(dto, userId);
  }

  @Get('trip/:tripId')
  findByTrip(@Param('tripId') tripId: string) {
    return this.notes.findByTrip(tripId);
  }

  @Patch(':id')
  update(@Param('id') id: string, @Body() dto: UpdateNoteDto) {
    return this.notes.update(id, dto);
  }

  @Delete(':id')
  remove(@Param('id') id: string) {
    return this.notes.remove(id);
  }
}
