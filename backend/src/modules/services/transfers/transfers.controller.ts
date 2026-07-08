import { Controller, Get, Post, Patch, Delete, Body, Param } from '@nestjs/common';
import { TransfersService } from './transfers.service';
import { CreateTransferDto, UpdateTransferDto } from './dto/create-transfer.dto';

@Controller('services/transfers')
export class TransfersController {
  constructor(private transfers: TransfersService) {}

  @Post()
  create(@Body() dto: CreateTransferDto) { return this.transfers.create(dto); }

  @Get('trip/:tripId')
  findByTrip(@Param('tripId') tripId: string) { return this.transfers.findByTrip(tripId); }

  @Get(':id')
  findById(@Param('id') id: string) { return this.transfers.findById(id); }

  @Patch(':id')
  update(@Param('id') id: string, @Body() dto: UpdateTransferDto) { return this.transfers.update(id, dto); }

  @Delete(':id')
  remove(@Param('id') id: string) { return this.transfers.remove(id); }
}
