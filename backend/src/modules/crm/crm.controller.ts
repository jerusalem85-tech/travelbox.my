import { Controller, Get, Post, Put, Delete, Body, Param, Query, UseGuards } from '@nestjs/common';
import { ApiTags, ApiBearerAuth, ApiOperation } from '@nestjs/swagger';
import { AuthGuard } from '@nestjs/passport';
import { CrmService } from './crm.service';

@ApiTags('CRM')
@ApiBearerAuth()
@UseGuards(AuthGuard('jwt'))
@Controller('crm')
export class CrmController {
  constructor(private service: CrmService) {}

  @Post() create(@Body() data: any) { return this.service.create(data); }
  @Get() findAll(@Query() query: any) { return this.service.findAll(query); }
  @Get(':id') findById(@Param('id') id: string) { return this.service.findById(id); }
  @Put(':id') update(@Param('id') id: string, @Body() data: any) { return this.service.update(id, data); }
  @Delete(':id') remove(@Param('id') id: string) { return this.service.remove(id); }
}
