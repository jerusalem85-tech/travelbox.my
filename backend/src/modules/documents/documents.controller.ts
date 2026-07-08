import { Controller, Get, Post, Patch, Delete, Body, Param, Query } from '@nestjs/common';
import { DocumentsService } from './documents.service';
import { CreateDocumentDto } from './dto/create-document.dto';
import { CurrentUser } from '../../common/decorators/current-user.decorator';
import { DocumentStatus } from '@prisma/client';

@Controller('documents')
export class DocumentsController {
  constructor(private docs: DocumentsService) {}

  @Post('generate/:tripId')
  generate(@Param('tripId') tripId: string, @Body() dto: CreateDocumentDto, @CurrentUser('id') userId: string) {
    return this.docs.generate(tripId, dto, userId);
  }

  @Get()
  findAll(
    @Query('tripId') tripId?: string,
    @Query('type') type?: string,
    @Query('status') status?: string,
  ) {
    return this.docs.findAll(tripId, type, status);
  }

  @Get(':id')
  findById(@Param('id') id: string) { return this.docs.findById(id); }

  @Patch(':id/status')
  updateStatus(@Param('id') id: string, @Body('status') status: DocumentStatus) {
    return this.docs.updateStatus(id, status);
  }

  @Delete(':id')
  remove(@Param('id') id: string) { return this.docs.remove(id); }
}
