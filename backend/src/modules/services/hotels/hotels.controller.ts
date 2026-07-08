import { Controller, Get, Post, Patch, Delete, Body, Param } from '@nestjs/common';
import { HotelsService } from './hotels.service';
import { CreateHotelDto, UpdateHotelDto } from './dto/create-hotel.dto';

@Controller('services/hotels')
export class HotelsController {
  constructor(private hotels: HotelsService) {}

  @Post()
  create(@Body() dto: CreateHotelDto) { return this.hotels.create(dto); }

  @Get('trip/:tripId')
  findByTrip(@Param('tripId') tripId: string) { return this.hotels.findByTrip(tripId); }

  @Get(':id')
  findById(@Param('id') id: string) { return this.hotels.findById(id); }

  @Patch(':id')
  update(@Param('id') id: string, @Body() dto: UpdateHotelDto) { return this.hotels.update(id, dto); }

  @Delete(':id')
  remove(@Param('id') id: string) { return this.hotels.remove(id); }
}
