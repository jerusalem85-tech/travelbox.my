import { Controller, Get, Post, Patch, Delete, Body, Param, Query } from '@nestjs/common';
import { InvoicesService } from './invoices.service';
import { CreateInvoiceDto, UpdateInvoiceDto } from './dto/create-invoice.dto';

@Controller('invoices')
export class InvoicesController {
  constructor(private invoices: InvoicesService) {}

  @Post()
  create(@Body() dto: CreateInvoiceDto) { return this.invoices.create(dto); }

  @Get()
  findAll(@Query('tripId') tripId?: string, @Query('status') status?: string) {
    return this.invoices.findAll(tripId, status);
  }

  @Get(':id')
  findById(@Param('id') id: string) { return this.invoices.findById(id); }

  @Patch(':id')
  update(@Param('id') id: string, @Body() dto: UpdateInvoiceDto) { return this.invoices.update(id, dto); }

  @Delete(':id')
  remove(@Param('id') id: string) { return this.invoices.remove(id); }
}
