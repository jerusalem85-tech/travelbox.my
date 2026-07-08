import { Controller, Get, Post, Patch, Delete, Body, Param, Query } from '@nestjs/common';
import { PaymentsService } from './payments.service';
import { CreatePaymentDto, UpdatePaymentDto } from './dto/create-payment.dto';
import { CurrentUser } from '../../common/decorators/current-user.decorator';

@Controller('payments')
export class PaymentsController {
  constructor(private payments: PaymentsService) {}

  @Post()
  create(@Body() dto: CreatePaymentDto, @CurrentUser('id') userId: string) {
    return this.payments.create(dto, userId);
  }

  @Get()
  findAll(
    @Query('tripId') tripId?: string,
    @Query('customerId') customerId?: string,
    @Query('supplierId') supplierId?: string,
  ) {
    return this.payments.findAll(tripId, customerId, supplierId);
  }

  @Get(':id')
  findById(@Param('id') id: string) {
    return this.payments.findById(id);
  }

  @Patch(':id')
  update(@Param('id') id: string, @Body() dto: UpdatePaymentDto) {
    return this.payments.update(id, dto);
  }

  @Delete(':id')
  remove(@Param('id') id: string) {
    return this.payments.remove(id);
  }

  @Get('trip/:tripId/balance')
  getTripBalance(@Param('tripId') tripId: string) {
    return this.payments.getTripBalance(tripId);
  }
}
