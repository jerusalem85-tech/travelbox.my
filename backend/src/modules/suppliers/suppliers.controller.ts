import { Controller, Get, Post, Patch, Delete, Body, Param, Query } from '@nestjs/common';
import { SuppliersService } from './suppliers.service';
import { CreateSupplierDto, UpdateSupplierDto } from './dto/create-supplier.dto';

@Controller('suppliers')
export class SuppliersController {
  constructor(private suppliers: SuppliersService) {}

  @Post()
  create(@Body() dto: CreateSupplierDto) {
    return this.suppliers.create(dto);
  }

  @Get()
  findAll(@Query('type') type?: string) {
    return this.suppliers.findAll(type);
  }

  @Get(':id')
  findById(@Param('id') id: string) {
    return this.suppliers.findById(id);
  }

  @Patch(':id')
  update(@Param('id') id: string, @Body() dto: UpdateSupplierDto) {
    return this.suppliers.update(id, dto);
  }

  @Delete(':id')
  remove(@Param('id') id: string) {
    return this.suppliers.remove(id);
  }
}
