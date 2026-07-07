import { Controller, Get, Post, Body, Patch, Param, Delete, UseGuards, Query } from '@nestjs/common';
import { AuthGuard } from '@nestjs/passport';
import { InvoicesService } from './invoices.service';
import { CreateInvoiceDto } from './dto/create-invoice.dto';
import { UpdateInvoiceDto } from './dto/update-invoice.dto';
import { CurrentUser } from '../../core/decorators/current-user.decorator';

@UseGuards(AuthGuard('jwt'))
@Controller('invoices')
export class InvoicesController {
  constructor(private readonly invoicesService: InvoicesService) {}

  @Post()
  create(@Body() dto: CreateInvoiceDto, @CurrentUser('id') userId: string) {
    return this.invoicesService.create(dto, userId);
  }

  @Get()
  findAll() {
    return this.invoicesService.findAll();
  }

  @Get(':id')
  findOne(@Param('id') id: string) {
    return this.invoicesService.findOne(id);
  }

  @Patch(':id')
  update(@Param('id') id: string, @Body() dto: UpdateInvoiceDto) {
    return this.invoicesService.update(id, dto);
  }

  @Delete(':id')
  remove(@Param('id') id: string) {
    return this.invoicesService.remove(id);
  }
}
